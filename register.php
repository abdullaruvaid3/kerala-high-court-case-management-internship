<?php
$conn = pg_connect("host=localhost dbname=court_db user=courtuser password=YOUR_PASSWORD");
if (!$conn) {
    die("Database connection failed");
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $result = pg_query_params($conn,
        'INSERT INTO users (name, email, password) VALUES ($1, $2, $3)',
        array($name, $email, $password));

    if ($result) {
        $message = "Account created. <a href='login.php'>Go to login</a>";
    } else {
        $message = "This email is already registered.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Court Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img src="logo.svg" class="logo" alt="Court logo">
<div class="card" style="max-width:420px;">
    <h2>Court Registration</h2>
    <?php if ($message) { ?><p><?php echo $message; ?></p><?php } ?>
    <form method="POST">
        Name: <input type="text" name="name" required>
        Email: <input type="email" name="email" required>
        Password: <input type="password" name="password" required>
        <button type="submit">Create account</button>
    </form>
    <p><a href="login.php">Already registered? Log in</a></p>
</div>
</body>
</html>
