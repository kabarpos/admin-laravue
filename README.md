# Admin Laravue

Admin Laravue adalah sebuah panel administrasi modern yang dibangun dengan Laravel, Vue, dan Inertia.js. Panel ini menggunakan konsep Single Page Application (SPA) dengan kemampuan server-side rendering, memberikan pengalaman pengguna yang mulus tanpa mengorbankan SEO dan performa.

![Admin Panel](https://via.placeholder.com/1200x600?text=Admin+Laravue+Dashboard)

## Fitur

- 🔒 Autentikasi dan Manajemen Pengguna
- 👮 RBAC (Role-Based Access Control)
- 🎨 UI Komponen berbasis ShadCN/Vue
- 🌙 Mode Gelap/Terang
- 📱 Responsif untuk semua perangkat
- 🚀 Laravel + Vue + Inertia Stack
- ⚡ Pemuatan halaman super cepat (tanpa refresh)
- 🧩 Struktur modular, mudah untuk extend

## Persyaratan

- PHP >= 8.1
- Node.js >= 16
- Composer
- MySQL atau PostgreSQL
- Ekstensi PHP yang diperlukan:
  - BCMath
  - Ctype
  - Fileinfo
  - JSON
  - Mbstring
  - OpenSSL
  - PDO
  - Tokenizer
  - XML

## Instalasi

### 1. Clone repositori

```bash
git clone https://github.com/username/admin-laravue.git
cd admin-laravue
```

### 2. Instalasi dependensi

```bash
composer install
npm install
```

### 3. Konfigurasi lingkungan

```bash
cp .env.example .env
php artisan key:generate
```

Sesuaikan pengaturan database dan aplikasi lainnya di file `.env`.

### 4. Migrasi dan seed database

```bash
php artisan migrate --seed
```

### 5. Kompilasi assets (development)

```bash
npm run dev
```

Untuk build produksi:

```bash
npm run build
```

### 6. Jalankan server

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000`.

## Struktur Aplikasi

```
admin-laravue/
├── app/                    # Kode PHP Laravel
│   ├── Http/
│   │   ├── Controllers/    # Controller aplikasi
│   │   └── Middleware/     # Middleware aplikasi
│   └── Models/             # Model Eloquent
├── database/
│   ├── migrations/         # Migrasi database
│   └── seeders/            # Seeder database
├── resources/
│   ├── js/                 # Kode JavaScript/Vue
│   │   ├── components/     # Komponen Vue reusable
│   │   ├── layouts/        # Layout aplikasi
│   │   ├── pages/          # Halaman Inertia
│   │   ├── utils/          # Fungsi utilitas
│   │   └── composables/    # Vue composables reusable
│   └── views/              # Blade templates
└── routes/                 # Definisi route
```

## Struktur Folder Vue dan Penamaan

### Penamaan File dan Folder
- Folder komponen: `resources/js/pages/`
- Komponen Admin: `resources/js/pages/admin/`
- Subfolder Admin menggunakan format PascalCase: `Roles`, `Users`, `Permissions`
- File Vue menggunakan format PascalCase: `Index.vue`, `Create.vue`, `Edit.vue`

### Inertia Path
- Format path yang benar: `admin/Roles/Index` (tanpa prefix `pages/`)
- Path di `app.ts` sudah menambahkan prefix `./pages/` secara otomatis

### Konvensi Route
- Gunakan kebab-case untuk penamaan route: `admin.roles.index`

## Best Practices Code

### Avatar dan Foto Profil
Untuk menangani avatar dan foto profil, gunakan utilitas yang disediakan:

```js
import { getAvatarUrl, getUserAvatarPath, validateAvatarFile } from '@/utils/avatarUtils';

// Mendapatkan path avatar valid
const avatarPath = getUserAvatarPath(user);

// Mengkonversi path ke URL lengkap
const avatarUrl = getAvatarUrl(avatarPath);

// Validasi file sebelum upload
const validation = validateAvatarFile(file);
if (!validation.valid) {
  // Tampilkan pesan error
}
```

### Server-side Logging
Untuk kesalahan di sisi server, gunakan:

```php
use Illuminate\Support\Facades\Log;

try {
    // Kode yang berisiko menghasilkan error
} catch (\Exception $e) {
    Log::error('Deskripsi error', [
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
```

## Deployment

### Shared Hosting

1. Kompilasi assets untuk produksi:
```bash
npm run build
```

2. Upload semua file ke server hosting Anda
3. Pastikan document root mengarah ke direktori `public/`
4. Setup file `.env` untuk konfigurasi produksi
5. Jalankan migrasi:
```bash
php artisan migrate --force
```

### VPS/Cloud

1. Siapkan server dengan Nginx/Apache, PHP, dan MySQL
2. Clone repositori ke server
3. Setup virtual host ke direktori `public/`
4. Ikuti langkah instalasi standar di atas
5. Untuk peningkatan performa:
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Kontribusi

Kontribusi sangat diterima! Silakan ikuti langkah-langkah berikut:

1. Fork repositori
2. Buat branch fitur (`git checkout -b feature/amazing-feature`)
3. Commit perubahan Anda (`git commit -m 'Add some amazing feature'`)
4. Push ke branch (`git push origin feature/amazing-feature`)
5. Buat Pull Request

## Lisensi

Didistribusikan di bawah Lisensi MIT. Lihat `LICENSE` untuk informasi lebih lanjut.

## Kontak

Nama Anda - [@twitter_handle](https://twitter.com/twitter_handle) - email@example.com

Link Proyek: [https://github.com/username/admin-laravue](https://github.com/username/admin-laravue) 