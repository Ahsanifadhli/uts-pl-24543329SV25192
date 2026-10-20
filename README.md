# SPIRA (Sistem Pencatatan Ilmu Religi dan Akademik)

Program Studi Sarjana Terapan Teknologi Rekayasa Perangkat Lunak, Departemen Teknik Elektro dan Informatika, Sekolah Vokasi, Universitas Gadjah Mada (2026).

- **Nama**: Ahsani Fadhli Ilahi
- **NIM**: 24/543329/SV/25192
- **Mata Kuliah**: Konstruksi dan Evolusi Perangkat Lunak
- **Dosen Pengampu**: Galih Malela Damaraji, S.Pd., M.Eng

---

## 📖 Deskripsi Aplikasi

**SPIRA** adalah aplikasi web untuk para akademisi dan penuntut ilmu dalam mencatat hasil kegiatan belajar, seperti kajian keagamaan, webinar, dan seminar, agar lebih terstruktur dan terdokumentasi. Aplikasi ini memadukan catatan akademik dan religi dalam satu platform sebagai sarana dokumentasi, evaluasi, dan refleksi.

### Fitur Utama

- **Autentikasi berbasis token** (Laravel Sanctum): login, logout, dan proteksi seluruh endpoint selain login.
- **Manajemen Catatan (CRUD lengkap)**: menampilkan daftar catatan, melihat detail, menambah, mengubah, dan menghapus catatan.
- **Formulir catatan** dengan atribut: nama catatan, nama pemateri/ustadz, tempat, tanggal, isi catatan, dan sumber referensi (bisa lebih dari satu).
- **Antarmuka Vue.js** yang berkomunikasi dengan backend melalui REST API.

---

## 🏗️ Arsitektur &amp; Teknologi

| Lapisan | Teknologi |
|---|---|
| Frontend | Vue 3 + Vite, Vue Router, Axios |
| Backend | Laravel 12 (REST API) + Laravel Sanctum |
| Basis Data | MySQL |
| Containerisasi | Docker, Docker Compose |
| CI/CD | GitHub Actions + GitHub Container Registry (GHCR) |
| Server Aplikasi (bonus) | FrankenPHP |

Alur data: **Vue (browser)** → permintaan HTTP + token lewat Axios → **Laravel API** (validasi, autentikasi Sanctum, logika bisnis) → **basis data** (penyimpanan permanen) → balasan JSON kembali ke Vue untuk ditampilkan.

---

## 📁 Struktur Repository

```
uts-pl-24543329SV25192/
├── backend/                  # Aplikasi Laravel (REST API + Sanctum)
│   ├── app/
│   ├── routes/api.php
│   ├── tests/Feature/        # Feature test endpoint API
│   ├── Dockerfile            # Image backend (versi PHP ter-pin, cache composer)
│   └── .dockerignore
├── frontend/                 # Aplikasi Vue 3
│   ├── src/
│   ├── Dockerfile            # Multi-stage: build Node.js → sajikan via Nginx
│   └── .dockerignore
├── .github/workflows/ci.yaml # CI (test backend, build frontend) + CD (deploy GHCR)
├── postman/                  # Postman Collection hasil ekspor
├── docker-compose.yml        # Menjalankan db + backend + frontend sekaligus
├── .env.example              # Contoh konfigurasi (tanpa nilai rahasia asli)
└── README.md
```

---

## 🚀 Menjalankan Seluruh Sistem (Docker Compose)

Seluruh sistem dapat dijalankan dengan satu perintah dari root repository:

```bash
# 1. Salin berkas contoh environment lalu sesuaikan kredensialnya
cp .env.example .env

# 2. Bangun dan jalankan seluruh service (db, backend, frontend)
docker compose up -d --build

# 3. Memeriksa status seluruh service
docker compose ps
```

Setelah seluruh container berjalan:
- **Frontend (Vue)** dapat diakses melalui peramban, mis. `http://localhost:5173` (atau port yang dipetakan di `docker-compose.yml`).
- **Backend (Laravel API)** melayani permintaan pada `http://localhost:8000/api`.

Menghentikan sistem:

```bash
docker compose down          # hentikan container (data tetap aman di named volume)
docker compose up -d         # jalankan kembali — data sebelumnya masih ada
```

> Data basis data disimpan dalam **named volume**, sehingga tetap bertahan setelah `docker compose down` lalu `up` kembali.

---

## 🔑 Konfigurasi Environment

Kredensial basis data dan nilai rahasia **tidak** disimpan di dalam repository. Berkas `.env` diabaikan oleh Git; gunakan `.env.example` sebagai acuan. Contoh variabel:

```env
# Backend (Laravel)
APP_KEY=
DB_CONNECTION=mysql
DB_HOST=db                 # nama service di jaringan Docker Compose, bukan IP
DB_PORT=3306
DB_DATABASE=spira
DB_USERNAME=spira_user
DB_PASSWORD=

# Frontend (Vue)
VITE_API_URL=http://localhost:8000/api
```

Pada lingkungan CI/CD, nilai rahasia seperti `APP_KEY` dan `DB_PASSWORD` disimpan di **GitHub Secrets** (Settings → Secrets and variables → Actions), bukan di dalam kode.

---

## 📡 Dokumentasi API

Seluruh permintaan dan balasan berformat **JSON**. Kode status HTTP mengikuti konvensi: `200` sukses, `201` data dibuat, `422` validasi gagal, `401` token tidak ada/invalid.

| Method | Endpoint | Keterangan | Auth |
|---|---|---|---|
| `POST` | `/api/login` | Login, mengembalikan token | ✗ |
| `POST` | `/api/logout` | Logout, mencabut token | ✓ |
| `GET` | `/api/catatan` | Menampilkan seluruh catatan | ✓ |
| `GET` | `/api/catatan/{id}` | Menampilkan satu catatan | ✓ |
| `POST` | `/api/catatan` | Menambah catatan (→ `201`) | ✓ |
| `PUT` | `/api/catatan/{id}` | Mengubah catatan | ✓ |
| `DELETE` | `/api/catatan/{id}` | Menghapus catatan | ✓ |

Endpoint yang membutuhkan autentikasi harus menyertakan header:

```
Authorization: Bearer <token>
```

### Contoh Atribut Catatan

```json
{
  "nama_catatan": "Kajian Tafsir Surah Al-Fatihah",
  "pemateri": "Ustadz Ahmad",
  "tempat": "Masjid Kampus UGM",
  "tanggal": "2026-10-20",
  "isi_catatan": "Ringkasan pembahasan ...",
  "referensi": ["Tafsir Ibnu Katsir", "Tafsir As-Sa'di"]
}
```

---

## 🧪 Pengujian

Backend dilengkapi minimal dua **feature test** untuk endpoint API:

```bash
cd backend
php artisan test
```

Pengujian mencakup skenario seperti: login mengembalikan token, akses endpoint tanpa token menghasilkan `401`, pembuatan catatan mengembalikan `201`, dan validasi gagal mengembalikan `422`.

---

## 📮 Postman Collection

Seluruh endpoint dapat diuji dengan Postman. Berkas koleksi hasil ekspor tersimpan di folder [`postman/`](postman/) pada repository ini, dan dapat langsung diimpor ke Postman.

---

## 🌿 Alur Percabangan (Git Branching Strategy)

Kode **tidak pernah** di-push langsung ke `main` atau `dev`. Seluruh perubahan masuk melalui Pull Request:

```
feature/<nama-fitur>  --PR-->  dev  --PR-->  main
```

- **`main`** : kondisi stabil & siap dinilai.
- **`dev`** : integrasi fitur sebelum rilis.
- **`feature/<nama-fitur>`** : pengembangan fitur spesifik.

Branch `main` dan `dev` dilindungi **branch protection** yang mewajibkan Pull Request dan status check CI lulus sebelum merge.

---

## ⚙️ CI/CD (GitHub Actions)

Berkas [`.github/workflows/ci.yaml`](.github/workflows/ci.yaml) memuat:

1. **Job `test-backend`** : menjalankan `php artisan test`.
2. **Job `build-frontend`** : menjalankan `npm ci` lalu `npm run build`.
3. **Job `deploy`** : hanya berjalan untuk push ke `main`; membangun image Docker backend dan frontend lalu mendorongnya ke **GHCR** (`ghcr.io`) dengan tag bernomor commit (bukan `latest`). Pada Pull Request, branch `dev`, dan branch fitur, pipeline berhenti setelah job test dan build.

---

## 🐳 Containerisasi

- **`backend/Dockerfile`** : image Laravel dengan base image versi ter-pin (bukan `latest`); `composer.json` dan `composer.lock` disalin dan dipasang sebelum kode aplikasi agar cache lapisan bekerja.
- **`frontend/Dockerfile`** : **multi-stage build**: tahap pertama membangun aplikasi Vue dengan Node.js, tahap kedua menyajikan hasil build dengan **Nginx**. Image akhir tidak memuat `node_modules`.
- Masing-masing folder memiliki **`.dockerignore`**, dan tidak ada nilai rahasia yang ditulis di dalam Dockerfile.

---

## 🔄 Alur Data Vue → Laravel → Basis Data

1. Pengguna login di halaman Vue; token dari Laravel disimpan dan dikirim pada setiap permintaan berikutnya.
2. Vue memanggil endpoint Laravel melalui Axios (alamat API dari variabel lingkungan, bukan ditulis mati).
3. Laravel memvalidasi token (Sanctum), memproses logika, dan membaca/menulis data ke basis data.
4. Basis data menyimpan data secara permanen di named volume.
5. Balasan JSON dikembalikan ke Vue untuk ditampilkan kepada pengguna.

Antar service saling terhubung melalui **nama service** di jaringan Docker Compose (`db`, `backend`, `frontend`), bukan melalui alamat IP laptop.

---

## 📝 Konvensi Pesan Commit

Mengikuti standar [Conventional Commits](https://www.conventionalcommits.org/): `feat`, `fix`, `test`, `ci`, `docs`, `chore`, `refactor`.
