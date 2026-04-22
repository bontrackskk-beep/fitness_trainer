<?php
session_start();
include("db.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zen Fit | Home</title>
    <link rel="stylesheet" href="style.css?v=20260418">
</head>
<body>
<div class="app-shell">
    <header class="app-topbar">
        <div class="app-brand">
            <div class="app-brand-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div>
                <p>Zen Fit</p>
                <h1>Unified Fitness Platform</h1>
            </div>
        </div>

        <nav class="app-nav">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </nav>
    </header>

    <main class="app-grid">
        <section class="app-hero-panel">
            <p class="app-eyebrow">Elevate your routine</p>
            <h1>One cleaner place for goals, workouts, nutrition, and progress.</h1>
            <p>
                Start with a personalized goal, move into a premium dashboard, and manage your entire
                training flow through a consistent interface built for clarity and momentum.
            </p>
            <div class="app-nav" style="margin-top:24px;">
                <a href="login.php" class="app-button">Login</a>
                <a href="register.php" class="app-button-secondary">Create Account</a>
            </div>
        </section>

        <section class="app-section-head" style="margin-bottom:0;">
            <div>
                <p class="app-eyebrow">Platform highlights</p>
                <h2>Everything now follows the same design direction.</h2>
            </div>
            <p>Consistent styling makes the app feel more complete and easier to trust from page to page.</p>
        </section>

        <section class="app-grid-3">
            <article class="app-grid-card">
                <span class="app-chip">Goals</span>
                <h3 style="margin-top:16px;">Goal-based planning</h3>
                <p>Select the result you want and shape your dashboard around it.</p>
            </article>
            <article class="app-grid-card">
                <span class="app-chip">Tracking</span>
                <h3 style="margin-top:16px;">Visible progress</h3>
                <p>Track weight and calories with cleaner summaries and chart-driven feedback.</p>
            </article>
            <article class="app-grid-card">
                <span class="app-chip">Support</span>
                <h3 style="margin-top:16px;">Trainer and plan access</h3>
                <p>Browse workouts, nutrition plans, and trainer options in a connected experience.</p>
            </article>
        </section>
    </main>

    <?php include('footer.php'); ?>
</div>
</body>
</html>
