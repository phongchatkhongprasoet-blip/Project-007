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

try {
    // ค้นหาลูกค้าจากชื่อผู้ใช้
    $stmt = $conn->prepare("SELECT * FROM customers WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $data['username']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบรหัสผ่าน (รองรับทั้งรหัสที่เข้ารหัสด้วย password_hash และข้อมูลเดิมที่ยังไม่เข้ารหัส)
    $ok = false;
    if ($user) {
        if (password_verify($data['password'], $user['password'])) {
            $ok = true;
        } else if ($user['password'] === $data['password']) {
            $ok = true;
        }
    }

    if ($ok) {
        echo json_encode([
            "success" => true,
            "message" => "เข้าสู่ระบบสำเร็จ",
            "user" => [
                "customer_id" => $user['customer_id'],
                "firstName"   => $user['firstName'],
                "lastName"    => $user['lastName'],
                "username"    => $user['username']
            ]
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง"
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
?>
