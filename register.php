<?php
session_start();
include('db.php');

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $goal = mysqli_real_escape_string($conn, $_POST['goal']);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
    if($check && mysqli_num_rows($check) > 0){
        $error = "An account with this email already exists.";
    } else {
        $sql = "INSERT INTO users (name, email, password, goal) VALUES ('$name', '$email', '$pass', '$goal')";
        if(mysqli_query($conn, $sql)){
            header("Location: login.php");
            exit();
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Zen Fit</title>
    <link rel="stylesheet" href="style.css?v=20260418">
</head>
<body>
<div class="app-shell">
    <main class="app-auth">
        <section class="app-auth-side">
            <div>
                <p class="app-eyebrow">Create account</p>
                <h2>Start with a smarter training setup.</h2>
                <p>
                    Set up your account, choose your starting goal, and unlock the same upgraded
                    dashboard, workout, nutrition, and progress experience across the app.
                </p>
            </div>

            <div class="app-auth-highlights">
                <div class="app-auth-highlight">
                    <strong>Choose your path</strong>
                    Start with weight loss, muscle gain, fitness, or yoga.
                </div>
                <div class="app-auth-highlight">
                    <strong>Track meaningful progress</strong>
                    Build a history of weight and calorie data over time.
                </div>
                <div class="app-auth-highlight">
                    <strong>Explore trainers and plans</strong>
                    Get workouts, meal plans, and guidance in one place.
                </div>
            </div>
        </section>

        <section class="app-auth-panel">
            <div>
                <p class="app-eyebrow">New member</p>
                <h1>Create Account</h1>
                <p>Complete the form below to join the platform and personalize your experience.</p>
            </div>

            <?php if(isset($error)){ ?>
            <div class="app-card" style="margin-top:18px; padding:16px; border-radius:20px; color:#ffd4cf;">
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php } ?>

            <form action="" method="POST" class="app-form" style="margin-top:22px;">
                <input type="text" name="name" class="app-input" placeholder="Full name" required>
                <input type="email" name="email" class="app-input" placeholder="Email address" required>
                <input type="password" name="password" class="app-input" placeholder="Password" required>
                <select name="goal" class="app-select" required>
                    <option value="" disabled selected>Select your goal</option>
                    <option value="Weight Loss">Weight Loss</option>
                    <option value="Weight Gain">Weight Gain</option>
                    <option value="Fitness">Fitness</option>
                    <option value="Yoga">Yoga</option>
                    <option value="Muscle Gain">Muscle Gain</option>
                </select>
                <button type="submit" name="register" class="app-button">Create Account</button>
            </form>

            <p class="app-auth-footer">Already registered? <a href="login.php">Login here</a></p>
        </section>
    </main>

    <?php include('footer.php'); ?>
</div>
</body>
</html>
