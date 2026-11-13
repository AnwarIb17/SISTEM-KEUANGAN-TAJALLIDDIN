-- Database: pondok_keuangan
-- Created: 2026-11-08

CREATE DATABASE IF NOT EXISTS pondok_keuangan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE pondok_keuangan;

-- Auth
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Master Data
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE alumni (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE expense_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Operasional Pondok
CREATE TABLE infaq_monthly (
    id INT AUTO_INCREMENT PRIMARY KEY,
    class_id INT NOT NULL,
    bulan_tahun DATE NOT NULL COMMENT 'Format: YYYY-MM-01',
    amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_class_month (class_id, bulan_tahun)
) ENGINE=InnoDB;

CREATE TABLE operational_income (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE operational_expense (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES expense_categories(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Alumni Fund
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    nominal_per_alumni DECIMAL(15,2) NOT NULL,
    deadline DATE,
    installment_count INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE alumni_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumni_id INT NOT NULL,
    program_id INT NOT NULL,
    amount_paid DECIMAL(15,2) NOT NULL DEFAULT 0,
    paid_installments INT NOT NULL DEFAULT 0,
    tanggal_bayar DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (alumni_id) REFERENCES alumni(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE,
    UNIQUE KEY unique_alumni_program (alumni_id, program_id)
) ENGINE=InnoDB;

-- Installment schedule per program (due dates per installment)
CREATE TABLE program_installments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    program_id INT NOT NULL,
    installment_number INT NOT NULL,
    nominal DECIMAL(15,2) NOT NULL,
    due_date DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_program_installment (program_id, installment_number),
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Per-installment payment history for each alumni participant
CREATE TABLE alumni_installment_payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumni_payment_id INT NOT NULL,
    program_id INT NOT NULL,
    installment_number INT NOT NULL,
    due_date DATE NOT NULL,
    paid_date DATE NULL,
    paid_amount DECIMAL(15,2) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_alumni_installment (alumni_payment_id, installment_number),
    FOREIGN KEY (alumni_payment_id) REFERENCES alumni_payments(id) ON DELETE CASCADE,
    FOREIGN KEY (program_id) REFERENCES programs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Seed Data
INSERT INTO users (username, password_hash) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'); 
-- password: password

INSERT INTO classes (name) VALUES 
('SIBYAN'),
('PPA'),
('PPB'),
('IBTIDA 1'),
('IBTIDA 2'),
('IBTIDA 3');

INSERT INTO expense_categories (name) VALUES 
('Makanan'),
('Listrik'),
('Air'),
('Transportasi'),
('Lainnya');
