<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-3">รายชื่อลูกค้า</h2>
    <div class="text-end mb-3">
      <router-link to="/add_customer" class="btn btn-primary">Add+</router-link>
    </div>
    <!-- ตารางแสดงข้อมูลลูกค้า -->
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ลำดับที่</th>        <!-- index -->
          <th>รหัสลูกค้า</th>     <!-- customer_id -->
          <th>ชื่อ</th>            <!-- firstName -->
          <th>นามสกุล</th>        <!-- lastName -->
          <th>เบอร์โทร</th>       <!-- phone -->
          <th>ชื่อผู้ใช้</th>      <!-- username -->
          <th>จัดการ</th>         <!-- ปุ่มลบ -->
        </tr>
      </thead>

      <tbody>
        <!-- วนลูปข้อมูล customers -->
        <tr v-for="(item,index) in customers" :key="item.customer_id">
          <td>{{ index + 1 }}</td>       <!-- แสดงลำดับที่ (เริ่มจาก 1) -->
          <td>{{ item.customer_id }}</td> <!-- รหัสลูกค้า -->
          <td>{{ item.firstName }}</td>   <!-- ชื่อ -->
          <td>{{ item.lastName }}</td>    <!-- นามสกุล -->
          <td>{{ item.phone }}</td>       <!-- เบอร์โทร -->
          <td>{{ item.username }}</td>    <!-- ชื่อผู้ใช้ -->
          <td class="text-center">
            <!-- ปุ่มลบข้อมูล -->
            <button class="btn btn-danger btn-sm" @click="deleteCustomer(item.customer_id)">ลบ</button>
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
  name: "CustomerList", // ชื่อ component

  setup() {
    // -----------------------------
    // state (ตัวแปร reactive)
    // -----------------------------
    const customers = ref([]); // เก็บข้อมูลลูกค้า (array)
    const loading = ref(true); // สถานะโหลดข้อมูล
    const error = ref(null);   // เก็บ error
    const message = ref("");   // เก็บข้อความผลการลบ

    // -----------------------------
    // ฟังก์ชันดึงข้อมูลจาก API
    // -----------------------------
    const fetchdata = async () => {
      try {
        // เรียก API (PHP)
        const response = await fetch("http://localhost/68707650/php_api/show_customer.php");

        // ตรวจสอบว่าการเรียกสำเร็จหรือไม่
        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลได้");
        }

        // แปลง response เป็น JSON
        customers.value = await response.json();

      } catch (err) {
        // ถ้า error ให้เก็บข้อความไว้แสดง
        error.value = err.message;

      } finally {
        // ไม่ว่าจะสำเร็จหรือ error ให้หยุด loading
        loading.value = false;
      }
    };

    // -----------------------------
    // ฟังก์ชันลบข้อมูลลูกค้า
    // -----------------------------
    const deleteCustomer = async (id) => {
      // ถามยืนยันก่อนลบ
      if (!confirm("ต้องการลบข้อมูลลูกค้ารหัส " + id + " ใช่หรือไม่?")) {
        return;
      }

      try {
        // เรียก API (PHP) ส่งรหัสไปลบ
        const response = await fetch("http://localhost/68707650/php_api/delete_customer.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ customer_id: id })
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
      customers,
      loading,
      error,
      message,
      deleteCustomer
    };
  }
};
</script>
<style lang="">
    
</style>