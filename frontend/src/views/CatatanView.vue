<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../lib/api'
import { clearToken } from '../lib/auth'

const router = useRouter()
const daftar = ref([])
const loading = ref(true)
const error = ref('')

const form = ref(kosong())
const editingId = ref(null)
const referensiText = ref('')

function kosong() {
  return {
    nama_catatan: '',
    pemateri: '',
    tempat: '',
    tanggal: '',
    isi_catatan: '',
  }
}

async function muatCatatan() {
  loading.value = true
  try {
    const response = await api.get('/catatan')
    daftar.value = response.data
  } catch {
    error.value = 'Gagal memuat catatan.'
  } finally {
    loading.value = false
  }
}

function referensiArray() {
  return referensiText.value
    .split(',')
    .map((r) => r.trim())
    .filter((r) => r.length > 0)
}

async function simpan() {
  error.value = ''
  const payload = { ...form.value, referensi: referensiArray() }
  try {
    if (editingId.value) {
      await api.put(`/catatan/${editingId.value}`, payload)
    } else {
      await api.post('/catatan', payload)
    }
    resetForm()
    await muatCatatan()
  } catch (e) {
    error.value = e.response?.data?.message || 'Gagal menyimpan catatan.'
  }
}

function editCatatan(catatan) {
  editingId.value = catatan.id
  form.value = {
    nama_catatan: catatan.nama_catatan,
    pemateri: catatan.pemateri,
    tempat: catatan.tempat,
    tanggal: catatan.tanggal ? catatan.tanggal.substring(0, 10) : '',
    isi_catatan: catatan.isi_catatan,
  }
  referensiText.value = (catatan.referensi || []).join(', ')
  window.scrollTo({ top: 0, behavior: 'smooth' })
}

async function hapus(id) {
  if (!confirm('Hapus catatan ini?')) return
  try {
    await api.delete(`/catatan/${id}`)
    await muatCatatan()
  } catch {
    error.value = 'Gagal menghapus catatan.'
  }
}

function resetForm() {
  form.value = kosong()
  referensiText.value = ''
  editingId.value = null
}

async function logout() {
  try {
    await api.post('/logout')
  } catch {
    // abaikan; tetap bersihkan token lokal
  }
  clearToken()
  router.push({ name: 'login' })
}

onMounted(muatCatatan)
</script>

<template>
  <div class="page">
    <header class="topbar">
      <div>
        <h1>SPIRA</h1>
        <span>Daftar Catatan</span>
      </div>
      <button class="logout" @click="logout">Logout</button>
    </header>

    <main class="content">
      <section class="card form-card">
        <h2>{{ editingId ? 'Ubah Catatan' : 'Tambah Catatan' }}</h2>
        <form @submit.prevent="simpan">
          <div class="grid">
            <div>
              <label>Nama Catatan</label>
              <input v-model="form.nama_catatan" required />
            </div>
            <div>
              <label>Pemateri / Ustadz</label>
              <input v-model="form.pemateri" required />
            </div>
            <div>
              <label>Tempat</label>
              <input v-model="form.tempat" required />
            </div>
            <div>
              <label>Tanggal</label>
              <input v-model="form.tanggal" type="date" required />
            </div>
          </div>
          <label>Isi Catatan</label>
          <textarea v-model="form.isi_catatan" rows="4" required></textarea>
          <label>Sumber Referensi (pisahkan dengan koma)</label>
          <input v-model="referensiText" placeholder="Kitab A, Kitab B" />

          <p v-if="error" class="error">{{ error }}</p>

          <div class="actions">
            <button type="submit">{{ editingId ? 'Simpan Perubahan' : 'Tambah' }}</button>
            <button v-if="editingId" type="button" class="secondary" @click="resetForm">Batal</button>
          </div>
        </form>
      </section>

      <section class="card">
        <h2>Catatan Tersimpan ({{ daftar.length }})</h2>
        <p v-if="loading">Memuat...</p>
        <p v-else-if="daftar.length === 0" class="empty">Belum ada catatan.</p>
        <table v-else>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Pemateri</th>
              <th>Tempat</th>
              <th>Tanggal</th>
              <th>Referensi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in daftar" :key="c.id">
              <td>{{ c.nama_catatan }}</td>
              <td>{{ c.pemateri }}</td>
              <td>{{ c.tempat }}</td>
              <td>{{ c.tanggal ? c.tanggal.substring(0, 10) : '-' }}</td>
              <td>{{ (c.referensi || []).join(', ') || '-' }}</td>
              <td class="aksi">
                <button class="mini" @click="editCatatan(c)">Ubah</button>
                <button class="mini danger" @click="hapus(c.id)">Hapus</button>
              </td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</template>

<style scoped>
.topbar {
  background: #1d4ed8;
  color: #fff;
  padding: 16px 24px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.topbar h1 { margin: 0; font-size: 22px; }
.topbar span { font-size: 13px; opacity: 0.85; }
.logout {
  background: rgba(255, 255, 255, 0.15);
  color: #fff;
  border: 1px solid rgba(255, 255, 255, 0.4);
  padding: 8px 16px;
  border-radius: 8px;
  cursor: pointer;
}
.content {
  max-width: 900px;
  margin: 24px auto;
  padding: 0 16px;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.card {
  background: #fff;
  border-radius: 12px;
  padding: 24px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}
.card h2 { margin-top: 0; font-size: 18px; }
.grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}
label { display: block; font-size: 13px; font-weight: 600; margin: 10px 0 4px; }
input, textarea {
  width: 100%;
  padding: 9px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
}
.actions { margin-top: 16px; display: flex; gap: 10px; }
button {
  background: #1d4ed8;
  color: #fff;
  border: none;
  padding: 10px 18px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
}
button.secondary { background: #6b7280; }
table { width: 100%; border-collapse: collapse; font-size: 14px; }
th, td { text-align: left; padding: 10px; border-bottom: 1px solid #eef0f4; }
th { color: #6b7280; font-size: 12px; text-transform: uppercase; }
.aksi { display: flex; gap: 6px; }
.mini { padding: 6px 12px; font-size: 13px; }
.mini.danger { background: #dc2626; }
.error { color: #dc2626; font-size: 13px; }
.empty { color: #9ca3af; }
</style>
