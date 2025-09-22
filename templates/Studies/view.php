<?php
require_once ROOT . DS . 'src' . DS . 'Helper' . DS . 'CurrencyHelper.php';
use App\Helper\CurrencyHelper;
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading">Actions</h4>
            <a href="/studies/edit/<?= h($study['id']) ?>" class="btn btn-primary mb-2">Edit Study</a>
            <form method="post" action="/studies/delete/<?= h($study['id']) ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this study?');">
                <button type="submit" class="btn btn-danger mb-2">Delete Study</button>
            </form>
            <a href="/studies" class="btn btn-secondary mb-2">List Studies</a>
            <a href="/studies/add" class="btn btn-success mb-2">New Study</a>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="studies view content">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-chart-line"></i> Market Replay Study Details</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-user"></i> Student Information</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Student:</strong> 
                                        <a href="/students/view/<?= h($study['student']['id']) ?>" class="text-decoration-none"><?= h($study['student']['name']) ?></a>
                                    </p>
                                    <p><strong>Email:</strong> <?= h($study['student']['email']) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-calendar"></i> Study Dates</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Study Date:</strong> <?= h($study['study_date']) ?></p>
                                    <p><strong>Created:</strong> <?= h(date('Y-m-d H:i:s', strtotime($study['created']))) ?></p>
                                    <p><strong>Modified:</strong> <?= h(date('Y-m-d H:i:s', strtotime($study['modified']))) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-bar"></i> Trading Performance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <div class="card bg-success text-white">
                                                <div class="card-body">
                                                    <h3><?= h($study['wins']) ?></h3>
                                                    <p class="mb-0">Wins</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-danger text-white">
                                                <div class="card-body">
                                                    <h3><?= h($study['losses']) ?></h3>
                                                    <p class="mb-0">Losses</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card bg-info text-white">
                                                <div class="card-body">
                                                    <h3><?= h($study['total_trades']) ?></h3>
                                                    <p class="mb-0">Total Trades</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="card <?= $study['win_rate'] >= 50 ? 'bg-success' : 'bg-warning' ?> text-white">
                                                <div class="card-body">
                                                    <h3><?= number_format($study['win_rate'], 2) ?>%</h3>
                                                    <p class="mb-0">Win Rate</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-dollar-sign"></i> Financial Performance</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-6">
                                            <div class="card <?= $study['profit_loss'] >= 0 ? 'bg-success' : 'bg-danger' ?> text-white">
                                                <div class="card-body">
                                                    <h3><?= CurrencyHelper::formatForUser($study['profit_loss'], $study['user']['currency'] ?? 'BRL') ?></h3>
                                                    <p class="mb-0">Total Profit/Loss</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-secondary text-white">
                                                <div class="card-body">
                                                    <h3><?= $study['total_trades'] > 0 ? CurrencyHelper::formatForUser($study['profit_loss'] / $study['total_trades'], $study['user']['currency'] ?? 'BRL') : CurrencyHelper::formatForUser(0, $study['user']['currency'] ?? 'BRL') ?></h3>
                                                    <p class="mb-0">Average Per Trade</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb"></i> Performance Analysis</h6>
                                <?php if ($study['win_rate'] >= 60): ?>
                                    <p class="mb-0"><strong>Excellent performance!</strong> Win rate of <?= number_format($study['win_rate'], 2) ?>% indicates strong trading skills.</p>
                                <?php elseif ($study['win_rate'] >= 50): ?>
                                    <p class="mb-0"><strong>Good performance.</strong> Win rate of <?= number_format($study['win_rate'], 2) ?>% shows consistent profitability potential.</p>
                                <?php else: ?>
                                    <p class="mb-0"><strong>Room for improvement.</strong> Win rate of <?= number_format($study['win_rate'], 2) ?>% suggests reviewing trading strategy.</p>
                                <?php endif; ?>
                                
                                <?php if ($study['profit_loss'] > 0): ?>
                                    <p class="mb-0">Positive profit/loss of <?= CurrencyHelper::formatForUser($study['profit_loss'], $study['user']['currency'] ?? 'BRL') ?> demonstrates effective risk management.</p>
                                <?php else: ?>
                                    <p class="mb-0">Consider reviewing position sizing and risk management strategies.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>