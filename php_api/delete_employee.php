<?php

include 'condb.php';

// รับข้อมูล JSON ที่ส่งมาจาก Vue
$data = json_decode(file_get_contents("php://input"), true);

// ตรวจสอบว่ามีรหัสพนักงานส่งมาหรือไม่
if (!isset($data['emp_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "ไม่พบรหัสพนักงาน"
    ]);
    exit;
}

try {
    $sql = "DELETE FROM employee WHERE emp_id = :emp_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':emp_id' => $data['emp_id']
    ]);

    // ตรวจสอบว่ามีข้อมูลถูกลบจริงหรือไม่
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => true,
            "message" => "ลบข้อมูลเรียบร้อย"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "ไม่พบข้อมูลที่ต้องการลบ"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
