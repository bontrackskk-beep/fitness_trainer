<?php
session_start();
include("db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

if(isset($_POST['goal'])){
    $goal = $_POST['goal'];
    $user_id = $_SESSION['user_id'];

    mysqli_query($conn, "UPDATE users SET goal='$goal' WHERE id='$user_id'");
    header("Location: dashboard.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$current_goal_result = mysqli_query($conn, "SELECT goal FROM users WHERE id='$user_id'");
$user_row = mysqli_fetch_assoc($current_goal_result);
$current_goal = $user_row['goal'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Goal</title>
    <link rel="stylesheet" href="style.css?v=20260418">
    <style>
        :root {
            --goal-bg: #08131d;
            --goal-bg-soft: #101f2d;
            --goal-panel: rgba(10, 21, 33, 0.82);
            --goal-panel-strong: rgba(13, 28, 43, 0.94);
            --goal-text: #f4f7fb;
            --goal-muted: #9cb2c7;
            --goal-accent: #ff7a18;
            --goal-accent-soft: #ffb347;
            --goal-line: rgba(255, 255, 255, 0.1);
            --goal-shadow: 0 28px 70px rgba(0, 0, 0, 0.34);
        }

        * {
            box-sizing: border-box;
        }

        body.goal-page {
            margin: 0;
            min-height: 100vh;
            font-family: 'Outfit', sans-serif;
            color: var(--goal-text);
            background:
                radial-gradient(circle at top left, rgba(255, 122, 24, 0.20), transparent 28%),
                radial-gradient(circle at 85% 15%, rgba(63, 210, 255, 0.14), transparent 24%),
                linear-gradient(135deg, #050b11 0%, #0a1521 48%, #0f2130 100%);
        }

        .goal-layout {
            width: min(1180px, calc(100% - 40px));
            margin: 0 auto;
            padding: 28px 0 56px;
        }

        .goal-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: 8px 0 28px;
        }

        .goal-brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .goal-brand-badge {
            width: 54px;
            height: 54px;
            border-radius: 16px;
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.94);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.22);
        }

        .goal-brand-badge img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .goal-brand-copy p,
        .goal-brand-copy h1,
        .goal-hero-copy p,
        .goal-hero-copy h2,
        .goal-section-head p,
        .goal-section-head h3,
        .goal-summary p,
        .goal-summary h3 {
            margin: 0;
        }

        .goal-brand-copy p,
        .goal-eyebrow {
            color: var(--goal-muted);
            text-transform: uppercase;
            letter-spacing: 2px;
            font-size: 12px;
        }

        .goal-brand-copy h1 {
            margin-top: 5px;
            font-size: 24px;
            font-weight: 800;
        }

        .goal-nav {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .goal-nav a {
            text-decoration: none;
            color: var(--goal-text);
            border: 1px solid var(--goal-line);
            background: rgba(255, 255, 255, 0.04);
            padding: 12px 18px;
            border-radius: 999px;
            font-weight: 600;
            transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease;
        }

        .goal-nav a:last-child {
            background: linear-gradient(135deg, var(--goal-accent-soft), var(--goal-accent));
            color: #111;
            border-color: transparent;
        }

        .goal-nav a:hover,
        .goal-card:hover {
            transform: translateY(-3px);
        }

        .goal-hero {
            display: grid;
            grid-template-columns: minmax(0, 1.1fr) minmax(340px, 0.9fr);
            gap: 24px;
            align-items: stretch;
        }

        .goal-hero-copy,
        .goal-summary,
        .goal-selector {
            border: 1px solid var(--goal-line);
            border-radius: 30px;
            background: var(--goal-panel);
            box-shadow: var(--goal-shadow);
            backdrop-filter: blur(16px);
        }

        .goal-hero-copy {
            padding: 40px;
            position: relative;
            overflow: hidden;
        }

        .goal-hero-copy::after {
            content: "";
            position: absolute;
            right: -70px;
            bottom: -90px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 122, 24, 0.32), transparent 66%);
        }

        .goal-hero-copy h2 {
            margin-top: 12px;
            max-width: 10ch;
            font-size: clamp(42px, 6vw, 74px);
            line-height: 0.95;
            letter-spacing: -2px;
        }

        .goal-hero-copy .goal-lead {
            margin-top: 20px;
            max-width: 54ch;
            font-size: 17px;
            line-height: 1.8;
            color: #c4d3e0;
        }

        .goal-highlight-row {
            margin-top: 28px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .goal-pill {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.05);
            color: #dce6ef;
            font-size: 14px;
        }

        .goal-pill strong {
            color: #fff;
        }

        .goal-summary {
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02)),
                var(--goal-panel-strong);
        }

        .goal-summary-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-top: 18px;
        }

        .goal-metric {
            padding: 18px;
            border-radius: 22px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        .goal-metric strong {
            display: block;
            font-size: 30px;
            margin-bottom: 4px;
        }

        .goal-metric span,
        .goal-summary-note {
            color: var(--goal-muted);
        }

        .goal-summary-note {
            margin-top: 20px;
            line-height: 1.7;
            font-size: 14px;
        }

        .goal-selector {
            margin-top: 24px;
            padding: 28px;
        }

        .goal-section-head {
            display: flex;
            justify-content: space-between;
            gap: 18px;
            align-items: end;
            margin-bottom: 22px;
        }

        .goal-section-head h3 {
            margin-top: 6px;
            font-size: 30px;
        }

        .goal-section-head .goal-mini-note {
            max-width: 32ch;
            color: var(--goal-muted);
            line-height: 1.6;
            font-size: 14px;
            text-align: right;
        }

        .goal-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .goal-card {
            width: 100%;
            min-height: 190px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 26px;
            padding: 24px;
            color: var(--goal-text);
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.06), rgba(255, 255, 255, 0.02)),
                rgba(8, 19, 29, 0.88);
            text-align: left;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        }

        .goal-card:hover {
            border-color: rgba(255, 179, 71, 0.7);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.28);
        }

        .goal-card--selected {
            border-color: #ff7a18 !important;
            box-shadow: 0 0 0 2px rgba(255, 122, 24, 0.4), 0 18px 40px rgba(0, 0, 0, 0.28);
            background: linear-gradient(180deg, rgba(255, 122, 24, 0.12), rgba(255, 255, 255, 0.04)), rgba(8, 19, 29, 0.88);
        }

        .goal-card--selected .goal-card-code {
            background: linear-gradient(135deg, rgba(255, 179, 71, 0.5), rgba(255, 122, 24, 0.5)) !important;
        }

        .goal-card--selected .goal-card-tag {
            border-color: #ff7a18;
            color: #ff7a18;
        }

        .goal-card--wide {
            grid-column: 1 / -1;
            min-height: 150px;
        }

        .goal-card-top {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: start;
        }

        .goal-card-code {
            width: 52px;
            height: 52px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(255, 179, 71, 0.22), rgba(255, 122, 24, 0.22));
            color: #ffd7a5;
            font-weight: 800;
            letter-spacing: 1px;
            flex-shrink: 0;
        }

        .goal-card-tag {
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 12px;
            color: #dce4ec;
            background: rgba(255, 255, 255, 0.04);
            white-space: nowrap;
        }

        .goal-card h4 {
            margin: 18px 0 10px;
            font-size: 24px;
        }

        .goal-card p {
            margin: 0;
            color: #b7c7d5;
            line-height: 1.7;
            font-size: 15px;
        }

        .goal-card-footer {
            margin-top: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            color: #f7d5ae;
            font-weight: 600;
            font-size: 14px;
        }

        .goal-card-footer span:last-child {
            color: #fff;
        }

        @media (max-width: 920px) {
            .goal-hero {
                grid-template-columns: 1fr;
            }

            .goal-hero-copy h2 {
                max-width: none;
            }

            .goal-grid {
                grid-template-columns: 1fr;
            }

            .goal-card--wide {
                grid-column: auto;
                min-height: 190px;
            }
        }

        @media (max-width: 640px) {
            .goal-layout {
                width: min(100% - 24px, 1180px);
                padding-top: 18px;
            }

            .goal-topbar,
            .goal-section-head,
            .goal-card-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .goal-hero-copy,
            .goal-summary,
            .goal-selector {
                padding: 22px;
                border-radius: 24px;
            }

            .goal-hero-copy h2 {
                font-size: 40px;
                line-height: 1.02;
            }

            .goal-summary-grid {
                grid-template-columns: 1fr;
            }

            .goal-section-head .goal-mini-note {
                text-align: left;
            }
        }
    </style>
</head>

<body class="goal-page">
<div class="goal-layout">
    <header class="goal-topbar">
        <div class="goal-brand">
            <div class="goal-brand-badge"><img src="assets/zenfit-logo.png" alt="Zen Fit logo"></div>
            <div class="goal-brand-copy">
                <p>Zen Fit</p>
                <h1>Choose Your Focus</h1>
            </div>
        </div>

        <nav class="goal-nav">
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <main>
        <section class="goal-hero">
            <div class="goal-hero-copy">
                <p class="goal-eyebrow">Goal selection</p>
                <h2>Build a plan that matches your body goal.</h2>
                <p class="goal-lead">
                    Choose the direction you want to push next and we will align the app around it,
                    from workouts and trainers to habit suggestions and recovery guidance.
                </p>

                <div class="goal-highlight-row">
                    <div class="goal-pill"><strong>5</strong> focused tracks</div>
                    <div class="goal-pill"><strong>Quick setup</strong> one tap to continue</div>
                    <div class="goal-pill"><strong>Flexible</strong> change anytime</div>
                </div>
            </div>

            <aside class="goal-summary">
                <div>
                    <p class="goal-eyebrow">Recommended flow</p>
                    <h3>Pick your priority, then let the dashboard adapt.</h3>
                    <div class="goal-summary-grid">
                        <div class="goal-metric">
                            <strong>01</strong>
                            <span>Select a track</span>
                        </div>
                        <div class="goal-metric">
                            <strong>05</strong>
                            <span>Tailored goal paths</span>
                        </div>
                    </div>
                </div>

                <p class="goal-summary-note">
                    A sharper goal page should feel premium and easy to trust, so this version
                    focuses on hierarchy, spacing, and clear decision cards instead of plain buttons.
                </p>
            </aside>
        </section>

        <section class="goal-selector">
            <div class="goal-section-head">
                <div>
                    <p class="goal-eyebrow">Choose one</p>
                    <h3>What are you training for right now?</h3>
                </div>
                <p class="goal-mini-note">Each card sends your selection immediately and updates your profile goal.</p>
            </div>

            <form method="POST" class="goal-grid">
                <button type="submit" name="goal" value="Weight Loss" class="goal-card<?php echo ($current_goal === 'Weight Loss') ? ' goal-card--selected' : ''; ?>">
                    <div>
                        <div class="goal-card-top">
                            <span class="goal-card-code">WL</span>
                            <span class="goal-card-tag">Lean and active</span>
                        </div>
                        <h4>Weight Loss</h4>
                        <p>Calorie-smart routines, cardio support, and sustainable fat-loss progression.</p>
                    </div>
                    <div class="goal-card-footer">
                        <span>Best for consistency</span>
                        <span><?php echo ($current_goal === 'Weight Loss') ? 'Selected' : 'Select Goal'; ?></span>
                    </div>
                </button>

                <button type="submit" name="goal" value="Weight Gain" class="goal-card<?php echo ($current_goal === 'Weight Gain') ? ' goal-card--selected' : ''; ?>">
                    <div>
                        <div class="goal-card-top">
                            <span class="goal-card-code">WG</span>
                            <span class="goal-card-tag">Fuel and recover</span>
                        </div>
                        <h4>Weight Gain</h4>
                        <p>Structured eating support and balanced muscle-friendly training for steady progress.</p>
                    </div>
                    <div class="goal-card-footer">
                        <span>Best for bulk phases</span>
                        <span><?php echo ($current_goal === 'Weight Gain') ? 'Selected' : 'Select Goal'; ?></span>
                    </div>
                </button>

                <button type="submit" name="goal" value="Fitness" class="goal-card<?php echo ($current_goal === 'Fitness') ? ' goal-card--selected' : ''; ?>">
                    <div>
                        <div class="goal-card-top">
                            <span class="goal-card-code">FT</span>
                            <span class="goal-card-tag">Balanced performance</span>
                        </div>
                        <h4>Fitness</h4>
                        <p>General strength, conditioning, mobility, and an all-round sustainable routine.</p>
                    </div>
                    <div class="goal-card-footer">
                        <span>Best everyday option</span>
                        <span><?php echo ($current_goal === 'Fitness') ? 'Selected' : 'Select Goal'; ?></span>
                    </div>
                </button>

                <button type="submit" name="goal" value="Yoga" class="goal-card<?php echo ($current_goal === 'Yoga') ? ' goal-card--selected' : ''; ?>">
                    <div>
                        <div class="goal-card-top">
                            <span class="goal-card-code">YG</span>
                            <span class="goal-card-tag">Mobility and control</span>
                        </div>
                        <h4>Yoga</h4>
                        <p>Flexibility, posture, breathwork, recovery, and a calmer training rhythm.</p>
                    </div>
                    <div class="goal-card-footer">
                        <span>Best for recovery</span>
                        <span><?php echo ($current_goal === 'Yoga') ? 'Selected' : 'Select Goal'; ?></span>
                    </div>
                </button>

                <button type="submit" name="goal" value="Muscle Gain" class="goal-card goal-card--wide<?php echo ($current_goal === 'Muscle Gain') ? ' goal-card--selected' : ''; ?>">
                    <div>
                        <div class="goal-card-top">
                            <span class="goal-card-code">MG</span>
                            <span class="goal-card-tag">Strength and size</span>
                        </div>
                        <h4>Muscle Gain</h4>
                        <p>Higher-volume lifting, progressive overload, and training structure built for stronger physiques.</p>
                    </div>
                    <div class="goal-card-footer">
                        <span>Best for hypertrophy focus</span>
                        <span><?php echo ($current_goal === 'Muscle Gain') ? 'Selected' : 'Select Goal'; ?></span>
                    </div>
                </button>
            </form>
        </section>
    </main>

    <?php include('footer.php'); ?>
</div>
</body>
</html>
