<?php
include 'common/connection.php';

$userId = trim($_POST['userId']);

$html = '';

try {
    // Fetch user info
    $userStmt = $pdo->prepare("SELECT * FROM user WHERE userid = :userId");
    $userStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $userStmt->execute();
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    $idactive = $user['one_club_status'] ?? 0;

    // Fetch cart items
    $cartStmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE userid = :userId");
    $cartStmt->bindParam(':userId', $userId, PDO::PARAM_INT);
    $cartStmt->execute();
    $cartItems = $cartStmt->fetchAll(PDO::FETCH_ASSOC);

    if (!empty($cartItems)) {
        $i = 1;
        foreach ($cartItems as $cart) {
            $proId = $cart['product_id'];

            // Fetch product info
            $proStmt = $pdo->prepare("SELECT * FROM tbl_product WHERE id = :proId");
            $proStmt->bindParam(':proId', $proId, PDO::PARAM_INT);
            $proStmt->execute();
            $product = $proStmt->fetch(PDO::FETCH_ASSOC);

            if ($product) {
                // Price logic
                $price = $product['dp_price']; // can adjust based on $idactive if needed

                $availableQty = $product['qty'];

                $html .= '<tr>';
                $html .= '<th><button type="button" class="btn btn-sm btn-danger" onclick="removeProduct(' . $cart['id'] . ')"><i class="fa fa-close"></i></button></th>';
                $html .= '<th>' . $i . '</th>';
                $html .= '<th><img src="../productimage/' . $product['img'] . '" width="80" height="50"></th>';
                $html .= '<th>' . ucwords($product['title']) . '</th>';
                $html .= '<th> Rs -'  . $product['price'] . '/-</th>';
                $html .= '<th> PV -'  . $product['pv'] . '</th>';
                $html .= '<th><span onclick="increaseQty(' . $cart['id'] . ', ' . $availableQty . ')" class="qtyBtn" style="margin-right:5px;"><i class="fa fa-plus"></i></span>';
                $html .= '<span class="qtyValue" data-cart-id="' . $cart['id'] . '" style="width:50px;background:white">' . $cart['qty'] . '</span>';
                $html .= '<span onclick="decreaseQty(' . $cart['id'] . ')" class="qtyBtn" style="margin-left:5px"><i class="fa fa-minus"></i></span></th>';
                $html .= '<th>Rs - ' . ((int)$cart['qty'] * (int)$price) . ' /-</th>';
                $html .= '</tr>';

                $i++;
            }
        }
    } else {
        $html .= '<tr>';
        $html .= '<th colspan="7" class="text-center">No Product In Your Cart <a href="all-product" class="btn btn-primary btn-sm">Click Here For Shopping</a></th>';
        $html .= '</tr>';
    }
} catch (PDOException $e) {
    $html = '<tr><th colspan="7" class="text-center">Error: ' . $e->getMessage() . '</th></tr>';
}

echo $html;
?>
