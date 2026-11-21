<?php

include 'condb.php';

// รับข้อมูล JSON ที่ส่งมาจาก Vue
$data = json_decode(file_get_contents("php://input"), true);

// ตรวจสอบว่ามีรหัสลูกค้าส่งมาหรือไม่
if (!isset($data['customer_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "ไม่พบรหัสลูกค้า"
    ]);
    exit;
}

try {
    $sql = "DELETE FROM customers WHERE customer_id = :customer_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':customer_id' => $data['customer_id']
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
