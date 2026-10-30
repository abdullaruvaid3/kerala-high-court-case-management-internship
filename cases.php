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

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
    $result = pg_query_params($conn,
        "SELECT id, case_number, title, petitioner, date_filed, hearing_date, status
         FROM cases
         WHERE case_number ILIKE $1 OR title ILIKE $1
         ORDER BY date_filed DESC, id DESC",
        array("%$q%"));
} else {
    $result = pg_query($conn,
        "SELECT id, case_number, title, petitioner, date_filed, hearing_date, status
         FROM cases ORDER BY date_filed DESC, id DESC");
}
$cases = pg_fetch_all($result) ?: [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Case List</title>
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
    <h2>Case List</h2>
    <form method="GET">
        <input type="text" name="q" placeholder="Search by case number or title" value="<?php echo htmlspecialchars($q); ?>">
        <button type="submit">Search</button>
    </form>
    <p>Showing <?php echo count($cases); ?> case(s).</p>
    <table>
        <tr><th>Case No.</th><th>Title</th><th>Petitioner</th><th>Filed</th><th>Hearing</th><th>Status</th><th>Action</th></tr>
        <?php foreach ($cases as $c) { ?>
            <tr>
                <td><?php echo htmlspecialchars($c['case_number']); ?></td>
                <td><?php echo htmlspecialchars($c['title']); ?></td>
                <td><?php echo htmlspecialchars($c['petitioner']); ?></td>
                <td><?php echo htmlspecialchars($c['date_filed']); ?></td>
                <td><?php echo htmlspecialchars($c['hearing_date']); ?></td>
                <td><?php echo htmlspecialchars($c['status']); ?></td>
                <td><a href="edit_case.php?id=<?php echo (int)$c['id']; ?>"><i class="fa-solid fa-pen"></i> Edit</a> <form method="POST" action="delete_case.php" style="display:inline" onsubmit="return confirm('Delete this case?');"><input type="hidden" name="id" value="<?php echo (int)$c['id']; ?>"><button type="submit" class="btn-delete"><i class="fa-solid fa-trash"></i> Delete</button></form></td>
            </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
