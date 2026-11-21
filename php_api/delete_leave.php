<?php
include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['leave_id'])) {
    echo json_encode([
        "success" => false,
        "message" => "ไม่พบรหัสใบลา"
    ]);
    exit;
}

try {
    // ยกเลิกได้เฉพาะใบลาที่ยังรออนุมัติ
    $stmt = $conn->prepare("DELETE FROM leaves WHERE leave_id = :leave_id AND status = 'รออนุมัติ'");
    $stmt->execute([':leave_id' => $data['leave_id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode([
            "success" => true,
            "message" => "ยกเลิกใบลาเรียบร้อย"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "ยกเลิกได้เฉพาะใบลาที่ยังรออนุมัติเท่านั้น"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>
