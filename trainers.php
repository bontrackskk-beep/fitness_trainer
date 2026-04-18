<?php
session_start();
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$trainers = [];
$result = mysqli_query($conn, "SELECT * FROM trainers ORDER BY rating DESC");
if($result){
    while($row = mysqli_fetch_assoc($result)){
        $trainers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
</head>
<body>
<div class="app-shell">
    <header class="app-topbar">
        <div class="app-brand">
            <div class="app-brand-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div>
                <p>Zen Fit</p>
                <h1>Available Trainers</h1>
            </div>
        </div>

        <nav class="app-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="app-grid">
        <section class="app-hero-panel">
            <p class="app-eyebrow">Coaching support</p>
            <h1>Find the right trainer for your current phase.</h1>
            <p>
                Compare specialization, experience, rating, and price, then book a session
                through the corrected trainer booking flow.
            </p>
        </section>

        <section>
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow">Trainer roster</p>
                    <h2>Choose a coach</h2>
                </div>
                <p>Book directly from these cards and keep your guidance workflow inside the app.</p>
            </div>

            <?php if(empty($trainers)){ ?>
            <div class="app-card">
                <p class="app-empty">No trainers are available yet.</p>
            </div>
            <?php } else { ?>
            <div class="app-grid-3">
                <?php foreach($trainers as $trainer){ ?>
                <article class="app-grid-card">
                    <span class="app-chip"><?php echo htmlspecialchars($trainer['specialization']); ?></span>
                    <h3 style="margin-top:16px;"><?php echo htmlspecialchars($trainer['name']); ?></h3>
                    <p><?php echo htmlspecialchars($trainer['experience']); ?> experience</p>
                    <p>Rating: <?php echo htmlspecialchars($trainer['rating']); ?>/5</p>
                    <p>Price: Rs. <?php echo htmlspecialchars($trainer['price']); ?></p>
                    <div style="margin-top:18px;">
                        <a href="book_trainer.php?id=<?php echo (int) $trainer['id']; ?>" class="app-button-secondary">Book Trainer</a>
                    </div>
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
