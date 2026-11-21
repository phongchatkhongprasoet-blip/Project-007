<template>
  <div class="container mt-4 col-md-4 bg-body-secondary ">
    <h2 class="text-center mb-3">เพิ่มข้อมูลอุปกรณ์</h2>
    <form @submit.prevent="addAsset" ref="assetForm">
      <div class="mb-2">
        <input v-model="asset.asset_code" class="form-control" placeholder="รหัสครุภัณฑ์ เช่น IT006" required />
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
      <div class="mb-2">
        <label class="form-label mb-0 small">รูปภาพ</label>
        <input type="file" accept="image/*" class="form-control" @change="onFileChange" />
      </div>
      <!-- แสดงตัวอย่างรูปที่เลือก -->
      <div v-if="preview" class="text-center mb-2">
        <img :src="preview" width="150" class="rounded border" />
      </div>
      <div class="text-center mt-4 ">
      <button type="submit" class="btn btn-primary mb-4">บันทึก</button> &nbsp;
      <button type="reset" class="btn btn-secondary mb-4" @click="resetForm">ยกเลิก</button>
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
        asset_code: "",
        asset_name: "",
        purchase_date: "",
        price: ""
      },
      file: null,     // ไฟล์รูปที่เลือก
      preview: "",    // URL สำหรับแสดงตัวอย่างรูป
      message: ""
    };
  },
  methods: {
    // เมื่อเลือกไฟล์รูป
    onFileChange(e) {
      this.file = e.target.files[0] || null;
      this.preview = this.file ? URL.createObjectURL(this.file) : "";
    },
    resetForm() {
      this.asset = { asset_code: "", asset_name: "", purchase_date: "", price: "" };
      this.file = null;
      this.preview = "";
    },
    async addAsset() {
      try {
        // ใช้ FormData เพราะต้องส่งไฟล์รูปไปด้วย
        const formData = new FormData();
        formData.append("asset_code", this.asset.asset_code);
        formData.append("asset_name", this.asset.asset_name);
        formData.append("purchase_date", this.asset.purchase_date);
        formData.append("price", this.asset.price);
        if (this.file) {
          formData.append("image", this.file);
        }

        const res = await fetch("http://localhost/68707650/php_api/add_asset.php", {
          method: "POST",
          body: formData
        });
        const data = await res.json();
        this.message = data.message;

        if (data.success) {
          // ✅ เคลียร์ข้อมูลในฟอร์มหลังบันทึกสำเร็จ
          this.$refs.assetForm.reset();
          this.resetForm();
        }

      } catch (err) {
        this.message = "เกิดข้อผิดพลาด: " + err.message;
      }
    }
  }
}
</script>
