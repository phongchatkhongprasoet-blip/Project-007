<?php
include 'condb.php';

// แสดงสินค้าพร้อมชื่อประเภท ถ้าส่ง ?category_id=... มาจะแสดงเฉพาะประเภทนั้น
try {
    $sql = "SELECT p.*, c.category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.category_id";

    if (!empty($_GET['category_id'])) {
        $stmt = $conn->prepare($sql . " WHERE p.category_id = :category_id ORDER BY p.product_id");
        $stmt->execute([':category_id' => $_GET['category_id']]);
    } else {
        $stmt = $conn->query($sql . " ORDER BY p.product_id");
    }

    $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($datas);
    
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
