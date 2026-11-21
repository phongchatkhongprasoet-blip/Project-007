<?php

include 'condb.php';
include 'upload_image.php';

// รับข้อมูลแบบ FormData (เพราะอาจมีไฟล์รูปภาพใหม่)
if (
    empty($_POST['asset_id']) ||
    empty($_POST['asset_code']) ||
    empty($_POST['asset_name'])
) {
    echo json_encode([
        "success" => false,
        "message" => "ข้อมูลไม่ครบ"
    ]);
    exit;
}

try {
    // ถ้าเลือกรูปใหม่ ให้อัปโหลดและลบรูปเก่า ถ้าไม่เลือกใช้รูปเดิม
    $image = uploadImage('image');

    if ($image !== null) {
        $old = $conn->prepare("SELECT image FROM assets WHERE asset_id = :id");
        $old->execute([':id' => $_POST['asset_id']]);
        $oldImage = $old->fetchColumn();
        if ($oldImage && file_exists(__DIR__ . '/images/' . $oldImage)) {
            unlink(__DIR__ . '/images/' . $oldImage);
        }
    }

    $sql = "UPDATE assets SET
                asset_code    = :asset_code,
                asset_name    = :asset_name,
                purchase_date = :purchase_date,
                price         = :price"
         . ($image !== null ? ", image = :image" : "") .
           " WHERE asset_id = :asset_id";

    $params = [
        ':asset_code'    => $_POST['asset_code'],
        ':asset_name'    => $_POST['asset_name'],
        ':purchase_date' => !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : null,
        ':price'         => $_POST['price'] !== '' ? $_POST['price'] : null,
        ':asset_id'      => $_POST['asset_id']
    ];
    if ($image !== null) {
        $params[':image'] = $image;
    }

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);

    echo json_encode([
        "success" => true,
        "message" => "แก้ไขข้อมูลเรียบร้อย"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
