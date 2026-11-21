<?php

include 'condb.php';

// รับข้อมูล JSON ที่ส่งมาจาก Vue
$data = json_decode(file_get_contents("php://input"), true);

// ตรวจสอบว่ามีรหัสข้อมูลติดต่อส่งมาหรือไม่
if (!isset($data['contact_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "ไม่พบรหัสข้อมูลติดต่อ"
    ]);
    exit;
}

try {
    $sql = "DELETE FROM contact WHERE contact_id = :contact_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':contact_id' => $data['contact_id']
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
