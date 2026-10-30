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

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
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
            "UPDATE cases SET case_number=$1, title=$2, petitioner=$3, hearing_date=$4, status=$5 WHERE id=$6",
            array($case_number, $title, $petitioner, $hearing, $status, $id));
        if ($ok) {
            header("Location: cases.php");
            exit;
        }
        $message = "Could not update case: " . pg_last_error($conn);
    }
}

$res = pg_query_params($conn, "SELECT * FROM cases WHERE id = $1", array($id));
$case = pg_fetch_assoc($res);
if (!$case) {
    die("Case not found");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Case</title>
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
    <h2>Edit Case</h2>
    <?php if ($message) { ?><p><?php echo htmlspecialchars($message); ?></p><?php } ?>
    <form method="POST">
        <input type="hidden" name="id" value="<?php echo (int)$case['id']; ?>">
        Case number:
        <input type="text" name="case_number" value="<?php echo htmlspecialchars($case['case_number']); ?>" required>
        Title:
        <input type="text" name="title" value="<?php echo htmlspecialchars($case['title']); ?>" required>
        Petitioner:
        <input type="text" name="petitioner" value="<?php echo htmlspecialchars($case['petitioner'] ?? ''); ?>">
        Hearing date:
        <input type="date" name="hearing_date" value="<?php echo htmlspecialchars($case['hearing_date'] ?? ''); ?>">
        Status:
        <select name="status">
            <?php foreach (['pending', 'heard', 'disposed'] as $s) { ?>
            <option value="<?php echo $s; ?>" <?php if ($case['status'] === $s) echo 'selected'; ?>><?php echo ucfirst($s); ?></option>
            <?php } ?>
        </select>
        <button type="submit">Save changes</button>
    </form>
</div>
</body>
</html>
