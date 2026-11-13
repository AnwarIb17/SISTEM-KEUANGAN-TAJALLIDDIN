# Pondok Keuangan - Setup Guide

Aplikasi keuangan pondok berbasis PHP 8.2 + MySQL (PDO, MVC sederhana, tanpa framework).

## Installation

### 1. Database Setup

Import `database/schema.sql` ke MySQL:

```bash
mysql -u root < database/schema.sql
```

Untuk database yang sudah berjalan, jalankan migration berikut secara berurutan:

```bash
php migrations/2026_11_13_add_installment_columns.php
php migrations/2026_11_13_add_alumni_payment_installments_table.php
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
├── migrations/
├── public/
│   ├── index.php
│   └── .htaccess
├── tests/
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
- Create Program/Kegiatan dengan target nominal dan jumlah cicilan
- Tracking Pembayaran (satu kali & cicilan)
- Pembayaran cicilan dengan validasi: tidak bisa melebihi target dan tidak bisa membayar cicilan yang sudah lunas
- Riwayat pembayaran per cicilan: nominal, jatuh tempo, jumlah dibayar, tanggal bayar, dan status (LUNAS/PARTIAL/BELUM)
- Detail pembayaran per program melalui ikon mata pada halaman detail alumni
- Filter Status (Lunas/Belum/Minus)
- Export Excel & PDF

✅ **Security**
- Login System dengan Session
- Password Hashing
- CSRF Protection Ready
- Input Validation

## Struktur Data Pembayaran

- `alumni_payments` — total pembayaran per alumni per program (`amount_paid`, `paid_installments`, `tanggal_bayar`).
- `program_installments` — jadwal cicilan per program (`installment_number`, `nominal`, `due_date`).
- `alumni_installment_payments` — riwayat pembayaran per cicilan (`paid_amount`, `paid_date`, `due_date`).

Jika program memiliki lebih dari satu cicilan, pembayaran dicatat per `installment_number`. Program dengan satu cicilan tetap mendukung pembayaran satu kali.

## Future Extensions

- Multi-user & Role System
- Ledger detail per transaksi pembayaran (setiap aksi bayar menjadi satu baris riwayat)
- SMS Notification
- Advanced Reporting
- API Layer

## Support

Untuk pertanyaan atau issues, check models/controllers/views structure.
