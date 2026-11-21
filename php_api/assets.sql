-- ตารางข้อมูลอุปกรณ์ (assets) สำหรับฐานข้อมูล db_shop
CREATE TABLE assets (
    asset_id INT AUTO_INCREMENT PRIMARY KEY,
    asset_code VARCHAR(50) NOT NULL COMMENT 'รหัสครุภัณฑ์',
    asset_name VARCHAR(255) NOT NULL COMMENT 'ชื่อครุภัณฑ์',
    purchase_date DATE COMMENT 'วันที่ซื้อ',
    price DECIMAL(10,2) COMMENT 'ราคา',
    image VARCHAR(100) COMMENT 'ชื่อไฟล์หรือ Path รูปภาพ',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO assets
    (asset_code, asset_name, purchase_date, price, image)
VALUES
    ('IT001', 'คอมพิวเตอร์ตั้งโต๊ะ', '2026-01-15', 25000.00, 'computer.jpg'),
    ('IT002', 'โน้ตบุ๊ก', '2026-02-10', 32000.00, 'notebook.jpg'),
    ('IT003', 'เครื่องพิมพ์', '2026-03-05', 8500.00, 'printer.jpg'),
    ('IT004', 'โปรเจกเตอร์', '2026-04-20', 18000.00, 'projector.jpg'),
    ('IT005', 'เครื่องสำรองไฟ UPS', '2026-05-12', 3500.00, 'ups.jpg');
