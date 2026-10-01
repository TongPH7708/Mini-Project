<?php
    session_start();

    require_once __DIR__ . '/config/database.php';

    $error = '';

    if($_SERVER['REQUEST_METHOD']=== 'POST'){
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmedPassword = $_POST['confirm_password'];

        if (empty($email) || empty($password) || empty($confirmedPassword)){
            $error = 'All fields are required';
            exit;
        }else if ($password !== $confirmedPassword){
            $error = 'Passwords do not match';
        }

        $check = $pdo -> prepare("SELECT * FROM users WHERE email = :email" );
        $check ->execute([':email' => $email]);

        if($check -> fetch()){
            $error = "The email is already registered.";
        }

        $check = $pdo -> prepare("SELECT * FROM users WHERE name = :name" );
        $check ->execute([':name' => $name]);

        if($check -> fetch()){
            $error = "You are already registered.";
        }

        $hashedpassword = password_hash($password, PASSWORD_DEFAULT);

        $statement = $pdo-> prepare("INSERT INTO users(name, email, password) VALUES (:name, :email, :password)");
        $statement -> execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $hashedpassword
        ]);

        $userId = $pdo -> lastInsertId();
        $_SESSION['users'] = [
        'id' => $userId,
        'name' => $name,
        'email' => $email
        ];

        header('Location: cashier.php');
        exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="registerCard">
        <h1>Sign up to join us today</h1>
        
        <?php if ($error): ?>
            <div class="errorMsg"><?=htmlspecialchars($error)?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="registerInput">
                <label for="name">Enter your name</label>
                <input type="text" id="name" name="name" placeholder="Your name..." required>
            </div>
            <div class="registerInput">
                <label for="email">Enter your email</label>
                <input type="email" id="email" name="email" placeholder="something@example.com" required>
            </div>
            <div class="registerInput">
                <label for="password">Enter your password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <div class="registerInput">
                <label for="confirm_password">Confirm password</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••" required>
            </div>

            <button type="submit">Submit</button>
        </form>

    </div>
</body>
</html>