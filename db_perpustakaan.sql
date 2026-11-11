-- Selfwise Library - ekspor database
-- Login awal: username "admin" / password "admin123"
CREATE DATABASE IF NOT EXISTS db_perpustakaan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE db_perpustakaan;

DROP TABLE IF EXISTS buku;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password VARCHAR(255) NOT NULL,
  nama_lengkap VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_username (username)
) ENGINE=InnoDB;

CREATE TABLE buku (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  judul VARCHAR(200) NOT NULL,
  kategori VARCHAR(50) NOT NULL,
  penulis VARCHAR(120) NOT NULL,
  tahun_terbit SMALLINT UNSIGNED NOT NULL,
  stok INT UNSIGNED NOT NULL DEFAULT 0,
  sampul VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_kategori (kategori)
) ENGINE=InnoDB;

INSERT INTO users (username, password, nama_lengkap) VALUES
('admin', '$2b$12$A6jZcLXnQS.xi379m6mU1ecHz4dZ4y3MUodU7U0V/xn/QoN0emj5C', 'Rehan Ramadhani');

INSERT INTO buku (judul, kategori, penulis, tahun_terbit, stok, sampul) VALUES
('Pemrograman Web dengan PHP dan MySQL', 'Teknologi', 'Betha Sidik', 2019, 4, NULL),
('Analisis dan Perancangan Sistem Informasi', 'Sistem Informasi', 'Jogiyanto HM', 2005, 0, NULL),
('Laskar Pelangi', 'Sastra', 'Andrea Hirata', 2005, 2, NULL);
