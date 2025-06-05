<?php
// session_start() DEVE essere la primissima cosa, prima di QUALSIASI output o include che potrebbe generare output.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include '../database/connection.php'; // Assicurati che connection.php NON chiami session_start() di nuovo se lo fa già qui.
                                     // Idealmente, session_start() è chiamato una sola volta.
?>
<!DOCTYPE html>
<html lang="it"> <!-- Modificato lang in 'it' -->
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

        .navbar .login-btn { /* Questo stile sembra per un bottone generico, non per il submit del login */
            background-color: transparent;
            border: 1px solid #00FF7F;
            color: #00FF7F;
            padding: 6px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-action { /* Stile per il bottone "Home" nella navbar */
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
            background-color: var(--hero-glow) !important; /* Specificità aumentata */
            border-color: #00FF7F !important;
            color: #00FF7F !important;
            transform: translateY(0);
            box-shadow: 0 4px 8px rgba(0, 255, 127, 0.3);
        }

        .btn-action .lucide-icon {
            margin-right: 6px;
            color: #00FF7F;
        }
        
        .action-icon { /* Per icone dentro .btn-action */
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
            height: calc(100vh - 70px); /* Altezza meno la navbar approssimativa */
            overflow: auto; /* Permetti lo scroll se il contenuto è troppo */
            background-color: #090c0a;
        }

        .login-left {
            flex: 0 0 40%;
            display: flex; /* Aggiunto per allineamento verticale */
            flex-direction: column;
            justify-content: center;
            padding: 40px; /* Ribilanciato padding */
            position: relative;
            background: linear-gradient(to bottom, var(--hero-glow) 0%, transparent 70%);
        }

        .login-left h1 {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #fff;
        }

        .login-left p {
            font-size: 14px;
            color: #a0a0a0;
            margin-bottom: 15px;
        }

        .login-form {
            width: 100%;
            max-width: 600px; /* O quanto serve per il design */
        }

        .form-group {
            margin-bottom: 15px; /* Aumentato spazio */
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
            display: inline-block; /* o block */
            width: 100%;
        }

        .form-group input {
            background-color: #1e1e1e;
            border: 1px solid #333333;
            color: #a0a0a0; /* Testo input */
            padding: 16px 16px 16px 48px; /* Spazio per icona */
            border-radius: 12px;
            width: 100%;
            font-size: 16px;
        }

        .form-group input:focus {
            background-color: #1e1e1e;
            border: 1px solid #00FF7F; /* Bordo focus */
            box-shadow: 0 0 0 0.2rem rgba(0, 255, 127, 0.25); /* Glow focus */
            color: #e0e0e0; /* Testo input focus */
            outline: none;
        }

        .form-group input::placeholder {
            color: #757575; /* Placeholder più scuro */
            opacity: 1; /* Assicurati che sia visibile */
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
         .password-toggle:hover {
            color: #00FF7F;
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

        .form-options label input { /* Nasconde il checkbox di default */
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
            border: solid #090c0a; /* Colore del segno di spunta */
            border-width: 0 3px 3px 0;
            transform: rotate(45deg);
        }

        .form-options a {
            color: #00FF7F;
            text-decoration: none;
        }

        .form-options a:hover {
            text-decoration: underline; /* Sottolinea al hover */
        }

        .login-submit-btn { /* Rinominato per chiarezza rispetto a .navbar .login-btn */
            background-color: rgba(0, 255, 127, 0.2);
            border: 2px solid #00FF7F;
            color: #00FF7F;
            padding: 12px;
            font-size: 18px;
            border-radius: 30px;
            width: 100%;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        .login-submit-btn:disabled {
            background-color: #333;
            border-color: #555;
            color: #888;
            cursor: not-allowed;
        }

        .login-submit-btn:hover:not(:disabled) {
            background-color: rgba(0, 255, 127, 0.3); /* Leggermente più scuro/opaco al hover */
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
        
        /* Stile per il messaggio di errore */
        .error-message-container {
            background-color: rgba(220, 53, 69, 0.1); /* Bootstrap $danger-bg-subtle */
            color: #dc3545; /* Bootstrap $danger-text-emphasis */
            border: 1px solid rgba(220, 53, 69, 0.4); /* Bootstrap $danger-border-subtle */
            padding: 1rem; /* Bootstrap $alert-padding-y $alert-padding-x */
            margin-bottom: 1rem; /* Bootstrap $alert-margin-bottom */
            border-radius: 0.375rem; /* Bootstrap $alert-border-radius */
            text-align: left; /* Allineamento testo */
            font-weight: 500; /* Leggermente più bold */
        }


        @media (min-width: 1024px) {
            .navbar {
                padding: 20px 40px;
            }

            .navbar .logo {
                font-size: 24px;
            }

            .navbar .login-btn { /* Stile per il bottone nella navbar (se diverso) */
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
             html, body {
                overflow: auto; /* Permetti scroll su mobile */
            }
            .login-section {
                flex-direction: column;
                height: auto; /* Altezza automatica per mobile */
            }

            .login-left {
                flex: 1 1 auto; /* Permetti di crescere e restringersi */
                padding: 30px 20px 20px 20px; /* Ridotto padding superiore */
                width: 100%;
            }

            .login-right {
                display: none; /* Nasconde immagine su mobile */
            }

            .login-left h1 {
                font-size: 28px; /* Dimensione font ridotta */
            }

            .login-left p {
                font-size: 14px;
                margin-bottom: 20px;
            }

            .login-form {
                max-width: 100%;
            }

            .form-group input {
                padding: 14px 14px 14px 44px; /* Adattato padding per mobile */
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

            .login-submit-btn {
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
                top: 0px; /* Aggiustato per centrare meglio */
                width: 5px;
                height: 10px;
                border-width: 0 2.5px 2.5px 0; /* Leggermente più spesso */
            }
        }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.359.0/dist/umd/lucide.min.js"></script> <!-- Rimosso defer per caricamento immediato se necessario per script inline -->
    <!-- Bootstrap JS non è strettamente necessario per questo form, ma lo lascio se serve altrove -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script> -->

    <nav class="navbar">
        <a href="../" class="logo"><i data-lucide="chef-hat" class="logo-icon"></i>FOODWISE</a>
        <a href="../" class="btn btn-action"><i data-lucide="home" class="action-icon"></i>Home</a>
    </nav>

    <section class="login-section">
        <div class="login-left">
            <h1>Bentornato</h1> <!-- Modificato -->
            <p>Accedi alla dashboard del tuo ristorante</p> <!-- Modificato -->

            <?php
            // Visualizzazione messaggio di errore
            if (isset($_SESSION['error_message'])) {
                echo '<div class="error-message-container">' . htmlspecialchars($_SESSION['error_message']) . '</div>';
                unset($_SESSION['error_message']); // Rimuovi il messaggio dopo averlo mostrato
            }
            ?>

            <form action="assets/auth.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="ristorante_login" class="label-custom">Codice Ristorante</label>
                    <div class="input-wrapper">
                        <input type="text" id="ristorante_login" name="ristorante_login" placeholder="Inserisci codice ristorante" required>
                        <i data-lucide="hash" class="input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="utente_login" class="label-custom">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="utente_login" name="utente_login" placeholder="Inserisci username" required>
                        <i data-lucide="user" class="input-icon"></i> <!-- Cambiato icona per username -->
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
                        Ricordami <!-- Modificato -->
                    </label>
                    <a href="#">Password dimenticata?</a> <!-- Modificato -->
                </div>
                <button type="submit" name="submit_login" class="login-submit-btn">Accedi</button> <!-- Modificato -->
                <div class="divider">OPPURE</div> <!-- Modificato -->
                <div class="signup-link">
                    Hai un account ristorante? <a href="../restaurant">Accedi qui</a> <!-- Modificato, presumendo che /restaurant sia per la registrazione -->
                </div>
            </form>
        </div>
        <div class="login-right">
            <img src="https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Immagine Ristorante">
        </div>
    </section>

    <script>
        let lucideInitialized = false; // Flag per tracciare l'inizializzazione

        function initializeLucide() {
            if (typeof lucide !== 'undefined' && !lucideInitialized) {
                lucide.createIcons();
                lucideInitialized = true;
                // console.log("Lucide icons created.");
            } else if (lucideInitialized) {
                // console.log("Lucide already initialized.");
            } else {
                // console.warn("Lucide library not found. Retrying...");
                setTimeout(initializeLucide, 100); // Riprova dopo un breve ritardo
            }
        }
        
        document.addEventListener("DOMContentLoaded", () => {
            initializeLucide(); // Chiama per inizializzare le icone

            function togglePasswordVisibility(passwordId, iconElement) {
                const passwordField = document.getElementById(passwordId);
                if (!passwordField || !iconElement) {
                    // console.error("Elementi passwordField o iconElement non trovati per togglePasswordVisibility");
                    return;
                }
                const isPassword = passwordField.type === "password";
                passwordField.type = isPassword ? "text" : "password";
                
                // Sostituisce l'icona intera invece di modificare solo l'attributo data-lucide
                // per forzare il rendering corretto da parte della libreria Lucide.
                iconElement.innerHTML = isPassword ? '<i data-lucide="eye-off"></i>' : '<i data-lucide="eye"></i>';
                lucide.createIcons({
                    attrs: {'stroke-width': 2}, // Puoi aggiungere attributi di default qui
                    icons: Array.from(iconElement.querySelectorAll('[data-lucide]'))
                });
            }

            const passwordToggle = document.getElementById('password-toggle');
            if (passwordToggle) {
                passwordToggle.addEventListener('click', () => {
                    togglePasswordVisibility('password_login', passwordToggle);
                });
            } else {
                // console.error("Elemento password-toggle non trovato");
            }

            const loginForm = document.querySelector('.login-form');
            if (loginForm) {
                loginForm.addEventListener('submit', function(event) {
                    // Disabilita il pulsante per prevenire invii multipli
                    const loginBtn = loginForm.querySelector('.login-submit-btn');
                    if (loginBtn) {
                         // Un piccolo ritardo per assicurare che i dati del form vengano catturati prima della disabilitazione
                        setTimeout(() => {
                            loginBtn.disabled = true;
                            loginBtn.textContent = 'Accesso in corso...'; // Modificato
                        }, 0);
                    }
                });
            }
        });
    </script>
</body>
</html>
