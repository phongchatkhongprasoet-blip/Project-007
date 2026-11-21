<template>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <router-link class="navbar-brand fw-bold" to="/">FK MONKEY</router-link>
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#navbarNav"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <router-link class="nav-link" to="/">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" to="/shop">Show Product</router-link>
          </li>
           <li class="nav-item" v-if="user">
            <router-link class="nav-link" to="/customer">Customer</router-link>
          </li>
          <li class="nav-item" v-if="user">
            <router-link class="nav-link" to="/employee">Employee</router-link>
          </li>
          <li class="nav-item" v-if="user">
            <router-link class="nav-link" to="/contact">Contact</router-link>
          </li>
          <li class="nav-item" v-if="user">
            <router-link class="nav-link" to="/asset">Asset</router-link>
          </li>
          <li class="nav-item" v-if="user">
            <router-link class="nav-link" to="/product">Product</router-link>
          </li>
          <li class="nav-item" v-if="user">
            <router-link class="nav-link" to="/category">Category</router-link>
          </li>
           <li class="nav-item">
            <router-link class="nav-link" to="/about">About</router-link>
          </li>
        </ul>

        <!-- ยังไม่ได้ Login แสดงปุ่ม Login / Login แล้วแสดงชื่อผู้ใช้และปุ่ม Logout -->
        <div class="d-flex align-items-center ms-lg-3">
          <router-link v-if="!user" to="/login" class="btn btn-outline-light btn-sm">Login</router-link>
          <template v-else>
            <span class="text-white me-2">สวัสดี, {{ user.firstName }} {{ user.lastName }}</span>
            <button class="btn btn-danger btn-sm" @click="logout">Logout</button>
          </template>
        </div>
      </div>
    </div>
  </nav>
</template>

<script>
export default {
  name: "Navbar",
  data() {
    return {
      user: null
    };
  },
  mounted() {
    this.loadUser();
  },
  watch: {
    // เปลี่ยนหน้าเมื่อไหร่ให้อ่านสถานะการ Login ใหม่
    $route() {
      this.loadUser();
    }
  },
  methods: {
    loadUser() {
      const data = localStorage.getItem("user");
      this.user = data ? JSON.parse(data) : null;
    },
    logout() {
      if (!confirm("ต้องการออกจากระบบหรือไม่?")) return;
      localStorage.removeItem("user");
      this.user = null;
      this.$router.push("/login");
    }
  }
}
</script>


