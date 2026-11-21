-- =========================================================
-- ระบบสั่งอาหารร้าน MK (แบบฝึกหัดที่ 9-10) ฐานข้อมูล db_shop
-- =========================================================

-- 1) ตารางประเภทสินค้า เก็บ รหัสประเภท และ ชื่อประเภท
CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY COMMENT 'รหัสประเภท',
    category_name VARCHAR(100) NOT NULL COMMENT 'ชื่อประเภท'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO categories (category_id, category_name) VALUES
    (1, 'เนื้อสัตว์'),
    (2, 'ผัก'),
    (3, 'เครื่องดื่ม');

-- 2) เพิ่มคอลัมน์ประเภทสินค้าในตารางสินค้า (products)
ALTER TABLE products
    ADD COLUMN category_id INT NULL COMMENT 'รหัสประเภท' AFTER product_name,
    ADD INDEX idx_category (category_id);

-- 3) ข้อมูลสินค้าร้าน MK (ลบข้อมูลตัวอย่างเดิมออกก่อน)
DELETE FROM products;
ALTER TABLE products AUTO_INCREMENT = 1;

INSERT INTO products (product_name, category_id, description, price, image, stock) VALUES
    ('หมูสามชั้นสไลซ์', 1, 'หมูสามชั้นหั่นบาง', 129.00, 'mk_pork.jpg', 30),
    ('เนื้อวัวสไลซ์', 1, 'เนื้อวัวหั่นบาง', 159.00, 'mk_beef.jpg', 25),
    ('ลูกชิ้นกุ้ง', 1, 'ลูกชิ้นกุ้งเด้ง', 89.00, 'mk_shrimpball.jpg', 40),
    ('เป็ดย่าง', 1, 'เป็ดย่างสูตร MK', 245.00, 'mk_duck.jpg', 20),
    ('ผักรวมจานใหญ่', 2, 'ผักรวมสำหรับสุกี้', 99.00, 'mk_vegset.jpg', 30),
    ('ผักกาดขาว', 2, 'ผักกาดขาวสด', 39.00, 'mk_cabbage.jpg', 50),
    ('เห็ดเข็มทอง', 2, 'เห็ดเข็มทองสด', 49.00, 'mk_mushroom.jpg', 50),
    ('ข้าวโพดอ่อน', 2, 'ข้าวโพดอ่อนสด', 39.00, 'mk_corn.jpg', 40),
    ('แตงโมปั่น', 3, 'แตงโมปั่นสด', 65.00, 'mk_watermelon.jpg', 24),
    ('ชาไทย', 3, 'ชาไทยเย็น', 45.00, 'mk_thaitea.jpg', 24),
    ('ชาเขียว', 3, 'ชาเขียวเย็น', 50.00, 'mk_greentea.jpg', 24),
    ('น้ำเปล่า', 3, 'น้ำดื่ม', 15.00, 'mk_water.jpg', 50);
