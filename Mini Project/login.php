<?php
    session_start();

    require_once __DIR__ . '/config/database.php';

    $error = '';

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $email =$_POST['email'];
        $password =$_POST['password'];

        if(empty($email) || empty($password)){
            echo "Please enter your email or password";
            exit;
        } 
    

        $statement = $pdo -> prepare('SELECT * FROM users WHERE email= :email');
        $statement -> execute([':email'=>$email]);
        $user =$statement ->fetch(PDO::FETCH_OBJ);

        if($user &&password_verify($password, $user ->password)){
            $_SESSION['authenticated']=true;
            $_SESSION['email']= $user -> email;
            header('Location:cashier.php');
            exit;
        }else{
            $error = 'Wrong Email or Password.';
        }

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    
    <div class="loginCard">
        <h1>Welcome Back!</h1>
        <p>Login to your account </p>

        <?php if ($error): ?>
            <div class="errorMsg"><?=htmlspecialchars($error)?></div>
        <?php endif; ?>

        <form method="POST" action="login.php" class="loginForm">
            <div class="loginInput">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="something@example.com" required>
            </div>
            <div class="loginInput">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>

            <button type="submit">Log In</button>
        </form>

        <p>Not Registered? <a href="register.php">Register Now</a></p>
        
    </div>
       
</body>
</html>