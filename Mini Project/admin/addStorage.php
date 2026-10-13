
<?php
session_start();

require_once __DIR__ . '/../config/database.php';


$error = '';

$suppliers = $pdo->query("SELECT id, name FROM supplier ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');

    $supplierId = ($_POST['supplier_id'] ?? '') === ''
        ? null
        : filter_input(INPUT_POST, 'supplier_id', FILTER_VALIDATE_INT);

    if (
        $name === ''
    ) {
        $error = 'Please enter valid storage details.';
    } elseif (
        $supplierId !== null &&
        ($supplierId === false || $supplierId < 1)
    ) {
        $error = 'Please select a valid supplier.';
    } else {
        try {
            $stmt = $pdo->prepare(
                "INSERT INTO storage (name, supplier_id)
                 VALUES (?, ?)"
            );

            $stmt->execute([
                $name,
                $supplierId
            ]);

            header('Location: storage.php?added=1');
            exit;

        } catch (PDOException $e) {
            $error = 'Unable to create storage. Please check your details and try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Storage</title>
    <link rel="stylesheet" href="add.css">
</head>
<body>
    <div class="card">

        <h2>Add New Storage</h2>

        <?php if ($error !== ''): ?>
            <p style="color: red;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <label for="name">Storage Name:</label>
            <input type="text" id="name" name="name" maxlength="255" required
                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            <br><br>


            <label for="supplier_id">Supplier (Optional):</label>
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

            <button type="submit">Save Storage</button>
            <a href="storage.php">Cancel</a>

        </form>
    </div>
</body>
</html>
