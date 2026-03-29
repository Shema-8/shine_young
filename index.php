<?php
session_start();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHINE YOUNG – Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --gold: #D4AF37;
            --gold-light: #F0D060;
            --dark: #0F1923;
            --dark2: #1A2635;
            --card: #ffffff;
            --text: #2d3748;
            --muted: #718096;
            --error: #e53e3e;
            --success: #38a169;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Animated background */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse 80% 60% at 20% 30%, rgba(212,175,55,0.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 80% 70%, rgba(212,175,55,0.08) 0%, transparent 60%);
            pointer-events: none;
        }

        .bg-pattern {
            position: fixed;
            inset: 0;
            background-image: 
                repeating-linear-gradient(
                    45deg,
                    transparent,
                    transparent 60px,
                    rgba(212,175,55,0.03) 60px,
                    rgba(212,175,55,0.03) 61px
                );
            pointer-events: none;
        }

        .page {
            display: flex;
            width: 100%;
            max-width: 960px;
            min-height: 560px;
            margin: 20px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
        }

        /* Left panel */
        .brand-panel {
            flex: 1;
            background: linear-gradient(145deg, var(--dark2) 0%, #0d1520 100%);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border-right: 1px solid rgba(212,175,55,0.15);
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212,175,55,0.15) 0%, transparent 70%);
            bottom: -80px;
            right: -80px;
        }

        .logo-mark {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .logo-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 8px 24px rgba(212,175,55,0.3);
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            color: white;
            letter-spacing: 0.5px;
        }

        .logo-text span {
            color: var(--gold);
        }

        .brand-content {
            position: relative;
            z-index: 1;
        }

        .brand-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 38px;
            font-weight: 900;
            color: white;
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .brand-content h2 em {
            font-style: italic;
            color: var(--gold);
        }

        .brand-content p {
            color: rgba(255,255,255,0.55);
            font-size: 15px;
            line-height: 1.7;
            max-width: 280px;
        }

        .brand-tags {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 28px;
        }

        .tag {
            background: rgba(212,175,55,0.12);
            border: 1px solid rgba(212,175,55,0.25);
            color: var(--gold-light);
            font-size: 12px;
            padding: 6px 14px;
            border-radius: 100px;
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .brand-footer {
            color: rgba(255,255,255,0.3);
            font-size: 12px;
        }

        /* Right panel – form */
        .form-panel {
            flex: 1.1;
            background: var(--card);
            padding: 60px 56px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 36px;
        }

        .form-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }

        .form-header p {
            color: var(--muted);
            font-size: 14px;
        }

        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 24px;
        }

        .alert-error {
            background: #FFF5F5;
            border: 1px solid #FED7D7;
            color: var(--error);
        }

        .alert-success {
            background: #F0FFF4;
            border: 1px solid #C6F6D5;
            color: var(--success);
        }

        .field {
            margin-bottom: 20px;
        }

        .field label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 16px;
            opacity: 0.5;
        }

        .field input {
            width: 100%;
            height: 48px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 0 16px 0 42px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: var(--text);
            background: #FAFAFA;
            transition: all 0.2s;
            outline: none;
        }

        .field input:focus {
            border-color: var(--gold);
            background: white;
            box-shadow: 0 0 0 3px rgba(212,175,55,0.1);
        }

        .field input::placeholder {
            color: #CBD5E0;
        }

        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #B8960C, var(--gold), #D4AF37);
            background-size: 200% 200%;
            border: none;
            border-radius: 10px;
            color: #1A1200;
            font-family: 'DM Sans', sans-serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: all 0.3s;
            box-shadow: 0 6px 20px rgba(212,175,55,0.35);
            margin-top: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(212,175,55,0.45);
            background-position: right center;
        }

        .btn-submit:active { transform: translateY(0); }

        .form-footer {
            text-align: center;
            margin-top: 24px;
            color: var(--muted);
            font-size: 14px;
        }

        .form-footer a {
            color: var(--dark);
            font-weight: 600;
            text-decoration: none;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 1px;
            transition: color 0.2s;
        }

        .form-footer a:hover { color: #B8960C; }

        .divider {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 24px 0;
            color: #CBD5E0;
            font-size: 12px;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #E2E8F0;
        }

        @media (max-width: 700px) {
            .brand-panel { display: none; }
            .form-panel { padding: 40px 28px; }
            .page { margin: 0; border-radius: 0; min-height: 100vh; }
        }
    </style>
</head>
<body>
<div class="bg-pattern"></div>

<div class="page">
    <!-- Left Brand Panel -->
    <div class="brand-panel">
        <div class="logo-mark">
            <div class="logo-icon">✦</div>
            <div class="logo-text">SHINE <span>YOUNG</span></div>
        </div>

        <div class="brand-content">
            <h2>Preserve Culture.<br><em>Empower</em> Youth.</h2>
            <p>A digital platform where Rwandan learners explore traditions, stories, proverbs, and heritage.</p>
            <div class="brand-tags">
                <span class="tag">📖 Stories</span>
                <span class="tag">🪶 Proverbs</span>
                <span class="tag">🎬 Media</span>
                <span class="tag">🏫 School Culture</span>
            </div>
        </div>

        <div class="brand-footer">© <?php echo date('Y'); ?> Shine Young Cultural Platform</div>
    </div>

    <!-- Right Form Panel -->
    <div class="form-panel">
        <div class="form-header">
            <h3>Welcome back</h3>
            <p>Sign in to your cultural learning account</p>
        </div>

        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 107) {
                echo '<div class="alert alert-error">⚠️ Invalid username or password. Please try again.</div>';
            } elseif ($_GET['error'] == 207) {
                echo '<div class="alert alert-error">🔒 Please log in before you proceed.</div>';
            } else {
                echo '<div class="alert alert-error">⚠️ Service unavailable. Please try again later.</div>';
            }
        } elseif (isset($_GET['success'])) {
            echo '<div class="alert alert-success">✅ Account created! You can now log in.</div>';
        }
        ?>

        <form method="post" action="./login.php">
            <div class="field">
                <label for="username">Username</label>
                <div class="input-wrap">
                    <span class="input-icon">👤</span>
                    <input type="text" name="username" id="username" placeholder="Enter your username" autocomplete="off" required />
                </div>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <span class="input-icon">🔑</span>
                    <input type="password" name="password" id="password" placeholder="Enter your password" required />
                </div>
            </div>

            <button type="submit" name="submit" class="btn-submit">Sign In →</button>
        </form>

        <div class="divider">or</div>

        <div class="form-footer">
            Don't have an account? <a href="./signup.php">Create one</a>
        </div>
    </div>
</div>
</body>
</html>
