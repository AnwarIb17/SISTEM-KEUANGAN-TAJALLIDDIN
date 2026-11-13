<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pondok Keuangan</title>
    <base href="<?= htmlspecialchars(BASE_URL) ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --paper: #f4efe2;
            --ink: #0e0e0e;
            --blue: #2138f5;
            --yellow: #ffd23f;
            --red: #ff6a5c;
            --muted: #5b564c;
            --title: 'Archivo Black', 'Arial Black', sans-serif;
            --body: 'Space Grotesk', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center;
            padding: 1.5rem;
            font-family: var(--body);
            color: var(--ink);
            background: var(--paper);
            background-image: radial-gradient(circle at 1px 1px, rgba(14,14,14,.09) 1px, transparent 0);
            background-size: 22px 22px;
        }
        .login-card {
            width: 100%; max-width: 420px;
            background: #fff;
            border: 3px solid var(--ink);
            box-shadow: 10px 10px 0 var(--ink);
            padding: 0 2rem 2.25rem;
            animation: rise .4s ease-out;
        }
        @keyframes rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .login-tag {
            margin: 0 -2rem 1.9rem;
            padding: .5rem 2rem;
            background: var(--yellow);
            border-bottom: 3px solid var(--ink);
            font-family: var(--title);
            font-size: .6rem;
            letter-spacing: .2em;
            text-transform: uppercase;
            display: flex; justify-content: space-between;
        }
        .brand {
            font-family: var(--title);
            font-size: 1.45rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            line-height: 1.1;
        }
        .brand-sub {
            font-size: .58rem;
            font-weight: 600;
            letter-spacing: .22em;
            text-transform: uppercase;
            color: var(--muted);
            margin-top: .4rem;
            margin-bottom: 1.5rem;
        }
        .divider { height: 3px; background: var(--ink); margin-bottom: 1.5rem; }
        .form-label { font-size: .64rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); margin-bottom: .45rem; }
        .input-group-text {
            background: var(--paper);
            border: 2.5px solid var(--ink);
            border-right: none;
            border-radius: 0;
            color: var(--ink);
            box-shadow: 3px 3px 0 rgba(14,14,14,.15);
        }
        .form-control {
            background: #fff;
            border: 2.5px solid var(--ink);
            border-radius: 0;
            color: var(--ink);
            font-size: .88rem;
            padding: .6rem .85rem;
            box-shadow: 3px 3px 0 rgba(14,14,14,.15);
        }
        .input-group > :not(:first-child) { border-left: 0; }
        .form-control::placeholder { color: #9a9284; opacity: 1; }
        .form-control:focus {
            background: #fff;
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(33,56,245,.16);
            color: var(--ink);
        }
        .btn-login {
            width: 100%;
            background: var(--yellow);
            color: var(--ink);
            border: 2.5px solid var(--ink);
            border-radius: 0;
            box-shadow: 4px 4px 0 var(--ink);
            padding: .68rem;
            font-family: var(--body);
            font-weight: 700;
            font-size: .8rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            transition: transform .1s ease, box-shadow .1s ease, background .12s ease;
        }
        .btn-login:hover { background: #f0c62f; transform: translate(-1px,-1px); box-shadow: 6px 6px 0 var(--ink); }
        .btn-login:active { transform: translate(2px,2px); box-shadow: 1px 1px 0 var(--ink); }
        .hint { text-align: center; font-size: .7rem; color: var(--muted); margin-top: 1.1rem; margin-bottom: 0; letter-spacing: .03em; }
        .alert-danger {
            background: #fff2f0;
            border: 2.5px solid var(--red);
            border-radius: 0;
            color: var(--ink);
            box-shadow: 4px 4px 0 rgba(14,14,14,.2);
            font-size: .8rem;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-tag"><span>Admin Access</span><span>01</span></div>
        <div class="brand">Pondok Keuangan</div>
        <div class="brand-sub">Sistem Pengelolaan Keuangan</div>
        <div class="divider"></div>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger mb-3">
            <?= htmlspecialchars($_SESSION['error']) ?>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <form method="POST" action="login">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
                </div>
            </div>
            <button type="submit" class="btn-login">Masuk</button>
            <p class="hint">Default: admin / password</p>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
