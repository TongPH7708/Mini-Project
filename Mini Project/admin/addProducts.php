
<?php
session_start();

require_once __DIR__ . '/../config/database.php';

$error = '';

$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC") ->fetchAll(PDO::FETCH_ASSOC);

$storages = $pdo->query("SELECT id, name FROM storage ORDER BY name ASC") ->fetchAll(PDO::FETCH_ASSOC);

$suppliers = $pdo->query("SELECT id, name FROM supplier ORDER BY name ASC") ->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = ($_POST['name'] ?? '');
    $price = $_POST['selling_price'] ?? '';
    $categoryId = filter_input(
        INPUT_POST, 'category_id', FILTER_VALIDATE_INT
    );
    $quantity = filter_input(
        INPUT_POST, 'quantity', FILTER_VALIDATE_INT
    );
    $storageId = filter_input(
        INPUT_POST, 'storage_id', FILTER_VALIDATE_INT
    );

    $supplierId = ($_POST['supplier_id'] ?? '') === ''
        ? null
        : filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT);

    $cost = $_POST['cost'] ?? '';

    if (
        $name === '' ||
        !is_numeric($price) ||
        (float)$price < 0 ||
        (string)$price !== '' && !is_numeric($price) ||
        $categoryId === false || $categoryId === null ||
        $quantity === false || $quantity === null || $quantity < 0 ||
        $storageId === false || $storageId === null
    ) {
        $error = 'Please enter valid product details.';
    } elseif (
        $supplierId !== null &&
        (
            $supplierId === false ||
            !is_numeric($cost) ||
            (float)$cost < 0
        )
    ) {
        $error = 'Please enter a valid supplier and supplier cost.';
    } else {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare(
            "INSERT INTO products
                (name, selling_price, category_id, quantity, storage_id)
                VALUES (?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $name,
            $price,
            $categoryId,
            $quantity,
            $storageId
        ]);

        $productId = $pdo->lastInsertId();

        if ($supplierId !== null) {
            $stmt = $pdo->prepare(
                "INSERT INTO supplier_products
                    (supplier_id, product_id, cost)
                    VALUES (?, ?, ?)"
            );

            $stmt->execute([
                $supplierId,
                $productId,
                $cost
            ]);
        }

        $pdo->commit();

        header('Location: dashboard.php?added=1');
        exit;

        } 
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>

    <h2>Add New Product</h2>

    <?php if ($error !== ''): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="POST">

        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" maxlength="255" required
            value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
        <br><br>

        <label for="selling_price">Selling Price:</label>
        <input type="number" id="selling_price" name="selling_price" min="0" step="0.01" required
            value="<?= htmlspecialchars($_POST['selling_price'] ?? '') ?>">
        <br><br>

        <label for="category_id">Category:</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select a category</option>

            <?php foreach ($categories as $category): ?>
                <option
                    value="<?= (int)$category['id'] ?>"
                    <?= (string)($_POST['category_id'] ?? '') ===
                        (string)$category['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($category['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="quantity">Initial Quantity:</label>
        <input type="number" id="quantity" name="quantity" min="0" step="1" required
            value="<?= htmlspecialchars($_POST['quantity'] ?? '0') ?>">
        <br><br>

        <label for="storage_id">Storage Location:</label>
        <select id="storage_id" name="storage_id" required>
            <option value="">Select storage</option>

            <?php foreach ($storages as $storage): ?>
                <option
                    value="<?= (int)$storage['id'] ?>"
                    <?= (string)($_POST['storage_id'] ?? '') ===
                        (string)$storage['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($storage['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <h3>Supplier Information (Optional)</h3>

        <label for="supplier_id">Supplier:</label>
        <select id="supplier_id" name="supplier_id">
            <option value="">No supplier selected</option>

            <?php foreach ($suppliers as $supplier): ?>
                <option
                    value="<?= (int)$supplier['id'] ?>"
                    <?= (string)($_POST['supplier_id'] ?? '') ===
                        (string)$supplier['id'] ? 'selected' : '' ?>
                >
                    <?= htmlspecialchars($supplier['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>

        <label for="cost">Supplier Cost:</label>
        <input type="number" id="cost" name="cost" min="0" step="0.01" value="<?= htmlspecialchars($_POST['cost'] ?? '') ?>">
        <br><br>

        <button type="submit">Save Product</button>
        <a href="dashboard.php">Cancel</a>

    </form>

</body>
</html>
