-- ============================================================
-- DATABASE SETUP — tap
-- Run this script as root on your local MySQL server.
-- These same credentials are used on the production server.
-- ============================================================

CREATE DATABASE IF NOT EXISTS `tap`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `tap`;

-- ============================================================
-- GRANT (run as root)
-- Creates/updates the 'root'@'%' user with the production password.
-- This allows PHP's PDO (host=dbmy) to connect.
-- If your local MySQL is bound to localhost, also grant localhost:
-- ============================================================
-- ALTER USER 'root'@'localhost' IDENTIFIED BY 'password';
-- FLUSH PRIVILEGES;

-- ============================================================
-- V1 Tables  (used by /api/v1/*)
-- ============================================================

CREATE TABLE IF NOT EXISTS `users` (
    `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`       VARCHAR(150)  NOT NULL,
    `email`      VARCHAR(150)  NOT NULL UNIQUE,
    `created_at` TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `productos` (
    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `sku`         VARCHAR(50)       NOT NULL UNIQUE,
    `name`        VARCHAR(200)      NOT NULL,
    `description` TEXT,
    `price`       DECIMAL(10, 2)    NOT NULL DEFAULT 0.00,
    `stock`       INT               NOT NULL DEFAULT 0,
    `created_at`  TIMESTAMP         DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP         DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- V2 Tables  (used by /api/v2/*)
-- ============================================================

CREATE TABLE IF NOT EXISTS `api_users` (
    `id`            BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username`      VARCHAR(100)                    NOT NULL UNIQUE,
    `email`         VARCHAR(150)                    NOT NULL UNIQUE,
    `password_hash` VARCHAR(255)                    NOT NULL,
    `status`        ENUM('ACTIVE', 'INACTIVE')      DEFAULT 'ACTIVE',
    `created_at`    TIMESTAMP                       DEFAULT CURRENT_TIMESTAMP,
    `updated_at`    TIMESTAMP                       DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `api_tokens` (
    `id`         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id`    BIGINT UNSIGNED NOT NULL,
    `token`      VARCHAR(255)    NOT NULL UNIQUE,
    `expires_at` DATETIME        NOT NULL,
    `revoked`    BOOLEAN         DEFAULT FALSE,
    `created_at` TIMESTAMP       DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_api_tokens_user`
        FOREIGN KEY (`user_id`)
        REFERENCES `api_users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- Seed data
-- ============================================================

-- V1 sample products (will be skipped if SKUs already exist)
INSERT IGNORE INTO `productos` (`sku`, `name`, `description`, `price`, `stock`) VALUES
('PROD-001', 'Laptop Gaming',     'Laptop de alto rendimiento para gaming',          15999.99, 10),
('PROD-002', 'Mouse Inalámbrico', 'Mouse ergonómico con conexión Bluetooth',            349.00, 50),
('PROD-003', 'Teclado Mecánico',  'Teclado mecánico RGB con switches Cherry MX',       1200.00, 25);

-- V2 API users for authentication testing
-- username=admin   / password=Admin1234!
-- username=inactivo / password=Admin1234!  (status=INACTIVE — will be rejected by login)
INSERT IGNORE INTO `api_users` (`username`, `email`, `password_hash`, `status`) VALUES
('admin',    'admin@miapp.local',    '$2y$10$tgY.N.0kyB6VWo5lG4BY1OlCdRrt0fOgv1ik3o.gm2trbM197bw.e', 'ACTIVE'),
('inactivo', 'inactivo@miapp.local', '$2y$10$tgY.N.0kyB6VWo5lG4BY1OlCdRrt0fOgv1ik3o.gm2trbM197bw.e', 'INACTIVE');
