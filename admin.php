<?php
include('db.php');

$bookings = [];
$result = mysqli_query($conn, "SELECT * FROM bookings ORDER BY id DESC LIMIT 10");
if($result){
    while($row = mysqli_fetch_assoc($result)){
        $bookings[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
</head>
<body>
<div class="app-shell">
    <header class="app-topbar">
        <div class="app-brand">
            <div class="app-brand-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div>
                <p>Zen Fit</p>
                <h1>Admin Panel</h1>
            </div>
        </div>

        <nav class="app-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="app-grid">
        <section class="app-hero-panel">
            <p class="app-eyebrow">Management view</p>
            <h1>Review recent bookings in the same unified theme.</h1>
            <p>
                The admin screen now matches the rest of the platform while still surfacing the latest trainer bookings.
            </p>
        </section>

        <section class="app-card">
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow">Booking records</p>
                    <h2>Recent bookings</h2>
                </div>
                <p>Track booking activity without switching to a completely different UI style.</p>
            </div>

            <?php if(empty($bookings)){ ?>
            <p class="app-empty">No bookings found yet.</p>
            <?php } else { ?>
            <div class="app-table-wrap">
                <table class="app-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Trainer ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($bookings as $booking){ ?>
                        <tr>
                            <td><?php echo (int) $booking['user_id']; ?></td>
                            <td><?php echo (int) $booking['trainer_id']; ?></td>
                            <td><?php echo htmlspecialchars($booking['date']); ?></td>
                            <td><?php echo htmlspecialchars($booking['time']); ?></td>
                            <td><span class="app-chip"><?php echo htmlspecialchars($booking['status']); ?></span></td>
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
</body>
</html>
