<?php
$layout      = 'app';
$pageTitle   = '404 — No trobat';
$breadcrumbs = [['label' => '404']];
ob_start();
http_response_code(404);
?>

<div style="text-align:center;padding:4rem 2rem">
    <div style="font-size:4rem;margin-bottom:1rem">🔍</div>
    <h1 style="font-size:2rem;color:var(--gray-700);margin-bottom:.5rem">404 — Pàgina no trobada</h1>
    <p style="color:var(--gray-500);margin-bottom:2rem">La ruta que has demanat no existeix.</p>
    <a href="/" class="btn btn-primary">← Tornar al dashboard</a>
</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';
