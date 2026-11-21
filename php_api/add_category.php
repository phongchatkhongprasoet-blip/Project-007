<?php

include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty(trim($data['category_name'] ?? ''))) {
    echo json_encode([
        "success" => false,
        "message" => "กรุณากรอกชื่อประเภท"
    ]);
    exit;
}

try {
    // ตรวจสอบชื่อประเภทซ้ำ
    $check = $conn->prepare("SELECT COUNT(*) FROM categories WHERE category_name = :name");
    $check->execute([':name' => trim($data['category_name'])]);
    if ($check->fetchColumn() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "มีชื่อประเภทนี้อยู่แล้ว"
        ]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (:category_name)");
    $stmt->execute([':category_name' => trim($data['category_name'])]);

    echo json_encode([
        "success" => true,
        "message" => "เพิ่มประเภทสินค้าเรียบร้อย"
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
