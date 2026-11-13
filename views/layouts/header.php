<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pondok Keuangan - <?= $title ?? 'Dashboard' ?></title>
    <base href="<?= htmlspecialchars(BASE_URL) ?>">

    <!-- Bootstrap 5.3 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --paper: #f4efe2;
            --paper-2: #efe6d1;
            --ink: #0e0e0e;
            --blue: #2138f5;
            --blue-dark: #1526c9;
            --yellow: #ffd23f;
            --mint: #9be7c4;
            --red: #ff6a5c;
            --violet: #7c6cf0;
            --muted: #5b564c;
            --title: 'Archivo Black', 'Arial Black', sans-serif;
            --body: 'Space Grotesk', 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: var(--body);
            color: var(--ink);
            background: var(--paper);
            background-image: radial-gradient(circle at 1px 1px, rgba(14,14,14,.08) 1px, transparent 0);
            background-size: 24px 24px;
        }
        ::selection { background: var(--yellow); color: var(--ink); }
        h1, h2, h3, h4, h5, h6 { font-family: var(--title); color: var(--ink); }

        /* ---------- Top navigation ---------- */
        .topnav {
            position: sticky; top: 0; z-index: 1030;
            display: flex; align-items: center; gap: .9rem;
            padding: .75rem 1.25rem 1rem;
            background: var(--paper);
            border-bottom: 3px solid var(--ink);
            overflow-x: auto; white-space: nowrap;
            scrollbar-width: none;
        }
        .topnav::-webkit-scrollbar { display: none; }

        .brand { display: inline-flex; align-items: center; gap: .7rem; text-decoration: none; color: var(--ink); flex-shrink: 0; }
        .brand-mark {
            width: 40px; height: 40px; flex-shrink: 0;
            display: grid; place-items: center;
            background: var(--ink); color: var(--yellow);
            border: 2.5px solid var(--ink);
            box-shadow: 4px 4px 0 var(--yellow);
            font-size: 1.05rem;
        }
        .brand-name { font-family: var(--title); font-size: 1rem; letter-spacing: .05em; text-transform: uppercase; line-height: 1; display: block; }
        .brand-sub { display: block; font-size: .54rem; letter-spacing: .2em; text-transform: uppercase; color: var(--muted); margin-top: .3rem; }

        .pill {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .42rem .8rem;
            font-size: .68rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase;
            text-decoration: none; line-height: 1.25; white-space: nowrap;
            border: 2.5px solid var(--ink); background: #fff; color: var(--ink);
            box-shadow: 4px 4px 0 var(--ink);
            transition: transform .12s ease, box-shadow .12s ease, background .12s ease, color .12s ease;
        }
        .pill:hover { transform: translate(-2px,-2px); box-shadow: 6px 6px 0 var(--ink); }
        .pill:active { transform: translate(2px,2px); box-shadow: 1px 1px 0 var(--ink); }
        .pill-blue { background: var(--blue); color: #fff; }
        .pill-blue:hover { background: var(--blue-dark); }
        .pill-yellow { background: var(--yellow); color: var(--ink); }
        .pill-yellow:hover { background: #f0c62f; }
        .pill-red { background: var(--red); color: #fff; }
        .pill-red:hover { background: #ee5546; }
        .pill-green { background: var(--mint); color: var(--ink); }
        .pill.active { background: var(--blue); color: #fff; box-shadow: 4px 4px 0 var(--ink); }

        .header-tools { margin-left: auto; display: flex; align-items: center; gap: .7rem; flex-shrink: 0; }
        .clock { font-family: var(--title); font-size: .95rem; letter-spacing: .01em; white-space: nowrap; }

        /* ---------- Layout ---------- */
        .main-content { width: 100%; max-width: 1240px; margin: 0 auto; padding: 1.5rem 1.25rem 3rem; }
        .main-content h1 { font-size: clamp(1.55rem, 3vw, 2.25rem); line-height: 1.1; margin-bottom: .35rem; }
        .main-content h1 i { color: var(--blue); margin-right: .5rem; }

        /* ---------- Utilities ---------- */
        .text-primary { color: var(--blue) !important; }
        .text-success { color: #12855f !important; }
        .text-danger { color: #d63f31 !important; }
        .text-warning { color: #9a7300 !important; }
        .text-info { color: #3b4bd8 !important; }
        .text-muted { color: var(--muted) !important; }
        .bg-primary-soft { background: rgba(33,56,245,.1) !important; }
        .bg-success-soft { background: rgba(155,231,196,.4) !important; }
        .bg-danger-soft { background: rgba(255,106,92,.16) !important; }
        .bg-info-soft { background: rgba(124,108,240,.16) !important; }

        /* ---------- Cards ---------- */
        .card {
            background: #fff;
            border: 3px solid var(--ink);
            border-radius: 0;
            box-shadow: 6px 6px 0 rgba(14,14,14,.18);
            overflow: hidden;
        }
        .card-header { background: var(--paper); border-bottom: 3px solid var(--ink); padding: .8rem 1.1rem; }
        .card-header.bg-white { background: var(--paper) !important; }
        .card-header.bg-primary { background: var(--blue) !important; color: #fff !important; }
        .card-header h6, .card-header h5 { margin: 0; font-size: .78rem; letter-spacing: .1em; text-transform: uppercase; }
        .card-body { padding: 1.1rem; }

        .stat-card {
            background: #fff;
            border: 3px solid var(--ink);
            box-shadow: 6px 6px 0 rgba(14,14,14,.18);
            padding: 1.2rem;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .stat-card h6 { font-size: .64rem; letter-spacing: .1em; text-transform: uppercase; color: var(--muted); margin-bottom: .4rem; }
        .stat-card h3 { font-family: var(--title); font-size: 1.5rem; margin-bottom: .15rem; }
        .stat-card p { color: var(--muted); }
        .stat-card .icon {
            flex-shrink: 0;
            width: 40px; height: 40px; display: grid; place-items: center;
            font-size: 1.15rem;
            background: var(--yellow); color: var(--ink);
            border: 2.5px solid var(--ink);
            box-shadow: 3px 3px 0 var(--ink);
            transition: background .12s ease;
        }
        .stat-card:hover .icon { background: var(--mint); }

        /* ---------- Badges ---------- */
        .badge {
            border-radius: 0 !important;
            border: 2px solid var(--ink);
            font-weight: 700; text-transform: uppercase;
            font-size: .6rem; letter-spacing: .06em;
            padding: .4em .65em;
        }
        .badge.bg-success { background: var(--mint) !important; color: var(--ink) !important; }
        .badge.bg-warning { background: var(--yellow) !important; color: var(--ink) !important; }
        .badge.bg-danger { background: var(--red) !important; color: #fff !important; }
        .badge.bg-primary { background: var(--blue) !important; color: #fff !important; }
        .badge.bg-secondary { background: #e6dfd0 !important; color: var(--ink) !important; }
        .status-lunas { background: var(--mint) !important; color: var(--ink) !important; }
        .status-belum { background: var(--yellow) !important; color: var(--ink) !important; }
        .status-minus { background: var(--red) !important; color: #fff !important; }

        /* ---------- Tables ---------- */
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: var(--ink);
            --bs-table-border-color: var(--ink);
            --bs-table-hover-bg: rgba(255,210,63,.28);
            border: 2.5px solid var(--ink);
            margin-bottom: 0;
        }
        .table thead th {
            background: var(--ink); color: var(--paper);
            border: 2px solid var(--ink);
            font-family: var(--title);
            font-size: .62rem; letter-spacing: .08em; text-transform: uppercase;
            padding: .6rem .8rem; white-space: nowrap;
        }
        .table tbody td { border: 1.5px solid #c9c2b2; padding: .55rem .8rem; vertical-align: middle; }
        .table-hover > tbody > tr:hover > * { background: rgba(255,210,63,.28); color: var(--ink); }

        /* ---------- Forms ---------- */
        .form-label { font-size: .64rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; color: var(--muted); margin-bottom: .45rem; }
        .form-control, .form-select {
            background: #fff; color: var(--ink);
            border: 2.5px solid var(--ink);
            border-radius: 0;
            font-family: var(--body); font-size: .86rem;
            padding: .55rem .8rem;
            box-shadow: 3px 3px 0 rgba(14,14,14,.15);
        }
        .form-control:focus, .form-select:focus {
            background: #fff; color: var(--ink);
            border-color: var(--blue);
            box-shadow: 0 0 0 4px rgba(33,56,245,.16);
        }
        .form-control::placeholder { color: #9a9284; opacity: 1; }
        .input-group-text {
            background: var(--paper) !important;
            border: 2.5px solid var(--ink);
            border-right: none;
            border-radius: 0 !important;
            color: var(--ink); font-weight: 700;
            box-shadow: 3px 3px 0 rgba(14,14,14,.15);
        }
        .input-group > :not(:first-child) { border-left: 0; }
        .form-check-input { border: 2px solid var(--ink); border-radius: 0; }
        .form-check-input:checked { background-color: var(--blue); border-color: var(--blue); }
        .form-check-label { color: var(--muted); font-size: .82rem; }

        /* ---------- Buttons ---------- */
        .btn {
            border-radius: 0 !important;
            border: 2.5px solid var(--ink);
            font-family: var(--body); font-weight: 700;
            text-transform: uppercase; letter-spacing: .04em;
            font-size: .75rem; padding: .5rem .85rem;
            box-shadow: 3px 3px 0 var(--ink);
            transition: transform .1s ease, box-shadow .1s ease, background .12s ease, color .12s ease;
        }
        .btn:hover { transform: translate(-1px,-1px); box-shadow: 5px 5px 0 var(--ink); }
        .btn:active { transform: translate(2px,2px); box-shadow: 1px 1px 0 var(--ink); }
        .btn-primary { background: var(--blue); color: #fff; }
        .btn-primary:hover { background: var(--blue-dark); color: #fff; }
        .btn-warning, .btn-gold { background: var(--yellow); color: var(--ink); }
        .btn-warning:hover, .btn-gold:hover { background: #f0c62f; color: var(--ink); }
        .btn-success { background: var(--mint); color: var(--ink); }
        .btn-success:hover { background: #84dab2; color: var(--ink); }
        .btn-danger { background: var(--red); color: #fff; }
        .btn-danger:hover { background: #ee5546; color: #fff; }
        .btn-info { background: var(--violet); color: #fff; }
        .btn-secondary { background: #fff; color: var(--ink); }
        .btn-secondary:hover { background: var(--paper-2); color: var(--ink); }
        .btn-outline-primary { background: #fff; color: var(--blue); border-color: var(--ink); }
        .btn-outline-primary:hover { background: var(--blue); color: #fff; }
        .btn-outline-danger { background: #fff; color: #d63f31; border-color: var(--ink); }
        .btn-outline-danger:hover { background: var(--red); color: #fff; }
        .btn-outline-success { background: #fff; color: #12855f; border-color: var(--ink); }
        .btn-outline-success:hover { background: var(--mint); color: var(--ink); }
        .btn-outline-secondary, .btn-outline-info { background: #fff; color: var(--ink); border-color: var(--ink); }
        .btn-outline-secondary:hover, .btn-outline-info:hover { background: var(--paper-2); color: var(--ink); }
        .btn-sm { padding: .3rem .5rem; font-size: .66rem; box-shadow: 2px 2px 0 var(--ink); }
        .btn-sm:hover { box-shadow: 4px 4px 0 var(--ink); }
        .btn-lg { padding: .7rem 1.2rem; font-size: .85rem; }

        /* ---------- Dropdown ---------- */
        .dropdown-menu { background: #fff; border: 2.5px solid var(--ink); border-radius: 0; box-shadow: 6px 6px 0 var(--ink); padding: .3rem; }
        .dropdown-item { color: var(--ink); font-size: .8rem; padding: .45rem .7rem; font-weight: 500; }
        .dropdown-item:hover, .dropdown-item:focus { background: var(--yellow); color: var(--ink); }

        /* ---------- Alerts ---------- */
        .alert { border-radius: 0; border: 2.5px solid var(--ink); box-shadow: 4px 4px 0 rgba(14,14,14,.2); background: #fff; color: var(--ink); }
        .alert-success { background: #effbf5; border-color: #12855f; }
        .alert-danger { background: #fff2f0; border-color: #d63f31; }

        /* ---------- Modal ---------- */
        .modal-content { background: #fff; color: var(--ink); border: 3px solid var(--ink); border-radius: 0; box-shadow: 12px 12px 0 rgba(14,14,14,.35); }
        .modal-header { background: var(--paper); border-bottom: 3px solid var(--ink); }
        .modal-footer { border-top: 3px solid var(--ink); }
        .modal-title { font-family: var(--title); font-size: .95rem; text-transform: uppercase; letter-spacing: .05em; }
        .btn-close { filter: none; opacity: 1; }
        .modal-backdrop.show { opacity: .55; }

        /* ---------- Misc ---------- */
        .list-group-item { background: #fff; border: 2px solid var(--ink); border-radius: 0; color: var(--ink); }
        .list-group-item-action:hover { background: var(--yellow); color: var(--ink); }
        .pagination { gap: .4rem; }
        .page-link { border: 2.5px solid var(--ink); border-radius: 0 !important; color: var(--ink); background: #fff; font-weight: 700; box-shadow: 2px 2px 0 var(--ink); }
        .page-item.active .page-link { background: var(--blue); color: #fff; border-color: var(--ink); }
        .page-link:hover { background: var(--yellow); color: var(--ink); }
        .chart-container { position: relative; height: 300px; }

        @media (max-width: 991px) {
            .clock { display: none; }
        }
        @media (max-width: 767px) {
            .main-content { padding: 1.1rem .8rem 2.5rem; }
            .brand-sub { display: none; }
            .topnav { padding: .6rem .8rem .85rem; gap: .6rem; }
        }
    </style>
</head>
<body>
    <header class="topnav">
        <a class="brand" href="dashboard">
            <span class="brand-mark"><i class="fas fa-mosque"></i></span>
            <span>
                <span class="brand-name">Pondok Keuangan</span>
                <span class="brand-sub">Sistem Kas &amp; Infaq</span>
            </span>
        </a>

        <nav class="d-flex align-items-center gap-2">
            <a href="dashboard" class="pill<?= strpos($_SERVER['REQUEST_URI'], 'dashboard') !== false ? ' active' : '' ?>">Dashboard</a>
            <a href="income" class="pill<?= strpos($_SERVER['REQUEST_URI'], 'income') !== false ? ' active' : '' ?>">Pemasukkan</a>
            <a href="expense" class="pill<?= strpos($_SERVER['REQUEST_URI'], 'expense') !== false ? ' active' : '' ?>">Pengeluaran</a>
            <a href="infaq" class="pill<?= strpos($_SERVER['REQUEST_URI'], 'infaq') !== false ? ' active' : '' ?>">Infaq</a>
            <a href="program" class="pill<?= strpos($_SERVER['REQUEST_URI'], 'program') !== false ? ' active' : '' ?>">Program</a>
            <a href="alumni" class="pill<?= strpos($_SERVER['REQUEST_URI'], 'alumni') !== false ? ' active' : '' ?>">Alumni</a>
        </nav>

        <div class="header-tools">
            <span class="clock" id="topbarClock"><?= date('d M Y') ?></span>
            <a href="expense" class="pill pill-yellow"><i class="fas fa-plus"></i>Transaksi</a>
            <a href="logout" class="pill pill-red" title="Logout"><i class="fas fa-arrow-right-from-bracket"></i></a>
        </div>
    </header>

    <div class="main-content">
        <!-- Notifications -->
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999;">
            <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['success']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?= htmlspecialchars($_SESSION['error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); endif; ?>
        </div>
