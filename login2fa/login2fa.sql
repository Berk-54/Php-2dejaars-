-- login2fa.sql
-- Importeer dit in phpMyAdmin (tab Importeren) of run in SQL-tab.

CREATE DATABASE IF NOT EXISTS login2fa
CHARACTER SET utf8mb4
COLLATE utf8mb4_general_ci;

USE login2fa;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  2fa_secret VARCHAR(255) NOT NULL
);
