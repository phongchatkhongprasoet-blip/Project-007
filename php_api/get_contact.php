<?php
include 'condb.php';

// รับรหัสข้อมูลติดต่อจาก URL เช่น get_contact.php?id=1
if (!isset($_GET['id'])) {
    echo json_encode(["error" => "ไม่พบรหัสข้อมูลติดต่อ"]);
    exit;
}

try {
    $stmt = $conn->prepare("SELECT * FROM contact WHERE contact_id = :id");
    $stmt->execute([':id' => $_GET['id']]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($data ? $data : ["error" => "ไม่พบข้อมูล"]);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
