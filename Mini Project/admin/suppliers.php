
<?php
    session_start();

    require_once __DIR__ . '/../config/database.php';

    $stmt = $pdo->query(
        "SELECT supplier.id AS supplier_id,
        supplier.name AS supplier_name,
        products.id AS product_id,
        products.name AS product_name,
        supplier_products.cost AS product_cost
        FROM supplier
        JOIN supplier_products ON supplier.id = supplier_products.supplier_id
        JOIN products ON supplier_products.product_id = products.id
        ORDER BY supplier.id ASC"
    );

    $suppliers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $groupedSuppliers = [];

    foreach ($suppliers as $supplier) {
        $id = $supplier['supplier_id'];

        if (!isset($groupedSuppliers[$id])) {
            $groupedSuppliers[$id] = [
                'supplier_id' => $supplier['supplier_id'],
                'supplier_name' => $supplier['supplier_name'],
                'products' => []
            ];
        }

        $groupedSuppliers[$id]['products'][] = [
            'product_id' => $supplier['product_id'],
            'product_name' => $supplier['product_name'],
            'product_cost' => $supplier['product_cost']
        ];
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
        <div class="supplierList">
            <?php foreach($groupedSuppliers as $supplier): ?>
                <h1>
                    Supplier ID: <?= htmlspecialchars($supplier['supplier_id']) ?>
                </h1>

                <h2>
                    Supplier Name: <?= htmlspecialchars($supplier['supplier_name']) ?>
                </h2>

                <div class="list">
                    <div>
                        <div><p>ID</p></div>
                        <div><p>Products</p></div>
                        <div><p>Cost</p></div>
                    </div>

                    <?php foreach($supplier['products'] as $product): ?>
                        <div>
                            <div>
                                <p><?= htmlspecialchars($product['product_id']) ?></p>
                            </div>

                            <div>
                                <p><?= htmlspecialchars($product['product_name']) ?></p>
                            </div>

                            <div>
                                <p><?= htmlspecialchars($product['product_cost']) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
