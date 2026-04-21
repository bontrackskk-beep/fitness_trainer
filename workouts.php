<?php
session_start();
include('db.php');

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_goal = '';

$goal_query = mysqli_query($conn, "SELECT goal FROM users WHERE id='$user_id' LIMIT 1");
if($goal_query && mysqli_num_rows($goal_query) > 0){
    $user = mysqli_fetch_assoc($goal_query);
    $user_goal = $user['goal'];
}

$matching_workouts = [];
$other_workouts = [];

$res = mysqli_query($conn, "SELECT * FROM workouts ORDER BY id DESC");
if($res){
    while($row = mysqli_fetch_assoc($res)){
        if($user_goal && $row['goal'] == $user_goal){
            $matching_workouts[] = $row;
        } else {
            $other_workouts[] = $row;
        }
    }
}

$workouts = array_merge($matching_workouts, $other_workouts);
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
                Your goal is <strong style="color:#ff7a18;"><?php echo htmlspecialchars($user_goal ?: 'Not set'); ?></strong>.
                Workouts matching your goal are shown first.
            </p>
        </section>

        <?php if(!empty($matching_workouts)){ ?>
        <section>
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow" style="color:#45d483;">Recommended for you</p>
                    <h2>Workouts matching "<?php echo htmlspecialchars($user_goal); ?>"</h2>
                </div>
                <p>These workouts are aligned with your selected goal.</p>
            </div>

            <div class="app-grid-2">
                <?php foreach($matching_workouts as $workout){ ?>
                <article class="app-grid-card" style="border-color:rgba(69,212,131,0.4);">
                    <span class="app-chip" style="background:rgba(69,212,131,0.15);border-color:rgba(69,212,131,0.3);color:#45d483;"><?php echo htmlspecialchars($workout['goal']); ?> ✓</span>
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
        </section>
        <?php } ?>

        <?php if(empty($matching_workouts)){ ?>
        <section>
            <div class="app-section-head">
                <div>
                    <p class="app-eyebrow">Available routines</p>
                    <h2>Workout programs</h2>
                </div>
                <p>All training routines in your library.</p>
            </div>

            <div class="app-grid-2">
                <?php foreach($other_workouts as $workout){ ?>
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
        </section>
        <?php } ?>
    </main>

    <?php include('footer.php'); ?>
</div>
</body>
</html>
