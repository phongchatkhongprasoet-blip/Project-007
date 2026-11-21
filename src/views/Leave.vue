<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-1">📝 ระบบการลางาน</h2>
    <p class="text-muted" v-if="user">
      ผู้ใช้งาน: {{ user.firstName }} {{ user.lastName }}
      <span v-if="isEmployee">(รหัสพนักงาน {{ user.emp_id }})</span>
    </p>

    <!-- ผู้ใช้ที่ไม่ใช่พนักงาน -->
    <div v-if="!isEmployee" class="alert alert-warning">
      ระบบลางานสำหรับพนักงานเท่านั้น กรุณาเข้าสู่ระบบด้วยบัญชีพนักงาน
    </div>

    <template v-else>
      <!-- สรุปสิทธิ์การลา -->
      <div class="row mb-3">
        <div class="col-md-4 mb-2" v-for="q in quotas" :key="q.type">
          <div class="card text-center">
            <div class="card-body py-2">
              <h6 class="card-title mb-1">{{ q.type }}</h6>
              <h4 class="mb-0">{{ usedDays(q.type) }} / {{ q.max }} วัน</h4>
              <small class="text-muted">คงเหลือ {{ q.max - usedDays(q.type) }} วัน</small>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- ฟอร์มยื่นใบลา -->
        <div class="col-md-4 mb-3">
          <div class="bg-body-secondary p-3 rounded">
            <h5 class="mb-3">ยื่นใบลา</h5>
            <form @submit.prevent="addLeave">
              <label class="form-label mb-0">ประเภทการลา</label>
              <select v-model="form.leave_type" class="form-select mb-2" required>
                <option v-for="q in quotas" :key="q.type" :value="q.type">{{ q.type }}</option>
              </select>
              <label class="form-label mb-0">วันที่เริ่มลา</label>
              <input type="date" v-model="form.start_date" class="form-control mb-2" required />
              <label class="form-label mb-0">วันที่สิ้นสุด</label>
              <input type="date" v-model="form.end_date" class="form-control mb-2" required />
              <div class="mb-2">จำนวนวันลา: <b>{{ formDays }}</b> วัน</div>
              <label class="form-label mb-0">เหตุผลการลา</label>
              <textarea v-model="form.reason" class="form-control mb-3" rows="2" placeholder="ระบุเหตุผล" required></textarea>
              <div class="text-center">
                <button type="submit" class="btn btn-primary">ยื่นใบลา</button> &nbsp;
                <button type="button" class="btn btn-secondary" @click="resetForm">ยกเลิก</button>
              </div>
            </form>
            <div v-if="formMessage" :class="['alert', 'mt-3', 'mb-0', formOk ? 'alert-success' : 'alert-danger']">
              {{ formMessage }}
            </div>
          </div>
        </div>

        <!-- ประวัติการลาของฉัน -->
        <div class="col-md-8 mb-3">
          <h5>ประวัติการลาของฉัน</h5>
          <table class="table table-bordered table-striped align-middle">
            <thead class="table-dark">
              <tr>
                <th>ลำดับที่</th>
                <th>ประเภท</th>
                <th>วันที่ลา</th>
                <th class="text-center">จำนวนวัน</th>
                <th>เหตุผล</th>
                <th class="text-center">สถานะ</th>
                <th class="text-center">จัดการ</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in myLeaves" :key="item.leave_id">
                <td>{{ index + 1 }}</td>
                <td>{{ item.leave_type }}</td>
                <td class="text-nowrap">{{ showRange(item) }}</td>
                <td class="text-center">{{ item.days }}</td>
                <td>{{ item.reason }}</td>
                <td class="text-center"><span :class="['badge', badge(item.status)]">{{ item.status }}</span></td>
                <td class="text-center">
                  <button v-if="item.status === 'รออนุมัติ'" class="btn btn-danger btn-sm" @click="deleteLeave(item)">ยกเลิก</button>
                  <span v-else>-</span>
                </td>
              </tr>
              <tr v-if="myLeaves.length === 0">
                <td colspan="7" class="text-center text-muted">ยังไม่มีประวัติการลา</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- รายการใบลาทั้งหมด สำหรับหัวหน้างานพิจารณา -->
      <h5 class="mt-2">รายการใบลาทั้งหมด (หัวหน้างานพิจารณาอนุมัติ)</h5>
      <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
          <tr>
            <th>รหัสใบลา</th>
            <th>พนักงาน</th>
            <th>ประเภท</th>
            <th>วันที่ลา</th>
            <th class="text-center">จำนวนวัน</th>
            <th>เหตุผล</th>
            <th class="text-center">สถานะ</th>
            <th class="text-center">การพิจารณา</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in leaves" :key="item.leave_id">
            <td>{{ item.leave_id }}</td>
            <td>{{ item.firstName }} {{ item.lastName }}</td>
            <td>{{ item.leave_type }}</td>
            <td class="text-nowrap">{{ showRange(item) }}</td>
            <td class="text-center">{{ item.days }}</td>
            <td>{{ item.reason }}</td>
            <td class="text-center"><span :class="['badge', badge(item.status)]">{{ item.status }}</span></td>
            <td class="text-center text-nowrap">
              <template v-if="item.status === 'รออนุมัติ'">
                <button class="btn btn-success btn-sm" @click="updateStatus(item, 'อนุมัติ')">อนุมัติ</button>
                &nbsp;
                <button class="btn btn-warning btn-sm" @click="updateStatus(item, 'ไม่อนุมัติ')">ไม่อนุมัติ</button>
              </template>
              <span v-else>-</span>
            </td>
          </tr>
        </tbody>
      </table>

      <div v-if="message" class="alert alert-info">{{ message }}</div>
      <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
      <div v-if="error" class="alert alert-danger">{{ error }}</div>
    </template>
  </div>
</template>

<script>
// import ฟังก์ชันจาก Vue (Composition API)
import { ref, computed, onMounted } from "vue";

export default {
  name: "LeaveSystem", // หน้าระบบการลางาน

  setup() {
    const api = "http://localhost/68707650/php_api/";

    // ผู้ใช้ที่ Login อยู่ (เก็บไว้ใน localStorage ตอน Login)
    const user = ref(JSON.parse(localStorage.getItem("user") || "null"));
    const isEmployee = computed(() => user.value && user.value.role === "employee");

    // สิทธิ์การลาต่อปี
    const quotas = [
      { type: "ลาป่วย", max: 30 },
      { type: "ลากิจ", max: 6 },
      { type: "ลาพักร้อน", max: 10 }
    ];

    const leaves = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const message = ref("");

    const emptyForm = () => ({ leave_type: "ลาป่วย", start_date: "", end_date: "", reason: "" });
    const form = ref(emptyForm());
    const formMessage = ref("");
    const formOk = ref(false);

    // ใบลาของผู้ใช้ที่ Login
    const myLeaves = computed(() =>
      leaves.value.filter(l => isEmployee.value && Number(l.emp_id) === Number(user.value.emp_id))
    );

    // จำนวนวันที่ลาไปแล้ว (นับเฉพาะที่อนุมัติ)
    const usedDays = (type) =>
      myLeaves.value
        .filter(l => l.leave_type === type && l.status === "อนุมัติ")
        .reduce((sum, l) => sum + Number(l.days), 0);

    // คำนวณจำนวนวันจากฟอร์ม
    const formDays = computed(() => {
      if (!form.value.start_date || !form.value.end_date) return 0;
      const d = (new Date(form.value.end_date) - new Date(form.value.start_date)) / 86400000 + 1;
      return d > 0 ? d : 0;
    });

    // แสดงวันที่แบบไทย เช่น 3/11/2569
    const thaiDate = (s) => {
      const [y, m, d] = s.split("-");
      return Number(d) + "/" + Number(m) + "/" + (Number(y) + 543);
    };
    const showRange = (item) =>
      item.start_date === item.end_date
        ? thaiDate(item.start_date)
        : thaiDate(item.start_date) + " - " + thaiDate(item.end_date);

    const badge = (status) =>
      status === "อนุมัติ" ? "bg-success" : status === "ไม่อนุมัติ" ? "bg-danger" : "bg-warning text-dark";

    // ดึงข้อมูลใบลาทั้งหมด
    const fetchdata = async () => {
      try {
        const res = await fetch(api + "show_leave.php");
        if (!res.ok) throw new Error("ไม่สามารถดึงข้อมูลได้");
        leaves.value = await res.json();
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    const resetForm = () => {
      form.value = emptyForm();
      formMessage.value = "";
    };

    // ยื่นใบลา
    const addLeave = async () => {
      try {
        const res = await fetch(api + "add_leave.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ ...form.value, emp_id: user.value.emp_id })
        });
        const data = await res.json();
        formOk.value = data.success;
        formMessage.value = data.message;
        if (data.success) {
          form.value = emptyForm();
          fetchdata();
        }
      } catch (err) {
        formOk.value = false;
        formMessage.value = "เกิดข้อผิดพลาด: " + err.message;
      }
    };

    // ยกเลิกใบลา (เฉพาะที่ยังรออนุมัติ)
    const deleteLeave = async (item) => {
      if (!confirm("ต้องการยกเลิกใบลานี้ใช่หรือไม่?")) return;
      try {
        const res = await fetch(api + "delete_leave.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ leave_id: item.leave_id })
        });
        const data = await res.json();
        message.value = data.message;
        if (data.success) fetchdata();
      } catch (err) {
        message.value = "เกิดข้อผิดพลาด: " + err.message;
      }
    };

    // หัวหน้างานอนุมัติ / ไม่อนุมัติ
    const updateStatus = async (item, status) => {
      if (!confirm(status + " ใบลาของ " + item.firstName + " ใช่หรือไม่?")) return;
      try {
        const res = await fetch(api + "update_leave_status.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ leave_id: item.leave_id, status: status })
        });
        const data = await res.json();
        message.value = data.message;
        if (data.success) fetchdata();
      } catch (err) {
        message.value = "เกิดข้อผิดพลาด: " + err.message;
      }
    };

    onMounted(() => {
      fetchdata();
    });

    return { user, isEmployee, quotas, leaves, myLeaves, loading, error, message,
             form, formMessage, formOk, formDays, usedDays, showRange, badge,
             resetForm, addLeave, deleteLeave, updateStatus };
  }
};
</script>
