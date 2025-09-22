<?php
require_once ROOT . DS . 'src' . DS . 'Helper' . DS . 'CurrencyHelper.php';
use App\Helper\CurrencyHelper;
?>
<link rel="stylesheet" href="/css/studies.css">

<div class="studies index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-chart-bar gradient-text"></i> Estudos de Replay de Mercado</h3>
        <a href="/studies/add" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Novo Estudo
        </a>
    </div>

    <!-- Filtro por Mercado -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label for="marketFilter" class="form-label mb-2">
                        <i class="fas fa-filter me-2"></i>Filtrar por Mercado
                    </label>
                </div>
                <div class="col-md-6">
                    <select id="marketFilter" class="form-select">
                        <option value="">Todos os mercados</option>
                        <?php if (!empty($markets)): ?>
                            <?php foreach ($markets as $market): ?>
                                <option value="<?= h($market['id']) ?>"><?= h($market['name']) ?> (<?= h($market['code']) ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button id="clearFilter" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Limpar
                    </button>
                </div>
            </div>
        </div>
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
                                <span class="stat-label">Estudos</span>
                                <span class="stat-value"><?= h($monthData['total_studies']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <span class="stat-label">Gain</span>
                                <span class="stat-value text-success"><?= h($monthData['total_wins']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <span class="stat-label">Loss</span>
                                <span class="stat-value text-danger"><?= h($monthData['total_losses']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <?php 
                                $totalTrades = $monthData['total_wins'] + $monthData['total_losses'];
                                $winRate = $totalTrades > 0 ? round($monthData['total_wins'] / $totalTrades * 100, 2) : 0;
                            ?>
                            <div class="stat-item">
                                <span class="stat-label">Taxa de Acerto</span>
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
                                <span class="stat-label">P&L Total</span>
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
                                    <th>Estudante</th>
                                    <th>Mercado</th>
                                    <th>Data do Estudo</th>
                                    <th>Gain</th>
                                    <th>Loss</th>
                                    <th>Taxa de Acerto</th>
                                    <th>P&L</th>
                                    <th class="actions">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthData['studies'] as $study): ?>
                                <tr class="clickable-row study-row" 
                                    data-study-id="<?= h($study['id']) ?>" 
                                    data-market-id="<?= h($study['market_id'] ?? '') ?>"
                                    style="cursor: pointer;">
                                    <td><?= h($study['id']) ?></td>
                                    <td><?= isset($study['student_name']) && $study['student_name'] ? '<a href="/students/view/' . h($study['student_id']) . '">' . h($study['student_name']) . '</a>' : 'N/A' ?></td>
                                    <td>
                                        <?php if (isset($study['market_name']) && $study['market_name']): ?>
                                            <span class="badge bg-info"><?= h($study['market_name']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
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
                                        <a href="/studies/view/<?= h($study['id']) ?>" class="btn btn-sm btn-outline-primary " title="Ver"><i class="fas fa-eye"></i></a>
                                        <a href="/studies/edit/<?= h($study['id']) ?>" class="btn btn-sm btn-outline-warning" title="Editar"><i class="fas fa-edit"></i></a>
                                        <form method="post" action="/studies/delete/<?= h($study['id']) ?>" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir # <?= h($study['id']) ?>?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="fas fa-trash"></i></button>
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
                <h5 class="text-muted">Nenhum estudo encontrado</h5>
                <p class="text-muted">Comece criando seu primeiro estudo de market replay.</p>
                <a href="/studies/add" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Criar Primeiro Estudo
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

    // Funcionalidade de filtro por mercado
    const marketFilter = document.getElementById('marketFilter');
    const clearFilter = document.getElementById('clearFilter');
    
    function filterStudies() {
        const selectedMarketId = marketFilter.value;
        const monthCards = document.querySelectorAll('.month-card');
        
        monthCards.forEach(card => {
            const studyRows = card.querySelectorAll('.study-row');
            let visibleStudies = 0;
            let monthStats = {
                totalStudies: 0,
                totalWins: 0,
                totalLosses: 0,
                totalProfitLoss: 0
            };
            
            studyRows.forEach(row => {
                const rowMarketId = row.getAttribute('data-market-id');
                const shouldShow = !selectedMarketId || rowMarketId === selectedMarketId;
                
                if (shouldShow) {
                    row.style.display = '';
                    visibleStudies++;
                    
                    // Recalcular estatísticas (se necessário, pode ser implementado)
                    monthStats.totalStudies++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Mostrar/ocultar o card do mês baseado se há estudos visíveis
            if (visibleStudies > 0) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Mostrar mensagem se nenhum estudo for encontrado
        const hasVisibleCards = Array.from(monthCards).some(card => card.style.display !== 'none');
        let noResultsMessage = document.getElementById('noResultsMessage');
        
        if (!hasVisibleCards && selectedMarketId) {
            if (!noResultsMessage) {
                noResultsMessage = document.createElement('div');
                noResultsMessage.id = 'noResultsMessage';
                noResultsMessage.className = 'card mt-4';
                noResultsMessage.innerHTML = `
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum estudo encontrado</h5>
                        <p class="text-muted">Não há estudos para o mercado selecionado.</p>
                    </div>
                `;
                document.querySelector('.studies.index.content').appendChild(noResultsMessage);
            }
            noResultsMessage.style.display = '';
        } else if (noResultsMessage) {
            noResultsMessage.style.display = 'none';
        }
    }
    
    marketFilter.addEventListener('change', filterStudies);
    
    clearFilter.addEventListener('click', function() {
        marketFilter.value = '';
        filterStudies();
    });
});
</script>