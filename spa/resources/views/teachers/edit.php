<?php
$layout      = 'app';
$pageTitle   = 'Editar professor';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Professors', 'url' => '/teachers'],
    ['label' => 'Editar: ' . ($teacher['name'] ?? '')],
];

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title">✏️ Editar professor</div>
        <div class="page-subtitle"><?= e($teacher['name'] ?? '') ?></div>
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

        <form action="/teachers/<?= e($teacher['id']) ?>" method="POST">
            <input type="hidden" name="_method" value="PUT">

            <div class="form-group">
                <label class="form-label" for="name">
                    Nom complet <span class="required">*</span>
                </label>
                <input
                    type="text" id="name" name="name"
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                    value="<?= e($teacher['name'] ?? '') ?>"
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
                    type="text" id="specialty" name="specialty"
                    class="form-control <?= isset($errors['specialty']) ? 'is-invalid' : '' ?>"
                    value="<?= e($teacher['specialty'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['specialty'])): ?>
                    <div class="form-error">⚠ <?= e($errors['specialty']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Desar canvis</button>
                <a href="/teachers" class="btn btn-secondary">Cancel·lar</a>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';
