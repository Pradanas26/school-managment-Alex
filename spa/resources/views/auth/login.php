<?php
/**
 * LOGIN VIEW — spa/resources/views/auth/login.php
 *
 * Pàgina d'inici de sessió via GitHub OAuth
 */
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sessió — School Management</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #4f46e5;
            --primary-dark: #3730a3;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-400: #9ca3af;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .login-card {
            background: white;
            border-radius: 16px;
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 25px 50px rgba(0,0,0,.3);
            text-align: center;
        }

        .logo {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }

        .login-card h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.25rem;
        }

        .login-card p.subtitle {
            color: var(--gray-400);
            font-size: .875rem;
            margin-bottom: 2rem;
        }

        .btn-github {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            width: 100%;
            padding: .875rem 1.5rem;
            background: #24292f;
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background .15s, transform .1s;
        }

        .btn-github:hover { background: #1c2128; transform: translateY(-1px); }
        .btn-github:active { transform: translateY(0); }

        .btn-github svg {
            width: 20px;
            height: 20px;
            fill: white;
            flex-shrink: 0;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1.5rem 0;
            color: var(--gray-400);
            font-size: .8rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-200);
        }

        .alert-error {
            background: #fef2f2;
            color: #991b1b;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: .75rem 1rem;
            font-size: .875rem;
            margin-bottom: 1.5rem;
            text-align: left;
        }

        .config-warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 1rem;
            font-size: .8rem;
            color: #92400e;
            text-align: left;
            margin-top: 1.5rem;
        }

        .config-warning code {
            background: #fef3c7;
            padding: .1rem .3rem;
            border-radius: 4px;
            font-family: monospace;
            font-size: .75rem;
        }

        .footer-note {
            margin-top: 1.5rem;
            font-size: .75rem;
            color: var(--gray-400);
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="logo">🎓</div>
        <h1>School Management</h1>
        <p class="subtitle">Sistema de gestió escolar</p>

        <?php if (!empty($error)): ?>
            <div class="alert-error">
                ❌ <?= e(match($error) {
                    'no_config'              => 'El OAuth no està configurat. Afegeix les variables al .env.',
                    'invalid_state'          => 'Error de seguretat CSRF. Torna-ho a intentar.',
                    'token_exchange_failed'  => 'No s\'ha pogut obtenir el token de GitHub.',
                    'user_fetch_failed'      => 'No s\'ha pogut obtenir la informació de l\'usuari.',
                    default                  => $error
                }) ?>
            </div>
        <?php endif; ?>

        <?php if ($hasConfig): ?>
            <a href="/auth/github" class="btn-github">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                </svg>
                Iniciar sessió amb GitHub
            </a>
        <?php else: ?>
            <a href="#" class="btn-github" style="opacity:.5;cursor:not-allowed;" onclick="return false">
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/>
                </svg>
                Iniciar sessió amb GitHub
            </a>

            <div class="config-warning">
                <strong>⚙️ Configuració necessària</strong><br><br>
                Afegeix al fitxer <code>.env</code> de l'arrel del projecte:<br><br>
                <code>GITHUB_CLIENT_ID=el_teu_client_id</code><br>
                <code>GITHUB_CLIENT_SECRET=el_teu_secret</code><br>
                <code>APP_URL=http://localhost:8000</code><br><br>
                Crea l'app a: <strong>GitHub → Settings → Developer settings → OAuth Apps</strong><br>
                Callback URL: <code>http://localhost:8000/auth/callback</code>
            </div>
        <?php endif; ?>

        <p class="footer-note">
            Sistema de gestió escolar — Autenticació via OAuth 2.0
        </p>
    </div>
</body>
</html>
