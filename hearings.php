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

$filter = $_GET['status'] ?? '';
if (in_array($filter, ['pending', 'heard', 'disposed'])) {
    $result = pg_query_params($conn,
        "SELECT case_number, title, hearing_date, status FROM upcoming_hearings WHERE status = $1 ORDER BY hearing_date",
        array($filter));
} else {
    $filter = '';
    $result = pg_query($conn,
        "SELECT case_number, title, hearing_date, status FROM upcoming_hearings ORDER BY hearing_date");
}
$rows = pg_fetch_all($result) ?: [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hearings</title>
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

<div class="card">
    <h2>Upcoming Hearings</h2>
    <form method="GET">
        Filter by status:
        <select name="status">
            <option value="">All</option>
            <option value="pending" <?php if ($filter === 'pending') echo 'selected'; ?>>Pending</option>
            <option value="heard" <?php if ($filter === 'heard') echo 'selected'; ?>>Heard</option>
            <option value="disposed" <?php if ($filter === 'disposed') echo 'selected'; ?>>Disposed</option>
        </select>
        <button type="submit">Filter</button>
    </form>
    <p>Showing <?php echo count($rows); ?> hearing(s).</p>
    <table>
        <tr><th>Case No.</th><th>Title</th><th>Hearing</th><th>Status</th></tr>
        <?php foreach ($rows as $r) { ?>
        <tr>
            <td><?php echo htmlspecialchars($r['case_number']); ?></td>
            <td><?php echo htmlspecialchars($r['title']); ?></td>
            <td><?php echo htmlspecialchars($r['hearing_date']); ?></td>
            <td><?php echo htmlspecialchars($r['status']); ?></td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
