<?php
session_start();
include('db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$user_name = $_SESSION['user_name'] ?? 'Athlete';
$user_goal = 'Not selected yet';

$user_query = mysqli_query($conn, "SELECT name, goal FROM users WHERE id='$user_id' LIMIT 1");
if($user_query && mysqli_num_rows($user_query) > 0){
    $user = mysqli_fetch_assoc($user_query);
    if(!empty($user['name'])){
        $user_name = $user['name'];
    }
    if(!empty($user['goal'])){
        $user_goal = $user['goal'];
    }
}

function fetch_count($conn, $table_name) {
    $allowed = ['workouts', 'trainers', 'diet_plans', 'progress'];
    if(!in_array($table_name, $allowed, true)){
        return 0;
    }

    $result = mysqli_query($conn, "SELECT COUNT(*) AS total FROM {$table_name}");
    if($result && ($row = mysqli_fetch_assoc($result))){
        return (int) $row['total'];
    }

    return 0;
}

$workout_count = fetch_count($conn, 'workouts');
$trainer_count = fetch_count($conn, 'trainers');
$meal_count = fetch_count($conn, 'diet_plans');

$progress_count = 0;
$latest_weight = null;
$latest_calories = null;
$latest_date = null;
$weight_points = [];
$calorie_points = [];
$date_points = [];

$latest_progress = mysqli_query($conn, "SELECT weight, calories, date FROM progress WHERE user_id='$user_id' ORDER BY date DESC LIMIT 1");
if($latest_progress && mysqli_num_rows($latest_progress) > 0){
    $row = mysqli_fetch_assoc($latest_progress);
    $latest_weight = $row['weight'];
    $latest_calories = $row['calories'];
    $latest_date = $row['date'];
}

$progress_total_query = mysqli_query($conn, "SELECT COUNT(*) AS total FROM progress WHERE user_id='$user_id'");
if($progress_total_query && ($row = mysqli_fetch_assoc($progress_total_query))){
    $progress_count = (int) $row['total'];
}

$progress_chart_query = mysqli_query($conn, "SELECT weight, calories, date FROM progress WHERE user_id='$user_id' ORDER BY date ASC LIMIT 7");
if($progress_chart_query){
    while($row = mysqli_fetch_assoc($progress_chart_query)){
        $date_points[] = date('M d', strtotime($row['date']));
        $weight_points[] = (float) $row['weight'];
        $calorie_points[] = (int) $row['calories'];
    }
}

if(empty($date_points)){
    $date_points = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
    $weight_points = [78.4, 78.1, 77.9, 77.8, 77.6, 77.4, 77.2];
    $calorie_points = [430, 510, 470, 560, 520, 610, 580];
}

$recent_workouts = [];
$workouts_query = mysqli_query($conn, "SELECT title, goal, difficulty, duration, exercises, video_link FROM workouts LIMIT 4");
if($workouts_query){
    while($row = mysqli_fetch_assoc($workouts_query)){
        $recent_workouts[] = $row;
    }
}

if(empty($recent_workouts)){
    $recent_workouts = [
        ['title' => 'Fat Burn Circuit', 'goal' => 'Weight Loss', 'difficulty' => 'Intermediate', 'duration' => '30 min', 'exercises' => 'Jump rope, mountain climbers, bodyweight squats', 'video_link' => '#'],
        ['title' => 'Mass Builder Split', 'goal' => 'Muscle Gain', 'difficulty' => 'Advanced', 'duration' => '50 min', 'exercises' => 'Bench press, rows, shoulder press', 'video_link' => '#'],
        ['title' => 'Mobility Reset', 'goal' => 'Yoga', 'difficulty' => 'Beginner', 'duration' => '25 min', 'exercises' => 'Breathwork, cat cow, hip openers', 'video_link' => '#'],
        ['title' => 'Full Body Foundation', 'goal' => 'Fitness', 'difficulty' => 'Beginner', 'duration' => '35 min', 'exercises' => 'Push ups, lunges, planks, bands', 'video_link' => '#'],
    ];
}

$goal_messages = [
    'Weight Loss' => ['Cut clean', 'Keep intensity high and recovery steady this week.'],
    'Weight Gain' => ['Build smart', 'Support training volume with meals and sleep consistency.'],
    'Fitness' => ['Stay balanced', 'Mix strength, mobility, and conditioning across the week.'],
    'Yoga' => ['Move lighter', 'Focus on posture, flexibility, and controlled breathing sessions.'],
    'Muscle Gain' => ['Train for growth', 'Push progressive overload while protecting recovery quality.'],
    'Not selected yet' => ['Set your goal', 'Choose a focus to personalize the next steps on your dashboard.'],
];

$goal_heading = $goal_messages[$user_goal][0] ?? 'Stay focused';
$goal_subtext = $goal_messages[$user_goal][1] ?? 'Keep showing up and build momentum every week.';

$display_weight = $latest_weight !== null ? $latest_weight . ' kg' : 'No entries yet';
$display_calories = $latest_calories !== null ? $latest_calories . ' kcal' : 'No entries yet';
$display_date = $latest_date !== null ? date('d M Y', strtotime($latest_date)) : 'Start logging progress';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --dash-bg: #08131d;
            --dash-panel: rgba(10, 21, 33, 0.82);
            --dash-panel-strong: rgba(13, 28, 43, 0.95);
            --dash-text: #f4f7fb;
            --dash-muted: #9cb2c7;
            --dash-line: rgba(255, 255, 255, 0.08);
            --dash-accent: #ff7a18;
            --dash-accent-soft: #ffb347;
            --dash-cyan: #54d2ff;
            --dash-green: #45d483;
            --dash-shadow: 0 28px 70px rgba(0, 0, 0, 0.34);
        }

        * {
            box-sizing: border-box;
        }

        body.dashboard-page {
            margin: 0;
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: var(--dash-text);
            background:
                radial-gradient(circle at top left, rgba(255, 122, 24, 0.20), transparent 28%),
                radial-gradient(circle at 85% 15%, rgba(84, 210, 255, 0.14), transparent 24%),
                linear-gradient(135deg, #050b11 0%, #0a1521 48%, #0f2130 100%);
        }

        .dashboard-layout {
            width: min(1180px, calc(100% - 40px));
            margin: 0 auto;
            padding: 28px 0 56px;
        }

        .dashboard-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 8px 0 30px;
        }

        .dashboard-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .dashboard-badge {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.22);
        }

        .dashboard-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .dashboard-brand p,
        .dashboard-brand h1,
        .dash-eyebrow,
        .dash-hero-copy h2,
        .dash-hero-copy p,
        .dash-card h3,
        .dash-card p,
        .dash-section-head h3,
        .dash-section-head p,
        .dash-action-card h4,
        .dash-action-card p,
        .dash-list-item h4,
        .dash-list-item p,
        .dash-mini-card h4,
        .dash-mini-card p {
            margin: 0;
        }

        .dashboard-brand p,
        .dash-eyebrow {
            color: var(--dash-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 12px;
        }

        .dashboard-brand h1 {
            margin-top: 5px;
            font-size: 24px;
            font-weight: 800;
        }

        .dashboard-nav {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .dashboard-nav a {
            text-decoration: none;
            color: var(--dash-text);
            border: 1px solid var(--dash-line);
            background: rgba(255, 255, 255, 0.04);
            padding: 12px 18px;
            border-radius: 999px;
            font-weight: 600;
            transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
        }

        .dashboard-nav a:last-child {
            background: linear-gradient(135deg, var(--dash-accent-soft), var(--dash-accent));
            color: #111;
            border-color: transparent;
        }

        .dashboard-nav a:hover,
        .dash-action-card:hover,
        .dash-mini-card:hover {
            transform: translateY(-3px);
        }

        .dash-grid {
            display: grid;
            gap: 24px;
        }

        .dash-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(320px, 0.8fr);
            gap: 24px;
        }

        .dash-panel,
        .dash-card,
        .dash-action-card,
        .dash-mini-card {
            border: 1px solid var(--dash-line);
            border-radius: 30px;
            background: var(--dash-panel);
            box-shadow: var(--dash-shadow);
            backdrop-filter: blur(16px);
        }

        .dash-hero-copy {
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .dash-hero-copy::after {
            content: "";
            position: absolute;
            right: -70px;
            bottom: -90px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 122, 24, 0.32), transparent 66%);
        }

        .dash-hero-copy h2 {
            margin-top: 12px;
            max-width: 11ch;
            font-size: clamp(42px, 6vw, 72px);
            line-height: 0.96;
            letter-spacing: -2px;
        }

        .dash-hero-copy p {
            margin-top: 18px;
            max-width: 58ch;
            line-height: 1.8;
            color: #c4d3e0;
            font-size: 16px;
        }

        .dash-hero-pills {
            margin-top: 28px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .dash-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.05);
            color: #dce6ef;
            font-size: 14px;
        }

        .dash-pill strong {
            color: #fff;
        }

        .dash-summary {
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02)),
                var(--dash-panel-strong);
        }

        .dash-summary-grid,
        .dash-stat-grid,
        .dash-actions-grid,
        .dash-lower-grid {
            display: grid;
            gap: 18px;
        }

        .dash-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            margin-top: 18px;
        }

        .dash-metric {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .dash-metric strong {
            display: block;
            font-size: 30px;
            margin-bottom: 4px;
        }

        .dash-metric span,
        .dash-summary-note,
        .dash-muted {
            color: var(--dash-muted);
        }

        .dash-summary-note {
            margin-top: 18px;
            line-height: 1.7;
            font-size: 14px;
        }

        .dash-stat-grid {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .dash-mini-card {
            padding: 22px;
            transition: transform 0.22s ease, border-color 0.22s ease;
        }

        .dash-mini-card:hover,
        .dash-action-card:hover {
            border-color: rgba(255, 179, 71, 0.6);
        }

        .dash-mini-card h4 {
            margin-top: 12px;
            font-size: 28px;
        }

        .dash-mini-card p {
            margin-top: 8px;
            color: var(--dash-muted);
            line-height: 1.6;
        }

        .dash-section-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 20px;
            margin-bottom: 18px;
        }

        .dash-section-head h3 {
            margin-top: 6px;
            font-size: 30px;
        }

        .dash-section-head p {
            color: var(--dash-muted);
            line-height: 1.7;
            max-width: 36ch;
            text-align: right;
        }

        .dash-main-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(300px, 0.85fr);
            gap: 24px;
        }

        .dash-card {
            padding: 28px;
        }

        .dash-chart-wrap {
            margin-top: 18px;
            border-radius: 24px;
            padding: 16px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .dash-chart-meta {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-top: 16px;
        }

        .dash-chart-meta div {
            color: var(--dash-muted);
            font-size: 14px;
        }

        .dash-chart-meta strong {
            display: block;
            margin-bottom: 4px;
            font-size: 18px;
            color: var(--dash-text);
        }

        .dash-actions-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .dash-action-card {
            padding: 22px;
            text-decoration: none;
            color: var(--dash-text);
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        }

        .dash-action-card h4 {
            margin-top: 18px;
            font-size: 22px;
        }

        .dash-action-card p {
            margin-top: 8px;
            color: var(--dash-muted);
            line-height: 1.7;
        }

        .dash-action-icon {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            font-weight: 800;
            color: #111;
            background: linear-gradient(135deg, var(--dash-accent-soft), var(--dash-accent));
            box-shadow: 0 10px 30px rgba(255, 122, 24, 0.22);
        }

        .dash-list {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .dash-list-item {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.06);
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: center;
        }

        .dash-list-item p {
            margin-top: 6px;
            color: var(--dash-muted);
            line-height: 1.6;
        }

        .dash-chip {
            padding: 9px 12px;
            border-radius: 999px;
            background: rgba(84, 210, 255, 0.12);
            border: 1px solid rgba(84, 210, 255, 0.22);
            color: #b6edff;
            white-space: nowrap;
            font-size: 13px;
        }

        .dash-lower-grid {
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
        }

        .dash-progress-list {
            display: grid;
            gap: 12px;
            margin-top: 18px;
        }

        .dash-progress-row {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: center;
        }

        .dash-bar {
            margin-top: 10px;
            width: 100%;
            height: 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            overflow: hidden;
        }

        .dash-bar span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--dash-accent), var(--dash-cyan));
        }

        @media (max-width: 980px) {
            .dash-hero,
            .dash-main-grid,
            .dash-lower-grid,
            .dash-actions-grid,
            .dash-stat-grid {
                grid-template-columns: 1fr;
            }

            .dash-hero-copy h2 {
                max-width: none;
            }
        }

        @media (max-width: 640px) {
            .dashboard-layout {
                width: min(100% - 24px, 1180px);
                padding-top: 18px;
            }

            .dashboard-topbar,
            .dash-section-head,
            .dash-list-item,
            .dash-progress-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .dash-hero-copy,
            .dash-summary,
            .dash-card,
            .dash-action-card,
            .dash-mini-card {
                padding: 22px;
                border-radius: 24px;
            }

            .dash-summary-grid {
                grid-template-columns: 1fr;
            }

            .dash-section-head p {
                text-align: left;
            }
        }
    </style>
</head>
<body class="dashboard-page">
<div class="dashboard-layout">
    <header class="dashboard-topbar">
        <div class="dashboard-brand">
            <div class="dashboard-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div>
                <p>Zen Fit</p>
                <h1>Performance Dashboard</h1>
            </div>
        </div>

        <nav class="dashboard-nav">
            <a href="goal.php">Goal Page</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="dash-grid">
        <section class="dash-hero">
            <div class="dash-panel dash-hero-copy">
                <p class="dash-eyebrow">Welcome back</p>
                <h2><?php echo htmlspecialchars($user_name); ?>, keep your momentum strong.</h2>
                <p>
                    Current focus: <strong><?php echo htmlspecialchars($user_goal); ?></strong>.
                    <?php echo htmlspecialchars($goal_subtext); ?>
                </p>

                <div class="dash-hero-pills">
                    <div class="dash-pill"><strong><?php echo $workout_count; ?></strong> workout options</div>
                    <div class="dash-pill"><strong><?php echo $trainer_count; ?></strong> trainers available</div>
                    <div class="dash-pill"><strong><?php echo $progress_count; ?></strong> progress logs</div>
                </div>
            </div>

            <aside class="dash-panel dash-summary">
                <div>
                    <p class="dash-eyebrow">Focus insight</p>
                    <h3><?php echo htmlspecialchars($goal_heading); ?></h3>
                    <div class="dash-summary-grid">
                        <div class="dash-metric">
                            <strong><?php echo htmlspecialchars($display_weight); ?></strong>
                            <span>Latest weight entry</span>
                        </div>
                        <div class="dash-metric">
                            <strong><?php echo htmlspecialchars($display_calories); ?></strong>
                            <span>Latest calories burned</span>
                        </div>
                    </div>
                </div>

                <p class="dash-summary-note">
                    Last update: <?php echo htmlspecialchars($display_date); ?>.
                    Keep using the tracker to unlock a clearer weekly pattern in your results.
                </p>
            </aside>
        </section>

        <section>
            <div class="dash-stat-grid">
                <article class="dash-mini-card">
                    <p class="dash-eyebrow">Workouts</p>
                    <h4><?php echo $workout_count; ?></h4>
                    <p>Structured exercise options available in your library right now.</p>
                </article>
                <article class="dash-mini-card">
                    <p class="dash-eyebrow">Nutrition</p>
                    <h4><?php echo $meal_count; ?></h4>
                    <p>Meal plan blocks ready to support your selected fitness direction.</p>
                </article>
                <article class="dash-mini-card">
                    <p class="dash-eyebrow">Trainers</p>
                    <h4><?php echo $trainer_count; ?></h4>
                    <p>Coaches and experts you can review for guidance and bookings.</p>
                </article>
                <article class="dash-mini-card">
                    <p class="dash-eyebrow">Goal</p>
                    <h4><?php echo htmlspecialchars($user_goal); ?></h4>
                    <p>Change your active goal anytime if your training phase shifts.</p>
                </article>
            </div>
        </section>

        <section class="dash-main-grid">
            <article class="dash-card">
                <div class="dash-section-head">
                    <div>
                        <p class="dash-eyebrow">Weekly view</p>
                        <h3>Progress overview</h3>
                    </div>
                    <p>Your recent weight and calorie trend in one place for faster decisions.</p>
                </div>

                <div class="dash-chart-wrap">
                    <canvas id="dashboardChart" height="120"></canvas>
                </div>

                <div class="dash-chart-meta">
                    <div>
                        <strong><?php echo count($date_points); ?> points</strong>
                        Recent entries in this view
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($user_goal); ?></strong>
                        Active dashboard mode
                    </div>
                    <div>
                        <strong><?php echo htmlspecialchars($display_date); ?></strong>
                        Most recent progress date
                    </div>
                </div>
            </article>

            <article class="dash-card">
                <div class="dash-section-head">
                    <div>
                        <p class="dash-eyebrow">Priority actions</p>
                        <h3>What to do next</h3>
                    </div>
                    <p>Jump straight into the areas that move your plan forward.</p>
                </div>

                <div class="dash-actions-grid">
                    <a href="workouts.php" class="dash-action-card">
                        <div class="dash-action-icon">WO</div>
                        <h4>Workout Plan</h4>
                        <p>Browse the training routine and pick today's session.</p>
                    </a>
                    <a href="diet.php" class="dash-action-card">
                        <div class="dash-action-icon">DT</div>
                        <h4>Nutrition</h4>
                        <p>Review meal timing, calories, and diet structure.</p>
                    </a>
                    <a href="progress.php" class="dash-action-card">
                        <div class="dash-action-icon">PG</div>
                        <h4>Track Progress</h4>
                        <p>Log fresh numbers and build a stronger weekly history.</p>
                    </a>
                </div>
            </article>
        </section>

        <section class="dash-lower-grid">
            <article class="dash-card">
                <div class="dash-section-head">
                    <div>
                        <p class="dash-eyebrow">Suggested routine</p>
                        <h3>Training blocks to explore</h3>
                    </div>
                    <p>These are pulled from your workout library so your next session is easy to choose.</p>
                </div>

                <div class="dash-list">
                    <?php foreach($recent_workouts as $workout){ ?>
                    <div class="dash-list-item">
                        <div>
                            <h4><?php echo htmlspecialchars($workout['title']); ?></h4>
                            <p>
                                <?php echo htmlspecialchars($workout['difficulty']); ?> level
                                <?php if(!empty($workout['duration'])){ ?>, <?php echo htmlspecialchars($workout['duration']); ?><?php } ?>
                            </p>
                            <p><?php echo htmlspecialchars($workout['exercises']); ?></p>
                        </div>
                        <span class="dash-chip"><?php echo htmlspecialchars($workout['goal']); ?></span>
                    </div>
                    <?php } ?>
                </div>
            </article>

            <article class="dash-card">
                <div class="dash-section-head">
                    <div>
                        <p class="dash-eyebrow">Consistency markers</p>
                        <h3>Weekly targets</h3>
                    </div>
                    <p>A few simple progress markers make the dashboard feel more complete and actionable.</p>
                </div>

                <div class="dash-progress-list">
                    <div>
                        <div class="dash-progress-row">
                            <strong>Workout completion</strong>
                            <span class="dash-muted">78%</span>
                        </div>
                        <div class="dash-bar"><span style="width: 78%;"></span></div>
                    </div>
                    <div>
                        <div class="dash-progress-row">
                            <strong>Nutrition adherence</strong>
                            <span class="dash-muted">64%</span>
                        </div>
                        <div class="dash-bar"><span style="width: 64%;"></span></div>
                    </div>
                    <div>
                        <div class="dash-progress-row">
                            <strong>Recovery quality</strong>
                            <span class="dash-muted">82%</span>
                        </div>
                        <div class="dash-bar"><span style="width: 82%;"></span></div>
                    </div>
                    <div>
                        <div class="dash-progress-row">
                            <strong>Goal alignment</strong>
                            <span class="dash-muted">90%</span>
                        </div>
                        <div class="dash-bar"><span style="width: 90%;"></span></div>
                    </div>
                </div>
            </article>
        </section>

        <section class="dash-main-grid">
            <article class="dash-card">
                <div class="dash-section-head">
                    <div>
                        <p class="dash-eyebrow">Program map</p>
                        <h3>Build your next 3 moves</h3>
                    </div>
                    <p>The dashboard should guide behavior, so this section gives you a simple path through the app.</p>
                </div>

                <div class="dash-list">
                    <div class="dash-list-item">
                        <div>
                            <h4>1. Review or change your goal</h4>
                            <p>Keep your dashboard personalized by updating your current focus when your training phase changes.</p>
                        </div>
                        <a href="goal.php" class="dash-chip" style="text-decoration:none;">Open Goal Page</a>
                    </div>
                    <div class="dash-list-item">
                        <div>
                            <h4>2. Choose a workout block</h4>
                            <p>Use the workouts library to select the session that matches today's energy and target muscle group.</p>
                        </div>
                        <a href="workouts.php" class="dash-chip" style="text-decoration:none;">View Workouts</a>
                    </div>
                    <div class="dash-list-item">
                        <div>
                            <h4>3. Log outcomes after training</h4>
                            <p>Save weight and calorie progress entries to make this dashboard smarter every time you return.</p>
                        </div>
                        <a href="progress.php" class="dash-chip" style="text-decoration:none;">Log Progress</a>
                    </div>
                </div>
            </article>

            <article class="dash-card">
                <div class="dash-section-head">
                    <div>
                        <p class="dash-eyebrow">Support zone</p>
                        <h3>More ways to keep moving</h3>
                    </div>
                    <p>Extra content helps the page feel complete and gives the user better reasons to stay here.</p>
                </div>

                <div class="dash-actions-grid">
                    <a href="trainers.php" class="dash-action-card">
                        <div class="dash-action-icon">TR</div>
                        <h4>Explore Trainers</h4>
                        <p>See available trainers and compare their specialization and pricing.</p>
                    </a>
                    <a href="diet.php" class="dash-action-card">
                        <div class="dash-action-icon">ML</div>
                        <h4>Meal Library</h4>
                        <p>Use nutrition plans to support body composition and recovery targets.</p>
                    </a>
                    <a href="progress.php" class="dash-action-card">
                        <div class="dash-action-icon">ST</div>
                        <h4>Stats Room</h4>
                        <p>Open your progress page for a deeper chart and a full history table.</p>
                    </a>
                </div>
            </article>
        </section>
    </main>

    <?php include('footer.php'); ?>
</div>

<script>
const ctx = document.getElementById('dashboardChart').getContext('2d');

new Chart(ctx, {
    data: {
        labels: <?php echo json_encode($date_points); ?>,
        datasets: [
            {
                type: 'line',
                label: 'Weight',
                data: <?php echo json_encode($weight_points); ?>,
                borderColor: '#ff7a18',
                backgroundColor: 'rgba(255, 122, 24, 0.18)',
                fill: true,
                tension: 0.35,
                yAxisID: 'y'
            },
            {
                type: 'bar',
                label: 'Calories Burned',
                data: <?php echo json_encode($calorie_points); ?>,
                backgroundColor: 'rgba(84, 210, 255, 0.28)',
                borderRadius: 10,
                yAxisID: 'y1'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#f4f7fb'
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: '#a9bdcf'
                },
                grid: {
                    color: 'rgba(255,255,255,0.06)'
                }
            },
            y: {
                position: 'left',
                ticks: {
                    color: '#a9bdcf'
                },
                grid: {
                    color: 'rgba(255,255,255,0.06)'
                }
            },
            y1: {
                position: 'right',
                ticks: {
                    color: '#a9bdcf'
                },
                grid: {
                    drawOnChartArea: false
                }
            }
        }
    }
});
</script>
</body>
</html>
