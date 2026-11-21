<template>
  <div class="container mt-4 col-md-4 bg-body-secondary ">
    <h2 class="text-center mb-3">ติดต่อเรา</h2>
    <form @submit.prevent="addContact">
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
      <button type="submit" class="btn btn-primary mb-4">บันทึก</button> &nbsp;
      <button type="reset" class="btn btn-secondary mb-4">ยกเลิก</button>
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
        fullName: "",
        email: "",
        phone: "",
        subject: "",
        message: ""
      },
      message: ""
    };
  },
  methods: {
    async addContact() {
      try {
        const res = await fetch("http://localhost/68707650/php_api/add_contact.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.contact)
        });
        const data = await res.json();
        this.message = data.message;

        if (data.success) {
          // ✅ เคลียร์ข้อมูลใน textbox หลังบันทึกสำเร็จ
          this.contact = { fullName: "", email: "", phone: "", subject: "", message: "" };
        }

      } catch (err) {
        this.message = "เกิดข้อผิดพลาด: " + err.message;
      }
    }
  }
}
</script>
