<?php

include 'condb.php';
include 'upload_image.php';

// รับข้อมูลแบบ FormData (เพราะอาจมีไฟล์รูปภาพใหม่)
if (
    empty($_POST['product_id']) ||
    empty($_POST['product_name']) ||
    empty($_POST['category_id']) ||
    !isset($_POST['price']) || $_POST['price'] === ''
) {
    echo json_encode([
        "success" => false,
        "message" => "ข้อมูลไม่ครบ"
    ]);
    exit;
}

try {
    // ถ้าเลือกรูปใหม่ ให้อัปโหลดและลบรูปเก่า ถ้าไม่เลือกใช้รูปเดิม
    $image = uploadImage('image', 'mk');

    if ($image !== null) {
        $old = $conn->prepare("SELECT image FROM products WHERE product_id = :id");
        $old->execute([':id' => $_POST['product_id']]);
        $oldImage = $old->fetchColumn();
        if ($oldImage && file_exists(__DIR__ . '/images/' . $oldImage)) {
            unlink(__DIR__ . '/images/' . $oldImage);
        }
    }

    $sql = "UPDATE products SET
                product_name = :product_name,
                category_id  = :category_id,
                description  = :description,
                price        = :price,
                stock        = :stock"
         . ($image !== null ? ", image = :image" : "") .
           " WHERE product_id = :product_id";

    $params = [
        ':product_name' => $_POST['product_name'],
        ':category_id'  => $_POST['category_id'],
        ':description'  => $_POST['description'] ?? '',
        ':price'        => $_POST['price'],
        ':stock'        => $_POST['stock'] !== '' ? $_POST['stock'] : 0,
        ':product_id'   => $_POST['product_id']
    ];
    if ($image !== null) {
        $params[':image'] = $image;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        "success" => true,
        "message" => "แก้ไขสินค้าเรียบร้อย"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
