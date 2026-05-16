<?php
$layout      = 'app';
$pageTitle   = 'Nova assignatura';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Assignatures', 'url' => '/subjects'],
    ['label' => 'Nova assignatura'],
];
ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title">📚 Nova assignatura</div>
        <div class="page-subtitle">Registra una nova assignatura al sistema</div>
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

        <form action="/subjects" method="POST">

            <div class="form-group">
                <label class="form-label" for="name">
                    Nom de l'assignatura <span class="required">*</span>
                </label>
                <input
                    type="text" id="name" name="name"
                    class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                    value="<?= e($old['name'] ?? '') ?>"
                    placeholder="p.ex. Programació, Bases de Dades, Anglès…"
                    required
                >
                <?php if (isset($errors['name'])): ?>
                    <div class="form-error">⚠ <?= e($errors['name']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label class="form-label" for="courseId">
                    Curs <span class="required">*</span>
                </label>
                <?php if (!empty($courses)): ?>
                <select id="courseId" name="courseId"
                        class="form-control <?= isset($errors['courseId']) ? 'is-invalid' : '' ?>"
                        required>
                    <option value="">-- Selecciona un curs --</option>
                    <?php foreach ($courses as $c): ?>
                    <option value="<?= e($c['id']) ?>"
                        <?= ($old['courseId'] ?? '') === $c['id'] ? 'selected' : '' ?>>
                        <?= e($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?php else: ?>
                <div class="alert" style="background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:.75rem;font-size:.875rem;color:#92400e">
                    ⚠️ No hi ha cursos disponibles. Crea un curs primer.
                </div>
                <input type="text" id="courseId" name="courseId"
                    class="form-control <?= isset($errors['courseId']) ? 'is-invalid' : '' ?>"
                    value="<?= e($old['courseId'] ?? '') ?>"
                    placeholder="ID del curs"
                    style="margin-top:.5rem"
                    required>
                <?php endif; ?>
                <?php if (isset($errors['courseId'])): ?>
                    <div class="form-error">⚠ <?= e($errors['courseId']) ?></div>
                <?php endif; ?>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">✅ Crear assignatura</button>
                <a href="/subjects" class="btn btn-secondary">Cancel·lar</a>
            </div>

        </form>
    </div>
</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';