<?php
    session_start();

    require_once __DIR__ . '/../config/database.php';


   $stmt = $pdo->query("SELECT
    storage.id AS storage_id,
    storage.name AS storage_name,
    storage.space,
    products.id AS product_id,
    products.name AS product_name,
    products.quantity
    FROM storage
    LEFT JOIN products
    ON storage.id = products.storage_id
    ORDER BY storage.id;"); 
   $storages = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $storageId = $row['storage_id'];

    if (!isset($storages[$storageId])) {
        $storages[$storageId] = [
            'storage_id' => $row['storage_id'],
            'storage_name' => $row['storage_name'],
            'space' => $row['space'],
            'products' => []
        ];
    }
    if ($row['product_id'] !== null) {
        $storages[$storageId]['products'][] = [
            'product_id' => $row['product_id'],
            'product_name' => $row['product_name'],
            'quantity' => $row['quantity']
        ];
    }
}



   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $button = $_POST['action'];
        $remove = $_POST['remove'];
        $addProducts = $_POST['addProducts'];
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);


        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;

        if ($remove === 'remove'){
            $delete = $pdo -> prepare(
                "DELETE FROM storage WHERE id = ?"
            );
            $delete->execute([$id]);
        }


   }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="navbar">
        <a href="../cashier.php">
            <div class="notCurrent">
                <p>Cashier</p>
            </div>
        </a>
        <a href="../inventory.php">
            <div class="notCurrent">
                <p>Inventory</p>
            </div>
        </a>
        <a href="admin/dashboard.php">
            <div class="current">
                <p>Admin</p>
            </div>
        </a>
    </div>
    <div class="header">
        <h2>Admin Dashboard</h2>
    </div>
    <div class="middle">
        <div class="sideNav">
            <a href="dashboard.php"><div><h2>View Products</h2></div></a>
            <a href="storage.php"><div><h2>View Storage</h2></div></a>
            <a href="suppliers.php"><div><h2>View Suppliers</h2></div></a>
        </div>
        <div class="content">
            <div class="storageList">
                    <?php foreach($storages as $storage): ?>
                        <h1><?=htmlspecialchars($storage['storage_name'])?></h1>
                        <h2>Space:<?=htmlspecialchars($storage['space'])?></h2>
                        <?php foreach($storage['products'] as $product): ?>
                            <div class="list">
                                <div>
                                    <div><p>ID</p></div>
                                    <div><p>Products</p></div>
                                    <div><p>Quantity</p></div>
                                </div>
                                <div>
                                    <div><p><?= htmlspecialchars($product['product_id'])?></p></div>
                                    <div><p><?= htmlspecialchars($product['product_name'])?></p></div> 
                                    <div><p><?= htmlspecialchars($product['quantity'])?> </p></div>
                                </div>
                                
                                <div>
                                    <button type="submit" name="remove" value="remove" id="remove">X</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </div>
        </div>
</body>
</html>