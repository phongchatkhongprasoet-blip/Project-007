<template>
  <div class="container mt-5 col-md-4 bg-body-secondary p-4 rounded">
    <h2 class="text-center mb-3">เข้าสู่ระบบ</h2>
    <form @submit.prevent="login">
      <div class="mb-2">
        <input v-model="user.username" class="form-control" placeholder="ชื่อผู้ใช้" required />
      </div>
      <div class="mb-2">
        <input type="password" v-model="user.password" class="form-control" placeholder="รหัสผ่าน" required />
      </div>
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary">เข้าสู่ระบบ</button> &nbsp;
        <router-link to="/add_customer" class="btn btn-secondary">ลงทะเบียน</router-link>
      </div>
    </form>

    <div v-if="message" class="alert alert-danger mt-3">
      {{ message }}
    </div>
  </div>
</template>


<script>
export default {
  data() {
    return {
      user: {
        username: "",
        password: ""
      },
      message: ""
    };
  },
  methods: {
    async login() {
      try {
        const res = await fetch("http://localhost/68707650/php_api/login.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(this.user)
        });
        const data = await res.json();

        if (data.success) {
          // ✅ เก็บข้อมูลผู้ใช้ไว้ใน localStorage แล้วไปหน้าแรก
          localStorage.setItem("user", JSON.stringify(data.user));
          this.$router.push("/");
        } else {
          this.message = data.message;
        }

      } catch (err) {
        this.message = "เกิดข้อผิดพลาด: " + err.message;
      }
    }
  }
}
</script>
