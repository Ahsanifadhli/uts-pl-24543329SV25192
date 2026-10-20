import axios from 'axios'
import { getToken, clearToken } from './auth'

// Alamat API diambil dari variabel lingkungan, bukan ditulis mati di kode
const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: { Accept: 'application/json' },
})

// Sisipkan token pada setiap permintaan
api.interceptors.request.use((config) => {
  const token = getToken()
  if (token) {
    config.headers.Authorization = `Bearer ${token}`
  }
  return config
})

// Jika token tidak valid (401), bersihkan token
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response && error.response.status === 401) {
      clearToken()
    }
    return Promise.reject(error)
  },
)

export default api
