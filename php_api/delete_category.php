<?php

include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['category_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "ไม่พบรหัสประเภท"
    ]);
    exit;
}

try {
    // ห้ามลบประเภทที่ยังมีสินค้าใช้อยู่
    $check = $conn->prepare("SELECT COUNT(*) FROM products WHERE category_id = :id");
    $check->execute([':id' => $data['category_id']]);
    $count = $check->fetchColumn();
    if ($count > 0) {
        echo json_encode([
            "success" => false,
            "message" => "ลบไม่ได้ เพราะยังมีสินค้าในประเภทนี้ $count รายการ"
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM categories WHERE category_id = :category_id");
    $stmt->execute([':category_id' => $data['category_id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => true,
            "message" => "ลบประเภทสินค้าเรียบร้อย"
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
