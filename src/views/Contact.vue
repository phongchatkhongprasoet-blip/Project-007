<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-3">ข้อมูลติดต่อเรา</h2>
    <div class="text-end mb-3">
      <router-link to="/add_contact" class="btn btn-primary">Add+</router-link>
    </div>
    <!-- ตารางแสดงข้อมูลติดต่อเรา -->
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ลำดับที่</th>         <!-- index -->
          <th>รหัส</th>             <!-- contact_id -->
          <th>ชื่อ-นามสกุล</th>     <!-- fullName -->
          <th>อีเมล</th>            <!-- email -->
          <th>เบอร์โทร</th>         <!-- phone -->
          <th>หัวข้อ</th>           <!-- subject -->
          <th>ข้อความ</th>          <!-- message -->
          <th>จัดการ</th>           <!-- ปุ่มแก้ไข / ลบ -->
        </tr>
      </thead>

      <tbody>
        <!-- วนลูปข้อมูล contacts -->
        <tr v-for="(item,index) in contacts" :key="item.contact_id">
          <td>{{ index + 1 }}</td>         <!-- แสดงลำดับที่ (เริ่มจาก 1) -->
          <td>{{ item.contact_id }}</td>   <!-- รหัส -->
          <td>{{ item.fullName }}</td>     <!-- ชื่อ-นามสกุล -->
          <td>{{ item.email }}</td>        <!-- อีเมล -->
          <td>{{ item.phone }}</td>        <!-- เบอร์โทร -->
          <td>{{ item.subject }}</td>      <!-- หัวข้อ -->
          <td>{{ item.message }}</td>      <!-- ข้อความ -->
          <td class="text-center text-nowrap">
            <!-- ปุ่มแก้ไขข้อมูล: ไปหน้า edit_contact พร้อมรหัส -->
            <router-link :to="'/edit_contact/' + item.contact_id" class="btn btn-warning btn-sm">แก้ไข</router-link>
            &nbsp;
            <!-- ปุ่มลบข้อมูล -->
            <button class="btn btn-danger btn-sm" @click="deleteContact(item.contact_id)">ลบ</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Message: แสดงผลการลบข้อมูล -->
    <div v-if="message" class="alert alert-info">
      {{ message }}
    </div>

    <!-- Loading: แสดงระหว่างรอข้อมูล -->
    <div v-if="loading" class="text-center">
      <p>กำลังโหลดข้อมูล...</p>
    </div>

    <!-- Error: แสดงเมื่อเกิดข้อผิดพลาด -->
    <div v-if="error" class="alert alert-danger">
      {{ error }}
    </div>
  </div>
</template>

<script>
// import ฟังก์ชันจาก Vue (Composition API)
import { ref, onMounted } from "vue";

export default {
  name: "ContactList", // ชื่อ component

  setup() {
    // -----------------------------
    // state (ตัวแปร reactive)
    // -----------------------------
    const contacts = ref([]);  // เก็บข้อมูลติดต่อเรา (array)
    const loading = ref(true); // สถานะโหลดข้อมูล
    const error = ref(null);   // เก็บ error
    const message = ref("");   // เก็บข้อความผลการลบ

    // -----------------------------
    // ฟังก์ชันดึงข้อมูลจาก API
    // -----------------------------
    const fetchdata = async () => {
      try {
        // เรียก API (PHP)
        const response = await fetch("http://localhost/68707650/php_api/show_contact.php");

        // ตรวจสอบว่าการเรียกสำเร็จหรือไม่
        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลได้");
        }

        // แปลง response เป็น JSON
        contacts.value = await response.json();

      } catch (err) {
        // ถ้า error ให้เก็บข้อความไว้แสดง
        error.value = err.message;

      } finally {
        // ไม่ว่าจะสำเร็จหรือ error ให้หยุด loading
        loading.value = false;
      }
    };

    // -----------------------------
    // ฟังก์ชันลบข้อมูลติดต่อ
    // -----------------------------
    const deleteContact = async (id) => {
      // ถามยืนยันก่อนลบ
      if (!confirm("ต้องการลบข้อมูลติดต่อรหัส " + id + " ใช่หรือไม่?")) {
        return;
      }

      try {
        // เรียก API (PHP) ส่งรหัสไปลบ
        const response = await fetch("http://localhost/68707650/php_api/delete_contact.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ contact_id: id })
        });

        const data = await response.json();
        message.value = data.message;

        // ถ้าลบสำเร็จ ให้โหลดข้อมูลใหม่
        if (data.success) {
          fetchdata();
        }

      } catch (err) {
        message.value = "เกิดข้อผิดพลาด: " + err.message;
      }
    };

    // -----------------------------
    // lifecycle: ทำงานเมื่อ component โหลดเสร็จ
    // -----------------------------
    onMounted(() => {
      fetchdata(); // เรียก API ทันที
    });

    // -----------------------------
    // return ค่าไปใช้ใน template
    // -----------------------------
    return {
      contacts,
      loading,
      error,
      message,
      deleteContact
    };
  }
};
</script>
