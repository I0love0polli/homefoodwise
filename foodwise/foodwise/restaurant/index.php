<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="76x76" href="../favicon/76.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon/32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon/16.png">
    <title>FoodWise - Login</title>
    <style>
        :root {
            --hero-glow: rgba(0, 255, 127, 0.2);
            --transition-duration: 0.8s;
        }

        html, body {
            height: 100%;
            overflow: hidden;
            margin: 0;
            box-sizing: border-box;
            background-color: #090c0a;
            color: #fff;
            font-family: Arial, sans-serif;
        }

        * {
            box-sizing: border-box;
        }

        /* Navbar styles */
        .navbar {
            background-color: #090c0a;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            top: 0;
            z-index: 1000;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
            color: white;
            text-decoration: none;
        }

        .navbar .login-btn {
            background-color: transparent;
            border: 1px solid #00FF7F;
            color: #00FF7F;
            padding: 6px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--hero-glow);
            border: 2px solid #00FF7F;
            color: #00FF7F;
            padding: 8px 16px;
            font-size: 18px;
            border-radius: 50px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-weight: 700;
            min-width: 120px;
        }

        .btn-action:hover {
            background-color: var(--hero-glow);
            border-color: #00FF7F;
            color: #00FF7F;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .btn-action:active {
            background-color: var(--hero-glow);
            border-color: #00FF7F;
            color: #00FF7F;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .btn-action .lucide-icon {
            margin-right: 6px;
            color: #00FF7F;
        }

        .action-icon {
            width: 20px;
            height: 20px;
            margin-right: 6px;
            margin-bottom: 4px;
            stroke-width: 3;
        }

        .logo-icon {
            width: 32px;
            height: 32px;
            margin-right: 6px;
            margin-bottom: 4px;
            stroke-width: 2.2;
        }

        /* Login section styles */
        .login-section {
            display: flex;
            height: 100vh;
            overflow: hidden;
            background-color: #090c0a;
        }

        .login-left {
            flex: 0 0 40%;
            padding-top: 15px;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
            position: relative;
            background: linear-gradient(to bottom, var(--hero-glow) 0%, transparent 70%);
        }

        .login-left h1 {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #fff;
        }

        .btn-action:active {
            background-color: var(--hero-glow) !important;
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .login-left p {
            font-size: 14px;
            color: #a0a0a0;
            margin-bottom: 15px;
        }

        .login-form {
            width: 100%;
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 15px;
            color: white;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .form-group input {
            background-color: #1e1e1e;
            border: 1px solid #333333;
            color: #a0a0a0;
            padding: 16px 16px 16px 48px;
            border-radius: 12px;
            width: 100%;
            font-size: 16px;
        }

        .form-group input:focus {
            background-color: #1e1e1e;
            border: 1px solid #333333;
            box-shadow: none;
            color: #a0a0a0;
            outline: none;
        }

        .form-group input::placeholder {
            color: #a0a0a0;
            opacity: 0.7;
        }

        .input-icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            color: #a0a0a0;
            pointer-events: none;
            width: 24px;
            height: 24px;
        }

        .password-toggle {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 2;
            color: #a0a0a0;
            cursor: pointer;
            width: 24px;
            height: 24px;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .form-options label {
            color: #a0a0a0;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .form-options label input {
            display: none;
        }

        .form-options label .custom-checkbox {
            width: 20px;
            height: 20px;
            background-color: #1e1e1e;
            border: 2px solid #444;
            border-radius: 6px;
            margin-right: 8px;
            position: relative;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .form-options label input:checked + .custom-checkbox {
            background-color: #00FF7F;
            border-color: #00FF7F;
        }

        .form-options label input:checked + .custom-checkbox::after {
            content: '';
            position: absolute;
            left: 5px;
            top: 1px;
            width: 6px;
            height: 12px;
            border: solid #090c0a;
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        .form-options a {
            color: #00FF7F;
            text-decoration: none;
        }

        .form-options a:hover {
            color: #00FF7F;
        }

        .login-btn {
            background-color: rgba(0, 255, 127, 0.2);
            border: 2px solid #00FF7F;
            color: #00FF7F;
            padding: 16px;
            font-size: 18px;
            border-radius: 30px;
            width: 100%;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .divider {
            display: flex;
            align-items: center;
            color: #a0a0a0;
            margin: 30px 0;
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #444;
            margin: 0 15px;
        }

        .signup-link {
            text-align: center;
            font-size: 14px;
            color: #a0a0a0;
        }

        .signup-link a {
            color: #00FF7F;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .login-right {
            flex: 0 0 60%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #090c0a;
            padding: 0;
            overflow: hidden;
        }

        .login-right img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 0;
        }

        @media (min-width: 1024px) {
            .navbar {
                padding: 20px 40px;
            }

            .navbar .logo {
                font-size: 24px;
            }

            .navbar .login-btn {
                padding: 8px 16px;
                font-size: 16px;
            }
        }

        @media (min-width: 1200px) {
            .navbar {
                padding: 20px 60px;
            }
        }

        @media (max-width: 1024px) {
            .login-section {
                flex-direction: column;
            }

            .login-left {
                padding-top: 30px;
                padding: 20px 20px 20px 20px; /* Further reduced top padding */
                width: 100%;
            }

            .login-right {
                display: none;
            }

            .login-left h1 {
                font-size: 28px;
            }

            .login-left p {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .login-form {
                max-width: 100%;
            }

            .form-group input {
                padding: 12px 12px 12px 40px;
                font-size: 14px;
                border-radius: 10px;
            }

            .input-icon {
                left: 12px;
                width: 20px;
                height: 20px;
            }

            .password-toggle {
                right: 12px;
                width: 20px;
                height: 20px;
            }

            .login-btn {
                padding: 12px;
                font-size: 16px;
                border-radius: 25px;
            }

            .form-options label .custom-checkbox {
                width: 18px;
                height: 18px;
                margin-right: 6px;
            }

            .form-options label input:checked + .custom-checkbox::after {
                left: 4px;
                top: 0;
                width: 5px;
                height: 10px;
                border-width: 0 2px 2px 0;
            }
        }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <nav class="navbar">
        <a href="" class="logo"><i data-lucide="chef-hat" class="logo-icon"></i>FOODWISE</a>
        <a href="../" class="btn btn-action"><i data-lucide="home" class="action-icon"></i>Home</a>
    </nav>

    <section class="login-section">
        <div class="login-left">
            <h1>Welcome Back</h1>
            <p>Log in to your restaurant dashboard</p>
            
            <!-- Visualizzazione messaggio di errore -->
            <?php
            if (isset($_SESSION['error_message'])) {
                echo '<div style="color: red; margin-bottom: 20px;">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <!-- Aggiunta del form -->
            <form action="assets/auth.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="ristorante_login" class="label-custom">Restaurant ID</label>
                    <div class="input-wrapper">
                        <input type="text" id="ristorante_login" name="ristorante_login" placeholder="Your restaurant ID" required>
                        <i data-lucide="mail" class="input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password_login">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_login" name="password_login" placeholder="••••••••" required>
                        <i data-lucide="lock" class="input-icon"></i>
                        <i data-lucide="eye" class="password-toggle" id="password-toggle"></i>
                    </div>
                </div>
                <div class="form-options">
                    <label>
                        <input type="checkbox" id="remember-me" name="remember-me">
                        <span class="custom-checkbox"></span>
                        Remember me
                    </label>
                    <a href="#">Forgot password?</a>
                </div>
                <button type="submit" name="submit_login" class="login-btn">Log In</button>
                <div class="divider">OR</div>
                <div class="signup-link">
                    Don’t have an account? <a href="../signup">Registrati</a>
                </div>
                <div class="signup-link">
                    Sei un dipendente? <a href="../login">Accedi</a>
                </div>
            </form>
        </div>
        <div class="login-right">
            <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Restaurant Image">
        </div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof lucide !== 'undefined' && !window.lucideInitialized) {
                lucide.createIcons();
                window.lucideInitialized = true;
                console.log("Lucide inizializzato");
            } else {
                console.warn("Libreria Lucide non trovata o già inizializzata");
            }

            function togglePasswordVisibility(passwordId, iconId) {
                const passwordField = document.getElementById(passwordId);
                const icon = document.getElementById(iconId);
                if (!passwordField || !icon) {
                    console.error("Elementi passwordField o icon non trovati");
                    return;
                }
                const isPassword = passwordField.type === "password";
                passwordField.type = isPassword ? "text" : "password";
                const newIconType = isPassword ? 'eye-off' : 'eye';
                console.log("Tentativo di aggiornare icona a:", newIconType);

                icon.setAttribute('data-lucide', newIconType);

                const svg = icon.querySelector('svg');
                if (svg) {
                    svg.remove();
                }

                lucide.createIcons({ icons: [icon] });
                console.log("Icona aggiornata a:", icon.getAttribute('data-lucide'));
            }

            const passwordToggle = document.getElementById('password-toggle');
            if (passwordToggle) {
                passwordToggle.addEventListener('click', () => {
                    console.log("Clic su password-toggle");
                    togglePasswordVisibility('password_login', 'password-toggle');
                });
            } else {
                console.error("Elemento password-toggle non trovato");
            }
        });
    </script>
</body>
</html>