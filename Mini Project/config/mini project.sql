CREATE DATABASE STC_PH;
USE STC_PH;

CREATE TABLE supplier (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    cost DECIMAL(10,2),
    storage_location VARCHAR(255)
);

CREATE TABLE storage (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    space INT,
    supplier_id INT,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id)
);

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50)
);

CREATE TABLE products (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    selling_price DECIMAL(10,2),
    category VARCHAR(255) NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    storage_id INT NOT NULL,
    supplier_id INT NOT NULL,
    FOREIGN KEY (storage_id) REFERENCES storage(id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id)
);

CREATE TABLE aging (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    on_hand INT,
    receive_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sell_time TIMESTAMP NULL,
    aging_cost DECIMAL(10,2),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
INSERT INTO supplier (name, cost, storage_location) VALUES 
('Phillips', 2.00, 'A1'),
('Credit Suisse', 1.50, 'A2'),
('Brembo', 9.00, 'A3');

INSERT INTO storage (name, space, supplier_id) VALUES
('A1', 250, 1),
('A2', 100, 2),
('A3', 75, 3);

INSERT INTO products (name, selling_price, category, storage_id, supplier_id) VALUES 
('Screwdriver', 400.00, 'Tools', 1, 1),
('Toilet', 5.00, 'Household Accessories', 2, 2),
('Wheelbarrows', 75.00, 'Construction Tools', 3, 3);
