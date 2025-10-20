CREATE DATABASE IF NOT EXISTS `datahub` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `datahub`;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','member','operator') NOT NULL DEFAULT 'member',
  `balance` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `operator_firms` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `contact_email` VARCHAR(150) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `data_records` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `member_id` INT NOT NULL,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `category` VARCHAR(100) NOT NULL,
  `status` ENUM('pending','approved','rejected','sold') NOT NULL DEFAULT 'pending',
  `price` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `sold_to` INT DEFAULT NULL,
  `sold_price` DECIMAL(12,2) DEFAULT NULL,
  `commission` DECIMAL(12,2) DEFAULT NULL,
  `sold_at` TIMESTAMP NULL DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_data_member` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_data_sold_to` FOREIGN KEY (`sold_to`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `transactions` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `buyer_id` INT DEFAULT NULL,
  `seller_id` INT DEFAULT NULL,
  `record_id` INT DEFAULT NULL,
  `amount` DECIMAL(12,2) NOT NULL,
  `commission` DECIMAL(12,2) NOT NULL DEFAULT 0,
  `type` VARCHAR(50) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_transactions_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_transactions_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_transactions_record` FOREIGN KEY (`record_id`) REFERENCES `data_records` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS `settings` (
  `key` VARCHAR(100) NOT NULL PRIMARY KEY,
  `value` VARCHAR(255) NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO `users` (`name`, `email`, `password`, `role`, `balance`)
VALUES ('Sistem Yöneticisi', 'admin@datahub.local', '$2y$12$35t.CeHyjsMjY8RaQTcjCeS/UpPJ4aRv0pvGAyNc7b.B4vkQbRD9G', 'admin', 0)
ON DUPLICATE KEY UPDATE email = email;

INSERT INTO `settings` (`key`, `value`) VALUES ('commission_rate', '0.1')
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
