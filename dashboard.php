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

$summary = pg_fetch_all(pg_query($conn, "SELECT status, total FROM case_summary")) ?: [];
$hearings = pg_fetch_all(pg_query($conn, "SELECT case_number, title, hearing_date, status FROM upcoming_hearings")) ?: [];

$counts = ['pending' => 0, 'heard' => 0, 'disposed' => 0];
$total = 0;
foreach ($summary as $row) {
    $counts[$row['status']] = (int)$row['total'];
    $total += (int)$row['total'];
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Court Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <style>
        .wrap { width: 100%; max-width: 1000px; }
        .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 16px; }
        .stat {
            background: rgba(212, 175, 55, 0.12);
            border: 1px solid rgba(212, 175, 55, 0.5);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            color: #ffffff;
        }
        .stat i { font-size: 26px; color: #d4af37; }
        .stat .num { font-size: 36px; font-weight: bold; margin: 8px 0; }
        .charts { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 700px) { .charts { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="wrap">
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
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
        <div class="stats">
            <div class="stat"><i class="fa-solid fa-folder-open"></i><div class="num"><?php echo $total; ?></div><div>Total cases</div></div>
            <div class="stat"><i class="fa-solid fa-hourglass-half"></i><div class="num"><?php echo $counts['pending']; ?></div><div>Pending</div></div>
            <div class="stat"><i class="fa-solid fa-gavel"></i><div class="num"><?php echo $counts['heard']; ?></div><div>Heard</div></div>
            <div class="stat"><i class="fa-solid fa-circle-check"></i><div class="num"><?php echo $counts['disposed']; ?></div><div>Disposed</div></div>
        </div>
    </div>

    <div class="charts">
        <div class="card">
            <h3>Cases by status</h3>
            <canvas id="statusChart"></canvas>
        </div>
        <div class="card">
            <h3>Upcoming hearings</h3>
            <table>
                <tr><th>Case No.</th><th>Title</th><th>Date</th><th>Status</th></tr>
                <?php foreach ($hearings as $h) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($h['case_number']); ?></td>
                        <td><?php echo htmlspecialchars($h['title']); ?></td>
                        <td><?php echo htmlspecialchars($h['hearing_date']); ?></td>
                        <td><?php echo htmlspecialchars($h['status']); ?></td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>

<script>
new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Heard', 'Disposed'],
        datasets: [{
            data: [<?php echo $counts['pending']; ?>, <?php echo $counts['heard']; ?>, <?php echo $counts['disposed']; ?>],
            backgroundColor: ['#f59e0b', '#7873f5', '#4ade80'],
            borderColor: '#0b1f3a'
        }]
    },
    options: { plugins: { legend: { labels: { color: '#e8edf5' } } } }
});
</script>
</body>
</html>
