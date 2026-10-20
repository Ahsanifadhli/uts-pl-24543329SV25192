<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '../lib/api'
import { setToken } from '../lib/auth'

const router = useRouter()
const email = ref('admin@spira.test')
const password = ref('')
const error = ref('')
const loading = ref(false)

async function handleLogin() {
  error.value = ''
  loading.value = true
  try {
    const response = await api.post('/login', {
      email: email.value,
      password: password.value,
    })
    setToken(response.data.token)
    router.push({ name: 'catatan' })
  } catch (e) {
    error.value = e.response?.data?.message || 'Login gagal. Periksa email dan kata sandi.'
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="login-wrapper">
    <form class="login-card" @submit.prevent="handleLogin">
      <h1>SPIRA</h1>
      <p class="subtitle">Sistem Pencatatan Ilmu Religi dan Akademik</p>

      <label>Email</label>
      <input v-model="email" type="email" required placeholder="email@contoh.test" />

      <label>Kata Sandi</label>
      <input v-model="password" type="password" required placeholder="••••••••" />

      <p v-if="error" class="error">{{ error }}</p>

      <button type="submit" :disabled="loading">
        {{ loading ? 'Memproses...' : 'Masuk' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.login-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 16px;
}
.login-card {
  background: #fff;
  padding: 32px;
  border-radius: 12px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
  width: 100%;
  max-width: 360px;
  display: flex;
  flex-direction: column;
}
h1 {
  margin: 0;
  color: #1d4ed8;
  text-align: center;
}
.subtitle {
  margin: 4px 0 24px;
  text-align: center;
  color: #6b7280;
  font-size: 13px;
}
label {
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 4px;
}
input {
  padding: 10px 12px;
  margin-bottom: 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
}
button {
  padding: 11px;
  background: #1d4ed8;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
}
button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
.error {
  color: #dc2626;
  font-size: 13px;
  margin: 0 0 12px;
}
</style>
