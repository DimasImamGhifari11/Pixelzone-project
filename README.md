# Pixelzone

Pixelzone adalah aplikasi e-commerce game berbasis web dengan sistem manajemen produk, kategori, autentikasi pengguna, dan fitur checkout. Dibangun menggunakan **Laravel 11** sebagai backend API dan **Vue.js 3** sebagai frontend SPA.

## Tech Stack

### Backend
- **PHP 8.2+** & **Laravel 11**
- **Laravel Sanctum** — Token-based API authentication
- **MySQL** — Database
- **PHPUnit** — Unit & Feature testing

### Frontend
- **Vue.js 3** (Composition API)
- **Vue Router 4** — Client-side routing
- **Axios** — HTTP client
- **Bootstrap 5** — UI framework
- **Vite** — Build tool & dev server
- **Cypress** — End-to-end testing

## Struktur Project

```
Pixelzone-Project/
├── Pixelzone-Backend/          # Laravel 11 REST API
│   ├── app/
│   │   ├── Http/Controllers/Api/
│   │   │   ├── AuthController.php
│   │   │   ├── CategoryController.php
│   │   │   └── ProductController.php
│   │   ├── Models/
│   │   │   ├── User.php
│   │   │   ├── Category.php
│   │   │   └── Product.php
│   │   └── Services/
│   │       └── ProductService.php
│   ├── database/
│   │   ├── migrations/
│   │   ├── factories/
│   │   └── seeders/
│   ├── routes/
│   │   └── api.php
│   └── tests/
│       ├── Feature/
│       │   ├── AuthTest.php
│       │   └── ProductTest.php
│       └── Unit/
│           └── ProductServiceTest.php
│
└── Pixelzone-Frontend/         # Vue.js 3 SPA
    ├── src/
    │   ├── views/
    │   │   ├── Auth/
    │   │   │   ├── Login.vue
    │   │   │   └── Register.vue
    │   │   ├── Dashboard.vue
    │   │   ├── Products.vue
    │   │   ├── Categories.vue
    │   │   ├── Checkout.vue
    │   │   ├── PaymentSuccess.vue
    │   │   ├── produk/
    │   │   │   ├── index.vue
    │   │   │   ├── create.vue
    │   │   │   └── Edit.vue
    │   │   └── categories/
    │   │       ├── Index.vue
    │   │       ├── CreateCategory.vue
    │   │       └── editcategory.vue
    │   ├── components/
    │   │   └── Notification.vue
    │   ├── router/
    │   │   └── index.js
    │   ├── store.js
    │   └── axios.js
    └── cypress/
        └── e2e/
            ├── login.cy.js
            ├── register.cy.js
            └── crud_product.cy.js
```

## Database Schema

```
┌──────────────────┐       ┌──────────────────────┐
│     users         │       │   personal_access     │
├──────────────────┤       │   _tokens             │
│ id (PK)          │       ├──────────────────────┤
│ name             │◄──────│ tokenable_id (FK)    │
│ email (unique)   │       │ tokenable_type       │
│ password         │       │ name                 │
│ created_at       │       │ token                │
│ updated_at       │       │ abilities            │
└──────────────────┘       │ expires_at           │
                           └──────────────────────┘

┌──────────────────┐       ┌──────────────────────┐
│   categories      │       │     products          │
├──────────────────┤       ├──────────────────────┤
│ id (PK)          │◄──────│ id (PK)              │
│ name             │       │ name                 │
│ created_at       │       │ description          │
│ updated_at       │       │ price (integer)      │
│                  │       │ category_id (FK)     │
│                  │       │ image (URL)          │
│                  │       │ created_at           │
│                  │       │ updated_at           │
└──────────────────┘       └──────────────────────┘

Relasi:
- Category hasMany Products
- Product belongsTo Category
- Cascade delete: hapus category → hapus semua produk terkait
```

## API Endpoints

### Autentikasi (Public)

| Method | Endpoint         | Deskripsi                        |
|--------|------------------|----------------------------------|
| POST   | `/api/register`  | Registrasi user baru             |
| POST   | `/api/login`     | Login dan mendapatkan token      |

### Protected (Memerlukan Bearer Token)

| Method | Endpoint              | Deskripsi                    |
|--------|-----------------------|------------------------------|
| GET    | `/api/user`           | Data user yang sedang login  |
| POST   | `/api/logout`         | Logout dan hapus token       |
| GET    | `/api/categories`     | Daftar semua kategori        |
| POST   | `/api/categories`     | Buat kategori baru           |
| GET    | `/api/categories/{id}`| Detail kategori              |
| PUT    | `/api/categories/{id}`| Update kategori              |
| DELETE | `/api/categories/{id}`| Hapus kategori               |
| GET    | `/api/products`       | Daftar semua produk          |
| POST   | `/api/products`       | Buat produk baru             |
| GET    | `/api/products/{id}`  | Detail produk                |
| PUT    | `/api/products/{id}`  | Update produk                |
| DELETE | `/api/products/{id}`  | Hapus produk                 |

### Contoh Request & Response

**Register:**
```json
// POST /api/register
// Request
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}

// Response (201)
{
  "success": true,
  "message": "User registered successfully",
  "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
  "token": "1|abc123..."
}
```

**Login:**
```json
// POST /api/login
// Request
{
  "email": "john@example.com",
  "password": "password123"
}

// Response (200)
{
  "success": true,
  "message": "Login successful",
  "user": { "id": 1, "name": "John Doe", "email": "john@example.com" },
  "token": "2|xyz789..."
}
```

**Create Product:**
```json
// POST /api/products
// Headers: Authorization: Bearer {token}
// Request
{
  "name": "Game Title",
  "description": "Deskripsi game",
  "price": 150000,
  "category_id": 1,
  "image": "https://example.com/image.jpg"
}

// Response (201)
{
  "success": true,
  "message": "Product created successfully",
  "data": { "id": 1, "name": "Game Title", "price": 150000, ... }
}
```

## Fitur Aplikasi

### Autentikasi
- Registrasi akun baru dengan validasi
- Login dengan email & password
- Token-based authentication (Laravel Sanctum)
- Route guard — halaman terproteksi hanya bisa diakses setelah login

### Manajemen Kategori
- CRUD kategori (Create, Read, Update, Delete)
- Validasi nama kategori

### Manajemen Produk
- CRUD produk lengkap
- Upload gambar via URL (dengan fitur paste dari clipboard)
- Validasi: nama (3-50 karakter), harga (1 - 5.000.000), URL gambar
- Relasi dengan kategori (dropdown pilihan)
- Eager loading kategori pada daftar produk

### Shopping & Checkout
- Dashboard menampilkan semua produk yang tersedia
- Tambah produk ke keranjang (disimpan di localStorage)
- Pilih 2-3 produk untuk bulk checkout
- Beli langsung produk satuan
- Halaman konfirmasi pembayaran berhasil

### Notifikasi
- Toast notification untuk feedback aksi (sukses/error)
- Tampil di bagian bawah layar dengan animasi fade

## Alur Autentikasi

```
┌─────────┐    POST /api/register     ┌─────────┐
│  Client  │ ───────────────────────► │  Server  │
│ (Vue.js) │                          │ (Laravel)│
│          │    POST /api/login        │          │
│          │ ───────────────────────► │          │
│          │ ◄─────────────────────── │          │
│          │    { token, user }        │          │
│          │                          │          │
│          │    GET /api/products      │          │
│          │    Authorization:         │          │
│          │    Bearer {token}         │          │
│          │ ───────────────────────► │          │
│          │ ◄─────────────────────── │          │
│          │    { data: [...] }        │          │
│          │                          │          │
│          │    POST /api/logout       │          │
│          │ ───────────────────────► │          │
│          │    Token dihapus          │          │
└─────────┘                          └─────────┘
```

## Instalasi & Setup

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js >= 16
- MySQL
- Git

### 1. Clone Repository

```bash
git clone https://github.com/DimasImamGhifari11/Pixelzone-project.git
cd Pixelzone-project
```

### 2. Setup Backend

```bash
cd Pixelzone-Backend

# Install dependencies
composer install

# Copy file environment
cp .env.example .env

# Generate application key
php artisan key:generate

# Konfigurasi database di file .env
# DB_DATABASE=pixel
# DB_USERNAME=root
# DB_PASSWORD=

# Jalankan migrasi database
php artisan migrate

# (Opsional) Jalankan seeder
php artisan db:seed

# Jalankan server
php artisan serve
```

Backend akan berjalan di `http://localhost:8000`

### 3. Setup Frontend

```bash
cd Pixelzone-Frontend

# Install dependencies
npm install

# Jalankan development server
npm run dev
```

Frontend akan berjalan di `http://localhost:5173`

## Menjalankan Testing

### Backend (PHPUnit)

```bash
cd Pixelzone-Backend

# Jalankan semua test
php artisan test

# Jalankan test dengan coverage
php artisan test --coverage
```

**Test yang tersedia:**

| Test File                | Jumlah Test | Deskripsi                                        |
|--------------------------|-------------|--------------------------------------------------|
| `Feature/AuthTest.php`   | 3           | Register, login, login dengan kredensial salah   |
| `Feature/ProductTest.php`| 5           | CRUD produk via API endpoint                     |
| `Unit/ProductServiceTest.php` | 8      | Unit test ProductService (create, read, update, delete, edge cases) |

Konfigurasi test menggunakan **SQLite in-memory** agar tidak mengganggu database development.

### Frontend (Cypress E2E)

```bash
cd Pixelzone-Frontend

# Buka Cypress GUI
npx cypress open

# Atau jalankan headless
npx cypress run
```

**Test yang tersedia:**

| Test File                   | Jumlah Test | Deskripsi                                       |
|-----------------------------|-------------|--------------------------------------------------|
| `login.cy.js`              | 2           | Login sukses, login gagal                        |
| `register.cy.js`           | 2           | Registrasi sukses, password tidak cocok          |
| `crud_product.cy.js`       | 6           | Create, read, update, delete produk + validasi   |

> **Catatan:** Pastikan backend dan frontend sudah berjalan sebelum menjalankan Cypress test.

## Konfigurasi Environment

### Backend (.env)

| Variable                    | Nilai Default            | Deskripsi                     |
|-----------------------------|--------------------------|-------------------------------|
| `APP_URL`                   | `http://localhost:8000`  | URL backend                   |
| `FRONTEND_URL`              | `http://localhost:5173`  | URL frontend (untuk CORS)     |
| `SANCTUM_STATEFUL_DOMAINS`  | `localhost:5173`         | Domain stateful Sanctum       |
| `DB_CONNECTION`             | `mysql`                  | Driver database               |
| `DB_DATABASE`               | `pixel`                  | Nama database                 |
| `DB_USERNAME`               | `root`                   | Username database             |
| `DB_PASSWORD`               |                          | Password database             |

### Frontend (vite.config.js)

Dev server secara otomatis mem-proxy request `/api` dan `/sanctum` ke `http://127.0.0.1:8000`.

## Arsitektur Aplikasi

```
┌─────────────────────────────────────────────────┐
│                   Frontend (Vue.js)              │
│                                                  │
│  ┌──────────┐  ┌─────────┐  ┌───────────────┐  │
│  │  Router   │  │  Store  │  │    Axios      │  │
│  │  Guard    │  │ (State) │  │  Interceptor  │  │
│  └────┬─────┘  └────┬────┘  └───────┬───────┘  │
│       │              │               │           │
│  ┌────▼──────────────▼───────────────▼────────┐ │
│  │              Views / Components             │ │
│  │  Login, Register, Dashboard, Products,     │ │
│  │  Categories, Checkout, PaymentSuccess      │ │
│  └────────────────────┬───────────────────────┘ │
└───────────────────────┼─────────────────────────┘
                        │ HTTP (REST API)
                        │ Bearer Token Auth
┌───────────────────────┼─────────────────────────┐
│                   Backend (Laravel)              │
│                       │                          │
│  ┌────────────────────▼───────────────────────┐ │
│  │            Sanctum Middleware               │ │
│  └────────────────────┬───────────────────────┘ │
│                       │                          │
│  ┌────────────────────▼───────────────────────┐ │
│  │              Controllers                    │ │
│  │  AuthController, ProductController,        │ │
│  │  CategoryController                        │ │
│  └────────────────────┬───────────────────────┘ │
│                       │                          │
│  ┌────────────────────▼───────────────────────┐ │
│  │          Services & Models                  │ │
│  │  ProductService, User, Product, Category   │ │
│  └────────────────────┬───────────────────────┘ │
│                       │                          │
│  ┌────────────────────▼───────────────────────┐ │
│  │              MySQL Database                 │ │
│  │  users, categories, products,              │ │
│  │  personal_access_tokens                    │ │
│  └────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────┘
```
