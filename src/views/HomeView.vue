<template>
  <div class="container mt-5">
    <div class="p-5 bg-body-secondary rounded text-center">
      <h1 class="fw-bold">ระบบการลางาน</h1>
      <p class="lead">FK MONKEY — ยื่นใบลา ตรวจสอบสิทธิ์การลา และติดตามสถานะการอนุมัติ</p>

      <!-- ยังไม่ได้ Login -->
      <div v-if="!user">
        <p>กรุณาเข้าสู่ระบบเพื่อใช้งานระบบลางาน</p>
        <router-link to="/login" class="btn btn-primary btn-lg">เข้าสู่ระบบ</router-link>
      </div>

      <!-- Login แล้ว -->
      <div v-else>
        <p>ยินดีต้อนรับ คุณ {{ user.firstName }} {{ user.lastName }}</p>
        <router-link to="/leave" class="btn btn-success btn-lg">ไปที่ระบบลางาน</router-link>
      </div>
    </div>

    <div class="row mt-4 text-center">
      <div class="col-md-4 mb-3">
        <div class="card h-100"><div class="card-body">
          <h5>📝 ยื่นใบลา</h5>
          <p class="mb-0">ลาป่วย ลากิจ ลาพักร้อน ระบบคำนวณจำนวนวันให้อัตโนมัติ</p>
        </div></div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card h-100"><div class="card-body">
          <h5>📊 สิทธิ์การลา</h5>
          <p class="mb-0">แสดงจำนวนวันที่ลาไปแล้วและวันลาคงเหลือของแต่ละประเภท</p>
        </div></div>
      </div>
      <div class="col-md-4 mb-3">
        <div class="card h-100"><div class="card-body">
          <h5>✅ อนุมัติการลา</h5>
          <p class="mb-0">หัวหน้างานพิจารณาอนุมัติ / ไม่อนุมัติ และติดตามสถานะได้ทันที</p>
        </div></div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'HomeView',
  data() {
    return {
      user: JSON.parse(localStorage.getItem("user") || "null")
    };
  }
}
</script>
