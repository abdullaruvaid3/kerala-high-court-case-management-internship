<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cases.php");
    exit;
}
$conn = pg_connect("host=localhost dbname=court_db user=courtuser password=YOUR_PASSWORD");
if (!$conn) {
    die("Database connection failed");
}
$id = (int)($_POST['id'] ?? 0);
pg_query_params($conn, "DELETE FROM cases WHERE id = $1", array($id));
header("Location: cases.php");
exit;
