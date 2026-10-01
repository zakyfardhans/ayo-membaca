````
# PLAN.md
# Sistem Manajemen Inventaris Buku Perpustakaan (AyoMembaca)
# Laravel CRUD Quiz

---

## 1. Project Overview

Membangun aplikasi web internal untuk mengelola inventaris buku
perpustakaan sekolah/perguruan tinggi menggunakan Laravel.

Aplikasi memiliki fitur:

- Manajemen data buku
- Relasi kategori dan buku
- Search buku
- Filter kategori
- Filter status stok
- Pagination 10 data per halaman
- Detail buku
- Upload cover buku
- Edit buku
- Penghapusan cover lama saat update
- Soft Delete
- Trash / Buku Terhapus
- Restore buku
- Force Delete
- Flash message / notification
- Seeder dan Factory
- Form Request Validation
- Resource Controller
- Service untuk upload/delete file
- Responsive UI

---

# 2. Target Teknologi

## Backend

- Laravel
- PHP
- MySQL / MariaDB

## Frontend

Gunakan:

- Blade
- Tailwind CSS
- Alpine.js
- Lucide Icons atau Heroicons

Tidak perlu menggunakan React/Vue/Inertia karena
fokus utama tugas adalah Laravel CRUD.

---

# 3. Konsep Aplikasi

Aplikasi dibagi menjadi:

```text
Dashboard
│
├── Buku
│   ├── Semua Buku
│   ├── Tambah Buku
│   └── Detail Buku
│
├── Kategori
│
└── Trash
    └── Buku Terhapus
````

Fokus utama penilaian:

```
Database
    ↓
Model & Relationship
    ↓
CRUD
    ↓
Validation
    ↓
Upload Cover
    ↓
Search & Filter
    ↓
Pagination
    ↓
Soft Delete
    ↓
Restore
    ↓
Force Delete
```

---

# 4\. UI / Design Direction

Gunakan screenshot referensi sebagai inspirasi visual,\
 tetapi ubah kontennya menjadi sistem internal inventaris buku.

Style:

- Clean
- Modern
- Professional
- Minimal
- Library/Admin Dashboard
- Responsive

Warna utama:

```
Primary:
#0B2A5B

Primary Dark:
#061C3D

Accent:
#F15A3A

Background:
#F8FAFC

White:
#FFFFFF

Text:
#172033

Secondary Text:
#64748B

Border:
#E2E8F0

Danger:
#DC2626

Warning:
#F59E0B

Success:
#16A34A
```

---

# 5\. Layout

Desktop:

```
┌─────────────────────────────────────────────────────────────┐
│                       TOP HEADER                            │
├───────────────┬─────────────────────────────────────────────┤
│               │                                             │
│               │              MAIN CONTENT                   │
│   SIDEBAR     │                                             │
│               │                                             │
│               │                                             │
│               │                                             │
│               │                                             │
└───────────────┴─────────────────────────────────────────────┘
```

Tidak perlu right sidebar seperti website Perpustakaan Jakarta.

Untuk tugas CRUD, gunakan layout:

```
Sidebar + Main Content
```

---

# 6\. Sidebar

Menu:

```
┌─────────────────────────┐
│ 📚 Library Inventory    │
├─────────────────────────┤
│                         │
│ Dashboard               │
│                         │
│ 📖 Buku                 │
│    Semua Buku           │
│    Tambah Buku          │
│                         │
│ 🗂 Kategori             │
│                         │
│ 🗑 Buku Terhapus        │
│                         │
└─────────────────────────┘
```

Active menu menggunakan:

```
background: #EFF6FF;
color: #0B2A5B;
font-weight: 600;
```

---

# 7\. Database Structure

Gunakan 2 tabel:

```
categories
    │
    │ 1
    │
    │ N
    ↓
books
```

Relationship:

```
Category
hasMany(Book)

Book
belongsTo(Category)
```

---

# 8\. Categories Table

Migration:

```
categories
```

Fields:

```
id
name
slug
created_at
updated_at
```

Detail:

```
id
    BIGINT
    PRIMARY KEY

name
    VARCHAR
    UNIQUE

slug
    VARCHAR
    UNIQUE

created_at
    TIMESTAMP

updated_at
    TIMESTAMP
```

Contoh:

```
1 | Fiksi        | fiksi
2 | Teknologi    | teknologi
3 | Sejarah      | sejarah
4 | Sains        | sains
5 | Filsafat     | filsafat
```

---

# 9\. Books Table

Migration:

```
books
```

Fields:

```
id
category_id
isbn
title
author
publisher
publication_year
stock
cover_image
synopsis
created_at
updated_at
deleted_at
```

Detail:

```
id
    BIGINT
    PRIMARY KEY

category_id
    BIGINT
    FOREIGN KEY

isbn
    VARCHAR
    UNIQUE

title
    VARCHAR

author
    VARCHAR

publisher
    VARCHAR

publication_year
    INTEGER

stock
    INTEGER
    DEFAULT 0

cover_image
    VARCHAR
    NULLABLE

synopsis
    TEXT
    NULLABLE

created_at
    TIMESTAMP

updated_at
    TIMESTAMP

deleted_at
    TIMESTAMP
    NULLABLE
```

Foreign key:

```
$table->foreignId('category_id')
    ->constrained('categories')
    ->onDelete('cascade');
```

Soft Delete:

```
$table->softDeletes();
```

---

# 10\. Model: Category

File:

```
app/Models/Category.php
```

Implement:

```
class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
    ];

    public function books()
    {
        return $this->hasMany(Book::class);
    }
}
```

---

# 11\. Model: Book

File:

```
app/Models/Book.php
```

Implement:

```
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'isbn',
        'title',
        'author',
        'publisher',
        'publication_year',
        'stock',
        'cover_image',
        'synopsis',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'stock' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
```

---

# 12\. Factory

File:

```
database/factories/BookFactory.php
```

Factory menghasilkan:

- ISBN
- title
- author
- publisher
- publication_year
- stock
- synopsis
- category_id

Contoh konsep:

```
return [
    'category_id' => Category::inRandomOrder()->first()->id,
    'isbn' => fake()->unique()->bothify('BK-###'),
    'title' => fake()->sentence(4),
    'author' => fake()->name(),
    'publisher' => fake()->company(),
    'publication_year' => fake()->numberBetween(1900, date('Y')),
    'stock' => fake()->numberBetween(0, 20),
    'synopsis' => fake()->paragraph(),
];
```

`cover_image` boleh:

```
NULL
```

karena dummy books tidak wajib mempunyai file gambar asli.

---

# 13\. Category Seeder

File:

```
database/seeders/CategorySeeder.php
```

Minimal:

```
Fiksi
Teknologi
Sejarah
Sains
Filsafat
```

Seeder harus menggunakan:

```
updateOrCreate()
```

atau cara lain yang mencegah duplicate ketika seeder dijalankan kembali.

---

# 14\. Book Seeder

File:

```
database/seeders/BookSeeder.php
```

Generate:

```
50 books
```

Contoh:

```
Book::factory(50)->create();
```

Pastikan CategorySeeder dijalankan terlebih dahulu.

---

# 15\. DatabaseSeeder

Urutan:

```
CategorySeeder
       ↓
BookSeeder
```

Contoh:

```
$this->call([
    CategorySeeder::class,
    BookSeeder::class,
]);
```

Command:

```
php artisan migrate:fresh --seed
```

Expected:

```
categories = 5
books      = 50
```

---

# 16\. Route Architecture

Gunakan Resource Controller.

File:

```
routes/web.php
```

Route utama:

```
Route::resource('books', BookController::class);
```

Tambahkan route Trash:

```
Route::get('/trash', [BookController::class, 'trash'])
    ->name('books.trash');

Route::patch('/trash/{book}/restore', [BookController::class, 'restore'])
    ->withTrashed()
    ->name('books.restore');

Route::delete('/trash/{book}/force-delete', [BookController::class, 'forceDelete'])
    ->withTrashed()
    ->name('books.forceDelete');
```

---

# 17\. Route List

Target:

```
GET       /books
          books.index

GET       /books/create
          books.create

POST      /books
          books.store

GET       /books/{book}
          books.show

GET       /books/{book}/edit
          books.edit

PUT/PATCH /books/{book}
          books.update

DELETE    /books/{book}
          books.destroy

GET       /trash
          books.trash

PATCH     /trash/{book}/restore
          books.restore

DELETE    /trash/{book}/force-delete
          books.forceDelete
```

---

# 18\. Controller

File:

```
app/Http/Controllers/BookController.php
```

Method:

```
index()
create()
store()
show()
edit()
update()
destroy()

trash()
restore()
forceDelete()
```

Controller jangan terlalu banyak berisi logic file handling.

File handling dipisahkan ke:

```
BookCoverService
```

---

# 19\. Form Request

Buat:

```
app/Http/Requests/StoreBookRequest.php
app/Http/Requests/UpdateBookRequest.php
```

Jangan melakukan semua validation langsung di Controller.

---

# 20\. StoreBookRequest

Required:

```
category_id
isbn
title
author
publisher
publication_year
stock
```

Rules:

```
'category_id' => [
    'required',
    'exists:categories,id',
],

'isbn' => [
    'required',
    'string',
    'max:50',
    'unique:books,isbn',
],

'title' => [
    'required',
    'string',
    'max:255',
],

'author' => [
    'required',
    'string',
    'max:255',
],

'publisher' => [
    'required',
    'string',
    'max:255',
],

'publication_year' => [
    'required',
    'integer',
    'between:1900,' . date('Y'),
],

'stock' => [
    'required',
    'integer',
    'min:0',
],

'cover_image' => [
    'nullable',
    'image',
    'mimes:jpg,jpeg,png',
    'max:2048',
],

'synopsis' => [
    'nullable',
    'string',
],
```

---

# 21\. UpdateBookRequest

Perbedaan penting:

ISBN harus unique tetapi boleh menggunakan ISBN milik\
 record yang sedang diedit.

Gunakan:

```
Rule::unique('books', 'isbn')
    ->ignore($this->book)
```

Contoh:

```
'isbn' => [
    'required',
    'string',
    'max:50',
    Rule::unique('books', 'isbn')
        ->ignore($this->book),
],
```

Ini merupakan salah satu poin penting rubrik.

---

# 22\. Cover Upload

Lokasi:

```
storage/app/public/covers
```

Upload menggunakan:

```
$request->file('cover_image')
    ->store('covers', 'public');
```

Database hanya menyimpan path:

```
covers/abc123.jpg
```

Bukan URL lengkap.

---

# 23\. Storage Link

Jalankan:

```
php artisan storage:link
```

Result:

```
public/storage
        ↓
storage/app/public
```

Image dapat ditampilkan:

```
<img
    src="{{ asset('storage/' . $book->cover_image) }}"
    alt="{{ $book->title }}"
>
```

---

# 24\. BookCoverService

File:

```
app/Services/BookCoverService.php
```

Tujuan:

Memisahkan logic:

```
upload cover
delete cover
replace cover
```

Method:

```
upload()
delete()
replace()
```

Contoh konsep:

```
public function upload(UploadedFile $file): string
{
    return $file->store('covers', 'public');
}
```

Delete:

```
public function delete(?string $path): void
{
    if ($path && Storage::disk('public')->exists($path)) {
        Storage::disk('public')->delete($path);
    }
}
```

---

# 25\. Create Flow

Flow:

```
User
 ↓
GET /books/create
 ↓
Form
 ↓
POST /books
 ↓
StoreBookRequest
 ↓
Validation
 ↓
BookCoverService
 ↓
Book::create()
 ↓
Redirect books.index
 ↓
Flash success
```

---

# 26\. Create Form

Form harus mempunyai:

```
Kategori
ISBN
Judul
Penulis
Penerbit
Tahun Terbit
Stok
Cover
Sinopsis
```

UI:

```
┌────────────────────────────────────────────┐
│ Tambah Buku                               │
├────────────────────────────────────────────┤
│                                            │
│ Kategori                                   │
│ [ Pilih Kategori ▼ ]                      │
│                                            │
│ ISBN                                       │
│ [ BK-001.............................. ]   │
│                                            │
│ Judul                                      │
│ [ .................................... ]   │
│                                            │
│ Penulis                                    │
│ [ .................................... ]   │
│                                            │
│ Penerbit                                   │
│ [ .................................... ]   │
│                                            │
│ Tahun Terbit        Stok                   │
│ [ 2024 ]            [ 10 ]                │
│                                            │
│ Cover Buku                                 │
│ [ Pilih File ]                             │
│                                            │
│ Sinopsis                                   │
│ [ .................................... ]   │
│ [ .................................... ]   │
│                                            │
│ [ Batal ]              [ Simpan Buku ]    │
└────────────────────────────────────────────┘
```

---

# 27\. Dynamic Category Dropdown

Category harus berasal dari database.

Controller:

```
$categories = Category::orderBy('name')->get();
```

View:

```
<select name="category_id">
    @foreach ($categories as $category)
        <option value="{{ $category->id }}">
            {{ $category->name }}
        </option>
    @endforeach
</select>
```

Jangan hardcode:

```
Fiksi
Teknologi
Sains
```

di HTML.

---

# 28\. Book Index

Halaman utama:

```
Buku
Kelola katalog dan inventaris buku perpustakaan.

[ + Tambah Buku ]
```

Kemudian search/filter.

---

# 29\. Search

Search berdasarkan:

```
title
author
isbn
```

Query:

```
$query->where(function ($q) use ($search) {
    $q->where('title', 'like', "%{$search}%")
      ->orWhere('author', 'like', "%{$search}%")
      ->orWhere('isbn', 'like', "%{$search}%");
});
```

---

# 30\. Filter Category

Parameter:

```
category
```

Contoh URL:

```
/books?category=2
```

Query:

```
if ($category) {
    $query->where('category_id', $category);
}
```

---

# 31\. Filter Stock

Parameter:

```
stock
```

Options:

```
Semua
Tersedia
Habis
```

Logic:

```
if ($stock === 'available') {
    $query->where('stock', '>', 0);
}

if ($stock === 'empty') {
    $query->where('stock', '=', 0);
}
```

---

# 32\. Search + Filter harus bisa bersamaan

Contoh:

```
/books?
search=laravel
&category=2
&stock=available
```

Semua filter harus bekerja bersamaan.

Jangan membuat query terpisah yang saling menimpa.

Gunakan satu query builder:

```
$query = Book::with('category');

if ($search) {
    ...
}

if ($category) {
    ...
}

if ($stock) {
    ...
}
```

---

# 33\. Pagination

Wajib:

```
10 books / page
```

Gunakan:

```
->paginate(10)
```

Untuk mempertahankan search/filter:

```
->withQueryString()
```

Contoh:

```
$books = $query
    ->latest()
    ->paginate(10)
    ->withQueryString();
```

Expected:

```
Page 1
10 books

Page 2
10 books

...

Page 5
10 books
```

untuk 50 dummy books.

---

# 34\. Index Table

Desktop:

```
┌────┬────────┬──────────────┬──────────┬────────┬───────┬──────────┐
│ #  │ Cover  │ Judul        │ Penulis  │ Kategori│ Stok │ Action   │
├────┼────────┼──────────────┼──────────┼────────┼───────┼──────────┤
│ 1  │ IMG    │ Laravel      │ John     │ Tech   │ 10    │ Detail   │
│ 2  │ IMG    │ Philosophy   │ Jane     │ Filsafat│ 2    │ Detail   │
└────┴────────┴──────────────┴──────────┴────────┴───────┴──────────┘
```

Action:

```
Detail
Edit
Hapus
```

---

# 35\. Stock Badge

Jika:

```
stock >= 3
```

Tampilkan:

```
10
Tersedia
```

Warna hijau.

Jika:

```
stock > 0 && stock < 3
```

Tampilkan:

```
2
Stok Menipis
```

Warna merah/orange.

Jika:

```
stock == 0
```

Tampilkan:

```
0
Habis
```

Warna abu/merah.

---

# 36\. Detail Buku

Route:

```
/books/{book}
```

Layout:

```
┌────────────────────────────────────────────────┐
│ Detail Buku                                    │
├──────────────────┬─────────────────────────────┤
│                  │ Judul Buku                  │
│                  │                             │
│   COVER IMAGE    │ Penulis                     │
│                  │ Penerbit                    │
│                  │ Tahun Terbit                │
│                  │ ISBN                        │
│                  │ Kategori                    │
│                  │ Stok                        │
├──────────────────┴─────────────────────────────┤
│ Sinopsis                                       │
│                                                │
│ Lorem ipsum...                                 │
└────────────────────────────────────────────────┘
```

---

# 37\. Edit Buku

Route:

```
/books/{book}/edit
```

Semua field otomatis terisi.

Cover lama:

```
┌──────────────────────┐
│                      │
│   CURRENT COVER      │
│                      │
└──────────────────────┘

Cover baru:
[ Choose File ]
```

Jika user tidak upload cover baru:

```
Cover lama tetap digunakan.
```

---

# 38\. Update Cover

Jika upload cover baru:

```
Old Cover
    ↓
Upload New Cover
    ↓
Save New Cover
    ↓
Delete Old Cover
    ↓
Update database
```

Pastikan tidak menghasilkan:

```
covers/
├── old-cover.jpg   ❌
├── new-cover.jpg
```

Yang diinginkan:

```
covers/
└── new-cover.jpg
```

---

# 39\. Delete Buku

Tombol:

```
Hapus
```

tidak boleh menggunakan:

```
$book->forceDelete();
```

Gunakan:

```
$book->delete();
```

Karena model menggunakan:

```
SoftDeletes
```

Database:

```
deleted_at = current timestamp
```

File cover:

```
TETAP ADA
```

karena buku masih dapat di-restore.

---

# 40\. Trash Page

Route:

```
/trash
```

Title:

```
Buku Terhapus
```

Ambil data:

```
Book::onlyTrashed()
```

UI:

```
┌────┬──────────────┬────────────┬─────────────────────┐
│ #  │ Judul        │ Deleted At │ Action              │
├────┼──────────────┼────────────┼─────────────────────┤
│ 1  │ Laravel      │ 01/10/2026 │ Restore | Permanen │
└────┴──────────────┴────────────┴─────────────────────┘
```

---

# 41\. Restore

Flow:

```
Trash
 ↓
Restore
 ↓
Book::restore()
 ↓
deleted_at = NULL
 ↓
Book kembali ke katalog
```

Code:

```
$book->restore();
```

Cover tidak perlu di-upload kembali.

---

# 42\. Force Delete

Force delete hanya tersedia di Trash.

Flow:

```
Trash
 ↓
Force Delete
 ↓
Delete Cover
 ↓
Force Delete Database
```

Urutan:

```
$this->coverService->delete($book->cover_image);

$book->forceDelete();
```

Hasil:

```
Database record → permanent deleted
Cover image     → permanent deleted
```

---

# 43\. Confirmation Delete

Sebelum delete:

```
Apakah kamu yakin ingin menghapus buku ini?

Buku akan dipindahkan ke Trash.

[ Batal ] [ Hapus ]
```

Untuk Force Delete:

```
PERINGATAN

Data akan dihapus secara permanen
dan tidak dapat dikembalikan.

[ Batal ] [ Hapus Permanen ]
```

Gunakan:

```
Alpine.js
```

atau confirm sederhana untuk MVP.

---

# 44\. Flash Messages

Setiap action harus memberikan feedback.

Create:

```
✓ Buku berhasil ditambahkan.
```

Update:

```
✓ Data buku berhasil diperbarui.
```

Delete:

```
✓ Buku berhasil dipindahkan ke Trash.
```

Restore:

```
✓ Buku berhasil dipulihkan.
```

Force Delete:

```
✓ Buku berhasil dihapus secara permanen.
```

---

# 45\. Flash Message Architecture

Controller:

```
return redirect()
    ->route('books.index')
    ->with('success', 'Buku berhasil ditambahkan.');
```

Blade:

```
@if(session('success'))
    <div>
        {{ session('success') }}
    </div>
@endif
```

Bisa ditingkatkan menggunakan:

```
Toast
```

dengan Alpine.js.

---

# 46\. Error Validation UI

Jika validation gagal:

```
Judul Buku
[........................]

⚠ Judul buku wajib diisi.
```

Setiap field menampilkan error masing-masing.

Gunakan:

```
@error('title')
    <p>{{ $message }}</p>
@enderror
```

---

# 47\. Cover Validation

Rules:

```
Format:
.jpg
.jpeg
.png

Maximum:
2MB
```

Validation:

```
'image',
'mimes:jpg,jpeg,png',
'max:2048'
```

Jika invalid:

```
Cover harus berupa JPG, JPEG, atau PNG
dengan ukuran maksimal 2MB.
```

---

# 48\. Empty State

Jika katalog kosong:

```
┌───────────────────────────────────────┐
│                                       │
│              📚                       │
│                                       │
│       Belum ada data buku             │
│                                       │
│   Silakan tambahkan buku pertama.     │
│                                       │
│       [ + Tambah Buku ]               │
│                                       │
└───────────────────────────────────────┘
```

Untuk search tidak ditemukan:

```
Tidak ada buku yang sesuai dengan
pencarian atau filter.
```

---

# 49\. Dashboard

Dashboard sederhana untuk mempercantik aplikasi.

Tidak perlu CRUD tambahan.

Tampilkan:

```
Total Buku
50

Total Kategori
5

Stok Tersedia
42

Stok Menipis
4

Buku Habis
4

Buku di Trash
3
```

Gunakan:

```
Book::count()
Book::where('stock', '>', 0)->count()
Book::whereBetween('stock', [1, 2])->count()
Book::where('stock', 0)->count()
Book::onlyTrashed()->count()
```

Dashboard bukan fokus utama penilaian.

---

# 50\. Reusable Components

Buat Blade components:

```
resources/views/components/

├── alert.blade.php
├── button.blade.php
├── input.blade.php
├── select.blade.php
├── modal.blade.php
├── badge.blade.php
├── pagination.blade.php
└── book-cover.blade.php
```

Tujuan:

Tidak mengulang HTML yang sama di banyak halaman.

---

# 51\. View Structure

```
resources/views/

├── layouts/
│   └── app.blade.php
│
├── components/
│   ├── alert.blade.php
│   ├── badge.blade.php
│   ├── button.blade.php
│   └── modal.blade.php
│
├── dashboard/
│   └── index.blade.php
│
├── books/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   ├── show.blade.php
│   ├── trash.blade.php
│   └── partials/
│       ├── form.blade.php
│       ├── filters.blade.php
│       └── table.blade.php
│
└── errors/
```

---

# 52\. Book Form Reuse

Jangan membuat form Create dan Edit sepenuhnya terpisah.

Gunakan:

```
books/partials/form.blade.php
```

Create:

```
@include('books.partials.form')
```

Edit:

```
@include('books.partials.form')
```

Perbedaan hanya:

```
method
action
button text
current cover
```

---

# 53\. Controller Architecture

Controller:

```
BookController
```

Responsibilities:

```
Receive Request
        ↓
Validate
        ↓
Call Service
        ↓
Save Model
        ↓
Redirect
```

Hindari controller menjadi terlalu besar.

---

# 54\. Service Architecture

Service:

```
BookCoverService
```

Responsibilities:

```
Upload
Delete
Replace
```

Controller tidak perlu mengetahui detail:

```
Storage::disk(...)
```

di setiap method.

---

# 55\. Recommended BookController Flow

## index()

```
Book::with('category')
 ↓
Search
 ↓
Category Filter
 ↓
Stock Filter
 ↓
Pagination 10
 ↓
View
```

## create()

```
Category::orderBy('name')->get()
 ↓
View
```

## store()

```
Validate
 ↓
Upload Cover
 ↓
Create Book
 ↓
Flash
 ↓
Redirect
```

## show()

```
Book::with('category')
 ↓
View
```

## edit()

```
Book
+
Categories
 ↓
View
```

## update()

```
Validate
 ↓
Check New Cover
 ↓
Replace Cover jika ada
 ↓
Update Book
 ↓
Flash
 ↓
Redirect
```

## destroy()

```
Book::delete()
 ↓
Soft Delete
 ↓
Flash
 ↓
Redirect
```

## trash()

```
Book::onlyTrashed()
 ↓
View
```

## restore()

```
Book::restore()
 ↓
Flash
 ↓
Redirect
```

## forceDelete()

```
Delete Cover
 ↓
Book::forceDelete()
 ↓
Flash
 ↓
Redirect
```

---

# 56\. Search Query Architecture

Gunakan query builder yang clean:

```
$query = Book::with('category');

$query->when($search, function ($query, $search) {
    $query->where(function ($q) use ($search) {
        $q->where('title', 'like', "%{$search}%")
          ->orWhere('author', 'like', "%{$search}%")
          ->orWhere('isbn', 'like', "%{$search}%");
    });
});

$query->when($category, function ($query, $category) {
    $query->where('category_id', $category);
});

$query->when($stock === 'available', function ($query) {
    $query->where('stock', '>', 0);
});

$query->when($stock === 'empty', function ($query) {
    $query->where('stock', 0);
});

$books = $query
    ->latest()
    ->paginate(10)
    ->withQueryString();
```

Ini penting agar:

```
Search
+
Category
+
Stock
+
Pagination
```

tetap bekerja bersama.

---

# 57\. N+1 Query Prevention

Index harus menggunakan:

```
Book::with('category')
```

Jangan:

```
Book::all()
```

kemudian melakukan query Category untuk setiap buku.

Dengan:

```
with('category')
```

data kategori di-load secara eager loading.

---

# 58\. File Management Rules

## Create

```
Upload
 ↓
storage/app/public/covers
 ↓
Save path to DB
```

## Update tanpa cover baru

```
Keep old cover
```

## Update dengan cover baru

```
Upload new cover
 ↓
Delete old cover
 ↓
Update DB
```

## Soft Delete

```
Database:
deleted_at = timestamp

File:
KEEP
```

## Restore

```
Database:
deleted_at = NULL

File:
KEEP
```

## Force Delete

```
Delete file
 ↓
Delete database permanently
```

---

# 59\. Important Soft Delete Rules

Model:

```
use SoftDeletes;
```

Normal query:

```
Book::all();
```

tidak menampilkan buku deleted.

Trash:

```
Book::onlyTrashed()->get();
```

Dengan deleted:

```
Book::withTrashed()->find($id);
```

Restore:

```
$book->restore();
```

Permanent:

```
$book->forceDelete();
```

---

# 60\. Testing Checklist

Minimal lakukan testing manual untuk:

## Create

- [ ] Tambah buku berhasil
- [ ] ISBN duplicate ditolak
- [ ] Category invalid ditolak
- [ ] Stock negatif ditolak
- [ ] Tahun \< 1900 ditolak
- [ ] Tahun \> tahun sekarang ditolak
- [ ] Cover JPG berhasil
- [ ] Cover JPEG berhasil
- [ ] Cover PNG berhasil
- [ ] File \> 2MB ditolak
- [ ] File selain image ditolak

## Read

- [ ] Buku tampil
- [ ] Detail tampil
- [ ] Category tampil
- [ ] Cover tampil

## Search

- [ ] Search title
- [ ] Search author
- [ ] Search ISBN

## Filter

- [ ] Filter category
- [ ] Filter tersedia
- [ ] Filter habis
- [ ] Search + filter bersamaan

## Pagination

- [ ] 10 item per page
- [ ] Page 2 bekerja
- [ ] Filter tetap aktif ketika pindah page

## Update

- [ ] Data bisa di-edit
- [ ] ISBN sendiri tidak dianggap duplicate
- [ ] ISBN milik buku lain ditolak
- [ ] Cover baru bisa diupload
- [ ] Cover lama terhapus

## Delete

- [ ] Buku masuk Trash
- [ ] Buku hilang dari katalog utama
- [ ] Cover masih ada

## Restore

- [ ] Buku kembali ke katalog
- [ ] Cover tetap tersedia

## Force Delete

- [ ] Record hilang permanen
- [ ] Cover terhapus permanen

---

# 61\. Security Checklist

- [ ] Gunakan FormRequest
- [ ] Validasi semua input
- [ ] Gunakan CSRF token
- [ ] Jangan percaya nama file dari user
- [ ] Batasi MIME image
- [ ] Batasi ukuran file
- [ ] Gunakan Laravel Storage
- [ ] Gunakan route model binding
- [ ] Escape output Blade
- [ ] Jangan expose path filesystem internal

---

# 62\. UI Responsive

## Desktop

```
Sidebar
+
Table
```

## Tablet

```
Sidebar kecil
+
Table
```

## Mobile

Table berubah menjadi card.

Contoh:

```
┌──────────────────────────────┐
│ [ COVER ]                    │
│                              │
│ Laravel untuk Pemula        │
│ John Doe                     │
│ Teknologi                    │
│                              │
│ Stok: 2                      │
│ ⚠ Stok Menipis              │
│                              │
│ [Detail] [Edit] [Hapus]      │
└──────────────────────────────┘
```

---

# 63\. Suggested Page Designs

## Dashboard

```
Dashboard
Ringkasan inventaris perpustakaan

┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐
│ Buku     │ │ Kategori │ │ Menipis  │ │ Habis    │
│ 50       │ │ 5        │ │ 4        │ │ 3        │
└──────────┘ └──────────┘ └──────────┘ └──────────┘
```

---

## Buku

```
Buku

Kelola koleksi dan inventaris buku.

[ Search.................... ] [Kategori ▼]
[Status ▼]                    [ + Tambah Buku ]

┌──────────────────────────────────────────────────────┐
│ Cover │ Judul │ Penulis │ Kategori │ Stok │ Action │
├──────────────────────────────────────────────────────┤
│      │       │         │          │      │        │
└──────────────────────────────────────────────────────┘

                    1 2 3 4 5
```

---

## Detail

```
Detail Buku

[ ← Kembali ]

┌─────────────┬─────────────────────────────┐
│             │ Laravel                     │
│   COVER     │ John Doe                    │
│             │ ABC Publisher               │
│             │ 2025                        │
│             │ BK-001                      │
│             │ Teknologi                   │
│             │                             │
│             │ Stok: 10                    │
└─────────────┴─────────────────────────────┘

Sinopsis
────────────────────────────────────────────
...
```

---

## Trash

```
Buku Terhapus

Data buku yang telah dipindahkan ke tempat sampah.

┌──────────────────────────────────────────────┐
│ Judul │ Kategori │ Dihapus │ Action         │
├──────────────────────────────────────────────┤
│ Book  │ Fiksi    │ 01/10   │ Restore        │
│       │          │         │ Hapus Permanen  │
└──────────────────────────────────────────────┘
```

---

# 64\. Project Folder Structure

Target final:

```
app/
├── Http/
│   ├── Controllers/
│   │   └── BookController.php
│   │
│   └── Requests/
│       ├── StoreBookRequest.php
│       └── UpdateBookRequest.php
│
├── Models/
│   ├── Book.php
│   └── Category.php
│
└── Services/
    └── BookCoverService.php

database/
├── factories/
│   └── BookFactory.php
│
├── migrations/
│   ├── xxxx_create_categories_table.php
│   └── xxxx_create_books_table.php
│
└── seeders/
    ├── CategorySeeder.php
    ├── BookSeeder.php
    └── DatabaseSeeder.php

resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   │
│   ├── components/
│   │   ├── alert.blade.php
│   │   ├── badge.blade.php
│   │   ├── button.blade.php
│   │   └── modal.blade.php
│   │
│   ├── dashboard/
│   │   └── index.blade.php
│   │
│   └── books/
│       ├── index.blade.php
│       ├── create.blade.php
│       ├── edit.blade.php
│       ├── show.blade.php
│       ├── trash.blade.php
│       └── partials/
│           ├── form.blade.php
│           ├── filters.blade.php
│           └── table.blade.php
│
└── css/
    └── app.css

routes/
└── web.php
```

---

# 65\. Artisan Commands

Setup:

```
composer create-project laravel/laravel library-inventory
```

Migration:

```
php artisan make:model Category -m
php artisan make:model Book -mf
```

Seeder:

```
php artisan make:seeder CategorySeeder
php artisan make:seeder BookSeeder
```

Controller:

```
php artisan make:controller BookController --resource
```

Request:

```
php artisan make:request StoreBookRequest
php artisan make:request UpdateBookRequest
```

Service:

```
Create manually:

app/Services/BookCoverService.php
```

Run:

```
php artisan migrate:fresh --seed
```

Storage:

```
php artisan storage:link
```

Server:

```
php artisan serve
```

---

# 66\. Implementation Order

Jangan mengerjakan semuanya sekaligus.

## Phase 1 — Laravel Setup

- [ ] Create Laravel project
- [ ] Configure `.env`
- [ ] Configure database
- [ ] Setup Tailwind
- [ ] Setup layout
- [ ] Setup sidebar

---

## Phase 2 — Database

- [ ] Category migration
- [ ] Book migration
- [ ] Foreign key
- [ ] Soft delete
- [ ] Category model
- [ ] Book model
- [ ] Relationship

Target:

```
Database sudah benar.
```

---

## Phase 3 — Seeder & Factory

- [ ] CategorySeeder
- [ ] BookFactory
- [ ] BookSeeder
- [ ] DatabaseSeeder
- [ ] Generate 50 books

Target:

```
5 categories
50 books
```

---

## Phase 4 — Read

- [ ] Book index
- [ ] Book show
- [ ] Pagination
- [ ] Search
- [ ] Category filter
- [ ] Stock filter
- [ ] Stock badges

Target:

```
User sudah bisa mencari dan melihat buku.
```

---

## Phase 5 — Create

- [ ] Create form
- [ ] StoreBookRequest
- [ ] Category dropdown
- [ ] Cover upload
- [ ] Validation
- [ ] Flash message

---

## Phase 6 — Update

- [ ] Edit form
- [ ] Pre-filled fields
- [ ] Current cover preview
- [ ] UpdateBookRequest
- [ ] ISBN unique ignore
- [ ] Replace cover
- [ ] Delete old cover

---

## Phase 7 — Soft Delete

- [ ] Destroy
- [ ] SoftDeletes
- [ ] Trash page
- [ ] onlyTrashed
- [ ] Restore
- [ ] Force Delete
- [ ] Delete physical cover

---

## Phase 8 — UI Polish

- [ ] Responsive
- [ ] Loading state
- [ ] Empty state
- [ ] Flash toast
- [ ] Confirmation modal
- [ ] Hover states
- [ ] Stock badges
- [ ] Icons
- [ ] Mobile card layout

---

## Phase 9 — Testing

- [ ] Create
- [ ] Read
- [ ] Update
- [ ] Delete
- [ ] Restore
- [ ] Force Delete
- [ ] Search
- [ ] Filter
- [ ] Pagination
- [ ] Upload
- [ ] Validation

---

# 67\. Rubric Mapping

## Database & Model — 20%

Checklist:

```
✓ categories migration
✓ books migration
✓ foreign key
✓ Category hasMany Book
✓ Book belongsTo Category
✓ SoftDeletes
✓ Factory
✓ CategorySeeder
✓ BookSeeder
✓ 50 dummy books
```

---

## Form Request & Validation — 20%

Checklist:

```
✓ StoreBookRequest
✓ UpdateBookRequest
✓ required fields
✓ category exists
✓ ISBN unique
✓ ISBN unique ignore saat update
✓ publication year validation
✓ stock >= 0
✓ image validation
✓ max 2MB
✓ JPG/JPEG/PNG
```

---

## CRUD & Upload — 30%

Checklist:

```
✓ Index
✓ Show
✓ Create
✓ Store
✓ Edit
✓ Update
✓ Delete
✓ Cover upload
✓ Cover preview
✓ Delete old cover
✓ Storage link
```

---

## Search, Filter & Pagination — 15%

Checklist:

```
✓ Search title
✓ Search author
✓ Search ISBN
✓ Filter category
✓ Filter stock available
✓ Filter stock empty
✓ Pagination 10
✓ withQueryString()
✓ Search + filter + pagination
```

---

## Soft Delete & Trash — 15%

Checklist:

```
✓ SoftDeletes
✓ delete()
✓ onlyTrashed()
✓ withTrashed()
✓ restore()
✓ forceDelete()
✓ Delete physical cover
```

---

# 68\. Final Acceptance Criteria

Project dianggap selesai apabila:

### Database

- [ ] 2 tabel berhasil dibuat
- [ ] Relasi One-to-Many berhasil
- [ ] Soft Delete aktif

### Seeder

- [ ] 5 kategori
- [ ] 50 buku

### CRUD

- [ ] Create
- [ ] Read
- [ ] Update
- [ ] Delete

### Upload

- [ ] JPG
- [ ] JPEG
- [ ] PNG
- [ ] Maximum 2MB
- [ ] Storage link
- [ ] Old image deleted ketika update

### Search

- [ ] Title
- [ ] Author
- [ ] ISBN

### Filter

- [ ] Category
- [ ] Available
- [ ] Empty

### Pagination

- [ ] 10/page
- [ ] Query filter tetap dipertahankan

### Soft Delete

- [ ] Delete → Trash
- [ ] Restore
- [ ] Force Delete
- [ ] Physical cover deletion

### UX

- [ ] Flash message
- [ ] Confirmation
- [ ] Validation errors
- [ ] Responsive
- [ ] Clean UI

---

# 69\. Definition of Done

MVP final harus memiliki alur:

```
                ┌───────────────┐
                │   Dashboard   │
                └───────┬───────┘
                        │
                        ↓
                ┌───────────────┐
                │   Semua Buku  │
                └───────┬───────┘
                        │
             ┌──────────┼──────────┐
             ↓          ↓          ↓
          Create      Detail      Edit
             │          │          │
             ↓          ↓          ↓
           Store                  Update
             │                     │
             └──────────┬──────────┘
                        ↓
                     Delete
                        │
                        ↓
                  ┌───────────┐
                  │   Trash   │
                  └─────┬─────┘
                        │
                 ┌──────┴──────┐
                 ↓             ↓
              Restore      Force Delete
                 │             │
                 ↓             ↓
              Katalog       Permanen
```

---

# 70\. Prioritas Pengerjaan

Prioritas utama berdasarkan bobot nilai:

```
                    PRIORITAS
                        │
             ┌──────────┴──────────┐
             │                     │
        CRUD + Upload        Database/Model
           30%                    20%
             │                     │
             └──────────┬──────────┘
                        │
              Validation 20%
                        │
             ┌──────────┴──────────┐
             │                     │
       Search/Filter          Soft Delete
           15%                    15%
```

Jadi jangan menghabiskan terlalu banyak waktu membuat dashboard\
 yang kompleks.

Yang paling penting:

1. **Database benar**
2. **CRUD benar**
3. **Validation benar**
4. **Upload/delete file benar**
5. **Search/filter/pagination benar**
6. **Soft delete/trash benar**
7. Baru polish UI
