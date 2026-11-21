-- ตารางข้อมูลติดต่อเรา (Contact) สำหรับฐานข้อมูล db_shop
CREATE TABLE IF NOT EXISTS `contact` (
  `contact_id` int(6) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT COMMENT 'รหัสข้อมูลติดต่อ',
  `fullName` varchar(100) NOT NULL COMMENT 'ชื่อ-นามสกุล',
  `email` varchar(100) NOT NULL COMMENT 'อีเมล',
  `phone` varchar(15) NOT NULL COMMENT 'เบอร์โทรศัพท์',
  `subject` varchar(150) NOT NULL COMMENT 'หัวข้อ',
  `message` text NOT NULL COMMENT 'ข้อความ',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'วันที่ส่ง',
  PRIMARY KEY (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `contact` (`fullName`, `email`, `phone`, `subject`, `message`) VALUES
('สมชาย ใจดี', 'somchai@gmail.com', '0812345678', 'สอบถามสินค้า', 'เสื้อยืดคอกลมมีไซซ์ XL ไหมครับ'),
('มานี มีนา', 'manee@gmail.com', '0898765432', 'แจ้งปัญหาการสั่งซื้อ', 'สั่งสินค้าไปแล้วยังไม่ได้รับของค่ะ');
