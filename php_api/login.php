<?php
include 'condb.php';

// รับข้อมูล username / password ที่ส่งมาแบบ JSON
$data = json_decode(file_get_contents("php://input"), true);

if (empty($data['username']) || empty($data['password'])) {
    echo json_encode([
        "success" => false,
        "message" => "กรุณากรอกชื่อผู้ใช้และรหัสผ่าน"
    ]);
    exit;
}

// ตรวจสอบรหัสผ่าน (รองรับทั้งรหัสที่เข้ารหัสด้วย password_hash และข้อมูลเดิมที่ยังไม่เข้ารหัส)
function checkPassword($input, $stored) {
    return password_verify($input, $stored) || $input === $stored;
}

try {
    // 1) ค้นหาจากตารางพนักงานก่อน
    $stmt = $conn->prepare("SELECT * FROM employee WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $data['username']]);
    $emp = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($emp && checkPassword($data['password'], $emp['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "เข้าสู่ระบบสำเร็จ",
            "user" => [
                "role"      => "employee",
                "emp_id"    => $emp['emp_id'],
                "firstName" => $emp['firstName'],
                "lastName"  => $emp['lastName'],
                "username"  => $emp['username']
            ]
        ]);
        exit;
    }

    // 2) ถ้าไม่ใช่พนักงาน ค้นหาจากตารางลูกค้า
    $stmt = $conn->prepare("SELECT * FROM customers WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && checkPassword($data['password'], $user['password'])) {
        echo json_encode([
            "success" => true,
            "message" => "เข้าสู่ระบบสำเร็จ",
            "user" => [
                "role"        => "customer",
                "customer_id" => $user['customer_id'],
                "firstName"   => $user['firstName'],
                "lastName"    => $user['lastName'],
                "username"    => $user['username']
            ]
        ]);
        exit;
    }

    echo json_encode([
        "success" => false,
        "message" => "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>
