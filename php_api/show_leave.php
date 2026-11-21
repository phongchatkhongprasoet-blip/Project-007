<?php
include 'condb.php';

try {
    // ดึงข้อมูลใบลาพร้อมชื่อพนักงาน (ถ้าส่ง emp_id มาจะแสดงเฉพาะของพนักงานคนนั้น)
    $sql = "SELECT l.*, e.firstName, e.lastName
            FROM leaves l
            LEFT JOIN employee e ON l.emp_id = e.emp_id";

    if (!empty($_GET['emp_id'])) {
        $sql .= " WHERE l.emp_id = :emp_id ORDER BY l.leave_id DESC";
        $stmt = $conn->prepare($sql);
        $stmt->execute([':emp_id' => $_GET['emp_id']]);
    } else {
        $sql .= " ORDER BY l.leave_id DESC";
        $stmt = $conn->query($sql);
    }

    $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($datas);

} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
