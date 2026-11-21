<?php
include 'condb.php';

// รับรหัสอุปกรณ์จาก URL เช่น get_asset.php?id=1
if (!isset($_GET['id'])) {
    echo json_encode(["error" => "ไม่พบรหัสอุปกรณ์"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM assets WHERE asset_id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($data ? $data : ["error" => "ไม่พบข้อมูล"]);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
