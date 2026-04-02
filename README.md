# ForTransac POS

Sistem Point of Sale sederhana dan modern untuk minimarket.

**Store:** ForTransac — Racoon City

## Akun Default

| Field    | Value             |
|----------|-------------------|
| Username | admin             |
| Password | admin123          |
| Email    | admin@admin.com   |

## Menjalankan dengan Docker

```bash
docker-compose up --build -d
```

- **App:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081
- **DB Port:** localhost:3307

## Menjalankan dengan XAMPP

1. Copy folder project ke `htdocs/fortransac`
2. Import `database.sql` ke MySQL via phpMyAdmin
3. Edit `config.php` jika perlu (DB_HOST, DB_NAME, DB_USER, DB_PASS)
4. Akses: `http://localhost/fortransac`

> Catatan XAMPP: `DB_HOST = localhost`, `DB_NAME = fortransac_db`, `DB_USER = root`, `DB_PASS = (kosong atau sesuai setup XAMPP)`

## Fitur

- **Kasir POS** — Input SKU atau klik produk, keranjang real-time, checkout & cetak struk
- **Manajemen Produk** — CRUD produk dengan auto-generate SKU (ALIAS-NAMA-BERAT)
- **Manajemen Kategori** — CRUD kategori dengan alias 3 karakter untuk SKU
- **Riwayat Transaksi** — Filter tanggal/kasir, detail transaksi, print/download struk
- **Daftar Kasir** — Lihat semua kasir dan statistik masing-masing
- **Profil** — View profile & edit profile (terpisah), ganti password
- **Auth** — Login/Register tanpa secret key

## Struktur File

```
fortransac/
├── config.php              # Konfigurasi utama
├── index.php               # Redirect otomatis
├── database.sql            # Schema + seed data
├── Dockerfile
├── docker-compose.yml
├── assets/
│   ├── css/style.css       # Stylesheet utama
│   └── js/
│       ├── app.js          # JS utama (sidebar, modal, dll)
│       └── kasir.js        # Logic cart POS
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── footer-auth.php
└── pages/
    ├── login.php
    ├── register.php
    ├── logout.php
    ├── kasir.php           # Halaman POS utama
    ├── produk.php          # Manajemen produk
    ├── kategori.php        # Manajemen kategori
    ├── transaksi.php       # Riwayat transaksi
    ├── detail-transaksi.php
    ├── daftar-kasir.php
    ├── profile.php         # View profil
    ├── edit-profile.php    # Edit profil
    └── struk-pdf.php       # Print/PDF struk
```

## Tech Stack

- **Backend:** PHP 7.2 native (mysqli, no framework, no PDO)
- **Database:** MySQL 5.7
- **Frontend:** HTML5, CSS3, JavaScript native
- **Server:** Apache2 (Ubuntu 18.04 / XAMPP)
- **Container:** Docker + Docker Compose
