<template>
  <div class="container mt-4 col-md-4 bg-body-secondary ">
    <h2 class="text-center mb-3">แก้ไขข้อมูลอุปกรณ์</h2>
    <form @submit.prevent="updateAsset">
      <div class="mb-2">
        <input v-model="asset.asset_id" class="form-control" placeholder="รหัส" disabled />
      </div>
      <div class="mb-2">
        <input v-model="asset.asset_code" class="form-control" placeholder="รหัสครุภัณฑ์" required />
      </div>
      <div class="mb-2">
        <input v-model="asset.asset_name" class="form-control" placeholder="ชื่อครุภัณฑ์" required />
      </div>
      <div class="mb-2">
        <label class="form-label mb-0 small">วันที่ซื้อ</label>
        <input type="date" v-model="asset.purchase_date" class="form-control" />
      </div>
      <div class="mb-2">
        <input type="number" step="0.01" min="0" v-model="asset.price" class="form-control" placeholder="ราคา (บาท)" />
      </div>
      <!-- รูปปัจจุบัน / รูปใหม่ที่เลือก -->
      <div class="text-center mb-2">
        <img v-if="preview" :src="preview" width="150" class="rounded border" />
        <img v-else-if="asset.image" :src="imageUrl + asset.image" width="150" class="rounded border" />
      </div>
      <div class="mb-2">
        <label class="form-label mb-0 small">เปลี่ยนรูปภาพ (ถ้าไม่เลือกจะใช้รูปเดิม)</label>
        <input type="file" accept="image/*" class="form-control" @change="onFileChange" />
      </div>
      <div class="text-center mt-4 ">
      <button type="submit" class="btn btn-warning mb-4">บันทึกการแก้ไข</button> &nbsp;
      <router-link to="/asset" class="btn btn-secondary mb-4">กลับ</router-link>
      </div>
    </form>

    <div v-if="message" class="alert alert-info mt-3">
      {{ message }}
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      asset: {
        asset_id: "",
        asset_code: "",
        asset_name: "",
        purchase_date: "",
        price: "",
        image: ""
      },
      file: null,     // ไฟล์รูปใหม่ที่เลือก
      preview: "",    // URL สำหรับแสดงตัวอย่างรูปใหม่
      imageUrl: "http://localhost/68707650/php_api/images/",
      message: ""
    };
  },
  // โหลดข้อมูลเดิมมาแสดงในฟอร์มเมื่อเปิดหน้า
  async mounted() {
    try {
      const id = this.$route.params.id; // รับรหัสจาก URL /edit_asset/:id
      const res = await fetch("http://localhost/68707650/php_api/get_asset.php?id=" + id);
      const data = await res.json();

      if (data.error) {
        this.message = data.error;
      } else {
        this.asset = data;
      }

    } catch (err) {
      this.message = "เกิดข้อผิดพลาด: " + err.message;
    }
  },
  methods: {
    // เมื่อเลือกไฟล์รูปใหม่
    onFileChange(e) {
      this.file = e.target.files[0] || null;
      this.preview = this.file ? URL.createObjectURL(this.file) : "";
    },
    async updateAsset() {
      try {
        // ใช้ FormData เพราะอาจต้องส่งไฟล์รูปใหม่ไปด้วย
        const formData = new FormData();
        formData.append("asset_id", this.asset.asset_id);
        formData.append("asset_code", this.asset.asset_code);
        formData.append("asset_name", this.asset.asset_name);
        formData.append("purchase_date", this.asset.purchase_date || "");
        formData.append("price", this.asset.price ?? "");
        if (this.file) {
          formData.append("image", this.file);
        }

        const res = await fetch("http://localhost/68707650/php_api/update_asset.php", {
          method: "POST",
          body: formData
        });
        const data = await res.json();
        this.message = data.message;

      } catch (err) {
        this.message = "เกิดข้อผิดพลาด: " + err.message;
      }
    }
  }
}
</script>
