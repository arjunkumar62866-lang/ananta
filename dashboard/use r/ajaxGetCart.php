<?php
include("common/connection.php");

$userId = $_POST['userId'] ?? 0;

$query = "
    SELECT c.id AS cart_id, p.id AS product_id, p.title, p.price, p.pv, p.img, c.quantity, (p.price * c.quantity) AS subtotal
    FROM tbl_cart c
    JOIN tbl_product p ON c.product_id = p.id
    WHERE c.user_id = :uid
";
$stmt = $pdo->prepare($query);
$stmt->execute([':uid' => $userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];
$count = 1;
foreach ($cartItems as $item) {
    $data[] = [
        '<button class="btn btn-danger btn-sm removeBtn" data-id="'.$item['cart_id'].'">X</button>',
        $count++,
        '<img src="../productimage/'.$item['img'].'" style="width:50px;height:50px;">',
        htmlspecialchars($item['title']),
        '₹'.$item['price'],
        $item['pv'],
        '
        <div class="qty-controls d-flex align-items-center">
            <button class="btn btn-sm btn-secondary decrease" data-id="'.$item['cart_id'].'">-</button>
            <span class="mx-2">'.$item['quantity'].'</span>
            <button class="btn btn-sm btn-secondary increase" data-id="'.$item['cart_id'].'">+</button>
        </div>',
        '₹'.$item['subtotal']
    ];
}

echo json_encode(["data" => $data]);
?>
