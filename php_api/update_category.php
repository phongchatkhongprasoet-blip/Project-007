<?php

include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['category_id']) || empty(trim($data['category_name'] ?? ''))) {
    echo json_encode([
        "success" => false,
        "message" => "ข้อมูลไม่ครบ"
    ]);
    exit;
}

try {
    // ตรวจสอบชื่อประเภทซ้ำกับรายการอื่น
    $check = $conn->prepare("SELECT COUNT(*) FROM categories WHERE category_name = :name AND category_id <> :id");
    $check->execute([':name' => trim($data['category_name']), ':id' => $data['category_id']]);
    if ($check->fetchColumn() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "มีชื่อประเภทนี้อยู่แล้ว"
        ]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE categories SET category_name = :category_name WHERE category_id = :category_id");
    $stmt->execute([
        ':category_name' => trim($data['category_name']),
        ':category_id'   => $data['category_id']
    ]);

    echo json_encode([
        "success" => true,
        "message" => "แก้ไขประเภทสินค้าเรียบร้อย"
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
