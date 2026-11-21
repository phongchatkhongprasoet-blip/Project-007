<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="mb-3">🍲 รายการสินค้า</h2>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <button class="btn btn-primary" @click="openAdd">เพิ่มสินค้า +</button>
      <!-- กรองตามประเภทสินค้า -->
      <div>
        <span class="me-2">ประเภทสินค้า :</span>
        <select class="form-select d-inline-block w-auto" v-model="filterCategory" @change="fetchProducts">
          <option value="">ทั้งหมด</option>
          <option v-for="c in categories" :key="c.category_id" :value="c.category_id">{{ c.category_name }}</option>
        </select>
      </div>
    </div>

    <!-- ตารางสินค้า -->
    <table class="table table-bordered table-striped align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>ชื่อสินค้า</th>
          <th>ประเภท</th>
          <th class="text-end">ราคา</th>
          <th class="text-end">จำนวน</th>
          <th class="text-center">รูปภาพ</th>
          <th class="text-center">การจัดการ</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in products" :key="item.product_id">
          <td>{{ item.product_id }}</td>
          <td>{{ item.product_name }}</td>
          <td>{{ item.category_name || "-" }}</td>
          <td class="text-end">{{ formatPrice(item.price) }}</td>
          <td class="text-end">{{ item.stock }}</td>
          <td class="text-center">
            <img v-if="item.image" :src="imageUrl + item.image" width="70" height="50" style="object-fit: cover;" class="rounded border" />
          </td>
          <td class="text-center text-nowrap">
            <button class="btn btn-warning btn-sm" @click="openEdit(item)">แก้ไข</button>
            &nbsp;
            <button class="btn btn-danger btn-sm" @click="deleteProduct(item.product_id)">ลบ</button>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="message" class="alert alert-info">{{ message }}</div>
    <div v-if="loading" class="text-center"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger">{{ error }}</div>

    <!-- Modal เพิ่ม / แก้ไขสินค้า -->
    <div v-if="showModal">
      <div class="modal d-block" tabindex="-1">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">{{ form.product_id ? "แก้ไขสินค้า" : "เพิ่มสินค้าใหม่" }}</h5>
              <button type="button" class="btn-close" @click="closeModal"></button>
            </div>
            <form @submit.prevent="saveProduct">
              <div class="modal-body">
                <label class="form-label mb-0">ชื่อสินค้า</label>
                <input v-model="form.product_name" class="form-control mb-2" required />
                <label class="form-label mb-0">ประเภทสินค้า</label>
                <select v-model="form.category_id" class="form-select mb-2" required>
                  <option value="">-- เลือกประเภท --</option>
                  <option v-for="c in categories" :key="c.category_id" :value="c.category_id">{{ c.category_name }}</option>
                </select>
                <label class="form-label mb-0">ราคา</label>
                <input type="number" step="0.01" min="0" v-model="form.price" class="form-control mb-2" required />
                <label class="form-label mb-0">จำนวน</label>
                <input type="number" min="0" v-model="form.stock" class="form-control mb-2" />
                <label class="form-label mb-0">รูปภาพ</label>
                <input type="file" accept="image/*" class="form-control mb-2" @change="onFileChange" />
                <div class="text-center">
                  <img v-if="preview" :src="preview" width="120" class="rounded border" />
                  <img v-else-if="form.image" :src="imageUrl + form.image" width="120" class="rounded border" />
                </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-success w-100">{{ form.product_id ? "บันทึกการแก้ไข" : "เพิ่มสินค้า" }}</button>
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
  name: "ProductManage", // หน้าจัดการข้อมูลสินค้า

  setup() {
    const api = "http://localhost/68707650/php_api/";
    const imageUrl = api + "images/";

    const products = ref([]);
    const categories = ref([]);
    const filterCategory = ref("");
    const loading = ref(true);
    const error = ref(null);
    const message = ref("");

    // ฟอร์มใน modal
    const showModal = ref(false);
    const emptyForm = { product_id: "", product_name: "", category_id: "", price: "", stock: "", image: "" };
    const form = ref({ ...emptyForm });
    const file = ref(null);
    const preview = ref("");

    const formatPrice = (p) => Number(p).toLocaleString("th-TH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    const fetchCategories = async () => {
      const res = await fetch(api + "show_category.php");
      categories.value = await res.json();
    };

    const fetchProducts = async () => {
      try {
        const url = api + "show_product.php" + (filterCategory.value ? "?category_id=" + filterCategory.value : "");
        const res = await fetch(url);
        if (!res.ok) throw new Error("ไม่สามารถดึงข้อมูลได้");
        products.value = await res.json();
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // เปิด modal เพิ่ม / แก้ไข
    const openAdd = () => {
      form.value = { ...emptyForm };
      file.value = null; preview.value = ""; message.value = "";
      showModal.value = true;
    };
    const openEdit = (item) => {
      form.value = { ...item };
      file.value = null; preview.value = ""; message.value = "";
      showModal.value = true;
    };
    const closeModal = () => { showModal.value = false; };

    const onFileChange = (e) => {
      file.value = e.target.files[0] || null;
      preview.value = file.value ? URL.createObjectURL(file.value) : "";
    };

    // บันทึก (เพิ่ม หรือ แก้ไข) ส่งแบบ FormData เพราะมีรูปภาพ
    const saveProduct = async () => {
      try {
        const formData = new FormData();
        if (form.value.product_id) formData.append("product_id", form.value.product_id);
        formData.append("product_name", form.value.product_name);
        formData.append("category_id", form.value.category_id);
        formData.append("description", form.value.description || "");
        formData.append("price", form.value.price);
        formData.append("stock", form.value.stock ?? "");
        if (file.value) formData.append("image", file.value);

        const target = form.value.product_id ? "update_product.php" : "add_product.php";
        const res = await fetch(api + target, { method: "POST", body: formData });
        const data = await res.json();
        message.value = data.message;

        if (data.success) {
          showModal.value = false;
          fetchProducts();
        }
      } catch (err) {
        message.value = "เกิดข้อผิดพลาด: " + err.message;
      }
    };

    // ลบสินค้า
    const deleteProduct = async (id) => {
      if (!confirm("ยืนยันการลบสินค้านี้หรือไม่?")) return;
      try {
        const res = await fetch(api + "delete_product.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ product_id: id })
        });
        const data = await res.json();
        message.value = data.message;
        if (data.success) fetchProducts();
      } catch (err) {
        message.value = "เกิดข้อผิดพลาด: " + err.message;
      }
    };

    onMounted(() => {
      fetchCategories();
      fetchProducts();
    });

    return { products, categories, filterCategory, loading, error, message, imageUrl,
             showModal, form, preview, formatPrice, fetchProducts,
             openAdd, openEdit, closeModal, onFileChange, saveProduct, deleteProduct };
  }
};
</script>
