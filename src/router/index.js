import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/about',
    name: 'about',
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () => import(/* webpackChunkName: "about" */ '../views/AboutView.vue')
  },
  {
    path: '/customer',
    name: 'customer',
    meta: { requiresAuth: true },
    component: () => import('../views/Customer.vue')
  },
  {
    path: '/employee',
    name: 'employee',
    meta: { requiresAuth: true },
    component: () => import('../views/Employee.vue')
  },
  {
    path: '/add_customer',
    name: 'add_customer',
    component: () => import('../views/Add_Customer.vue')
  },
  {
    path: '/add_employee',
    name: 'add_employee',
    meta: { requiresAuth: true },
    component: () => import('../views/Add_Employee.vue')
  },
  {
    path: '/contact',
    name: 'contact',
    meta: { requiresAuth: true },
    component: () => import('../views/Contact.vue')
  },
  {
    path: '/add_contact',
    name: 'add_contact',
    meta: { requiresAuth: true },
    component: () => import('../views/Add_Contact.vue')
  },
  {
    path: '/edit_contact/:id',
    name: 'edit_contact',
    meta: { requiresAuth: true },
    component: () => import('../views/Edit_Contact.vue')
  },
  {
    path: '/asset',
    name: 'asset',
    meta: { requiresAuth: true },
    component: () => import('../views/Asset.vue')
  },
  {
    path: '/add_asset',
    name: 'add_asset',
    meta: { requiresAuth: true },
    component: () => import('../views/Add_Asset.vue')
  },
  {
    path: '/edit_asset/:id',
    name: 'edit_asset',
    meta: { requiresAuth: true },
    component: () => import('../views/Edit_Asset.vue')
  },
  {
    path: '/shop',
    name: 'shop',
    component: () => import('../views/Shop.vue')
  },
  {
    path: '/product',
    name: 'product',
    meta: { requiresAuth: true },
    component: () => import('../views/Product.vue')
  },
  {
    path: '/category',
    name: 'category',
    meta: { requiresAuth: true },
    component: () => import('../views/Category.vue')
  },
  {
    path: '/leave',
    name: 'leave',
    meta: { requiresAuth: true },
    component: () => import('../views/Leave.vue')
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('../views/Login.vue')
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

// ตรวจสอบการ Login ก่อนเข้าหน้าที่ต้องเข้าสู่ระบบ
router.beforeEach((to, from, next) => {
  const user = localStorage.getItem('user')
  if (to.meta.requiresAuth && !user) {
    next('/login')
  } else if (to.name === 'login' && user) {
    next('/')
  } else {
    next()
  }
})

export default router
