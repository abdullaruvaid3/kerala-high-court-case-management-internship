<?php
session_start();
$conn = pg_connect("host=localhost dbname=court_db user=courtuser password=YOUR_PASSWORD");
if (!$conn) {
    die("Database connection failed");
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $result = pg_query_params($conn,
        'SELECT id, name, password FROM users WHERE email = $1',
        array($email));
    $user = pg_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['pending_user_id'] = $user['id'];
        $_SESSION['pending_name'] = $user['name'];
        header("Location: otp.php");
        exit;
    } else {
        $message = "Wrong email or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Court Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img src="logo.svg" class="logo" alt="Court logo">
<div class="card" style="max-width:420px;">
    <h2>Court Login</h2>
    <?php if ($message) { ?><p><?php echo $message; ?></p><?php } ?>
    <form method="POST">
        Email: <input type="email" name="email" required>
        Password: <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
    <p><a href="register.php">Create an account</a></p>
</div>
</body>
</html>
