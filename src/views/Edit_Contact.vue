<template>
  <div class="container mt-4 col-md-4 bg-body-secondary ">
    <h2 class="text-center mb-3">แก้ไขข้อมูลติดต่อ</h2>
    <form @submit.prevent="updateContact">
      <div class="mb-2">
        <input v-model="contact.contact_id" class="form-control" placeholder="รหัส" disabled />
      </div>
      <div class="mb-2">
        <input v-model="contact.fullName" class="form-control" placeholder="ชื่อ-นามสกุล" required />
      </div>
      <div class="mb-2">
        <input type="email" v-model="contact.email" class="form-control" placeholder="อีเมล" required />
      </div>
      <div class="mb-2">
        <input v-model="contact.phone" class="form-control" placeholder="เบอร์โทร" required />
      </div>
      <div class="mb-2">
        <input v-model="contact.subject" class="form-control" placeholder="หัวข้อ" required />
      </div>
      <div class="mb-2">
        <textarea v-model="contact.message" class="form-control" rows="3" placeholder="ข้อความ" required></textarea>
      </div>
      <div class="text-center mt-4 ">
      <button type="submit" class="btn btn-warning mb-4">บันทึกการแก้ไข</button> &nbsp;
      <router-link to="/contact" class="btn btn-secondary mb-4">กลับ</router-link>
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
      contact: {
        contact_id: "",
        fullName: "",
        email: "",
        phone: "",
        subject: "",
        message: ""
      },
      message: ""
    };
  },
  // โหลดข้อมูลเดิมมาแสดงในฟอร์มเมื่อเปิดหน้า
  async mounted() {
    try {
      const id = this.$route.params.id; // รับรหัสจาก URL /edit_contact/:id
      const res = await fetch("http://localhost/68707650/php_api/get_contact.php?id=" + id);
      const data = await res.json();

      if (data.error) {
        this.message = data.error;
      } else {
        this.contact = data;
      }

    } catch (err) {
      this.message = "เกิดข้อผิดพลาด: " + err.message;
    }
  },
  methods: {
    async updateContact() {
      try {
        const res = await fetch("http://localhost/68707650/php_api/update_contact.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.contact)
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
