-- إنشاء قاعدة البيانات
CREATE DATABASE IF NOT EXISTS store;
USE store;

-- إنشاء جدول المستخدمين
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    remember_token VARCHAR(64),
    token_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- إنشاء جدول المنتجات مع كل الأعمدة المتعلقة بالمنتج
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    isDeleted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- إنشاء جدول تتبع تاريخ المنتجات
CREATE TABLE IF NOT EXISTS product_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    action VARCHAR(10) NOT NULL,
    old_name VARCHAR(100),
    old_price DECIMAL(10,2),
    old_description TEXT,
    new_name VARCHAR(100),
    new_price DECIMAL(10,2),
    new_description TEXT,
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- إنشاء جدول الطلبات
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- إنشاء جدول عناصر الطلبات
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
);

-- إضافة الفهارس لتحسين الأداء
ALTER TABLE orders ADD INDEX idx_user_id (user_id);
ALTER TABLE orders ADD INDEX idx_status (status);
ALTER TABLE order_items ADD INDEX idx_order_id (order_id);
ALTER TABLE order_items ADD INDEX idx_product_id (product_id);
ALTER TABLE users ADD INDEX idx_email (email);
ALTER TABLE products ADD INDEX idx_product_name (product_name);

-- إعادة تنظيم Triggers للمنتجات
DELIMITER //

-- Trigger للإضافة
CREATE TRIGGER after_product_insert 
AFTER INSERT ON products
FOR EACH ROW
BEGIN
    INSERT INTO product_history (
        product_id, 
        action, 
        new_name, 
        new_price, 
        new_description
    )
    VALUES (
        NEW.id, 
        'INSERT', 
        NEW.product_name, 
        NEW.price, 
        NEW.description
    );
END//

-- Trigger للتحديث
CREATE TRIGGER after_product_update 
AFTER UPDATE ON products
FOR EACH ROW
BEGIN
    IF OLD.isDeleted != NEW.isDeleted THEN
        -- تسجيل عملية الحذف المنطقي
        INSERT INTO product_history (
            product_id, 
            action,
            old_name, 
            old_price, 
            old_description
        )
        VALUES (
            OLD.id, 
            'DELETE',
            OLD.product_name, 
            OLD.price, 
            OLD.description
        );
    ELSE
        -- تسجيل التحديثات العادية
        INSERT INTO product_history (
            product_id, 
            action,
            old_name, 
            old_price, 
            old_description,
            new_name, 
            new_price, 
            new_description
        )
        VALUES (
            OLD.id, 
            'UPDATE',
            OLD.product_name, 
            OLD.price, 
            OLD.description,
            NEW.product_name, 
            NEW.price, 
            NEW.description
        );
    END IF;
END//

-- Trigger لتحويل عملية الحذف إلى تحديث
CREATE TRIGGER before_product_delete 
BEFORE DELETE ON products
FOR EACH ROW
BEGIN  
    -- منع عملية الحذف الفعلي
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'لا يمكن حذف المنتجات. يتم تحديث حالتها فقط.';
END//

DELIMITER ;

-- إدراج بيانات المستخدمين كلمة المرور password
INSERT INTO users (username, email, password, role, created_at) VALUES
('admin', 'admin@store.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', CURRENT_TIMESTAMP),
('user1', 'user1@store.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', CURRENT_TIMESTAMP),
('user2', 'user2@store.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', CURRENT_TIMESTAMP);

-- إدراج بيانات المنتجات
INSERT INTO products (product_name, price, description, image) VALUES
('iPhone 14 Pro', 999.99, 'Latest Apple iPhone with advanced camera system and A16 Bionic chip', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-14-pro-finish-select-202209-6-7inch-deeppurple?wid=800'),
('Samsung 4K TV', 799.99, '55-inch Smart TV with Crystal Display and built-in streaming apps', 'https://image-us.samsung.com/SamsungUS/home/television-home-theater/tvs/04032023/TU8000.jpg?$product-details-jpg$'),
('Nike Air Max', 129.99, 'Comfortable running shoes with Air cushioning technology', 'https://static.nike.com/a/images/t_PDP_864_v1/f_auto,b_rgb:f5f5f5/17c8856f-6c3a-4232-a604-424a8145b7ea/air-max-270-shoes-V4DfZQ.png'),
('Sony PlayStation 5', 499.99, 'Next-gen gaming console with immersive graphics', 'https://gamingcenter.ly/wp-content/uploads/2023/12/Sony-PlayStation-5-Console-with-Wireless-Controller-Asian-Edition-White-and-Black-slim-Europe-4-jpg.webp'),
('Dell XPS 13', 999.99, 'Compact and powerful laptop with InfinityEdge display', 'https://www.notebookcheck.net/fileadmin/Notebooks/News/_nc4/Dell-XPS-13-9340-laptop.JPG'),
('Bose QuietComfort 35', 299.99, 'Noise-cancelling headphones with superior sound quality', 'https://m.media-amazon.com/images/I/612u463P8LL.jpg'),
('Apple Watch Series 7', 399.99, 'Smartwatch with health tracking features', 'https://www.apple.com/newsroom/images/product/watch/standard/Apple_watch-series7_hero_09142021_big.jpg.large.jpg'),
('GoPro HERO9', 399.99, 'Action camera with 5K video recording', 'https://m.media-amazon.com/images/I/81Szbr4wo5L.jpg'),
('Amazon Echo Dot', 49.99, 'Smart speaker with Alexa voice control', 'https://i.ebayimg.com/images/g/0DMAAOSwK05f-Nes/s-l1200.jpg'),
('Kindle Paperwhite', 129.99, 'E-reader with high-resolution display', 'https://m.media-amazon.com/images/I/61MdbBO+SEL._AC_UF1000,1000_QL80_.jpg');