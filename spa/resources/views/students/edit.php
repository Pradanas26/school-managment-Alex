<?php
$layout      = 'app';
$pageTitle   = 'Editar estudiant';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Estudiants', 'url' => '/students'],
    ['label' => 'Editar: ' . ($student['name'] ?? '')],
];

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title">✏️ Editar estudiant</div>
        <div class="page-subtitle"><?= e($student['name'] ?? '') ?></div>
    </div>
    <a href="/students" class="btn btn-secondary">← Tornar</a>
</div>

<div class="card" style="max-width:560px">
    <div class="card-header">
        <span class="card-title">Dades de l'estudiant</span>
        <span class="td-id" title="<?= e($student['id']) ?>">
            ID: <?= e(substr($student['id'], 0, 8)) ?>…
        </span>
    </div>
    <div class="card-body">

        <?php if (!empty($errors['api'])): ?>
        <div class="alert alert-error">
            <span class="alert-icon">❌</span>
            <?= e($errors['api']) ?>
        </div>
        <?php endif; ?>

        <form action="/students/<?= e($student['id']) ?>" method="POST">
            <input type="hidden" name="_method" value="PUT">

            <div class="form-group">
                <label class="form-label" for="name">
                    Nom complet <span class="required">*</span>
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                    value="<?= e($student['name'] ?? '') ?>"
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
                    value="<?= e($student['email'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['email'])): ?>
                    <div class="form-error">⚠ <?= e($errors['email']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Desar canvis</button>
                <a href="/students" class="btn btn-secondary">Cancel·lar</a>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';
