-- ตารางข้อมูลการลางาน
CREATE TABLE IF NOT EXISTS `leaves` (
  `leave_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสใบลา',
  `emp_id` int(6) UNSIGNED ZEROFILL NOT NULL COMMENT 'รหัสพนักงาน',
  `leave_type` varchar(50) NOT NULL COMMENT 'ประเภทการลา',
  `start_date` date NOT NULL COMMENT 'วันที่เริ่มลา',
  `end_date` date NOT NULL COMMENT 'วันที่สิ้นสุด',
  `days` int(11) NOT NULL COMMENT 'จำนวนวันลา',
  `reason` text NOT NULL COMMENT 'เหตุผลการลา',
  `status` varchar(20) NOT NULL DEFAULT 'รออนุมัติ' COMMENT 'สถานะ',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'วันที่ยื่นใบลา',
  PRIMARY KEY (`leave_id`),
  KEY `idx_emp` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `leaves` (`emp_id`, `leave_type`, `start_date`, `end_date`, `days`, `reason`, `status`, `created_at`) VALUES
(000001, 'ลาป่วย', '2026-11-03', '2026-11-04', 2, 'เป็นไข้หวัด', 'อนุมัติ', '2026-11-03 08:10:00'),
(000002, 'ลากิจ', '2026-11-12', '2026-11-12', 1, 'ไปทำธุระที่ธนาคาร', 'อนุมัติ', '2026-11-10 09:30:00'),
(000002, 'ลาพักร้อน', '2026-12-24', '2026-12-26', 3, 'พักผ่อนกับครอบครัว', 'รออนุมัติ', '2026-11-18 13:45:00');
