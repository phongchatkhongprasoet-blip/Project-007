<?php

include 'condb.php';

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['contact_id']) ||
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
    $sql = "UPDATE contact SET
                fullName = :fullName,
                email    = :email,
                phone    = :phone,
                subject  = :subject,
                message  = :message
            WHERE contact_id = :contact_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':fullName'   => $data['fullName'],
        ':email'      => $data['email'],
        ':phone'      => $data['phone'],
        ':subject'    => $data['subject'],
        ':message'    => $data['message'],
        ':contact_id' => $data['contact_id']
    ]);

    echo json_encode([
        "success" => true,
        "message" => "แก้ไขข้อมูลเรียบร้อย"
    ]);

} catch (PDOException $e) {
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage()
    ]);
}
