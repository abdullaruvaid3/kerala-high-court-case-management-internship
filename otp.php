<?php
session_start();
$conn = pg_connect("host=localhost dbname=court_db user=courtuser password=YOUR_PASSWORD");
if (!$conn) {
    die("Database connection failed");
}

if (!isset($_SESSION['pending_user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['pending_user_id'];

// Create a new 6-digit code, valid for 5 minutes
$code = str_pad(random_int(0, 999999), 6, "0", STR_PAD_LEFT);
pg_query_params($conn,
    "INSERT INTO otps (user_id, code, expires_at) VALUES ($1, $2, NOW() + INTERVAL '5 minutes')",
    array($user_id, $code));

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entered = $_POST['code'];
    $result = pg_query_params($conn,
        "SELECT id FROM otps WHERE user_id = $1 AND code = $2 AND used = FALSE AND expires_at > NOW() ORDER BY id DESC LIMIT 1",
        array($user_id, $entered));
    $row = pg_fetch_assoc($result);

    if ($row) {
        pg_query_params($conn, "UPDATE otps SET used = TRUE WHERE id = $1", array($row['id']));
        $_SESSION['user_id'] = $user_id;
        $_SESSION['name'] = $_SESSION['pending_name'];
        unset($_SESSION['pending_user_id'], $_SESSION['pending_name']);
        header("Location: dashboard.php");
        exit;
    } else {
        $message = "Wrong or expired code.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>OTP Verification</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<img src="logo.svg" class="logo" alt="Court logo">
<div class="card" style="max-width:420px;">
    <h2>Enter OTP</h2>
    <p>Demo code (shown on screen): <strong style="color:#d4af37; font-size:22px;"><?php echo $code; ?></strong></p>
    <?php if ($message) { ?><p><?php echo $message; ?></p><?php } ?>
    <form method="POST">
        Code: <input type="text" name="code" maxlength="6" required>
        <button type="submit">Verify</button>
    </form>
</div>
</body>
</html>

