<?php
    session_start();

    require_once __DIR__ . '/config/database.php';

    function isGuest()
    { 
    return ! isset ($_SESSION['user']);
    }

    function isEditor()
    { if(isset($_SESSION['user'])){
    if($_SESSION ['user']['role'] === 'admin' ||
       $_SESSION ['user']['role'] === 'editor'){
        return true;
       }
    }

    return false;
    }

    if(!isEditor()){
        header('Location: cashier.php');
        exit;
    }

   $sort = $_GET['sort'] ?? 'id_asc'; 
   $sortOptions = [ 'category_name_asc' => 'category_name ASC', 
   'category_name_desc' => 'category_name DESC', 
   'price_asc' => 'selling_price ASC', 
   'price_desc' => 'selling_price DESC', 
   'quantity_asc' => 'quantity ASC', 
   'quantity_desc' => 'quantity DESC', 
   'id_asc' => 'id ASC', 
   ];

   $orderBy = $sortOptions[$sort] ?? $sortOptions['id_asc']; 
   $stmt = $pdo->query("SELECT products.id, products.name, selling_price, quantity, 
   storage.name AS storage_name, 
   supplier.name AS supplier_name,
   categories.name AS category_name 
   FROM products
   LEFT JOIN supplier_products ON products.id = supplier_products.product_id
   LEFT JOIN supplier ON supplier_products.supplier_id = supplier.id
   LEFT JOIN storage ON products.storage_id = storage.id
   LEFT JOIN categories ON products.category_id = categories.id
   ORDER BY $orderBy"); 
   $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
   if (isset($_GET['format']) && $_GET['format'] === 'json') {
    header('Content-Type: application/json');
    echo json_encode($products);
    exit;
    }


   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $button = $_POST['action'];
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $number = $_POST["number"];

        if ($button === 'plus') {
            $increase = $pdo->prepare(
                "UPDATE products SET quantity = quantity + ? WHERE id = ?"
            );
            $increase->execute([$number, $id]);
        } elseif ($button === 'minus') {
            $decrease = $pdo->prepare(
                "UPDATE products SET quantity = quantity - ? WHERE id = ? AND quantity > 0"
            );
            $decrease->execute([$number, $id]);
        }


        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
   }
    
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
        <div>
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
            <a href="admin/dashboard.php">
                <div class="cashier">
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
    <div class="header">
        <h2>Inventory and Stock Management</h2>
    </div>
    <div class="middle">
        <div class="middleLeft">
            <div>
                <h3>Sort by:</h3>
                <ul class="sortList">
                    <a href="?sort=category_name_asc">Category A-Z</a>
                    <a href="?sort=category_name_desc">Category Z-A</a> 
                    <a href="?sort=price_asc">Price (Low to High)</a> 
                    <a href="?sort=price_desc">Price (High to Low)</a> 
                    <a href="?sort=quantity_asc">Quantity (Low to High)</a> 
                    <a href="?sort=quantity_desc">Quantity (High to Low)</a> 
                </ul>
            </div>
        </div>
        <div class="middleRight">
            <div class="title">
                <h2>Product List</h2>
                <input type="text" name="search" id="searchBar">
            </div>
            <div class="productTopRow">
                <div class="productID"><p>ID</p></div>
                <div class="product"><p>Products</p></div>
                <div class="quantity"><p>Quantity</p></div>
                <div class="cost"><p>Category</p></div>
                <div class="supplier"><p>Supplier</p></div>
                <div class="storage"><p>Storage</p></div>
                <div class="price"><p>Price</p></div>
                <div><p>Add/Remove</p></div>
            </div>
            <div class="productsGrid">
                <?php foreach($products as $product): ?>
                    <div class="productRow">
                        <div><p><?= htmlspecialchars($product['id']) ?></p></div>
                        <div><p><?= htmlspecialchars($product['name'])?></p></div>
                        <div><p><?= htmlspecialchars($product['quantity'])?></p></div>
                        <div><p><?= htmlspecialchars($product['category_name'])?></p></div>
                        <div><p><?= htmlspecialchars($product['supplier_name'])?></p></div>
                        <div><p><?= htmlspecialchars($product['storage_name'])?></p></div>
                        <div><p><?= htmlspecialchars($product['selling_price'])?></p></div>
                        <div>
                            <form method="POST">
                                <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                                <input type="number" name="number" value="1" min="1" step="1" placeholder="Add/Remove Amount">
                                <button type="submit" name="action" value="plus">+</button>
                                <button type="submit" name="action" value="minus">-</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>
<script src="inventory.js"></script>
</html>