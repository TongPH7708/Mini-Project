<?php
    session_start();

    require_once __DIR__ . '/config/database.php';

   $sort = $_GET['sort'] ?? 'id_asc'; 
   $sortOptions = [ 'category_asc' => 'category ASC', 
   'category_desc' => 'category DESC', 
   'price_asc' => 'selling_price ASC', 
   'price_desc' => 'selling_price DESC', 
   'quantity_asc' => 'quantity ASC', 
   'quantity_desc' => 'quantity DESC', 
   'id_asc' => 'id ASC', 
   ];

   $orderBy = $sortOptions[$sort] ?? $sortOptions['id_asc']; 
   $stmt = $pdo->query("SELECT * FROM products ORDER BY $orderBy"); 
   $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

   

    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>
    <link rel="stylesheet" href="inventory.css">
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
    <div class="header">
        <h1>Inventory and Stock Management</h1>
    </div>
    <div class="middle">
        <div class="middleLeft">
            <div>
                <h3>Sort by:</h3>
                <ul class="sortList">
                    <a href="?sort=category_asc">Category A-Z</a> 
                    <a href="?sort=price_asc">Price (Low to High)</a> 
                    <a href="?sort=price_desc">Price (High to Low)</a> 
                    <a href="?sort=quantity_asc">Quantity (Low to High)</a> 
                    <a href="?sort=quantity_desc">Quantity (High to Low)</a> 
                </ul>
            </div>
        </div>
        <div class="middleRight">
            <h2>Product List</h2>
            <div class="productTopRow">
                <div class="productID"><p>ID</p></div>
                <div class="product"><p>Products</p></div>
                <div class="quantity"><p>Quantity</p></div>
                <div class="cost"><p>Category</p></div>
                <div class="price"><p>Price</p></div>
            </div>
            <div class="productsGrid">
                <?php foreach($products as $product): ?>
                    <div class="productRow">
                        <div><p><?= htmlspecialchars($product['id']) ?></p></div>
                        <div><p><?= htmlspecialchars($product['name'])?></p></div>
                        <div><p><?= htmlspecialchars($product['quantity'])?></p></div>
                        <div><p><?= htmlspecialchars($product['category'])?></div>
                        <div><p><?= htmlspecialchars($product['selling_price'])?></p></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
</html>