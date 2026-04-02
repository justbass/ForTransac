-- ForTransac POS Database
-- MySQL 5.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE DATABASE IF NOT EXISTS `fortransac_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `fortransac_db`;

-- Kasir (Users)
CREATE TABLE `kasir` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kategori
CREATE TABLE `kategori` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `alias` char(3) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Produk
CREATE TABLE `produk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(191) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `kategori_id` int(11) NOT NULL,
  `berat` int(11) NOT NULL DEFAULT '0',
  `price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `stock` int(11) NOT NULL DEFAULT '0',
  `sold` int(11) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `fk_produk_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Transaksi
CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) NOT NULL,
  `kasir_id` int(11) NOT NULL,
  `grand_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `amount_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `change_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`),
  KEY `kasir_id` (`kasir_id`),
  CONSTRAINT `fk_transaksi_kasir` FOREIGN KEY (`kasir_id`) REFERENCES `kasir` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Detail Transaksi
CREATE TABLE `transaksi_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaksi_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `name` varchar(191) NOT NULL,
  `sku` varchar(100) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `discount` decimal(5,2) NOT NULL DEFAULT '0.00',
  `qty` int(11) NOT NULL DEFAULT '1',
  `subtotal` decimal(15,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `transaksi_id` (`transaksi_id`),
  KEY `produk_id` (`produk_id`),
  CONSTRAINT `fk_detail_transaksi` FOREIGN KEY (`transaksi_id`) REFERENCES `transaksi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_produk` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed: Admin account (password: admin123)
INSERT INTO `kasir` (`username`, `email`, `password`) VALUES
('admin', 'admin@admin.com', '$2y$10$1BBxdDE55zv5GUImeM2NNuTPNeUvqjny0UJSTG48yLqn9RmbxMTIy');

-- Seed: Categories
INSERT INTO `kategori` (`nama`, `alias`) VALUES
('Minuman', 'MNM'),
('Makanan', 'MKN'),
('Snack', 'SNK'),
('Rokok', 'RKK'),
('Kebersihan', 'KBR');

-- Seed: Products
INSERT INTO `produk` (`name`, `sku`, `discount`, `kategori_id`, `berat`, `price`, `stock`, `sold`) VALUES
('Aqua Botol', 'MNM-AQUA-600', 0, 1, 600, 4000, 100, 50),
('Teh Botol Sosro', 'MNM-TEHBTL-350', 0, 1, 350, 5000, 80, 30),
('Indomie Goreng', 'MKN-INDOMIEG-85', 5, 2, 85, 3500, 200, 150),
('Indomie Kuah', 'MKN-INDOMIEK-75', 0, 2, 75, 3000, 150, 100),
('Chitato Sapi Panggang', 'SNK-CHITATO-68', 0, 3, 68, 12000, 60, 40),
('Lays BBQ', 'SNK-LAYS-68', 0, 3, 68, 12000, 50, 20),
('Gudang Garam Surya', 'RKK-GGS-16', 0, 4, 16, 25000, 30, 10),
('Sabun Lifebuoy', 'KBR-LIFBUY-100', 0, 5, 100, 8000, 40, 15);
