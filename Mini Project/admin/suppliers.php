
<?php
    session_start();

    require_once __DIR__ . '/../config/database.php';

    function isGuest()
    { 
    return ! isset ($_SESSION['user']);
    }

    function isAdmin()
    { if(isset($_SESSION['user'])){
        if($_SESSION ['user']['role'] === 'admin'){
            return true;
        }
    }
    return false;
    }

    if(!isAdmin()){
        header('Location: /../inventory.php');
        exit;
    }

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

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $remove = $_POST['remove'];
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        $deleteAssociations = $pdo->prepare("DELETE FROM supplier_products WHERE supplier_id = ?");
        $deleteAssociations->execute([$id]);
        $updateStorage = $pdo->prepare("UPDATE storage SET supplier_id = NULL WHERE supplier_id = ?");
        $updateStorage->execute([$id]);
        $delete = $pdo->prepare("DELETE FROM supplier WHERE id = ?");
        $delete->execute([$id]);


        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="navbar">
        <div>
            <a href="/../cashier.php">
                <div class="notCurrent">
                    <p>Cashier</p>
                </div>
            </a>
            <a href="/../inventory.php">
                <div class="notCurrent">
                    <p>Inventory</p>
                </div>
            </a>
            <a href="dashboard.php">
                <div class="current">
                    <p>Admin</p>
                </div>
            </a>
        </div>
        <a href="/../logout.php">
            <div class="logout">
                <p>Logout</p>
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
            <div class="title">
                    <h2>Supplier List</h2>
                </div>
            <div>
                <a href="addSuppliers.php" class="storageSupplyButton">
                    <button class="addButton" type="button">Add Supplier</button>
                </a>
            </div>
            <?php foreach($groupedSuppliers as $supplier): ?>
                <div class= "sTitle">
                    <h2><?= htmlspecialchars($supplier['supplier_name']) ?> </h2>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?= (int)$supplier['supplier_id'] ?>">
                        <button name="remove" value="remove" id="remove">X</button>
                    </form>
                </div>

                <div class="list">
                    <div>
                        <div><p>ID</p></div>
                        <div><p>Products</p></div>
                        <div><p>Cost</p></div>
                    </div>

                    <?php foreach($supplier['products'] as $product): ?>
                        <div>
                            <div><p><?= htmlspecialchars($product['product_id']) ?></p></div>
                            <div><p><?= htmlspecialchars($product['product_name']) ?></p></div>
                            <div><p><?= htmlspecialchars($product['product_cost']) ?></p></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
