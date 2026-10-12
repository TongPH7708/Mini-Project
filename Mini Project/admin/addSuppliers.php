
<?php
session_start();

require_once __DIR__ . '/../config/database.php';


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $storageLocation = trim($_POST['storage_location'] ?? '');

    if ($name === '') {
        $error = 'Please enter valid supplier details.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO supplier (name)VALUES (?)");

            $stmt->execute([
                $name,
            ]);

            header('Location: suppliers.php?added=1');
            exit;

        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'This supplier name already exists.';
            } else {
                $error = 'Unable to create supplier. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Supplier</title>
    <link rel="stylesheet" href="add.css">
</head>
<body>
    <div class="card">
        <h2>Add New Supplier</h2>

        <?php if ($error !== ''): ?>
            <p style="color: red;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <label for="name">Supplier Name:</label>
            <input type="text" id="name" name="name" maxlength="255" required
                value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
            <br><br>

            <button type="submit">Save Supplier</button>
            <a href="suppliers.php">Cancel</a>

        </form>
    </div>
</body>
</html>
