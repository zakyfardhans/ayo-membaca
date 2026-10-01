# AyoMembaca

AyoMembaca adalah aplikasi web untuk mengelola inventaris perpustakaan. Pengelola dapat mengatur buku dan kategori, memantau stok, mengunggah dan membaca PDF, serta menggunakan Arnaru-AI untuk membantu membaca dan mencari koleksi.

## Fitur

- CRUD buku dan kategori.
- Pencarian berdasarkan judul, penulis, atau ISBN; filter kategori dan stok; pagination 10 buku per halaman.
- Upload sampul dan PDF, pembaca PDF, Trash, restore, dan hapus permanen.
- Asisten Baca dari PDF: tanya jawab, ringkasan, poin penting, glosarium, panduan baca, dan kuis.
- Rekomendasi buku, pencarian katalog dengan bahasa sehari-hari, dan saran metadata dari PDF.
- Dashboard ringkasan koleksi dan stok.

## Teknologi

- PHP 8.2+ dan Laravel 12.
- MySQL/MariaDB untuk pengembangan lokal; koneksi diatur melalui `.env`.
- Blade, Tailwind CSS 4, Vite, dan JavaScript.
- Arnaru-AI dipanggil dari backend Laravel.

## Persiapan

Pastikan PHP, Composer, Node.js/npm, dan database sudah tersedia. Dari folder project:

```powershell
composer install
Copy-Item .env.example .env
```

Jika `.env` sudah ada, jangan timpa file tersebut. Atur `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` untuk database lokal. Kemudian jalankan:

```powershell
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install
npm run build
```

Pada macOS/Linux, gunakan `cp .env.example .env` sebagai pengganti `Copy-Item`.

Jalankan aplikasi:

```powershell
php artisan serve
```

Buka alamat yang ditampilkan Artisan, biasanya `http://127.0.0.1:8000`.

## Development

Untuk menjalankan Laravel, queue, log, dan Vite bersamaan:

```powershell
composer dev
```

Atau jalankan Laravel dan Vite secara terpisah dengan `php artisan serve` dan `npm run dev`. Build asset produksi menggunakan `npm run build`.

## Arnaru-AI

Arnaru-AI diakses melalui `POST /api/chat` dari backend. API key tidak dikirim dari browser. Konfigurasi terdapat di `config/services.php` dan dapat ditimpa melalui `.env`:

```dotenv
ARNARU_AI_URL=https://arnaru-ai.vercel.app
ARNARU_AI_TOKEN=
ARNARU_AI_MODEL=gemini-3-flash
ARNARU_AI_TIMEOUT=120
ARNARU_AI_MAX_PDF_MB=4
```

`ARNARU_AI_TOKEN` opsional, bergantung pada konfigurasi penyedia. Jangan commit token atau kredensial ke repository. Dropdown dan validasi model menggunakan allowlist pada `config/services.php`.

PDF buku disimpan di `storage/app/public/books`, sampul di `storage/app/public/covers`, dan diakses melalui `public/storage`. Upload PDF katalog dibatasi 30 MB. Pengiriman PDF ke Arnaru-AI dibatasi 4 MiB secara default; PDF yang lebih besar tetap bisa dibaca di aplikasi, tetapi harus dikompres atau diganti dengan salinan lebih ringan sebelum digunakan oleh fitur AI.

## Data Demo

Setelah migrasi, data contoh dapat dimasukkan dengan:

```powershell
php artisan db:seed
```

> **Peringatan:** `BooksSeeder` menyinkronkan 60 buku contoh dan menghapus permanen buku aktif maupun di Trash yang ISBN-nya tidak ada dalam daftar demo. Jalankan hanya pada database pengembangan/demo, bukan database inventaris yang ingin dipertahankan. `CategoriesSeeder` menambah atau memperbarui kategori contoh, tetapi tidak menghapus kategori lain.

File sampul contoh tidak disertakan; aplikasi menampilkan fallback bila file gambar belum tersedia. PDF buku perlu diunggah melalui form sebelum Asisten Baca atau saran metadata dapat digunakan.

## Route Utama

| Route               | Kegunaan                                   |
| ------------------- | ------------------------------------------ |
| `/`                 | Dashboard inventaris                       |
| `/buku`             | Daftar, pencarian, dan filter buku         |
| `/buku/create`      | Tambah buku                                |
| `/buku/{book}`      | Detail buku, pembaca PDF, dan Asisten Baca |
| `/buku/{book}/edit` | Edit buku, upload PDF, dan saran metadata  |
| `/kategori`         | Kelola kategori                            |
| `/trash`            | Pulihkan atau hapus permanen buku          |

Endpoint AI aplikasi adalah route POST internal Laravel untuk chat PDF, rekomendasi/pencarian katalog, dan saran metadata. Route AI memiliki rate limit dan hanya mengirim request saat pengguna memulai aksi.

## Pengujian

```powershell
composer test
```

Atau jalankan langsung dengan `php artisan test`. PHPUnit memakai SQLite in-memory berdasarkan `phpunit.xml`; test tidak mengubah database pengembangan dari `.env`.

## Referensi

- [Rencana fitur dan rubrik](routes/PLAN.md)
- [Panduan kerja agent](AGENTS.md)
- [Dokumentasi Laravel 12](https://laravel.com/docs/12.x)
