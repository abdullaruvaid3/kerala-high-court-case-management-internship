<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$conn = pg_connect("host=localhost dbname=court_db user=courtuser password=YOUR_PASSWORD");
if (!$conn) {
    die("Database connection failed");
}

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $case_number = trim($_POST['case_number']);
    $title = trim($_POST['title']);
    $petitioner = trim($_POST['petitioner']);
    $hearing = $_POST['hearing_date'] ?: null;
    $status = $_POST['status'];

    if (!in_array($status, ['pending', 'heard', 'disposed'])) {
        $message = "Invalid status.";
    } else {
        $ok = pg_query_params($conn,
            "INSERT INTO cases (case_number, title, petitioner, hearing_date, status)
             VALUES ($1, $2, $3, $4, $5)",
            array($case_number, $title, $petitioner, $hearing, $status));
        $message = $ok ? "Case added successfully." : "Could not add case: " . pg_last_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Case</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div class="navbar">
    <span class="brand"><i class="fa-solid fa-scale-balanced"></i> Kerala High Court</span>
    <div>
        <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="cases.php"><i class="fa-solid fa-folder-open"></i> Cases</a>
        <a href="add_case.php"><i class="fa-solid fa-plus"></i> Add Case</a>
        <a href="hearings.php"><i class="fa-solid fa-calendar-days"></i> Hearings</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<div class="card" style="max-width:520px;">
    <h2>Add New Case</h2>
    <?php if ($message) { ?><p><?php echo htmlspecialchars($message); ?></p><?php } ?>
    <form method="POST">
        Case number (e.g. WP(C) 1234/2026):
        <input type="text" name="case_number" required>
        Title:
        <input type="text" name="title" required>
        Petitioner:
        <input type="text" name="petitioner">
        Hearing date:
        <input type="date" name="hearing_date">
        Status:
        <select name="status">
            <option value="pending">Pending</option>
            <option value="heard">Heard</option>
            <option value="disposed">Disposed</option>
        </select>
        <button type="submit">Add case</button>
    </form>
</div>
</body>
</html>
