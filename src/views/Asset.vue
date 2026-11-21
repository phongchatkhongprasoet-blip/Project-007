<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-3">ข้อมูลอุปกรณ์</h2>
    <div class="text-end mb-3">
      <router-link to="/add_asset" class="btn btn-primary">Add+</router-link>
    </div>
    <!-- ตารางแสดงข้อมูลอุปกรณ์ -->
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ลำดับที่</th>        <!-- index -->
          <th>รูปภาพ</th>          <!-- image -->
          <th>รหัสครุภัณฑ์</th>    <!-- asset_code -->
          <th>ชื่อครุภัณฑ์</th>     <!-- asset_name -->
          <th>วันที่ซื้อ</th>       <!-- purchase_date -->
          <th class="text-end">ราคา (บาท)</th> <!-- price -->
          <th>จัดการ</th>          <!-- ปุ่มแก้ไข / ลบ -->
        </tr>
      </thead>

      <tbody>
        <!-- วนลูปข้อมูล assets -->
        <tr v-for="(item,index) in assets" :key="item.asset_id">
          <td>{{ index + 1 }}</td>              <!-- แสดงลำดับที่ (เริ่มจาก 1) -->
          <td class="text-center">
            <!-- แสดงรูปจากโฟลเดอร์ php_api/images -->
            <img v-if="item.image" :src="imageUrl + item.image" width="80" height="60" style="object-fit: cover;" class="rounded border" />
            <span v-else class="text-muted">ไม่มีรูป</span>
          </td>
          <td>{{ item.asset_code }}</td>       <!-- รหัสครุภัณฑ์ -->
          <td>{{ item.asset_name }}</td>       <!-- ชื่อครุภัณฑ์ -->
          <td>{{ item.purchase_date }}</td>    <!-- วันที่ซื้อ -->
          <td class="text-end">{{ formatPrice(item.price) }}</td> <!-- ราคา -->
          <td class="text-center text-nowrap">
            <!-- ปุ่มแก้ไขข้อมูล: ไปหน้า edit_asset พร้อมรหัส -->
            <router-link :to="'/edit_asset/' + item.asset_id" class="btn btn-warning btn-sm">แก้ไข</router-link>
            &nbsp;
            <!-- ปุ่มลบข้อมูล -->
            <button class="btn btn-danger btn-sm" @click="deleteAsset(item.asset_id)">ลบ</button>
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
  name: "AssetList", // ชื่อ component

  setup() {
    // -----------------------------
    // state (ตัวแปร reactive)
    // -----------------------------
    const assets = ref([]);    // เก็บข้อมูลอุปกรณ์ (array)
    const loading = ref(true); // สถานะโหลดข้อมูล
    const error = ref(null);   // เก็บ error
    const message = ref("");   // เก็บข้อความผลการลบ
    const imageUrl = "http://localhost/68707650/php_api/images/"; // ที่อยู่รูปภาพ

    // -----------------------------
    // ฟังก์ชันจัดรูปแบบราคา เช่น 25000 -> 25,000.00
    // -----------------------------
    const formatPrice = (price) => {
      if (price === null || price === "") return "-";
      return Number(price).toLocaleString("th-TH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    };

    // -----------------------------
    // ฟังก์ชันดึงข้อมูลจาก API
    // -----------------------------
    const fetchdata = async () => {
      try {
        // เรียก API (PHP)
        const response = await fetch("http://localhost/68707650/php_api/show_asset.php");

        // ตรวจสอบว่าการเรียกสำเร็จหรือไม่
        if (!response.ok) {
          throw new Error("ไม่สามารถดึงข้อมูลได้");
        }

        // แปลง response เป็น JSON
        assets.value = await response.json();

      } catch (err) {
        // ถ้า error ให้เก็บข้อความไว้แสดง
        error.value = err.message;

      } finally {
        // ไม่ว่าจะสำเร็จหรือ error ให้หยุด loading
        loading.value = false;
      }
    };

    // -----------------------------
    // ฟังก์ชันลบข้อมูลอุปกรณ์
    // -----------------------------
    const deleteAsset = async (id) => {
      // ถามยืนยันก่อนลบ
      if (!confirm("ต้องการลบข้อมูลอุปกรณ์รหัส " + id + " ใช่หรือไม่?")) {
        return;
      }

      try {
        // เรียก API (PHP) ส่งรหัสไปลบ
        const response = await fetch("http://localhost/68707650/php_api/delete_asset.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ asset_id: id })
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
      assets,
      loading,
      error,
      message,
      imageUrl,
      formatPrice,
      deleteAsset
    };
  }
};
</script>
