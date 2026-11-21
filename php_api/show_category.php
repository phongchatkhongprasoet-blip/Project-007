<?php
include 'condb.php';

// แสดงประเภทสินค้า พร้อมจำนวนสินค้าในแต่ละประเภท
try {
    $stmt = $conn->query("SELECT c.category_id, c.category_name, COUNT(p.product_id) AS product_count
                          FROM categories c
                          LEFT JOIN products p ON p.category_id = c.category_id
                          GROUP BY c.category_id, c.category_name
                          ORDER BY c.category_id");
    $datas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($datas);
    
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
