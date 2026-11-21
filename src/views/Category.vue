<template>
  <div class="container mt-4 col-md-8">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-3">🏷️ ประเภทสินค้า</h2>
    <div class="text-end mb-3">
      <button class="btn btn-primary" @click="openAdd">เพิ่มประเภทสินค้า +</button>
    </div>

    <!-- ตารางประเภทสินค้า -->
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ลำดับที่</th>
          <th>รหัสประเภท</th>
          <th>ชื่อประเภท</th>
          <th class="text-center">จำนวนสินค้า</th>
          <th class="text-center">การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in categories" :key="item.category_id">
          <td>{{ index + 1 }}</td>
          <td>{{ item.category_id }}</td>
          <td>{{ item.category_name }}</td>
          <td class="text-center">{{ item.product_count }}</td>
          <td class="text-center text-nowrap">
            <button class="btn btn-warning btn-sm" @click="openEdit(item)">แก้ไข</button>
            &nbsp;
            <button class="btn btn-danger btn-sm" @click="deleteCategory(item)">ลบ</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="message" class="alert alert-info">{{ message }}</div>
    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Modal เพิ่ม / แก้ไขประเภทสินค้า -->
    <div v-if="showModal">
      <div class="modal d-block" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ form.category_id ? "แก้ไขประเภทสินค้า" : "เพิ่มประเภทสินค้า" }}</h5>
              <button type="button" class="btn-close" @click="showModal = false"></button>
            </div>
            <form @submit.prevent="saveCategory">
              <div class="modal-body">
                <div v-if="form.category_id" class="mb-2">
                  <label class="form-label mb-0">รหัสประเภท</label>
                  <input v-model="form.category_id" class="form-control" disabled />
                </div>
                <label class="form-label mb-0">ชื่อประเภท</label>
                <input v-model="form.category_name" class="form-control" placeholder="เช่น ของหวาน" required />
                <div v-if="modalMessage" class="alert alert-danger mt-2 mb-0 py-2">{{ modalMessage }}</div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success w-100">{{ form.category_id ? "บันทึกการแก้ไข" : "เพิ่มประเภทสินค้า" }}</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div class="modal-backdrop show"></div>
    </div>
  </div>
</template>

<script>
// import ฟังก์ชันจาก Vue (Composition API)
import { ref, onMounted } from "vue";

export default {
  name: "CategoryManage", // หน้าจัดการข้อมูลประเภทสินค้า

  setup() {
    const api = "http://localhost/68707650/php_api/";

    const categories = ref([]);
    const loading = ref(true);
    const error = ref(null);
    const message = ref("");

    const showModal = ref(false);
    const form = ref({ category_id: "", category_name: "" });
    const modalMessage = ref("");

    // ดึงข้อมูลประเภทสินค้า
    const fetchdata = async () => {
      try {
        const res = await fetch(api + "show_category.php");
        if (!res.ok) throw new Error("ไม่สามารถดึงข้อมูลได้");
        categories.value = await res.json();
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    const openAdd = () => {
      form.value = { category_id: "", category_name: "" };
      modalMessage.value = ""; message.value = "";
      showModal.value = true;
    };
    const openEdit = (item) => {
      form.value = { category_id: item.category_id, category_name: item.category_name };
      modalMessage.value = ""; message.value = "";
      showModal.value = true;
    };

    // บันทึก (เพิ่ม หรือ แก้ไข)
    const saveCategory = async () => {
      try {
        const target = form.value.category_id ? "update_category.php" : "add_category.php";
        const res = await fetch(api + target, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(form.value)
        });
        const data = await res.json();

        if (data.success) {
          showModal.value = false;
          message.value = data.message;
          fetchdata();
        } else {
          modalMessage.value = data.message; // เช่น ชื่อซ้ำ
        }
      } catch (err) {
        modalMessage.value = "เกิดข้อผิดพลาด: " + err.message;
      }
    };

    // ลบประเภทสินค้า (ลบไม่ได้ถ้ายังมีสินค้าในประเภทนั้น)
    const deleteCategory = async (item) => {
      if (!confirm("ต้องการลบประเภท \"" + item.category_name + "\" ใช่หรือไม่?")) return;
      try {
        const res = await fetch(api + "delete_category.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ category_id: item.category_id })
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

    return { categories, loading, error, message, showModal, form, modalMessage,
             openAdd, openEdit, saveCategory, deleteCategory };
  }
};
</script>
