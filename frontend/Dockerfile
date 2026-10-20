# ============================================================
# Tahap 1 — BUILD (Node.js)
# Membangun aplikasi Vue menjadi berkas statis di folder dist/.
# ============================================================
FROM node:20-alpine AS build

WORKDIR /app

# Alamat API saat build; di dalam Compose memakai path relatif /api
# yang diteruskan Nginx ke service backend.
ARG VITE_API_URL=/api
ENV VITE_API_URL=$VITE_API_URL

# Lapisan cache dependensi
COPY package.json package-lock.json ./
RUN npm ci

# Salin kode lalu build
COPY . .
RUN npm run build

# ============================================================
# Tahap 2 — SERVE (Nginx)
# Hanya menyajikan hasil build. Image akhir TIDAK memuat
# node_modules maupun toolchain Node.
# ============================================================
FROM nginx:1.27-alpine AS serve

# Salin konfigurasi Nginx (SPA fallback + proxy /api ke backend)
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Salin hasil build dari tahap sebelumnya
COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80
