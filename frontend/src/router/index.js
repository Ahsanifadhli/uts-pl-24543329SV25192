import { createRouter, createWebHistory } from 'vue-router'
import LoginView from '../views/LoginView.vue'
import CatatanView from '../views/CatatanView.vue'
import { isLoggedIn } from '../lib/auth'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', name: 'login', component: LoginView },
    { path: '/catatan', name: 'catatan', component: CatatanView, meta: { requiresAuth: true } },
  ],
})

// Guard: halaman yang butuh autentikasi dialihkan ke login bila belum punya token
router.beforeEach((to) => {
  if (to.meta.requiresAuth && !isLoggedIn()) {
    return { name: 'login' }
  }
  if (to.name === 'login' && isLoggedIn()) {
    return { name: 'catatan' }
  }
})

export default router
