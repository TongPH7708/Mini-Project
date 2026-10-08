<?php
    session_start();

    require_once __DIR__ . '/config/database.php';

$id = $_GET["id"] ?? null;

$stmt = $pdo->prepare("
    SELECT id, name, selling_price, category, quantity
    FROM products
    WHERE id = ?
");

$stmt->execute([$id]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

    
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
            <div class="cashier">
                <p>Cashier</p>
            </div>
        </a>
        <a href="inventory.php">
            <div class="inventory">
                <p>Inventory</p>
            </div>
        </a>
    </div>
    <div class="cashierContainer">
        <div class="cashierHeader">
            <h2>Enter Transaction - Detail</h2>
        </div>
        <div class="cashierMiddleTop">
            <div>
                <h1>Date</h1>
            </div>
            <div>
                <label for="Item No.">Item No.</label>
                <input type="number" id="item-identifier">

            </div>
            <div id="description">
                <label for="Description">Description</label>
                <input type="text">
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
            <div class="left">
                <p>Sub-Amount</p>
                <p>Tax Amount</p>

                <div>
                    <label for="discount">Discount</label>
                    <input type="number">
                </div>
                <div>
                    <label>Sales Tax</label>
                    <input type="number">
                </div>
                <div>
                    <label>Service Charge</label>
                    <input type="number">
                </div>
                <div>
                    <label>Rounding</label>
                    <input type="number">
                </div>

                <p class="netAmount">Net Amount</p>
            </div>

            <div class="right">
                <div class="cashierTable">
                    <div><p>Item No</p></div>
                    <div><p>Description</p></div>
                    <div><p>Qty</p></div>
                    <div><p>Price</p></div>
                    <div><p>Amount</p></div>
                </div>
                <div>
                    
                </div>

            
            </div>
        </div>
        <div class="cashierBottom">
            <button>Payment</button>
            <button>Print Receipt(F7)</button>
        </div>       
    </div>
</body>
<script src="cashier.js"></script>
</html>