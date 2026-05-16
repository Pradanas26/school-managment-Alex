<?php
$layout      = 'app';
$pageTitle   = 'Nou professor';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Professors', 'url' => '/teachers'],
    ['label' => 'Nou professor'],
];

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title">👨‍🏫 Nou professor</div>
        <div class="page-subtitle">Registra un nou professor al sistema</div>
    </div>
    <a href="/teachers" class="btn btn-secondary">← Tornar</a>
</div>

<div class="card" style="max-width:560px">
    <div class="card-header">
        <span class="card-title">Dades del professor</span>
    </div>
    <div class="card-body">

        <?php if (!empty($errors['api'])): ?>
        <div class="alert alert-error">
            <span class="alert-icon">❌</span><?= e($errors['api']) ?>
        </div>
        <?php endif; ?>

        <form action="/teachers" method="POST">

            <div class="form-group">
                <label class="form-label" for="name">
                    Nom complet <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                    value="<?= e($old['name'] ?? '') ?>"
                    placeholder="p.ex. Joan Martínez Puig"
                    required
                >
                <?php if (isset($errors['name'])): ?>
                    <div class="form-error">⚠ <?= e($errors['name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="specialty">
                    Especialitat <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="specialty"
                    name="specialty"
                    class="form-control <?= isset($errors['specialty']) ? 'is-invalid' : '' ?>"
                    value="<?= e($old['specialty'] ?? '') ?>"
                    placeholder="p.ex. Informàtica, Matemàtiques, Anglès…"
                    required
                >
                <?php if (isset($errors['specialty'])): ?>
                    <div class="form-error">⚠ <?= e($errors['specialty']) ?></div>
                <?php endif; ?>
                <div class="form-hint">Àrea de coneixement principal del professor.</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✅ Crear professor</button>
                <a href="/teachers" class="btn btn-secondary">Cancel·lar</a>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';
