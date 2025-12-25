-- База данных для туристической компании "Комфорт-отдых" (MySQL версия)
-- Инструкция: Сначала создайте базу данных в phpMyAdmin, затем выполните этот скрипт

-- Создание базы данных (раскомментируйте, если нужно создать БД)
-- CREATE DATABASE IF NOT EXISTS restapi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE restapi;

-- Таблица стран
CREATE TABLE IF NOT EXISTS countries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    capital VARCHAR(255),
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица клиентов
CREATE TABLE IF NOT EXISTS clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(255) NOT NULL,
    last_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    phone VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Таблица туров
CREATE TABLE IF NOT EXISTS tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    country_id INT NOT NULL,
    client_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    status VARCHAR(50) DEFAULT 'planned',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Вставка тестовых данных
INSERT INTO countries (name, capital, description) VALUES
('Турция', 'Анкара', 'Популярное направление для пляжного отдыха'),
('Египет', 'Каир', 'Курорты Красного моря'),
('Тайланд', 'Бангкок', 'Экзотический отдых в Азии');

INSERT INTO clients (first_name, last_name, email, phone) VALUES
('Иван', 'Петров', 'ivan@example.com', '+7 900 123 4567'),
('Мария', 'Сидорова', 'maria@example.com', '+7 900 765 4321');

INSERT INTO tours (country_id, client_id, start_date, end_date, price, status) VALUES
(1, 1, '2024-07-01', '2024-07-14', 85000.00, 'planned'),
(2, 2, '2024-08-15', '2024-08-22', 65000.00, 'confirmed');

