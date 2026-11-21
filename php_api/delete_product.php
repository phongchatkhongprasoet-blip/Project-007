<?php

include 'condb.php';

// รับข้อมูล JSON ที่ส่งมาจาก Vue
$data = json_decode(file_get_contents("php://input"), true);

// ตรวจสอบว่ามีรหัสสินค้าส่งมาหรือไม่
if (!isset($data['product_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "ไม่พบรหัสสินค้า"
    ]);
    exit;
}

try {
    // ดึงชื่อรูปเพื่อลบไฟล์รูปด้วย
    $old = $conn->prepare("SELECT image FROM products WHERE product_id = :product_id");
    $old->execute([':product_id' => $data['product_id']]);
    $oldImage = $old->fetchColumn();

    $sql = "DELETE FROM products WHERE product_id = :product_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':product_id' => $data['product_id']
    ]);

    // ตรวจสอบว่ามีข้อมูลถูกลบจริงหรือไม่
    if ($stmt->rowCount() > 0) {
        if ($oldImage && file_exists(__DIR__ . '/images/' . $oldImage)) {
            unlink(__DIR__ . '/images/' . $oldImage);
        }
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
