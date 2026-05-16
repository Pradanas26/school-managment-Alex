<?php
$layout      = 'app';
$pageTitle   = 'Assignatures';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => '/'],
    ['label' => 'Assignatures'],
];

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title"> Assignatures</div>
        <div class="page-subtitle"><?= count($subjects) ?> assignatures al sistema</div>
    </div>
    <a href="/subjects/create" class="btn btn-primary">＋ Nova assignatura</a>
</div>

<?php if ($error): ?>
<div class="alert alert-error">
    <span class="alert-icon">⚠️</span><?= e($error) ?>
</div>
<?php endif; ?>

<div class="card" x-data="{ deleteId: null, deleteName: '', assignSubjectId: null }">
    <div class="card-header">
        <span class="card-title">Llista d'assignatures</span>
        <span style="font-size:.8rem;color:var(--gray-400)"><?= count($subjects) ?> registres</span>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th style="width:120px">ID</th>
                    <th>Nom</th>
                    <th>ID Curs</th>
                    <th>Professor assignat</th>
                    <th style="width:200px">Accions</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($subjects)): ?>
                <tr class="empty-row">
                    <td colspan="5">
                        No hi ha assignatures. <a href="/subjects/create">Crea la primera assignatura</a>.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($subjects as $subject): ?>
                <tr>
                    <td class="td-id" title="<?= e($subject['id']) ?>">
                        <?= e(substr($subject['id'], 0, 8)) ?>…
                    </td>
                    <td><strong><?= e($subject['name']) ?></strong></td>
                    <td class="td-id" title="<?= e($subject['courseId']) ?>">
                        <?= e(substr($subject['courseId'], 0, 8)) ?>…
                    </td>
                    <td>
                        <?php if (!empty($subject['teacherId'])): ?>
                            <span class="badge badge-success">
                                ✓ <?= e($teachersMap[$subject['teacherId']] ?? substr($subject['teacherId'], 0, 8).'…') ?>
                            </span>
                        <?php else: ?>
                            <span class="badge badge-warning">Sense professor</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="td-actions">
                            <a href="/subjects/<?= e($subject['id']) ?>/edit"
                               class="btn btn-secondary btn-sm">✏️</a>
                            <button class="btn btn-success btn-sm"
                                    @click="assignSubjectId = '<?= e($subject['id']) ?>'">
                                👨‍🏫 Assignar
                            </button>
                            <button class="btn btn-danger btn-sm"
                                    @click="deleteId = '<?= e($subject['id']) ?>'; deleteName = '<?= e(addslashes($subject['name'])) ?>'">
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

    <!-- Modal: Assignar professor -->
    <template x-if="assignSubjectId">
        <div class="modal-backdrop" @click.self="assignSubjectId = null">
            <div class="modal">
                <div class="modal-title">👨‍🏫 Assignar professor</div>
                <div class="modal-body">
                    Selecciona el professor per a aquesta assignatura.
                    <?php if (empty($teachersMap)): ?>
                    <div class="alert alert-warning" style="margin-top:.75rem">
                        ⚠️ No hi ha professors disponibles. <a href="/teachers/create">Crea'n un primer</a>.
                    </div>
                    <?php endif; ?>
                </div>
                <form :action="'/subjects/' + assignSubjectId + '/assign-teacher'" method="POST">
                    <div class="form-group">
                        <label class="form-label">Professor</label>
                        <select name="teacherId" class="form-control" required>
                            <option value="">— Selecciona un professor —</option>
                            <?php foreach ($teachersMap as $tid => $tname): ?>
                            <option value="<?= e($tid) ?>"><?= e($tname) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="assignSubjectId = null">Cancel·lar</button>
                        <button type="submit" class="btn btn-primary">✅ Assignar</button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal: Eliminar -->
    <template x-if="deleteId">
        <div class="modal-backdrop" @click.self="deleteId = null">
            <div class="modal">
                <div class="modal-title">🗑️ Eliminar assignatura</div>
                <div class="modal-body">
                    Estàs segur que vols eliminar <strong x-text="deleteName"></strong>?
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" @click="deleteId = null">Cancel·lar</button>
                    <form :action="'/subjects/' + deleteId" method="POST" style="display:inline">
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
