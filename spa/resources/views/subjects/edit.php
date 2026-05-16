<?php
$layout      = 'app';
$pageTitle   = 'Editar assignatura';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Assignatures', 'url' => '/subjects'],
    ['label' => 'Editar: ' . ($subject['name'] ?? '')],
];

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title">✏️ Editar assignatura</div>
        <div class="page-subtitle"><?= e($subject['name'] ?? '') ?></div>
    </div>
    <a href="/subjects" class="btn btn-secondary">← Tornar</a>
</div>

<div class="card" style="max-width:560px">
    <div class="card-header">
        <span class="card-title">Dades de l'assignatura</span>
    </div>
    <div class="card-body">

        <?php if (!empty($errors['api'])): ?>
        <div class="alert alert-error">
            <span class="alert-icon">❌</span><?= e($errors['api']) ?>
        </div>
        <?php endif; ?>

        <form action="/subjects/<?= e($subject['id']) ?>" method="POST">
            <input type="hidden" name="_method" value="PUT">

            <div class="form-group">
                <label class="form-label" for="name">
                    Nom de l'assignatura <span class="required">*</span>
                </label>
                <input
                    type="text" id="name" name="name"
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                    value="<?= e($subject['name'] ?? '') ?>"
                    required
                >
                <?php if (isset($errors['name'])): ?>
                    <div class="form-error">⚠ <?= e($errors['name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label">Curs actual</label>
                <input type="text" class="form-control"
                       value="<?= e($coursesMap[$subject['courseId']] ?? $subject['courseId']) ?>" disabled readonly>
                <div class="form-hint">El curs no es pot canviar un cop creada l'assignatura.</div>
            </div>

            <?php if (!empty($subject['teacherId'])): ?>
            <div class="form-group">
                <label class="form-label">Professor assignat</label>
                <input type="text" class="form-control"
                       value="<?= e($teachersMap[$subject['teacherId']] ?? $subject['teacherId']) ?>" disabled readonly>
                <div class="form-hint">Per canviar el professor, usa el botó "Assignar" a la llista d'assignatures.</div>
            </div>
            <?php endif; ?>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Desar canvis</button>
                <a href="/subjects" class="btn btn-secondary">Cancel·lar</a>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';
