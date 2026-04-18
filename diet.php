<?php
session_start();
include('db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$plans = [];
$res = mysqli_query($conn, "SELECT * FROM diet_plans ORDER BY id DESC");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        $plans[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diet Plan | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
</head>
<body>
<div class="app-shell">
    <header class="app-topbar">
        <div class="app-brand">
            <div class="app-brand-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div>
                <p>Zen Fit</p>
                <h1>Nutrition Plans</h1>
            </div>
        </div>

        <nav class="app-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="app-grid">
        <section class="app-hero-panel">
            <p class="app-eyebrow">Fuel your progress</p>
            <h1>Build recovery and body goals through better meal structure.</h1>
            <p>
                These diet plans now read the actual database columns: goal, calories, meals, and schedule.
                That keeps the page consistent and working with your current schema.
            </p>
        </section>

        <section>
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow">Meal guidance</p>
                    <h2>Available diet plans</h2>
                </div>
                <p>Use these plans to support your current goal with better calorie structure and meal timing.</p>
            </div>

            <?php if(empty($plans)){ ?>
            <div class="app-card">
                <p class="app-empty">No diet plans are available yet.</p>
            </div>
            <?php } else { ?>
            <div class="app-grid-2">
                <?php foreach($plans as $plan){ ?>
                <article class="app-grid-card">
                    <span class="app-chip"><?php echo htmlspecialchars($plan['goal']); ?></span>
                    <h3 style="margin-top:16px;"><?php echo htmlspecialchars($plan['schedule']); ?></h3>
                    <p><strong style="color:var(--app-text);"><?php echo htmlspecialchars($plan['calories']); ?> kcal</strong> target intake</p>
                    <p><?php echo htmlspecialchars($plan['meals']); ?></p>
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
