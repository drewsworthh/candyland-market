<?php
session_start();
require_once __DIR__ . '/../app/config/database.php';

$pdo = Database::connect();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = 'Both email and password are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
    } else {
        $stmt = $pdo->prepare("
            SELECT id, first_name, last_name, email, password_hash, role
            FROM users
            WHERE email = ?
        ");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header('Location: index.php');
                exit();
            } else {
                $message = 'Incorrect password.';
            }
        } else {
            $message = 'No account found with that email.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Candyland Market</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <style>
        body {
            padding-top: 40px;
            background-color: #f9f9f9;
        }

        .login-box {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        h2 {
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-box">
            <h2>Login</h2>

            <?php if ($message !== ''): ?>
                <div class="alert alert-info">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>

                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" class="form-control" name="password" id="password" required>
                </div>

                <button type="submit" class="btn btn-primary">Login</button>
                <a href="index.php" class="btn btn-default">Back to Home</a>
            </form>
        </div>
    </div>
</body>
</html>