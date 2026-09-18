<?php
session_start();
include 'common/connection.php'; // your PDO connection

if (!isset($_SESSION['userid'])) {
    exit('User not logged in');
}

$userid = $_SESSION['userid'];
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'addToCart':
        $proId = $_POST['proId'];
        $qty = $_POST['proQty'];

        // Check if product exists
        $stmt = $pdo->prepare("SELECT * FROM tbl_cart WHERE user_id = :user AND product_id = :pid");
        $stmt->execute(['user' => $userid, 'pid' => $proId]);

        if ($stmt->rowCount() > 0) {
            echo "Product Already In Cart";
            exit;
        }

        $stmt = $pdo->prepare("INSERT INTO tbl_cart (user_id, product_id, quantity) VALUES (:user, :pid, :qty)");
        echo $stmt->execute(['user' => $userid, 'pid' => $proId, 'qty' => $qty])
            ? "Product Added Successfully"
            : "Something went wrong";
        break;

    case 'getCartProducts':
        $stmt = $pdo->prepare("SELECT c.id AS cart_id, p.title, p.img, p.price, p.pv, c.quantity 
                               FROM tbl_cart c
                               JOIN tbl_product p ON c.product_id = p.id
                               WHERE c.user_id = :user");
        $stmt->execute(['user' => $userid]);
        $cart = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($cart)) {
            echo "<tr><td colspan='7'>Your cart is empty</td></tr>";
        } else {
            $i = 1;
            foreach ($cart as $item) {
                echo "<tr>
                    <td>{$i}</td>
                    <td><img src='../productimage/{$item['img']}' width='50'></td>
                    <td>{$item['title']}</td>
                    <td>{$item['price']}</td>
                    <td>
                        <button onclick='decreaseQty({$item['cart_id']})'>-</button>
                        {$item['quantity']}
                        <button onclick='increaseQty({$item['cart_id']}, 10)'>+</button>
                    </td>
                    <td>" . ($item['price'] * $item['quantity']) . "</td>
                    <td><button onclick='removeProduct({$item['cart_id']})'>Remove</button></td>
                </tr>";
                $i++;
            }
        }
        break;

    case 'updateQty':
        $cartId = $_POST['cartId'];
        $type = $_POST['type'];

        if ($type == 'Add') {
            $pdo->prepare("UPDATE tbl_cart SET quantity = quantity + 1 WHERE id = :id AND user_id = :user")
                ->execute(['id' => $cartId, 'user' => $userid]);
            echo "Quantity Increased";
        } elseif ($type == 'Dec') {
            $pdo->prepare("UPDATE tbl_cart SET quantity = quantity - 1 WHERE id = :id AND quantity > 1 AND user_id = :user")
                ->execute(['id' => $cartId, 'user' => $userid]);
            echo "Quantity Decreased";
        }
        break;

    case 'removeProduct':
        $cartId = $_POST['cartId'];
        $pdo->prepare("DELETE FROM tbl_cart WHERE id = :id AND user_id = :user")
            ->execute(['id' => $cartId, 'user' => $userid]);
        echo "Product Removed";
        break;

    case 'countProduct':
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tbl_cart WHERE user_id = :user");
        $stmt->execute(['user' => $userid]);
        echo $stmt->fetchColumn();
        break;

    case 'totalAmount':
        $stmt = $pdo->prepare("SELECT SUM(p.price * c.quantity)
                               FROM tbl_cart c
                               JOIN tbl_product p ON c.product_id = p.id
                               WHERE c.user_id = :user");
        $stmt->execute(['user' => $userid]);
        echo $stmt->fetchColumn() ?: 0;
        break;
}
