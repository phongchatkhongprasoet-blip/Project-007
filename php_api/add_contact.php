<?php

include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['fullName']) ||
    !isset($data['email']) ||
    !isset($data['phone']) ||
    !isset($data['subject']) ||
    !isset($data['message'])
) {
    echo json_encode([
        "success" => false,
        "message" => "ข้อมูลไม่ครบ"
    ]);
    exit;
}

try {
    $sql = "INSERT INTO contact
            (fullName, email, phone, subject, message)
            VALUES
            (:fullName, :email, :phone, :subject, :message)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':fullName' => $data['fullName'],
        ':email'    => $data['email'],
        ':phone'    => $data['phone'],
        ':subject'  => $data['subject'],
        ':message'  => $data['message']
    ]);

    echo json_encode([
        "success" => true,
        "message" => "เพิ่มข้อมูลเรียบร้อย"
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
