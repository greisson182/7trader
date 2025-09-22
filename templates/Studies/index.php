<?php
require_once ROOT . DS . 'src' . DS . 'Helper' . DS . 'CurrencyHelper.php';
use App\Helper\CurrencyHelper;
?>
<link rel="stylesheet" href="/css/studies.css">

<div class="studies index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-chart-bar gradient-text"></i> Market Replay Studies</h3>
        <a href="/studies/add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>New Study
        </a>
    </div>

    <?php if (!empty($studiesByMonth)): ?>
        <?php foreach ($studiesByMonth as $monthKey => $monthData): ?>
            <div class="card month-card mb-4" data-month="<?= h($monthKey) ?>">
                <div class="card-header month-header" style="cursor: pointer;">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <?= h($monthData['display']) ?>
                            </h5>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <span class="stat-label">Studies</span>
                                <span class="stat-value"><?= h($monthData['total_studies']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <span class="stat-label">Wins</span>
                                <span class="stat-value text-success"><?= h($monthData['total_wins']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <span class="stat-label">Losses</span>
                                <span class="stat-value text-danger"><?= h($monthData['total_losses']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <?php 
                                $totalTrades = $monthData['total_wins'] + $monthData['total_losses'];
                                $winRate = $totalTrades > 0 ? round($monthData['total_wins'] / $totalTrades * 100, 2) : 0;
                            ?>
                            <div class="stat-item">
                                <span class="stat-label">Win Rate</span>
                                <span class="stat-value <?= $winRate >= 50 ? 'text-success' : 'text-warning' ?>">
                                    <?= $winRate ?>%
                                </span>
                            </div>
                        </div>
                        <div class="col-md-1 text-end">
                            <i class="fas fa-chevron-down expand-icon transition-all"></i>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <span class="stat-label">Total P&L</span>
                                <span class="stat-value <?= $monthData['total_profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= CurrencyHelper::formatForUser($monthData['total_profit_loss'], $monthData['studies'][0]['user']['currency'] ?? 'BRL') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-body month-studies" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Student</th>
                                    <th>Study Date</th>
                                    <th>Wins</th>
                                    <th>Losses</th>
                                    <th>Win Rate</th>
                                    <th>P&L</th>
                                    <th class="actions">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthData['studies'] as $study): ?>
                                <tr class="clickable-row" data-study-id="<?= h($study['id']) ?>" style="cursor: pointer;">
                                    <td><?= h($study['id']) ?></td>
                                    <td><?= isset($study['student_name']) && $study['student_name'] ? '<a href="/students/view/' . h($study['student_id']) . '">' . h($study['student_name']) . '</a>' : 'N/A' ?></td>
                                    <td><?= h($study['study_date'] ?? '') ?></td>
                                    <td><span class="badge bg-success"><?= h($study['wins'] ?? 0) ?></span></td>
                                    <td><span class="badge bg-danger"><?= h($study['losses'] ?? 0) ?></span></td>
                                    <td>
                                        <?php 
                                            $total_trades = ($study['wins'] ?? 0) + ($study['losses'] ?? 0);
                                            $win_rate = $total_trades > 0 ? round(($study['wins'] ?? 0) / $total_trades * 100, 2) : 0;
                                        ?>
                                        <span class="badge <?= $win_rate >= 50 ? 'bg-success' : 'bg-warning' ?>">
                                            <?= $win_rate ?>%
                                        </span>
                                    </td>
                                    <td class="<?= $study['profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <?= CurrencyHelper::formatForUser($study['profit_loss'] ?? 0, $study['user']['currency'] ?? 'BRL') ?>
                                    </td>
                                    <td class="actions" onclick="event.stopPropagation();">
                                        <a href="/studies/view/<?= h($study['id']) ?>" class="btn btn-sm btn-outline-primary" title="View"><i class="fas fa-eye"></i></a>
                                        <a href="/studies/edit/<?= h($study['id']) ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                        <form method="post" action="/studies/delete/<?= h($study['id']) ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete # <?= h($study['id']) ?>?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No studies found</h5>
                <p class="text-muted">Start by creating your first market replay study.</p>
                <a href="/studies/add" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Create First Study
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle month studies visibility
    document.querySelectorAll('.month-header').forEach(header => {
        header.addEventListener('click', function() {
            const card = this.closest('.month-card');
            const studiesBody = card.querySelector('.month-studies');
            const expandIcon = this.querySelector('.expand-icon');
            
            if (studiesBody.style.display === 'none') {
                studiesBody.style.display = 'block';
                expandIcon.classList.remove('fa-chevron-down');
                expandIcon.classList.add('fa-chevron-up');
                card.classList.add('expanded');
            } else {
                studiesBody.style.display = 'none';
                expandIcon.classList.remove('fa-chevron-up');
                expandIcon.classList.add('fa-chevron-down');
                card.classList.remove('expanded');
            }
        });
    });
    
    // Adicionar funcionalidade de clique nas linhas da tabela
    document.addEventListener('click', function(e) {
        const clickableRow = e.target.closest('.clickable-row');
        if (clickableRow) {
            const studyId = clickableRow.getAttribute('data-study-id');
            if (studyId) {
                window.location.href = '/studies/view/' + studyId;
            }
        }
    });
});
</script>