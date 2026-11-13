# Pondok Keuangan - Setup Guide

## Installation

### 1. Database Setup

Import `database/schema.sql` ke MySQL:

```bash
mysql -u root < database/schema.sql
```

Default credentials:
- Username: `admin`
- Password: `password`

### 2. XAMPP Configuration

Pastikan `mod_rewrite` aktif di Apache:

```ini
# httpd.conf
LoadModule rewrite_module modules/mod_rewrite.so
```

### 3. File Structure

```
SKT/
├── config/
│   ├── auth.php
│   └── database.php
├── controllers/
├── models/
├── public/
│   ├── index.php
│   └── .htaccess
├── views/
│   ├── layouts/
│   ├── auth/
│   ├── operasional/
│   └── alumni/
└── database/
    └── schema.sql
```

### 4. Akses Aplikasi

- URL: `http://localhost/SKT/`
- Login: admin / password

## Features

✅ **Operasional Pondok**
- Input Infaq (6 kelas)
- Pemasukkan Lainnya
- Pengeluaran + Kategori Dinamis
- Laporan Bulanan & Tahunan
- Dashboard dengan Chart

✅ **Keuangan Alumni**
- CRUD Data Alumni
- Create Program/Kegiatan
- Tracking Pembayaran
- Filter Status (Lunas/Belum/Minus)
- Export Excel & PDF

✅ **Security**
- Login System dengan Session
- Password Hashing
- CSRF Protection Ready
- Input Validation

## Future Extensions

- Multi-user & Role System
- Qurban/Jarah/Isra Mi'raj modules
- SMS Notification
- Advanced Reporting
- API Layer

## Support

Untuk pertanyaan atau issues, check models/controllers/views structure.
