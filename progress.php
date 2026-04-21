<?php
session_start();
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$user_goal = '';

$goal_query = mysqli_query($conn, "SELECT goal FROM users WHERE id='$user_id' LIMIT 1");
if($goal_query && mysqli_num_rows($goal_query) > 0){
    $user = mysqli_fetch_assoc($goal_query);
    $user_goal = $user['goal'];
}

$goal_exercises = [
    'Weight Loss' => [
        'Jump rope' => '300-500 kcal per 30 min',
        'Mountain climbers' => '250-400 kcal per 30 min',
        'Burpees' => '280-450 kcal per 30 min',
        'High knees' => '250-400 kcal per 30 min',
        'Box jumps' => '240-380 kcal per 30 min',
        'Running' => '300-500 kcal per 30 min'
    ],
    'Weight Gain' => [
        'Deadlifts' => 'Strength compound',
        'Bench press' => 'Chest strength',
        'Squats' => 'Lower body',
        'Pull-ups' => 'Back strength',
        'Dumbbell rows' => 'Back volume',
        'Overhead press' => 'Shoulder strength'
    ],
    'Fitness' => [
        'Push-ups' => 'Full body',
        'Planks' => 'Core stability',
        'Lunges' => 'Balance',
        'Burpees' => 'Cardio + strength',
        'Mountain climbers' => 'Core + cardio',
        'Rows' => 'Full body'
    ],
    'Yoga' => [
        'Sun salutations' => 'Flow warmup',
        'Warrior poses' => 'Strength + balance',
        'Tree pose' => 'Balance',
        'Cat-cow stretch' => 'Spine mobility',
        'Child pose' => 'Recovery',
        'Downward dog' => 'Full stretch'
    ],
    'Muscle Gain' => [
        'Deadlifts' => 'Heavy compound',
        'Bench press' => 'Chest mass',
        'Squats' => 'Leg mass',
        'Barbell rows' => 'Back thickness',
        'Overhead press' => 'Shoulder mass',
        'Bicep curls' => 'Isolation'
    ]
];

$recommended_exercises = $goal_exercises[$user_goal] ?? [
    'Push-ups' => 'Full body',
    'Planks' => 'Core stability',
    'Squats' => 'Lower body',
    'Lunges' => 'Balance'
];

$user_weights = [];
$user_dates = [];
$user_calories = [];

$chart_data = mysqli_query($conn, "SELECT * FROM progress WHERE user_id='$user_id' ORDER BY date ASC");
if($chart_data){
    while($row = mysqli_fetch_assoc($chart_data)){
        $user_weights[] = (float) $row['weight'];
        $user_calories[] = (int) $row['calories'];
        $user_dates[] = $row['date'];
    }
}

$history = [];
$data = mysqli_query($conn, "SELECT * FROM progress WHERE user_id='$user_id' ORDER BY date DESC");
if($data){
    while($row = mysqli_fetch_assoc($data)){
        $history[] = $row;
    }
}

if(isset($_POST['save'])){
    $weight = mysqli_real_escape_string($conn, $_POST['weight']);
    $calories = mysqli_real_escape_string($conn, $_POST['calories']);
    $date = date("Y-m-d");

    mysqli_query($conn, "INSERT INTO progress(user_id, weight, calories, date) VALUES('$user_id','$weight','$calories','$date')");
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
                Your goal is <strong style="color:#ff7a18;"><?php echo htmlspecialchars($user_goal ?: 'Not set'); ?></strong>.
                Recommended exercises based on your goal are shown below.
            </p>
        </section>

        <section class="app-card" style="border-color:rgba(69,212,131,0.4);">
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow" style="color:#45d483;">Recommended exercises</p>
                    <h2>Best for "<?php echo htmlspecialchars($user_goal ?: 'Fitness'); ?>"</h2>
                </div>
                <p>These exercises align with your selected goal for maximum results.</p>
            </div>
            <div class="app-grid-2">
                <?php foreach($recommended_exercises as $exercise => $desc){ ?>
                <div style="padding:16px; border-radius:16px; background:rgba(69,212,131,0.08); border:1px solid rgba(69,212,131,0.25);">
                    <h3 style="margin:0;font-size:18px;color:#45d483;"><?php echo htmlspecialchars($exercise); ?></h3>
                    <p style="margin:6px 0 0;color:#9cb2c7;font-size:14px;"><?php echo htmlspecialchars($desc); ?></p>
                </div>
                <?php } ?>
            </div>
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
        labels: <?php echo json_encode($user_dates); ?>,
        datasets: [
            {
                type: 'line',
                label: 'Weight',
                data: <?php echo json_encode($user_weights); ?>,
                borderColor: '#ff7a18',
                backgroundColor: 'rgba(255,122,24,0.18)',
                tension: 0.35,
                fill: true,
                yAxisID: 'y'
            },
            {
                type: 'bar',
                label: 'Calories',
                data: <?php echo json_encode($user_calories); ?>,
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
