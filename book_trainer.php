<?php
session_start();
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];
$trainer_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$success = null;

$trainer = null;
if($trainer_id > 0){
    $trainer_query = mysqli_query($conn, "SELECT * FROM trainers WHERE id='$trainer_id' LIMIT 1");
    if($trainer_query && mysqli_num_rows($trainer_query) > 0){
        $trainer = mysqli_fetch_assoc($trainer_query);
    }
}

if(isset($_POST['book']) && $trainer_id > 0){
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    $time = mysqli_real_escape_string($conn, $_POST['time']);

    mysqli_query($conn, "INSERT INTO bookings(user_id, trainer_id, date, time, status) VALUES('$user_id','$trainer_id','$date','$time','Booked')");
    $success = "Trainer booked successfully.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Trainer | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
</head>
<body>
<div class="app-shell">
    <header class="app-topbar">
        <div class="app-brand">
            <div class="app-brand-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div>
                <p>Zen Fit</p>
                <h1>Book Trainer</h1>
            </div>
        </div>

        <nav class="app-nav">
            <a href="trainers.php">Trainers</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main class="app-grid app-grid-2">
        <section class="app-hero-panel">
            <p class="app-eyebrow">Session booking</p>
            <h1>Reserve a trainer session in a cleaner flow.</h1>
            <p>
                Choose your date and time, confirm the booking, and keep your coaching support inside the same app experience.
            </p>
        </section>

        <section class="app-card">
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow">Booking form</p>
                    <h2><?php echo $trainer ? htmlspecialchars($trainer['name']) : 'Trainer not found'; ?></h2>
                </div>
                <p><?php echo $trainer ? htmlspecialchars($trainer['specialization']) : 'Use the trainers page to choose a valid trainer.'; ?></p>
            </div>

            <?php if($success){ ?>
            <div class="app-card" style="padding:16px; border-radius:20px; color:#d7ffe8; margin-bottom:18px;">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <?php } ?>

            <?php if($trainer){ ?>
            <form method="POST" class="app-form">
                <input type="date" name="date" class="app-input" required>
                <input type="time" name="time" class="app-input" required>
                <button type="submit" name="book" class="app-button">Confirm Booking</button>
            </form>
            <?php } else { ?>
            <p class="app-empty">No valid trainer selected.</p>
            <?php } ?>
        </section>
    </main>

    <?php include('footer.php'); ?>
</div>
</body>
</html>
