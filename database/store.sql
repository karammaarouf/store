-- Create the database
CREATE DATABASE IF NOT EXISTS store;
USE store;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create order_items table for individual products in each order
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- Add indexes for better performance
ALTER TABLE orders ADD INDEX idx_user_id (user_id);
ALTER TABLE orders ADD INDEX idx_status (status);
ALTER TABLE order_items ADD INDEX idx_order_id (order_id);
ALTER TABLE order_items ADD INDEX idx_product_id (product_id);
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE products ADD INDEX idx_product_name (product_name);
ALTER TABLE users ADD COLUMN remember_token VARCHAR(64), ADD COLUMN token_expiry DATETIME;

-- Add role to existing users if table already exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS role ENUM('user', 'admin') DEFAULT 'user';

-- Insert sample products
INSERT INTO products (product_name, price, description, image) VALUES
('iPhone 14 Pro', 999.99, 'Latest Apple iPhone with advanced camera system and A16 Bionic chip', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-finish-select-202209-6-7inch-deeppurple?wid=800'),
('Samsung 4K TV', 799.99, '55-inch Smart TV with Crystal Display and built-in streaming apps', 'https://images.samsung.com/is/image/samsung/p6pim/uk/ue55bu8000kxxu/gallery/uk-crystal-uhd-bu8000-ue55bu8000kxxu-531504476?$800_'),
('Nike Air Max', 129.99, 'Comfortable running shoes with Air cushioning technology', 'https://static.nike.com/a/images/t_PDP_864_v1/f_auto,b_rgb:f5f5f5/17c8856f-6c3a-4232-a604-424a8145b7ea/air-max-270-shoes-V4DfZQ.png'),
('PlayStation 5', 499.99, 'Next-gen gaming console with ultra-high-speed SSD', 'https://gmedia.playstation.com/is/image/SIEPDC/ps5-product-thumbnail-01-en-14sep21?$800px$'),
('MacBook Air M2', 1299.99, 'Thin and light laptop with Apple M2 chip and stunning Retina display', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/macbook-air-midnight-select-20220606?wid=800'),
('Canon EOS R6', 2499.99, 'Full-frame mirrorless camera for professional photography', 'https://www.canon.ie/media/eos_r6_mkii_front_rf24-105mm_f4l_is_usm_800x800_tcm24-2526802.jpg'),
('Apple Watch Series 8', 399.99, 'Advanced health monitoring and fitness tracking smartwatch', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/MKUQ3_VW_34FR+watch-45-alum-midnight-nc-8s_VW_34FR_WF_CO?wid=800'),
('Dyson V15', 699.99, 'Powerful cordless vacuum with laser dust detection', 'https://dyson-h.assetsadobe2.com/is/image/content/dam/dyson/images/products/primary/368587-01.png?$800$'),
('iPad Pro 12.9', 1099.99, 'Large display tablet with M2 chip for professional work', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/ipad-pro-12-11-select-202210?wid=800'),
('Sony WH-1000XM4', 349.99, 'Premium noise-cancelling wireless headphones', 'https://electronics.sony.com/image/5d02da5df552836db894cead8a68c13f/1686x950/');