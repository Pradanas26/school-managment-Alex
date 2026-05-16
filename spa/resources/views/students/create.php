<?php
$layout      = 'app';
$pageTitle   = 'Nou estudiant';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Estudiants', 'url' => '/students'],
    ['label' => 'Nou estudiant'],
];

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title">👩‍🎓 Nou estudiant</div>
        <div class="page-subtitle">Registra un nou estudiant al sistema</div>
    </div>
    <a href="/students" class="btn btn-secondary">← Tornar</a>
</div>

<div class="card" style="max-width:560px">
    <div class="card-header">
        <span class="card-title">Dades de l'estudiant</span>
    </div>
    <div class="card-body">

        <?php if (!empty($errors['api'])): ?>
        <div class="alert alert-error">
            <span class="alert-icon">❌</span>
            <?= e($errors['api']) ?>
        </div>
        <?php endif; ?>

        <form action="/students" method="POST">

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
                    placeholder="p.ex. Maria Garcia López"
                    required
                >
                <?php if (isset($errors['name'])): ?>
                    <div class="form-error">⚠ <?= e($errors['name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">
                    Correu electrònic <span class="required">*</span>
                </label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                    value="<?= e($old['email'] ?? '') ?>"
                    placeholder="p.ex. maria.garcia@escola.cat"
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error">⚠ <?= e($errors['email']) ?></div>
                <?php endif; ?>
                <div class="form-hint">Ha de ser un email únic al sistema.</div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✅ Crear estudiant</button>
                <a href="/students" class="btn btn-secondary">Cancel·lar</a>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';
