<?php
$layout       = 'app';
$pageTitle    = 'Dashboard';
$breadcrumbs  = [['label' => 'Dashboard']];
$apiOnline    = $apiOnline ?? false;

ob_start();
?>

<div class="page-header">
    <div>
        <div class="page-title">📊 Dashboard</div>
        <div class="page-subtitle">Resum general del sistema escolar</div>
    </div>
</div>

<?php if (!$apiOnline): ?>
<div class="alert alert-error">
    <span class="alert-icon">⚠️</span>
    <div>
        <strong>L'API no és accessible.</strong>
        Assegura't que el servidor de school-project està en marxa:<br>
        <code style="background:#fecaca;padding:.2rem .5rem;border-radius:4px;font-size:.82rem">
            php -S localhost:8000
        </code>
        (des de la carpeta <code>school-project/</code>)
    </div>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">👩‍🎓</div>
        <div>
            <div class="stat-number"><?= $totalStudents ?></div>
            <div class="stat-label">Estudiants</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">👨‍🏫</div>
        <div>
            <div class="stat-number"><?= $totalTeachers ?></div>
            <div class="stat-label">Professors</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon purple">📚</div>
        <div>
            <div class="stat-number"><?= $totalSubjects ?></div>
            <div class="stat-label">Assignatures</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange">✅</div>
        <div>
            <div class="stat-number"><?= $assignedSubjects ?></div>
            <div class="stat-label">Assignatures amb professor</div>
        </div>
    </div>
</div>

<!-- Quick overview grid -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.25rem">

    <!-- Recent Students -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">👩‍🎓 Últims estudiants</span>
            <a href="/students" class="btn btn-secondary btn-sm">Veure tots</a>
        </div>
        <?php $recent = array_slice($students, -5); ?>
        <?php if (empty($recent)): ?>
            <div class="card-body">
                <p style="color:var(--gray-400);font-size:.875rem;text-align:center;padding:1rem 0">
                    No hi ha estudiants encara. <a href="/students/create">Crea'n un</a>
                </p>
            </div>
        <?php else: ?>
        <div class="table-container">
            <table>
                <thead><tr><th>Nom</th><th>Email</th></tr></thead>
                <tbody>
                <?php foreach (array_reverse($recent) as $s): ?>
                    <tr>
                        <td><?= e($s['name']) ?></td>
                        <td style="color:var(--gray-500);font-size:.8rem"><?= e($s['email']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Recent Teachers -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">👨‍🏫 Professors</span>
            <a href="/teachers" class="btn btn-secondary btn-sm">Veure tots</a>
        </div>
        <?php if (empty($teachers)): ?>
            <div class="card-body">
                <p style="color:var(--gray-400);font-size:.875rem;text-align:center;padding:1rem 0">
                    No hi ha professors. <a href="/teachers/create">Crea'n un</a>
                </p>
            </div>
        <?php else: ?>
        <div class="table-container">
            <table>
                <thead><tr><th>Nom</th><th>Especialitat</th></tr></thead>
                <tbody>
                <?php foreach ($teachers as $t): ?>
                    <tr>
                        <td><?= e($t['name']) ?></td>
                        <td><span class="badge badge-primary"><?= e($t['specialty']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Subjects status -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Assignatures</span>
            <a href="/subjects" class="btn btn-secondary btn-sm">Gestionar</a>
        </div>
        <?php if (empty($subjects)): ?>
            <div class="card-body">
                <p style="color:var(--gray-400);font-size:.875rem;text-align:center;padding:1rem 0">
                    No hi ha assignatures. <a href="/subjects/create">Crea'n una</a>
                </p>
            </div>
        <?php else: ?>
        <div class="table-container">
            <table>
                <thead><tr><th>Assignatura</th><th>Professor</th></tr></thead>
                <tbody>
                <?php foreach ($subjects as $s): ?>
                    <tr>
                        <td><?= e($s['name']) ?></td>
                        <td>
                            <?php if ($s['teacherId']): ?>
                                <span class="badge badge-success">✓ Assignat</span>
                            <?php else: ?>
                                <span class="badge badge-warning">Pendent</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>

</div>

<?php
$content = ob_get_clean();
require SPA_PATH . '/resources/views/layouts/app.php';
