<?php
include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data['emp_id']) ||
    empty($data['leave_type']) ||
    empty($data['start_date']) ||
    empty($data['end_date']) ||
    empty($data['reason'])
) {
    echo json_encode([
        "success" => false,
        "message" => "ข้อมูลไม่ครบ"
    ]);
    exit;
}

// วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มลา
if ($data['end_date'] < $data['start_date']) {
    echo json_encode([
        "success" => false,
        "message" => "วันที่สิ้นสุดต้องไม่น้อยกว่าวันที่เริ่มลา"
    ]);
    exit;
}

// คำนวณจำนวนวันลา (นับรวมวันแรกและวันสุดท้าย)
$start = new DateTime($data['start_date']);
$end   = new DateTime($data['end_date']);
$days  = $start->diff($end)->days + 1;

try {
    // ตรวจสอบว่าลาซ้อนกับใบลาเดิมที่ยังไม่ถูกปฏิเสธหรือไม่
    $check = $conn->prepare("SELECT COUNT(*) FROM leaves
                             WHERE emp_id = :emp_id AND status <> 'ไม่อนุมัติ'
                             AND start_date <= :end_date AND end_date >= :start_date");
    $check->execute([
        ':emp_id'     => $data['emp_id'],
        ':start_date' => $data['start_date'],
        ':end_date'   => $data['end_date']
    ]);
    if ($check->fetchColumn() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "มีใบลาในช่วงวันที่นี้อยู่แล้ว"
        ]);
        exit;
    }

    $sql = "INSERT INTO leaves (emp_id, leave_type, start_date, end_date, days, reason, status)
            VALUES (:emp_id, :leave_type, :start_date, :end_date, :days, :reason, 'รออนุมัติ')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':emp_id'     => $data['emp_id'],
        ':leave_type' => $data['leave_type'],
        ':start_date' => $data['start_date'],
        ':end_date'   => $data['end_date'],
        ':days'       => $days,
        ':reason'     => $data['reason']
    ]);

    echo json_encode([
        "success" => true,
        "message" => "ยื่นใบลาเรียบร้อย (" . $days . " วัน) รอการอนุมัติ"
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>
