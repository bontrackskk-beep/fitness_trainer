<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("db.php");

echo "Step 1: db included <br>";

if(isset($_POST['login'])){
    echo "Step 2: form submitted <br>";

    $email = $_POST['email'];
    $password = $_POST['password'];

    echo "Step 3: got form values <br>";

    $query = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' LIMIT 1");

    if(!$query){
        die("SQL Error: " . mysqli_error($conn));
    }

    echo "Step 4: query success <br>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
</head>
<body>
<div class="app-shell">
    <main class="app-auth">
        <section class="app-auth-side">
            <div>
                <p class="app-eyebrow">Welcome back</p>
                <h2>Train with one clean system.</h2>
                <p>
                    Log in to manage your goal, workouts, diet plan, progress chart, and trainer sessions
                    from the same premium dashboard experience.
                </p>
            </div>

            <div class="app-auth-highlights">
                <div class="app-auth-highlight">
                    <strong>Goal-based planning</strong>
                    Your dashboard adapts to weight loss, fitness, yoga, or muscle gain priorities.
                </div>
                <div class="app-auth-highlight">
                    <strong>Progress visibility</strong>
                    Track weight and calories with charts, quick stats, and better weekly context.
                </div>
                <div class="app-auth-highlight">
                    <strong>One theme everywhere</strong>
                    The app now uses the same visual language across internal pages and authentication.
                </div>
            </div>
        </section>

        <section class="app-auth-panel">
            <div>
                <p class="app-eyebrow">Account access</p>
                <h1>Login</h1>
                <p>Use your email and password to continue to your fitness workspace.</p>
            </div>

            <?php if(isset($error)){ ?>
            <div class="app-card" style="margin-top:18px; padding:16px; border-radius:20px; color:#ffd4cf;">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php } ?>

            <form method="POST" class="app-form" style="margin-top:22px;">
                <input type="email" name="email" class="app-input" placeholder="Email address" required>
                <input type="password" name="password" class="app-input" placeholder="Password" required>
                <button type="submit" name="login" class="app-button">Login</button>
            </form>

            <p class="app-auth-footer">New here? <a href="register.php">Create an account</a></p>
        </section>
    </main>

    <!-- footer removed temporarily -->
</div>
</body>
</html>
