<?php
$layout      = 'app';
$pageTitle   = 'Professors';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Professors'],
];

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title"> Professors</div>
        <div class="page-subtitle"><?= count($teachers) ?> professors registrats</div>
    </div>
    <a href="/teachers/create" class="btn btn-primary">＋ Nou professor</a>
</div>

<?php if ($error): ?>
<div class="alert alert-error">
    <span class="alert-icon">⚠️</span><?= e($error) ?>
</div>
<?php endif; ?>

<div class="card" x-data="{ deleteId: null, deleteName: '' }">
    <div class="card-header">
        <span class="card-title">Llista de professors</span>
        <span style="font-size:.8rem;color:var(--gray-400)"><?= count($teachers) ?> registres</span>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:120px">ID</th>
                    <th>Nom</th>
                    <th>Especialitat</th>
                    <th style="width:150px">Accions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($teachers)): ?>
                <tr class="empty-row">
                    <td colspan="4">
                        No hi ha professors. <a href="/teachers/create">Crea el primer professor</a>.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td class="td-id" title="<?= e($teacher['id']) ?>">
                        <?= e(substr($teacher['id'], 0, 8)) ?>…
                    </td>
                    <td><strong><?= e($teacher['name']) ?></strong></td>
                    <td><span class="badge badge-primary"><?= e($teacher['specialty']) ?></span></td>
                    <td>
                        <div class="td-actions">
                            <a href="/teachers/<?= e($teacher['id']) ?>/edit"
                               class="btn btn-secondary btn-sm">✏️ Editar</a>
                            <button class="btn btn-danger btn-sm"
                                    @click="deleteId = '<?= e($teacher['id']) ?>'; deleteName = '<?= e(addslashes($teacher['name'])) ?>'">
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

    <!-- Modal d'eliminació -->
    <template x-if="deleteId">
        <div class="modal-backdrop" @click.self="deleteId = null">
            <div class="modal">
                <div class="modal-title">🗑️ Eliminar professor</div>
                <div class="modal-body">
                    Estàs segur que vols eliminar <strong x-text="deleteName"></strong>?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="deleteId = null">Cancel·lar</button>
                    <form :action="'/teachers/' + deleteId" method="POST" style="display:inline">
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
