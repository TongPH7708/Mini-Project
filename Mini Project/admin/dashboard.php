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

   $sort = $_GET['sort'] ?? 'id_asc'; 
   $sortOptions = [ 'category_name_asc' => 'categories.name ASC', 
   'category_name_desc' => 'categories.name DESC', 
   'price_asc' => 'selling_price ASC', 
   'price_desc' => 'selling_price DESC', 
   'quantity_asc' => 'quantity ASC', 
   'quantity_desc' => 'quantity DESC', 
   'id_asc' => 'id ASC', 
   ];

   $orderBy = $sortOptions[$sort] ?? $sortOptions['id_asc'];
   $stmt = $pdo->query("SELECT products.id, products.name, selling_price, quantity,
   categories.name AS category_name 
   FROM products
   JOIN categories ON products.category_id = categories.id
   ORDER BY $orderBy"); 
   $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_GET['format']) && $_GET['format'] === 'json') {
        header('Content-Type: application/json');
        echo json_encode($products);
        exit;
    }


   if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $button = $_POST['action'];
        $remove = $_POST['remove'];
        $addProducts = $_POST['addProducts'];
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $number = $_POST["number"];

        if ($remove === 'remove'){
            $deleteAssociations = $pdo->prepare("DELETE FROM supplier_products WHERE product_id = ?");
            $deleteAssociations->execute([$id]);
            $delete = $pdo -> prepare("DELETE FROM products WHERE id = ?");
            $delete->execute([$id]);
        }

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
    <title>Document</title>
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
            <div class="contentLeft">
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
            <div class="contentRight">
                <div class="title">
                    <h2>Product List</h2>
                    <div> <input type="text" name="search" id="searchBar"> </div>
                </div>
                <div>
                    <a href="addProducts.php">
                        <button class="addButton" type="button">Add Products</button>
                    </a>
                </div>
                <div class="productTopRow">
                    <div class="productID"><p>ID</p></div>
                    <div class="product"><p>Products</p></div>
                    <div class="quantity"><p>Quantity</p></div>
                    <div class="cost"><p>Category</p></div>
                    <div class="price"><p>Price</p></div>
                    <div><p>Instock/Outstock</p></div>
                    <div><p>Remove Product</p></div>
                </div>
                <div class="productsGrid">
                    <?php foreach($products as $product): ?>
                        <div class="productRow">
                            <div><p><?= htmlspecialchars($product['id']) ?></p></div>
                            <div><p><?= htmlspecialchars($product['name'])?></p></div>
                            <div><p><?= htmlspecialchars($product['quantity'])?></p></div>
                            <div><p><?= htmlspecialchars($product['category_name'])?></p></div>
                            <div><p><?= htmlspecialchars($product['selling_price'])?></p></div>
                            <div>
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                                    <input type="number" name="number" value="1" min="1" step="1" placeholder="Add/Remove Amount">
                                    <button type="submit" name="action" value="plus">+</button>
                                    <button type="submit" name="action" value="minus">-</button>
                                </form>
                            </div>
                            <div class="remove">
                                <form method="POST">
                                    <input type="hidden" name="id" value="<?= (int)$product['id'] ?>">
                                    <button name="remove" value="remove" id="remove">X</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="dashboard.js"></script>
</html>