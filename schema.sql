CREATE DATABASE IF NOT EXISTS ccalm
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE ccalm;

CREATE TABLE IF NOT EXISTS menu_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category VARCHAR(100) NOT NULL,
    name VARCHAR(180) NOT NULL,
    description TEXT NULL,
    price DECIMAL(10,2) NOT NULL,
    available TINYINT(1) NOT NULL DEFAULT 1,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_menu_category (category),
    INDEX idx_menu_available (available)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reservations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(150) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    email VARCHAR(190) NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests TINYINT UNSIGNED NOT NULL,
    notes TEXT NULL,
    status ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_reservation_date_time (reservation_date, reservation_time),
    INDEX idx_reservation_status (status)
) ENGINE=InnoDB;

INSERT INTO menu_items (category, name, description, price, available, sort_order)
SELECT 'Entrées', 'Terrine maison', 'Maison, pain frais & cornichons', 9.00, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Terrine maison');

INSERT INTO menu_items (category, name, description, price, available, sort_order)
SELECT 'Plats', 'Bœuf bourguignon', 'Mijoté maison, recette familiale', 18.00, 1, 2
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Bœuf bourguignon');

INSERT INTO menu_items (category, name, description, price, available, sort_order)
SELECT 'Plats', 'Plat du jour', 'Voir les offres du moment', 17.00, 1, 3
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Plat du jour');

INSERT INTO menu_items (category, name, description, price, available, sort_order)
SELECT 'Desserts', 'Crème brûlée', 'Classique maison', 8.00, 1, 4
WHERE NOT EXISTS (SELECT 1 FROM menu_items WHERE name = 'Crème brûlée');

-- Изчистваме старото примерно меню
use ccalm;
DELETE FROM menu_items;

-- Вкарваме реалните позиции от менюто на CCALM
INSERT INTO menu_items (name, description, price, category, available) VALUES 
('Harengs Marinés', 'Harengs marinés servis avec crème, pomme de terre et oignon', 7.00, 'Entrée', 1),
('Bœuf Bourguignon', 'Plat traditionnel du lundi - Boeuf mijoté façon grand-mère', 15.00, 'Plat', 1),
('Rougail Saucisses', 'Spécialité de La Réunion - Plat de la semaine', 15.00, 'Plat', 1),
('Crème Brûlée', 'Crème brûlée maison traditionnelle', 7.00, 'Dessert', 1);