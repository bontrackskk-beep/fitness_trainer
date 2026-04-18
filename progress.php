<?php
session_start();
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

if(isset($_POST['save'])){
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    $calories = mysqli_real_escape_string($conn, $_POST['calories']);
    $date = date("Y-m-d");

    mysqli_query($conn, "INSERT INTO progress(user_id, weight, calories, date) VALUES('$user_id','$weight','$calories','$date')");
}

$weights = [];
$dates = [];
$calories_chart = [];

$chart_data = mysqli_query($conn, "SELECT * FROM progress WHERE user_id='$user_id' ORDER BY date ASC");
if($chart_data){
    while($row = mysqli_fetch_assoc($chart_data)){
        $weights[] = (float) $row['weight'];
        $calories_chart[] = (int) $row['calories'];
        $dates[] = $row['date'];
    }
}

$history = [];
$data = mysqli_query($conn, "SELECT * FROM progress WHERE user_id='$user_id' ORDER BY date DESC");
if($data){
    while($row = mysqli_fetch_assoc($data)){
        $history[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progress | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="app-shell">
    <header class="app-topbar">
        <div class="app-brand">
            <div class="app-brand-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div>
                <p>Zen Fit</p>
                <h1>Progress Tracker</h1>
            </div>
        </div>

        <nav class="app-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="app-grid">
        <section class="app-hero-panel">
            <p class="app-eyebrow">Performance log</p>
            <h1>Track the numbers that show your consistency.</h1>
            <p>
                Add weight and calorie entries, then review them through a cleaner chart and history table.
            </p>
        </section>

        <section class="app-grid-2">
            <article class="app-card">
                <div class="app-section-head">
                    <div>
                        <p class="app-eyebrow">New entry</p>
                        <h2>Log progress</h2>
                    </div>
                    <p>Save one data point at a time and build a better weekly trend.</p>
                </div>

                <form method="POST" class="app-form">
                    <input type="number" step="0.1" name="weight" class="app-input" placeholder="Enter weight in kg" required>
                    <input type="number" name="calories" class="app-input" placeholder="Calories burned" required>
                    <button type="submit" name="save" class="app-button">Save Progress</button>
                </form>
            </article>

            <article class="app-card">
                <div class="app-section-head">
                    <div>
                        <p class="app-eyebrow">History size</p>
                        <h2><?php echo count($history); ?> entries</h2>
                    </div>
                    <p>Your saved logs help power dashboard insights and recent trends.</p>
                </div>

                <div class="app-grid-2">
                    <div class="app-stat">
                        <strong><?php echo !empty($history) ? htmlspecialchars($history[0]['weight']) . ' kg' : 'No data'; ?></strong>
                        <span>Latest weight</span>
                    </div>
                    <div class="app-stat">
                        <strong><?php echo !empty($history) ? htmlspecialchars($history[0]['calories']) : 'No data'; ?></strong>
                        <span>Latest calories</span>
                    </div>
                </div>
            </article>
        </section>

        <section class="app-card">
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow">Visual trend</p>
                    <h2>Progress chart</h2>
                </div>
                <p>Weight and calorie history in one chart for faster review.</p>
            </div>
            <div style="border-radius:24px; padding:16px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06);">
                <canvas id="progressChart" height="120"></canvas>
            </div>
        </section>

        <section class="app-card">
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow">Saved entries</p>
                    <h2>Progress history</h2>
                </div>
                <p>Review your previous log dates, weights, and calorie values.</p>
            </div>

            <?php if(empty($history)){ ?>
            <p class="app-empty">No progress entries yet.</p>
            <?php } else { ?>
            <div class="app-table-wrap">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Weight</th>
                            <th>Calories</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($history as $row){ ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['weight']); ?> kg</td>
                            <td><?php echo htmlspecialchars($row['calories']); ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <?php } ?>
        </section>
    </main>

    <?php include('footer.php'); ?>
</div>

<script>
const ctx = document.getElementById('progressChart').getContext('2d');
new Chart(ctx, {
    data: {
        labels: <?php echo json_encode($dates); ?>,
        datasets: [
            {
                type: 'line',
                label: 'Weight',
                data: <?php echo json_encode($weights); ?>,
                borderColor: '#ff7a18',
                backgroundColor: 'rgba(255,122,24,0.18)',
                tension: 0.35,
                fill: true,
                yAxisID: 'y'
            },
            {
                type: 'bar',
                label: 'Calories',
                data: <?php echo json_encode($calories_chart); ?>,
                backgroundColor: 'rgba(84,210,255,0.28)',
                borderRadius: 8,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: { color: '#f4f7fb' }
            }
        },
        scales: {
            x: {
                ticks: { color: '#a9bdcf' },
                grid: { color: 'rgba(255,255,255,0.06)' }
            },
            y: {
                ticks: { color: '#a9bdcf' },
                grid: { color: 'rgba(255,255,255,0.06)' }
            },
            y1: {
                position: 'right',
                ticks: { color: '#a9bdcf' },
                grid: { drawOnChartArea: false }
            }
        }
    }
});
</script>
</body>
</html>
