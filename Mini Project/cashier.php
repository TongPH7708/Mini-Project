<?php

session_start();

require_once __DIR__ . '/config/database.php';

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="cashier.css">
</head>
<body>
    <div class="navbar">
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
                <input type="text">
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
            <button id="refresh">End Transaction</button>
        </div>       
    </div>
</body>
<script src="cashier.js"></script>
</html>