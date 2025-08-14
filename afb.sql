-- Create database & user (adjust as needed)
CREATE DATABASE IF NOT EXISTS spelete_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE spelete_db;

-- Users (optional; future auth-ready)
CREATE TABLE IF NOT EXISTS users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name          VARCHAR(120) NOT NULL,
  email         VARCHAR(191) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NULL,
  role          ENUM('customer','admin') DEFAULT 'customer',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Categories (aligning to your pages: kids, male, female)
CREATE TABLE IF NOT EXISTS categories (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(120) NOT NULL,
  slug        VARCHAR(160) NOT NULL UNIQUE,
  is_active   TINYINT(1) DEFAULT 1,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Products
CREATE TABLE IF NOT EXISTS products (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(200) NOT NULL,
  slug         VARCHAR(220) NOT NULL UNIQUE,
  description  TEXT,
  price        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  image_url    VARCHAR(500),
  stock        INT NOT NULL DEFAULT 0,
  is_active    TINYINT(1) DEFAULT 1,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Product ↔ Category (many-to-many)
CREATE TABLE IF NOT EXISTS product_categories (
  product_id  INT UNSIGNED NOT NULL,
  category_id INT UNSIGNED NOT NULL,
  PRIMARY KEY (product_id, category_id),
  CONSTRAINT fk_pc_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
  CONSTRAINT fk_pc_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Newsletter subscribers
CREATE TABLE IF NOT EXISTS subscribers (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email      VARCHAR(191) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Contact messages
CREATE TABLE IF NOT EXISTS contacts (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name       VARCHAR(120) NOT NULL,
  email      VARCHAR(191) NOT NULL,
  subject    VARCHAR(200) NOT NULL,
  message    TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Orders
CREATE TABLE IF NOT EXISTS orders (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id        INT UNSIGNED NULL,
  guest_email    VARCHAR(191) NULL,
  subtotal       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status         ENUM('pending','paid','shipped','cancelled') DEFAULT 'pending',
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Order items
CREATE TABLE IF NOT EXISTS order_items (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id   INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  name       VARCHAR(200) NOT NULL,
  price      DECIMAL(10,2) NOT NULL,
  qty        INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_oi_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  CONSTRAINT fk_oi_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Seed categories
INSERT IGNORE INTO categories (name, slug) VALUES
('Kids', 'kids'), ('Men', 'male'), ('Women', 'female');

-- Seed products (sample)
INSERT IGNORE INTO products (name, slug, description, price, image_url, stock, is_active) VALUES
('Classic Tee (Men)', 'classic-tee-men', 'Soft cotton tee for everyday use.', 199.99, 'assets/img/products/tee-men.jpg', 50, 1),
('Summer Dress (Women)', 'summer-dress-women', 'Lightweight summer dress.', 349.00, 'assets/img/products/dress-women.jpg', 30, 1),
('Hoodie (Kids)', 'hoodie-kids', 'Comfy hoodie for kids.', 279.50, 'assets/img/products/hoodie-kids.jpg', 40, 1);

-- Map seed products
INSERT IGNORE INTO product_categories (product_id, category_id)
SELECT p.id, c.id FROM products p JOIN categories c
  ON ( (p.slug='classic-tee-men' AND c.slug='male')
    OR (p.slug='summer-dress-women' AND c.slug='female')
    OR (p.slug='hoodie-kids' AND c.slug='kids') );
