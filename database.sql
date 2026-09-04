CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(200) NOT NULL,
    `type` ENUM('CLIENT', 'ADMIN') NOT NULL DEFAULT 'CLIENT'
);

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL
);

CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(100)
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price FLOAT(16,2) UNSIGNED NOT NULL,
    description TEXT NOT NULL,
    category_id INT NOT NULL,
    supplier_id INT,
    FOREIGN KEY (category_id) REFERENCES categories(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

CREATE TABLE product_image (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(100) NOT NULL,
    image_order INT NOT NULL DEFAULT 1,
    product_id INT NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city VARCHAR(50) NOT NULL,
    street VARCHAR(100) NOT NULL,
    house_number VARCHAR(20) NOT NULL,
    postal_code VARCHAR(20),
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_status ENUM('IN PROCESS', 'FINISHED') NOT NULL DEFAULT 'IN PROCESS',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    payment_method ENUM('CARD', 'CASH') NOT NULL,
    payment_status ENUM('PENDING', 'PAID') NOT NULL DEFAULT 'PENDING',
    paid_at TIMESTAMP NULL,
    order_id INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id)
);

CREATE TABLE order_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_price FLOAT(16,2) UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT UNSIGNED NOT NULL DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);