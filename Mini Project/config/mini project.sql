CREATE DATABASE STC_PH;
USE STC_PH;

CREATE TABLE supplier (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE storage (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    supplier_id INT,
    FOREIGN KEY (supplier_id) REFERENCES supplier(id)
);

CREATE TABLE users (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'user'
);

CREATE TABLE categories (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE
);


CREATE TABLE products (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    selling_price DECIMAL(10,2),
    category_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 0,
    storage_id INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (storage_id) REFERENCES storage(id)
);

CREATE TABLE supplier_products (
    supplier_id INT NOT NULL,
    product_id INT NOT NULL,
    cost DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (supplier_id, product_id),
    FOREIGN KEY (supplier_id) REFERENCES supplier(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);


INSERT INTO supplier (name) VALUES 
('Youguan Hardware Sdn. Bhd.'),
('Taibeng Hardware & Machines Sdn. Bhd.'),
('Chingling Hardware Sdn. Bhd.'),
('Unicot Enterprise'),
('Cizen Sdn. Bhd.');

INSERT INTO storage (name, supplier_id) VALUES
('A1', 1),
('A2', 2),
('A3', 3);


INSERT INTO categories (name) VALUES
('Tools'),
('Household Accessories'),
('Construction Tools'),
('Painting Supplies');


INSERT INTO products (name, selling_price, category_id, quantity, storage_id) VALUES 
('Screwdriver', 8.00, 1, 50, 1),
('Wheelbarrow', 100.00, 3, 4, 1),
('Brush', 5.00, 4, 100, 2),
('Paint Tray', 2.50, 4, 75, 2),
('Common Ring Spanner', 5.00, 1, 50, 3),
('Cylinder Lock', 13.00, 2, 55, 3),
('Shower Hose', 15.00, 2, 16, 1),
('Bidet Spray Set', 25.00, 2, 19,1),
('Broom', 7.50, 2, 20, 3),
('Mop', 10.00, 2, 45, 3);


INSERT INTO supplier_products (supplier_id, product_id, cost) VALUES
(1, 1, 5.00),
(1,2, 80.00),
(2,3, 3.00),
(2,4, 1.75),
(3,5, 4.00),
(3,6, 7.80),
(4,7, 10.50),
(4,8, 20.00),
(5,9, 4.50),
(5,10, 7.00); 

ALTER TABLE users ADD CONSTRAINT chk_role CHECK (role IN ('user', 'editor', 'admin'));


INSERT INTO users (name, email, password, role) VALUES
('User','intern@example.com',   '$2y$12$xcbZ1QqEBRI2WgswqUPCfuczXzlotHwa662cwxgKmVP3n5OyiFg6W', 'user'),
('Editor', 'staff@example.com', '$2y$12$xcbZ1QqEBRI2WgswqUPCfuczXzlotHwa662cwxgKmVP3n5OyiFg6W', 'editor'),
('Admin','manager@example.com',  '$2y$12$xcbZ1QqEBRI2WgswqUPCfuczXzlotHwa662cwxgKmVP3n5OyiFg6W', 'admin');
