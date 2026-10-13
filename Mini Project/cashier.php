<?php

session_start();

require_once __DIR__ . '/config/database.php';

function isGuest()
{ 
return ! isset ($_SESSION['user']);
}


if (isset($_GET['id'])) {

    $id = $_GET['id'];

    $stmt = $pdo->prepare("
        SELECT id, name, selling_price, quantity
        FROM products
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');

    if ($product) {
        echo json_encode([
            'success' => true,
            'product' => $product
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
    }

    exit;
}

function isUser()
{ if(isset($_SESSION['user'])){
    if($_SESSION ['user']['role'] === 'admin' ||
       $_SESSION ['user']['role'] === 'editor' ||
       $_SESSION ['user']['role'] === 'user' ){
        return true;
       }
}

return false;
}

if( !isUser()){
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    header('Content-Type: application/json');
    $data = json_decode(file_get_contents('php://input'), true);
    $items = $data['items'] ?? null;

    $stmt = $pdo->prepare(" UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity >= ? ");
        foreach ($items as $item) {

            $id = filter_var(
                $item['id'] ?? null,
                FILTER_VALIDATE_INT
            );

            $quantity = filter_var(
                $item['quantity'] ?? null,
                FILTER_VALIDATE_INT
            );
        };
        $stmt->execute([$quantity, $id, $quantity]);
        $pdo->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Transaction completed successfully.'
        ]);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashier</title>
    <link rel="stylesheet" href="cashier.css">
</head>
<body>
    <div class="navbar">
        <div>
            <a href="cashier.php">
                <div class="current">
                    <p>Cashier</p>
                </div>
            </a>
            <a href="inventory.php">
                <div class="notCurrent">
                    <p>Inventory</p>
                </div>
            </a>
            <a href="admin/dashboard.php">
                <div class="notCurrent">
                    <p>Admin</p>
                </div>
            </a>
        </div>
        <a href="logout.php">
            <div class="logout">
                <p>Logout</p>
            </div>
        </a>
    </div>
    <div class="cashierContainer">
        <div class="cashierHeader">
            <h2>Enter Transaction - Detail</h2>
        </div>
        <div class="cashierMiddleTop">
            <div>
                <label for="Item No.">Item No.</label>
                <input type="number" id="item-identifier">

            </div>
            <div id="description">
                <label for="Description">Description</label>
                <input type="text" id="item-name">
            </div>
            <div id="quantity">
                <label for="Quantity">Quantity</label>
                <input type="number" id="item-quantity">
            </div>
            <div id="price">
                <label for="Price">Price</label>
                <input type="number" id="item-price">
            </div>
        </div>
        <div class="cashierMiddleBottom">
            <div class="right">
                <div class="cashierTable">
                    <div><p>Item No</p></div>
                    <div><p>Description</p></div>
                    <div><p>Qty</p></div>
                    <div><p>Price</p></div>
                    <div><p>Amount</p></div>
                </div>
                <div id="cashierList">
                    
                </div>
                <div class=finalRow>
                    <div></div>
                    <div></div>
                    <div></div>
                    <div class="total"><p>Total:</p></div>
                    <div id="grandTotal"></div>
                </div>
            </div>
        </div>
        <div class="cashierBottom">
            <button id="endTransaction">End Transaction</button>
            <button id="refresh">Refresh</button>
        </div>       
    </div>
</body>
<script src="cashier.js"></script>
</html>