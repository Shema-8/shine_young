<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHINE YOUNG – Sign Up</title>
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
            padding: 24px;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: 
                radial-gradient(ellipse 80% 60% at 80% 20%, rgba(212,175,55,0.1) 0%, transparent 60%),
                radial-gradient(ellipse 60% 80% at 20% 80%, rgba(212,175,55,0.07) 0%, transparent 60%);
            pointer-events: none;
        }

        .bg-pattern {
            position: fixed;
            inset: 0;
            background-image: repeating-linear-gradient(
                45deg, transparent, transparent 60px,
                rgba(212,175,55,0.025) 60px, rgba(212,175,55,0.025) 61px
            );
            pointer-events: none;
        }

        .page {
            display: flex;
            width: 100%;
            max-width: 980px;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
        }

        .brand-panel {
            width: 320px;
            flex-shrink: 0;
            background: linear-gradient(145deg, var(--dark2) 0%, #0d1520 100%);
            padding: 52px 44px;
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
            width: 280px; height: 280px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(212,175,55,0.12) 0%, transparent 70%);
            bottom: -60px; right: -80px;
        }

        .logo-mark {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, var(--gold), var(--gold-light));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            box-shadow: 0 6px 20px rgba(212,175,55,0.3);
        }

        .logo-text {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: white;
        }

        .logo-text span { color: var(--gold); }

        .brand-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 30px;
            font-weight: 900;
            color: white;
            line-height: 1.25;
            margin-bottom: 14px;
        }

        .brand-content h2 em { font-style: italic; color: var(--gold); }

        .brand-content p {
            color: rgba(255,255,255,0.5);
            font-size: 14px;
            line-height: 1.7;
        }

        .steps {
            margin-top: 32px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .step-num {
            width: 28px; height: 28px;
            background: rgba(212,175,55,0.15);
            border: 1px solid rgba(212,175,55,0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--gold);
            flex-shrink: 0;
        }

        .step-text {
            font-size: 13px;
            color: rgba(255,255,255,0.55);
        }

        .brand-footer {
            color: rgba(255,255,255,0.25);
            font-size: 12px;
        }

        /* Form panel */
        .form-panel {
            flex: 1;
            background: var(--card);
            padding: 52px 56px;
        }

        .form-header {
            margin-bottom: 32px;
        }

        .form-header h3 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 6px;
        }

        .form-header p { color: var(--muted); font-size: 14px; }

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

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px 20px;
        }

        .field { display: flex; flex-direction: column; }
        .field.full { grid-column: 1 / -1; }

        .field label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 7px;
            letter-spacing: 0.4px;
            text-transform: uppercase;
        }

        .input-wrap { position: relative; }

        .input-icon {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 15px;
            opacity: 0.45;
            pointer-events: none;
        }

        .field input {
            width: 100%;
            height: 46px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 0 14px 0 40px;
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

        .field input::placeholder { color: #CBD5E0; }

        .field input.is-error {
            border-color: var(--error);
            background: #FFF5F5;
        }

        .field-error {
            font-size: 11px;
            color: var(--error);
            margin-top: 5px;
            font-weight: 500;
        }

        .btn-submit {
            width: 100%;
            height: 50px;
            background: linear-gradient(135deg, #B8960C, var(--gold), #D4AF37);
            background-size: 200%;
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
            margin-top: 24px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(212,175,55,0.45);
        }

        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: var(--muted);
            font-size: 14px;
        }

        .form-footer a {
            color: var(--dark);
            font-weight: 600;
            text-decoration: none;
            border-bottom: 2px solid var(--gold);
            padding-bottom: 1px;
        }

        @media (max-width: 750px) {
            .brand-panel { display: none; }
            .form-panel { padding: 36px 24px; }
            .form-grid { grid-template-columns: 1fr; }
            .field.full { grid-column: 1; }
            .page { border-radius: 16px; }
        }
    </style>
</head>
<body>
<div class="bg-pattern"></div>

<div class="page">
    <!-- Brand Panel -->
    <div class="brand-panel">
        <div class="logo-mark">
            <div class="logo-icon">✦</div>
            <div class="logo-text">SHINE <span>YOUNG</span></div>
        </div>

        <div class="brand-content">
            <h2>Join the <em>Cultural</em> Movement.</h2>
            <p>Create your account and start exploring Rwanda's rich heritage and traditions.</p>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-text">Fill in your personal details</div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-text">Create a secure password</div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-text">Access all cultural content</div>
                </div>
            </div>
        </div>

        <div class="brand-footer">© <?php echo date('Y'); ?> Shine Young Cultural Platform</div>
    </div>

    <!-- Form Panel -->
    <div class="form-panel">
        <div class="form-header">
            <h3>Create your account</h3>
            <p>Join thousands of learners exploring Rwandan culture</p>
        </div>

        <?php
        $feedback = $_SESSION['feedback'] ?? [];
        $errors = $feedback['error'] ?? [];

        if (!empty($errors)) {
            echo '<div class="alert alert-error">⚠️ Please fix the highlighted fields below.</div>';
        } elseif (isset($_GET['error']) && $_GET['error'] == 109) {
            echo '<div class="alert alert-error">⚠️ Database error. Please try again.</div>';
        }
        unset($_SESSION['feedback']);
        ?>

        <form method="post" action="./sign.php">
            <div class="form-grid">

                <div class="field">
                    <label for="fnames">First Name</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" name="fnames" id="fnames"
                            placeholder="e.g. Jean Paul"
                            value="<?= htmlspecialchars($feedback['fnames'] ?? '') ?>"
                            class="<?= isset($errors['fnames']) ? 'is-error' : '' ?>" />
                    </div>
                    <?php if (isset($errors['fnames'])) echo '<span class="field-error">First name is too short (min 2 chars)</span>'; ?>
                </div>

                <div class="field">
                    <label for="lnames">Last Name</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input type="text" name="lnames" id="lnames"
                            placeholder="e.g. Uwimana"
                            value="<?= htmlspecialchars($feedback['lnames'] ?? '') ?>"
                            class="<?= isset($errors['lnames']) ? 'is-error' : '' ?>" />
                    </div>
                    <?php if (isset($errors['lnames'])) echo '<span class="field-error">Last name is too short (min 2 chars)</span>'; ?>
                </div>

                <div class="field">
                    <label for="username">Username</label>
                    <div class="input-wrap">
                        <span class="input-icon">🏷️</span>
                        <input type="text" name="username" id="username"
                            placeholder="Choose a username"
                            value="<?= htmlspecialchars($feedback['username'] ?? '') ?>"
                            class="<?= isset($errors['username']) ? 'is-error' : '' ?>"
                            autocomplete="off" />
                    </div>
                    <?php if (isset($errors['username'])) echo '<span class="field-error">Username must be at least 4 characters</span>'; ?>
                </div>

                <div class="field">
                    <label for="telephone">Telephone</label>
                    <div class="input-wrap">
                        <span class="input-icon">📞</span>
                        <input type="text" name="telephone" id="telephone"
                            placeholder="e.g. 0788000000"
                            value="<?= htmlspecialchars($feedback['telephone'] ?? '') ?>"
                            class="<?= isset($errors['telephone']) ? 'is-error' : '' ?>" />
                    </div>
                    <?php if (isset($errors['telephone'])) echo '<span class="field-error">Enter a valid phone number</span>'; ?>
                </div>

                <div class="field full">
                    <label for="category">Category / Role</label>
                    <div class="input-wrap">
                        <span class="input-icon">🎓</span>
                        <input type="text" name="names" id="category"
                            placeholder="e.g. Student, Teacher, Parent"
                            value="<?= htmlspecialchars($feedback['names'] ?? '') ?>"
                            class="<?= isset($errors['names']) ? 'is-error' : '' ?>" />
                    </div>
                    <?php if (isset($errors['names'])) echo '<span class="field-error">Category must be at least 4 characters</span>'; ?>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password" id="password"
                            placeholder="Min. 4 characters"
                            class="<?= isset($errors['password']) ? 'is-error' : '' ?>" />
                    </div>
                    <?php if (isset($errors['password'])) echo '<span class="field-error">Password must be at least 4 characters</span>'; ?>
                </div>

                <div class="field">
                    <label for="password2">Confirm Password</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒</span>
                        <input type="password" name="password2" id="password2"
                            placeholder="Repeat your password"
                            class="<?= isset($errors['password2']) ? 'is-error' : '' ?>" />
                    </div>
                    <?php if (isset($errors['password2'])) echo '<span class="field-error">Passwords do not match</span>'; ?>
                </div>

            </div>

            <button type="submit" name="submit" class="btn-submit">Create Account →</button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="./index.php">Sign in</a>
        </div>
    </div>
</div>
</body>
</html>
