<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="apple-touch-icon" sizes="76x76" href="../favicon/76.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../favicon/32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../favicon/16.png">
    <title>Registrati</title>
    <style>
        :root {
            --hero-glow: rgba(0, 255, 127, 0.2);
            --transition-duration: 0.8s;
        }

        html, body {
            height: 100%;
            margin: 0;
            box-sizing: border-box;
            background-color: #090c0a;
            color: #fff;
            font-family: Arial, sans-serif;
            overflow: hidden;
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
            position: sticky;
        }

        .navbar .logo {
            font-size: 20px;
            font-weight: bold;
            color
            : white;
            text-decoration: none;
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
            padding-top: 10px;
        }

        .btn-action:hover {
            background-color: var(--hero-glow);
            border-color: #00FF7F;
            color: #00FF7F;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .btn-action:active {
            background-color: var(--hero-glow) !important;
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
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

        .signup-section {
            display: flex;
            height: calc(100vh - 60px);
            background-color: #090c0a;
        }

        .signup-left {
            flex: 0 0 40%;
            display: flex;
            flex-direction: column;
            padding: 25px 50px;
            position: relative;
            background: linear-gradient(to bottom, var(--hero-glow) 0%, transparent 70%);
            overflow-y: auto;
        }

        .signup-right {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 50px;
            background: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D') no-repeat center center;
            background-size: cover;
            position: relative;
            justify-content: center;
        }

        .signup-right::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .signup-right-content {
            position: relative;
            z-index: 2;
            max-width: 400px;
        }

        .signup-right h2 {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .signup-right p {
            font-size: 16px;
            color: #a0a0a0;
            margin-bottom: 30px;
        }

        .stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item h3 {
            font-size: 24px;
            font-weight: bold;
            color: #00FF7F;
            margin-bottom: 5px;
        }

        .stat-item p {
            font-size: 14px;
            color: #a0a0a0;
            text-transform: uppercase;
        }

        .testimonial {
            font-style: italic;
            font-size: 16px;
            color: #a0a0a0;
            margin-bottom: 20px;
        }

        .author {
            display: flex;
            align-items: center;
        }

        .author img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .author-info h4 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .author-info p {
            font-size: 12px;
            color: #a0a0a0;
        }

        .signup-left h1 {
            font-size: 38px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #fff;
        }

        .signup-left p {
            font-size: 14px;
            color: #a0a0a0;
            margin-bottom: 20px;
        }

        .signup-form {
            width: 100%;
            max-width: 600px;
        }

        .form-group {
            margin-bottom: 10px;
        }

        .form-group label {
            display: block;
            font-size: 15px;
            color: white;
            margin-bottom: 5px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .input-wrapper {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .form-group input,
        .form-group select {
            background-color: #1e1e1e;
            border: 1px solid #333333;
            color: #a0a0a0;
            padding: 16px 16px 16px 48px;
            border-radius: 12px;
            width: 100%;
            font-size: 16px;
        }

        .form-group input:focus,
        .form-group select:focus {
            background-color: #1e1e1e;
            border: 1px solid #333333;
            box-shadow: none;
            color: #a0a0a0;
            outline: none;
        }

        .form-group input::placeholder,
        .form-group select::placeholder {
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

        .progress-container {
            position: relative;
            margin-bottom: 15px;
        }

        .progress {
            background-color: #444;
            height: 8px;
            border-radius: 4px;
            margin-bottom: 10px;
            position: relative;
        }

        .progress-bar {
            background-color: #00FF7F;
            height: 100%;
            border-radius: 4px;
        }

        .progress-dots {
            position: absolute;
            top: -8px;
            width: 100%;
            display: flex;
            justify-content: space-between;
        }

        .progress-dot {
            width: 24px;
            height: 24px;
            background-color: #444;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
            color: #fff;
            z-index: 2;
        }

        .progress-dot.active {
            background-color: #00FF7F;
        }

        .progress-dot.completed {
            background-color: #00FF7F;
        }

        .progress-labels {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #a0a0a0;
            width: 100%;
        }

        .progress-labels span {
            flex: 1;
            text-align: center;
            position: relative;
            width: 24px;
        }

        .progress-labels span.active {
            color: #fff;
        }

        .recap-section {
            margin-bottom: 20px;
        }

        .recap-section h3 {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .recap-item {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: #a0a0a0;
            margin-bottom: 5px;
        }

        .recap-item span:first-child {
            color: #fff;
            text-transform: uppercase;
        }

        @media (min-width: 1024px) {
            .navbar {
                padding: 20px 40px;
            }

            .navbar .logo {
                font-size: 24px;
            }

            .navbar .btn-action {
                padding: 8px 16px;
                font-size: 18px;
            }
        }

        @media (min-width: 1200px) {
            .navbar {
                padding: 20px 60px;
            }
        }

        @media (max-width: 1024px) {
            html, body {
                overflow: auto;
            }

            .navbar {
                position: fixed;
                top: 0;
                width: 100%;
                z-index: 1000;
            }

            .signup-section {
                flex-direction: column;
                height: auto;
                margin-top: 60px;
            }

            .signup-left {
                flex: 1;
                width: 100%;
                padding: 20px;
                max-width: 100%;
                overflow-y: visible;
                height: auto;
                top: 20px;
            }

            .signup-right {
                display: none;
            }

            .signup-left h1 {
                font-size: 28px;
            }

            .signup-left p {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .signup-form {
                max-width: 100%;
            }

            .form-group input,
            .form-group select {
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

            .btn-action {
                max-width: 100%;
            }
        }

        .divider {
            display: flex;
            align-items: center;
            color: #a0a0a0;
            margin: 30px 0;
            font-size: 14px;
        }

        .signup-link {
            text-align: center;
            font-size: 14px;
            color: #a0a0a0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #444;
            margin: 0 15px;
        }

        .signup-link a {
            color: #00FF7F;
            text-decoration: none;
        }

        .signup-link a:hover {
            text-decoration: underline;
        }

        .custom-terms{
            color:  #00FF7F;
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

    <section class="signup-section">
        <div class="signup-left">
            <h1>Create Your Account</h1>
            <p>Join hundreds of restaurants already using FOODWISE</p>
            <div class="signup-form" id="signup-form">
                <form action="../database/signup.php" method="POST">
                    <div class="step" id="step-1">
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar w-25" role="progressbar" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="progress-dots">
                                <div class="progress-dot active">1</div>
                                <div class="progress-dot">2</div>
                                <div class="progress-dot">3</div>
                            </div>
                            <div class="progress-labels">
                                <span class="active">Account Information</span>
                                <span>Security</span>
                                <span>Confirmation</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="restaurant-name">Restaurant Name</label>
                            <div class="input-wrapper">
                                <input type="text" id="restaurant-name" name="nome_ristorante" placeholder="Your restaurant name">
                                <i data-lucide="store" class="input-icon"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="vat-number">VAT Number</label>
                            <div class="input-wrapper">
                                <input type="text" id="vat-number" name="iva" placeholder="Your VAT number">
                                <i data-lucide="file-text" class="input-icon"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <div class="input-wrapper">
                                <input type="email" id="email" name="email" placeholder="your.email@example.com">
                                <i data-lucide="mail" class="input-icon"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <div class="input-wrapper">
                                <input type="tel" id="phone" name="telefono" placeholder="e.g. +1234567890">
                                <i data-lucide="phone" class="input-icon"></i>
                            </div>
                        </div>
                        <button type="button" class="btn-action" onclick="nextStep(1)" style="width: 100%">Next</button>
                    </div>
                    <div class="step" id="step-2" style="display: none;">
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar w-75" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="progress-dots">
                                <div class="progress-dot completed">1</div>
                                <div class="progress-dot active">2</div>
                                <div class="progress-dot">3</div>
                            </div>
                            <div class="progress-labels">
                                <span>Account Information</span>
                                <span class="active">Security</span>
                                <span>Confirmation</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="password" name="password" placeholder="••••••••">
                                <i data-lucide="lock" class="input-icon"></i>
                                <i data-lucide="eye" class="password-toggle" id="password-toggle"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirm Password</label>
                            <div class="input-wrapper">
                                <input type="password" id="confirm-password" name="confirm_password" placeholder="••••••••">
                                <i data-lucide="lock" class="input-icon"></i>
                                <i data-lucide="eye" class="password-toggle" id="confirm-password-toggle"></i>
                            </div>
                        </div>
                        <div class="form-options">
                            <label>
                                <input type="checkbox" id="two-fa">
                                <span class="custom-checkbox"></span>
                                Enable Two-Factor Authentication (2FA)
                            </label>
                        </div>
                        <button type="button" class="btn-action" onclick="nextStep(2)" style="width: 100%">Next</button>
                    </div>
                    <div class="step" id="step-3" style="display: none;">
                        <div class="progress-container">
                            <div class="progress">
                                <div class="progress-bar w-100" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="progress-dots">
                                <div class="progress-dot completed">1</div>
                                <div class="progress-dot completed">2</div>
                                <div class="progress-dot active">3</div>
                            </div>
                            <div class="progress-labels">
                                <span>Account Information</span>
                                <span>Security</span>
                                <span class="active">Confirmation</span>
                            </div>
                        </div>
                        <div class="recap-section">
                            <h3>Account Information</h3>
                            <div class="recap-item">
                                <span>Restaurant Name:</span>
                                <span id="recap-restaurant-name"></span>
                            </div>
                            <div class="recap-item">
                                <span>VAT Number:</span>
                                <span id="recap-vat-number"></span>
                            </div>
                            <div class="recap-item">
                                <span>Email:</span>
                                <span id="recap-email"></span>
                            </div>
                            <div class="recap-item">
                                <span>Phone Number:</span>
                                <span id="recap-phone"></span>
                            </div>
                        </div>
                        <div class="recap-section">
                            <h3>Security</h3>
                            <div class="recap-item">
                                <span>2FA:</span>
                                <span id="recap-two-fa"></span>
                            </div>
                        </div>
                        <div class="form-options">
                            <label>
                                <input type="checkbox" id="terms" name="terms">
                                <span class="custom-checkbox"></span>
                                I agree to the <a href="#" class="custom-terms"> Terms of Service </a> and <a href="#" class="custom-terms"> Privacy Policy </a>
                            </label>
                        </div>
                        <button type="submit" class="btn-action" name="submit" style="width: 100%">Confirm</button>
                    </div>
                    <div class="divider">OR</div>
                    <div class="signup-link">
                        Sei un proprietario di un ristorante? <a href="../restaurant">Accedi</a>
                    </div>
                    <br><br><br>
                </form>
            </div>
        </div>
        <div class="signup-right"></div>
    </section>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            if (typeof lucide !== 'undefined' && !window.lucideInitialized) {
                lucide.createIcons();
                window.lucideInitialized = true;
            }

            function togglePasswordVisibility(passwordId, iconId) {
                const passwordField = document.getElementById(passwordId);
                const icon = document.getElementById(iconId);
                if (!passwordField || !icon) return;
                const isPassword = passwordField.type === "password";
                passwordField.type = isPassword ? "text" : "password";
                const newIconType = isPassword ? 'eye-off' : 'eye';
                icon.setAttribute('data-lucide', newIconType);
                const svg = icon.querySelector('svg');
                if (svg) svg.remove();
                lucide.createIcons({ icons: [icon] });
            }

            const passwordToggle = document.getElementById('password-toggle');
            const confirmPasswordToggle = document.getElementById('confirm-password-toggle');
            if (passwordToggle) {
                passwordToggle.addEventListener('click', () => {
                    togglePasswordVisibility('password', 'password-toggle');
                });
            }
            if (confirmPasswordToggle) {
                confirmPasswordToggle.addEventListener('click', () => {
                    togglePasswordVisibility('confirm-password', 'confirm-password-toggle');
                });
            }
        });

        function nextStep(currentStep) {
            const steps = document.querySelectorAll('.step');

            // Validate current step
            const inputs = steps[currentStep - 1].querySelectorAll('input:not([type="checkbox"]), select');
            let isValid = true;
            inputs.forEach(input => {
                if (!input.value) {
                    isValid = false;
                    input.style.borderColor = '#ff0000';
                } else {
                    input.style.borderColor = '#333333';
                }
            });

            if (currentStep === 2) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm-password').value;
                if (password !== confirmPassword) {
                    isValid = false;
                    document.getElementById('confirm-password').style.borderColor = '#ff0000';
                    alert("Passwords do not match!");
                }
            }

            if (!isValid) return;

            // Hide current step, show next
            steps[currentStep - 1].style.display = 'none';
            steps[currentStep].style.display = 'block';

            // For step 3, populate recap
            if (currentStep === 2) {
                document.getElementById('recap-restaurant-name').textContent = document.getElementById('restaurant-name').value;
                document.getElementById('recap-vat-number').textContent = document.getElementById('vat-number').value;
                document.getElementById('recap-email').textContent = document.getElementById('email').value;
                document.getElementById('recap-phone').textContent = document.getElementById('phone').value;
                document.getElementById('recap-two-fa').textContent = document.getElementById('two-fa').checked ? 'Enabled' : 'Disabled';
            }
        }
    </script>
</body>
</html>



