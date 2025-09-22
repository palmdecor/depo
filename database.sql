-- Veritabanı şeması

CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_salt CHAR(64) NOT NULL,
    password_hash CHAR(128) NOT NULL,
    role ENUM('customer', 'admin') NOT NULL DEFAULT 'customer',
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

CREATE TABLE IF NOT EXISTS customer_reports (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

INSERT INTO users (first_name, last_name, phone, email, password_salt, password_hash, role, is_active)
VALUES ('Sistem', 'Yöneticisi', '+900000000000', 'admin@example.com', '3eb6c7f7b181bf380858cac5962aa4c3e3a4f6402ef963fe6686090d9ac9d2f6', '230afd8fc22f36138b5ce3d7afe0e176525d1163fa594c1267b707f7e59b4d2216d26fed01c7d256ba28a5a7f1bcf2b28b3858f9afec05537dcdaf51d228e83c', 'admin', 1);
