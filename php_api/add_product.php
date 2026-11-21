<?php

include 'condb.php';
include 'upload_image.php';

// รับข้อมูลแบบ FormData (เพราะมีไฟล์รูปภาพ)
if (
    empty($_POST['product_name']) ||
    empty($_POST['category_id']) ||
    !isset($_POST['price']) || $_POST['price'] === ''
) {
    echo json_encode([
        "success" => false,
        "message" => "กรุณากรอกชื่อสินค้า ประเภท และราคา"
    ]);
    exit;
}

try {
    // อัปโหลดรูป (ถ้ามี)
    $image = uploadImage('image', 'mk');

    $sql = "INSERT INTO products
            (product_name, category_id, description, price, image, stock)
            VALUES
            (:product_name, :category_id, :description, :price, :image, :stock)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':product_name' => $_POST['product_name'],
        ':category_id'  => $_POST['category_id'],
        ':description'  => $_POST['description'] ?? '',
        ':price'        => $_POST['price'],
        ':image'        => $image,
        ':stock'        => $_POST['stock'] !== '' ? $_POST['stock'] : 0
    ]);

    echo json_encode([
        "success" => true,
        "message" => "เพิ่มสินค้าเรียบร้อย"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
