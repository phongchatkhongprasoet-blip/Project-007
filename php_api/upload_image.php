<?php
// ฟังก์ชันอัปโหลดรูปภาพอุปกรณ์ ไปเก็บที่โฟลเดอร์ php_api/images/
// คืนค่า: ชื่อไฟล์ใหม่ (string) / null ถ้าไม่ได้เลือกรูป / โยน Exception ถ้าไฟล์ไม่ถูกต้อง
function uploadImage($field = 'image', $prefix = 'asset')
{
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // ไม่ได้เลือกรูป
    }

    if ($_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception("อัปโหลดรูปไม่สำเร็จ");
    }

    // อนุญาตเฉพาะไฟล์รูปภาพ
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    $allow = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allow)) {
        throw new Exception("รองรับเฉพาะไฟล์ jpg, jpeg, png, gif, webp");
    }
    if (getimagesize($_FILES[$field]['tmp_name']) === false) {
        throw new Exception("ไฟล์ที่เลือกไม่ใช่รูปภาพ");
    }

    // ตั้งชื่อไฟล์ใหม่ไม่ให้ซ้ำ
    $dir = __DIR__ . '/images/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    $newName = $prefix . '_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
    move_uploaded_file($_FILES[$field]['tmp_name'], $dir . $newName);

    return $newName;
}
?>
