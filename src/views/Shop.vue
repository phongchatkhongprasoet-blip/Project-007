<template>
  <div class="container mt-4">
    <!-- หัวข้อหน้า -->
    <h2 class="text-center mb-3">เมนูสินค้า</h2>

    <!-- เลือกดูสินค้าตามประเภท -->
    <div class="text-center mb-3">
      <span class="me-2">เลือกประเภท :</span>
      <select class="form-select d-inline-block w-auto" v-model="categoryId" @change="fetchProducts">
        <option value="">-- ทั้งหมด --</option>
        <option v-for="c in categories" :key="c.category_id" :value="c.category_id">{{ c.category_name }}</option>
      </select>
    </div>
    <div class="text-center mb-4">
      <button class="btn btn-sm me-2" :class="categoryId === '' ? 'btn-success' : 'btn-outline-success'" @click="selectCategory('')">ทั้งหมด</button>
      <button v-for="c in categories" :key="c.category_id" class="btn btn-sm me-2"
        :class="categoryId == c.category_id ? 'btn-success' : 'btn-outline-success'" @click="selectCategory(c.category_id)">
        {{ c.category_name }}
      </button>
    </div>

    <!-- ตะกร้าสินค้า -->
    <div v-if="cart.length" class="alert alert-success d-flex justify-content-between align-items-center">
      <span>🛒 สั่งแล้ว {{ totalQty }} รายการ รวม {{ formatPrice(totalPrice) }} บาท</span>
      <button class="btn btn-sm btn-outline-danger" @click="cart = []">ล้างตะกร้า</button>
    </div>

    <!-- การ์ดแสดงสินค้า -->
    <div class="row g-3">
      <div class="col-6 col-md-3" v-for="item in products" :key="item.product_id">
        <div class="card h-100 shadow-sm text-center">
          <img :src="imageUrl + item.image" class="card-img-top" style="height: 150px; object-fit: cover;" />
          <div class="card-body p-2">
            <h6 class="card-title mb-1">{{ item.product_name }}</h6>
            <small class="text-muted d-block mb-2">{{ formatPrice(item.price) }} บาท</small>
            <button class="btn btn-success btn-sm" @click="addToCart(item)">สั่งซื้อ</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading / Error -->
    <div v-if="loading" class="text-center mt-3"><p>กำลังโหลดข้อมูล...</p></div>
    <div v-if="error" class="alert alert-danger mt-3">{{ error }}</div>
  </div>
</template>

<script>
// import ฟังก์ชันจาก Vue (Composition API)
import { ref, computed, onMounted } from "vue";

export default {
  name: "ShopMenu", // หน้าสั่งซื้อสินค้าของลูกค้า

  setup() {
    const api = "http://localhost/68707650/php_api/";
    const imageUrl = api + "images/";

    const products = ref([]);   // สินค้า
    const categories = ref([]); // ประเภทสินค้า
    const categoryId = ref(""); // ประเภทที่เลือก ("" = ทั้งหมด)
    const cart = ref([]);       // ตะกร้าสินค้า
    const loading = ref(true);
    const error = ref(null);

    // จัดรูปแบบราคา เช่น 65 -> 65.00
    const formatPrice = (p) => Number(p).toLocaleString("th-TH", { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // ดึงประเภทสินค้า
    const fetchCategories = async () => {
      const res = await fetch(api + "show_category.php");
      categories.value = await res.json();
    };

    // ดึงสินค้า (กรองตามประเภทที่เลือก)
    const fetchProducts = async () => {
      try {
        loading.value = true;
        const url = api + "show_product.php" + (categoryId.value ? "?category_id=" + categoryId.value : "");
        const res = await fetch(url);
        if (!res.ok) throw new Error("ไม่สามารถดึงข้อมูลได้");
        products.value = await res.json();
      } catch (err) {
        error.value = err.message;
      } finally {
        loading.value = false;
      }
    };

    // เลือกประเภทจากปุ่ม
    const selectCategory = (id) => {
      categoryId.value = id;
      fetchProducts();
    };

    // เพิ่มสินค้าลงตะกร้า
    const addToCart = (item) => {
      const found = cart.value.find((c) => c.product_id === item.product_id);
      if (found) found.qty++;
      else cart.value.push({ ...item, qty: 1 });
    };
    const totalQty = computed(() => cart.value.reduce((s, c) => s + c.qty, 0));
    const totalPrice = computed(() => cart.value.reduce((s, c) => s + c.qty * Number(c.price), 0));

    onMounted(() => {
      fetchCategories();
      fetchProducts();
    });

    return { products, categories, categoryId, cart, loading, error, imageUrl,
             formatPrice, fetchProducts, selectCategory, addToCart, totalQty, totalPrice };
  }
};
</script>
