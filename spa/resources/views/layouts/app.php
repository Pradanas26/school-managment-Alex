<?php
use App\Http\Middleware\AuthMiddleware;
$authUser = AuthMiddleware::user();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'School Management') ?> — School SPA</title>

    <!-- Alpine.js per reactivitat (equivalent a Livewire light) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* ── Reset & Base ───────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #4f46e5;
            --primary-dark: #3730a3;
            --primary-light:#e0e7ff;
            --accent:       #06b6d4;
            --success:      #10b981;
            --danger:       #ef4444;
            --warning:      #f59e0b;
            --gray-50:  #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --sidebar-w: 240px;
            --topbar-h:  64px;
            --radius:    10px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.08);
            --shadow:    0 4px 12px rgba(0,0,0,.10);
            --shadow-lg: 0 8px 24px rgba(0,0,0,.12);
        }

        html, body { height: 100%; font-family: 'Segoe UI', system-ui, sans-serif; background: var(--gray-100); color: var(--gray-800); font-size: 15px; }

        /* ── Layout Shell ───────────────────────────────────────────── */
        .shell {
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ── Sidebar ────────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--gray-900);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            transition: transform .25s;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,.08);
            display: flex; align-items: center; gap: .75rem;
        }
        .sidebar-brand .icon {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem;
        }
        .sidebar-brand span {
            font-size: .95rem; font-weight: 700;
            color: white; letter-spacing: -.01em;
        }
        .sidebar-brand small {
            display: block; font-size: .72rem;
            color: var(--gray-400); font-weight: 400;
        }

        .sidebar-nav {
            flex: 1; padding: 1rem 0;
            overflow-y: auto;
        }

        .nav-section {
            padding: .5rem 1.5rem .25rem;
            font-size: .7rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .08em;
            color: var(--gray-500);
        }

        .nav-link {
            display: flex; align-items: center; gap: .75rem;
            padding: .6rem 1.5rem;
            color: var(--gray-400);
            text-decoration: none;
            font-size: .875rem;
            transition: all .15s;
            border-left: 3px solid transparent;
        }
        .nav-link:hover { background: rgba(255,255,255,.05); color: white; }
        .nav-link.active {
            background: rgba(79,70,229,.2);
            color: white;
            border-left-color: var(--primary);
        }
        .nav-link .nav-icon { font-size: 1.1rem; width: 20px; text-align: center; }

        .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(255,255,255,.08);
        }
        .api-badge {
            display: flex; align-items: center; gap: .5rem;
            font-size: .75rem; color: var(--gray-400);
        }
        .api-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--danger);
        }
        .api-dot.online { background: var(--success); }

        /* ── Main Content ───────────────────────────────────────────── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex; flex-direction: column;
            min-height: 100vh;
        }

        /* ── Topbar ─────────────────────────────────────────────────── */
        .topbar {
            height: var(--topbar-h);
            background: white;
            border-bottom: 1px solid var(--gray-200);
            display: flex; align-items: center;
            padding: 0 2rem;
            gap: 1rem;
            position: sticky; top: 0; z-index: 50;
            box-shadow: var(--shadow-sm);
            margin-left: -100px;
        }

        .breadcrumb {
            display: flex; align-items: center; gap: .5rem;
            font-size: .875rem; color: var(--gray-500);
        }
        .breadcrumb a { color: var(--primary); text-decoration: none; }
        .breadcrumb a:hover { text-decoration: underline; }
        .breadcrumb .sep { color: var(--gray-300); }
        .breadcrumb .current { color: var(--gray-700); font-weight: 500; }

        .topbar-spacer { flex: 1; }

        .topbar-actions { display: flex; align-items: center; gap: .75rem; }

        /* ── Page Content ───────────────────────────────────────────── */
        .page-content {
            flex: 1;
            padding: 4rem 4rem 4rem 0rem;
            width: 100%;
            margin-left: -30px;
        }

        .page-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;
        }
        .page-title { font-size: 1.5rem; font-weight: 700; color: var(--gray-900); }
        .page-subtitle { font-size: .875rem; color: var(--gray-500); margin-top: .2rem; }

        /* ── Alerts ─────────────────────────────────────────────────── */
        .alert {
            padding: .875rem 1rem; border-radius: var(--radius);
            margin-bottom: 1.25rem;
            display: flex; align-items: flex-start; gap: .75rem;
            font-size: .875rem;
        }
        .alert-success { background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
        .alert-error   { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .alert-warning { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
        .alert-info    { background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd; }
        .alert-icon    { font-size: 1.1rem; flex-shrink: 0; }

        /* ── Cards ──────────────────────────────────────────────────── */
        .card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            overflow: hidden;
        }
        .card-header {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-weight: 600; font-size: .95rem; color: var(--gray-800); }
        .card-body { padding: 1.5rem; }

        /* ── Stat Cards ─────────────────────────────────────────────── */
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; }
        .stat-card {
            background: white;
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow-sm);
            display: flex; align-items: center; gap: 1rem;
        }
        .stat-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }
        .stat-icon.blue   { background: #dbeafe; }
        .stat-icon.green  { background: #d1fae5; }
        .stat-icon.purple { background: #ede9fe; }
        .stat-icon.orange { background: #ffedd5; }
        .stat-number { font-size: 1.75rem; font-weight: 700; color: var(--gray-900); line-height: 1; }
        .stat-label  { font-size: .8rem; color: var(--gray-500); margin-top: .2rem; }

        /* ── Table ──────────────────────────────────────────────────── */
        .table-container { overflow-x: auto; width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            padding: .75rem 1rem;
            text-align: left;
            font-size: .75rem; font-weight: 600;
            text-transform: uppercase; letter-spacing: .06em;
            color: var(--gray-500);
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            white-space: nowrap;
        }
        tbody td {
            padding: .875rem 1rem;
            font-size: .875rem;
            color: var(--gray-700);
            border-bottom: 1px solid var(--gray-100);
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--gray-50); }

        .td-id {
            font-family: monospace; font-size: .75rem;
            color: var(--gray-400); max-width: 100px;
            overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
        }
        .td-actions { display: flex; gap: .5rem; flex-wrap: wrap; }

        .empty-row td {
            text-align: center; padding: 3rem;
            color: var(--gray-400); font-style: italic;
        }

        /* ── Badges ─────────────────────────────────────────────────── */
        .badge {
            display: inline-flex; align-items: center; gap: .3rem;
            padding: .2rem .6rem; border-radius: 999px;
            font-size: .75rem; font-weight: 500;
        }
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-gray    { background: var(--gray-100); color: var(--gray-500); }
        .badge-primary { background: var(--primary-light); color: var(--primary-dark); }

        /* ── Buttons ────────────────────────────────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1rem;
            border-radius: 7px; border: none; cursor: pointer;
            font-size: .85rem; font-weight: 500;
            text-decoration: none;
            transition: all .15s;
            white-space: nowrap;
        }
        .btn:disabled { opacity: .5; cursor: not-allowed; }

        .btn-primary   { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); }

        .btn-secondary { background: white; color: var(--gray-700); border: 1px solid var(--gray-300); }
        .btn-secondary:hover { background: var(--gray-50); }

        .btn-danger    { background: #fee2e2; color: var(--danger); }
        .btn-danger:hover { background: #fecaca; }

        .btn-success   { background: #d1fae5; color: #065f46; }
        .btn-success:hover { background: #a7f3d0; }

        .btn-sm { padding: .3rem .7rem; font-size: .8rem; }

        /* ── Forms ──────────────────────────────────────────────────── */
        .form-group { margin-bottom: 1.25rem; }
        .form-label {
            display: block; margin-bottom: .4rem;
            font-size: .875rem; font-weight: 500; color: var(--gray-700);
        }
        .form-label .required { color: var(--danger); margin-left: .2rem; }
        .form-control {
            width: 100%;
            padding: .6rem .875rem;
            border: 1px solid var(--gray-300);
            border-radius: 7px;
            font-size: .875rem;
            color: var(--gray-800);
            background: white;
            transition: border-color .15s, box-shadow .15s;
            appearance: none;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,.15);
        }
        .form-control.is-invalid { border-color: var(--danger); }
        .form-error { font-size: .8rem; color: var(--danger); margin-top: .3rem; }
        .form-hint  { font-size: .8rem; color: var(--gray-400); margin-top: .3rem; }

        select.form-control { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right .625rem center; background-size: 1.2em; padding-right: 2.5rem; }

        .form-actions { display: flex; gap: .75rem; align-items: center; margin-top: 1.5rem; }

        /* ── Modal (Alpine) ─────────────────────────────────────────── */
        .modal-backdrop {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 200;
            display: flex; align-items: center; justify-content: center;
            padding: 1rem;
        }
        .modal {
            background: white; border-radius: 12px;
            padding: 1.5rem; max-width: 420px; width: 100%;
            box-shadow: var(--shadow-lg);
        }
        .modal-title { font-size: 1.05rem; font-weight: 600; margin-bottom: .75rem; }
        .modal-body  { font-size: .875rem; color: var(--gray-600); margin-bottom: 1.25rem; }
        .modal-footer { display: flex; gap: .75rem; justify-content: flex-end; }

        /* ── Assign form inline ─────────────────────────────────────── */
        .assign-form { display: flex; gap: .5rem; align-items: center; flex-wrap: wrap; }
        .assign-form select { width: auto; min-width: 150px; flex: 1; }

        /* ── Responsive ─────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .main { margin-left: 0; }
            .topbar { padding: 0 1rem; }
            .page-content { padding: 1rem; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
<div class="shell">

    <!-- ── Sidebar ── -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="icon">🎓</div>
            <div>
                <span>School Manager</span>
                <small>Laravel SPA</small>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">General</div>
            <a href="/" class="nav-link <?= active('/') ?>">
                <span class="nav-icon">📊</span> Dashboard
            </a>

            <div class="nav-section" style="margin-top:.75rem">Gestió</div>
            <a href="/students" class="nav-link <?= active('/students') ?>">
                <span class="nav-icon">👩‍🎓</span> Estudiants
            </a>
            <a href="/teachers" class="nav-link <?= active('/teachers') ?>">
                <span class="nav-icon">👨‍🏫</span> Professors
            </a>
            <a href="/subjects" class="nav-link <?= active('/subjects') ?>">
                <span class="nav-icon">📚</span> Assignatures
            </a>
        </nav>

        <div class="sidebar-footer">
            <?php if ($authUser): ?>
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.6rem">
                <?php if (!empty($authUser['avatar_url'])): ?>
                <img src="<?= e($authUser['avatar_url']) ?>" alt="avatar"
                     style="width:32px;height:32px;border-radius:50%;border:2px solid rgba(255,255,255,.2)">
                <?php else: ?>
                <div style="width:32px;height:32px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-size:.9rem">👤</div>
                <?php endif; ?>
                <div style="overflow:hidden">
                    <div style="font-size:.75rem;color:white;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
                        <?= e($authUser['name'] ?? $authUser['login']) ?>
                    </div>
                    <div style="font-size:.65rem;color:var(--gray-500)">@<?= e($authUser['login'] ?? '') ?></div>
                </div>
            </div>
            <a href="/logout" style="display:block;text-align:center;font-size:.72rem;color:var(--gray-500);text-decoration:none;padding:.3rem;border-radius:6px;border:1px solid rgba(255,255,255,.1);transition:background .15s" onmouseover="this.style.background='rgba(255,255,255,.05)'" onmouseout="this.style.background=''">
                🚪 Tancar sessió
            </a>
            <?php endif; ?>
            <div class="api-badge" style="margin-top:.6rem">
                <div class="api-dot online"></div>
                API Backend
            </div>
            <div style="font-size:.7rem;color:var(--gray-600);margin-top:.3rem">
                <?= e(config('api_url')) ?>
            </div>
        </div>
    </aside>

    <!-- ── Main ── -->
    <div class="main">

        <!-- ── Topbar ── -->
        <header class="topbar">
            <button onclick="document.getElementById('sidebar').classList.toggle('open')"
                    style="display:none;background:none;border:none;cursor:pointer;font-size:1.2rem"
                    class="menu-btn" id="menuBtn">☰</button>

            <nav class="breadcrumb">
                <?php foreach ($breadcrumbs ?? [] as $i => $crumb): ?>
                    <?php if ($i > 0): ?><span class="sep">›</span><?php endif; ?>
                    <?php if (isset($crumb['url'])): ?>
                        <a href="<?= e($crumb['url']) ?>"><?= e($crumb['label']) ?></a>
                    <?php else: ?>
                        <span class="current"><?= e($crumb['label']) ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            </nav>

            <div class="topbar-spacer"></div>

            <div class="topbar-actions">
                <span style="font-size:.8rem;color:var(--gray-400)">
                    <?= date('d/m/Y') ?>
                </span>
                <?php if ($authUser): ?>
                <div style="display:flex;align-items:center;gap:.5rem;margin-left:.5rem;padding:.3rem .75rem;background:var(--gray-100);border-radius:20px">
                    <?php if (!empty($authUser['avatar_url'])): ?>
                    <img src="<?= e($authUser['avatar_url']) ?>" alt="avatar"
                         style="width:24px;height:24px;border-radius:50%">
                    <?php endif; ?>
                    <span style="font-size:.8rem;color:var(--gray-700);font-weight:500">
                        <?= e($authUser['name'] ?? $authUser['login']) ?>
                    </span>
                    <a href="/logout" style="font-size:.75rem;color:var(--gray-400);text-decoration:none" title="Tancar sessió">✕</a>
                </div>
                <?php endif; ?>
            </div>
        </header>

        <!-- ── Flash Messages ── -->
        <?php $flashSuccess = get_flash('success'); $flashError = get_flash('error'); ?>
        <?php if ($flashSuccess): ?>
        <div style="padding: .75rem 2rem 0">
            <div class="alert alert-success">
                <span class="alert-icon">✅</span>
                <span><?= e($flashSuccess) ?></span>
            </div>
        </div>
        <?php endif; ?>
        <?php if ($flashError): ?>
        <div style="padding: .75rem 2rem 0">
            <div class="alert alert-error">
                <span class="alert-icon">❌</span>
                <span><?= e($flashError) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <!-- ── Page Content ── -->
        <main class="page-content">
            <?= $content ?>
        </main>

    </div><!-- /.main -->
</div><!-- /.shell -->

<script>
    // Auto-hide alerts after 4 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            el.style.transition = 'opacity .5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);

    // Mobile menu
    const menuBtn = document.getElementById('menuBtn');
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth <= 768) {
        menuBtn.style.display = 'block';
    }
    document.addEventListener('click', e => {
        if (window.innerWidth <= 768 && !sidebar.contains(e.target) && e.target !== menuBtn) {
            sidebar.classList.remove('open');
        }
    });
</script>
</body>
</html>
<?php
