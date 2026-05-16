<?php
$layout      = 'app';
$pageTitle   = 'Estudiants';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Estudiants'],
];

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title">Estudiants</div>
        <div class="page-subtitle"><?= count($students) ?> estudiants registrats</div>
    </div>
    <a href="/students/create" class="btn btn-primary">
        ＋ Nou estudiant
    </a>
</div>

<?php if ($error): ?>
<div class="alert alert-error">
    <span class="alert-icon">⚠️</span>
    <?= e($error) ?>
</div>
<?php endif; ?>

<div class="card" x-data="{ deleteId: null, deleteName: '' }">
    <div class="card-header">
        <span class="card-title">Llista d'estudiants</span>
        <span style="font-size:.8rem;color:var(--gray-400)"><?= count($students) ?> registres</span>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:120px">ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th style="width:150px">Accions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($students)): ?>
                <tr class="empty-row">
                    <td colspan="4">
                        No hi ha estudiants. <a href="/students/create">Crea el primer estudiant</a>.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td class="td-id" title="<?= e($student['id']) ?>">
                        <?= e(substr($student['id'], 0, 8)) ?>…
                    </td>
                    <td><strong><?= e($student['name']) ?></strong></td>
                    <td style="color:var(--gray-500)"><?= e($student['email']) ?></td>
                    <td>
                        <div class="td-actions">
                            <a href="/students/<?= e($student['id']) ?>/edit"
                               class="btn btn-secondary btn-sm">✏️ Editar</a>
                            <button class="btn btn-danger btn-sm"
                                    @click="deleteId = '<?= e($student['id']) ?>'; deleteName = '<?= e(addslashes($student['name'])) ?>'">
                                🗑️
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Delete confirmation modal (Alpine.js) -->
    <template x-if="deleteId">
        <div class="modal-backdrop" @click.self="deleteId = null">
            <div class="modal">
                <div class="modal-title">🗑️ Eliminar estudiant</div>
                <div class="modal-body">
                    Estàs segur que vols eliminar <strong x-text="deleteName"></strong>?
                    Aquesta acció no es pot desfer.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="deleteId = null">Cancel·lar</button>
                    <form :action="'/students/' + deleteId" method="POST" style="display:inline">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';
