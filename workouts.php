<?php
session_start();
include('db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$workouts = [];
$res = mysqli_query($conn, "SELECT * FROM workouts ORDER BY id DESC");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        $workouts[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Workouts | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
</head>
<body>
<div class="app-shell">
    <header class="app-topbar">
        <div class="app-brand">
            <div class="app-brand-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div>
                <p>Zen Fit</p>
                <h1>Workout Library</h1>
            </div>
        </div>

        <nav class="app-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="app-grid">
        <section class="app-hero-panel">
            <p class="app-eyebrow">Training selection</p>
            <h1>Choose the session that fits your current goal.</h1>
            <p>
                Every workout now uses your actual database structure, including title, goal,
                difficulty, duration, exercises, and video link details.
            </p>
        </section>

        <section>
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow">Available routines</p>
                    <h2>Workout programs</h2>
                </div>
                <p>Use these sessions as your next training block and align them with your dashboard goal.</p>
            </div>

            <?php if(empty($workouts)){ ?>
            <div class="app-card">
                <p class="app-empty">No workouts are available yet.</p>
            </div>
            <?php } else { ?>
            <div class="app-grid-2">
                <?php foreach($workouts as $workout){ ?>
                <article class="app-grid-card">
                    <span class="app-chip"><?php echo htmlspecialchars($workout['goal']); ?></span>
                    <h3 style="margin-top:16px;"><?php echo htmlspecialchars($workout['title']); ?></h3>
                    <p><?php echo htmlspecialchars($workout['difficulty']); ?> level<?php if(!empty($workout['duration'])){ ?>, <?php echo htmlspecialchars($workout['duration']); ?><?php } ?></p>
                    <p><?php echo htmlspecialchars($workout['exercises']); ?></p>
                    <?php if(!empty($workout['video_link'])){ ?>
                    <div style="margin-top:18px;">
                        <a href="<?php echo htmlspecialchars($workout['video_link']); ?>" target="_blank" class="app-button-secondary">Open Video</a>
                    </div>
                    <?php } ?>
                </article>
                <?php } ?>
            </div>
            <?php } ?>
        </section>
    </main>

    <?php include('footer.php'); ?>
</div>
</body>
</html>
