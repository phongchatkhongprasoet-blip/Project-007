<?php

include 'condb.php';
include 'upload_image.php';

// รับข้อมูลแบบ FormData (เพราะมีไฟล์รูปภาพ)
if (
    empty($_POST['asset_code']) ||
    empty($_POST['asset_name'])
) {
    echo json_encode([
        "success" => false,
        "message" => "กรุณากรอกรหัสและชื่อครุภัณฑ์"
    ]);
    exit;
}

try {
    // อัปโหลดรูป (ถ้ามี)
    $image = uploadImage('image');

    $sql = "INSERT INTO assets
            (asset_code, asset_name, purchase_date, price, image)
            VALUES
            (:asset_code, :asset_name, :purchase_date, :price, :image)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':asset_code'    => $_POST['asset_code'],
        ':asset_name'    => $_POST['asset_name'],
        ':purchase_date' => !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : null,
        ':price'         => $_POST['price'] !== '' ? $_POST['price'] : null,
        ':image'         => $image
    ]);

    echo json_encode([
        "success" => true,
        "message" => "เพิ่มข้อมูลเรียบร้อย"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
