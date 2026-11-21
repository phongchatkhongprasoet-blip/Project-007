<?php
include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

// สถานะที่อนุญาต
$allow = ['อนุมัติ', 'ไม่อนุมัติ'];

if (empty($data['leave_id']) || empty($data['status']) || !in_array($data['status'], $allow)) {
    echo json_encode([
        "success" => false,
        "message" => "ข้อมูลไม่ถูกต้อง"
    ]);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE leaves SET status = :status WHERE leave_id = :leave_id");
    $stmt->execute([
        ':status'   => $data['status'],
        ':leave_id' => $data['leave_id']
    ]);

    echo json_encode([
        "success" => true,
        "message" => "บันทึกผลการพิจารณา: " . $data['status']
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>
