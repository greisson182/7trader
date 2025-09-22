<?php
/**
 * Dashboard do Estudante - Visualização mensal dos estudos
 * @var array $student
 * @var array $monthlyData
 * @var array $overallStats
 * @var array $chartLabels
 * @var array $chartProfitLoss
 * @var array $chartWinRate
 */

require_once ROOT . DS . 'src' . DS . 'Helper' . DS . 'CurrencyHelper.php';
use App\Helper\CurrencyHelper;
?>

<div class="students dashboard content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-5 fw-bold mb-2">
                <i class="bi bi-speedometer2 me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Dashboard
            </h1>
            <p class="text-muted mb-0">Performance analytics for <span class="fw-semibold text-primary"><?= h($student['name']) ?></span></p>
        </div>
        <div class="d-flex gap-2">
            <a href="/students" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>
                Back
            </a>
            <a href="/students/view/<?= $student['id'] ?>" class="btn btn-primary">
                <i class="bi bi-person me-2"></i>
                Profile
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <div class="col-lg-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3">
                        <i class="bi bi-journal-text"></i>
                    </div>
                    <h3 class="stat-number mb-2"><?= number_format($overallStats['total_studies']) ?></h3>
                    <p class="stat-label mb-0">Total Studies</p>
                    <div class="stat-trend">
                        <i class="bi bi-graph-up text-success"></i>
                        <small class="text-success">Active</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 text-success">
                        <i class="bi bi-percent"></i>
                    </div>
                    <h3 class="stat-number mb-2 text-success"><?= $overallStats['overall_win_rate'] ?>%</h3>
                    <p class="stat-label mb-0">Win Rate</p>
                    <div class="stat-trend">
                        <i class="bi bi-<?= $overallStats['overall_win_rate'] >= 50 ? 'graph-up text-success' : 'graph-down text-warning' ?>"></i>
                        <small class="<?= $overallStats['overall_win_rate'] >= 50 ? 'text-success' : 'text-warning' ?>">
                            <?= $overallStats['overall_win_rate'] >= 50 ? 'Good' : 'Needs Improvement' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 text-info">
                        <i class="bi bi-bar-chart"></i>
                    </div>
                    <h3 class="stat-number mb-2 text-info"><?= number_format($overallStats['total_trades']) ?></h3>
                    <p class="stat-label mb-0">Total Trades</p>
                    <div class="stat-trend">
                        <i class="bi bi-activity text-info"></i>
                        <small class="text-info">Volume</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="card glass stat-card h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-3 <?= $overallStats['total_profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <h3 class="stat-number mb-2 <?= $overallStats['total_profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                        <?= CurrencyHelper::formatForUser($overallStats['total_profit_loss'], $student['currency'] ?? 'BRL') ?>
                    </h3>
                    <p class="stat-label mb-0">Total P&L</p>
                    <div class="stat-trend">
                        <i class="bi bi-<?= $overallStats['total_profit_loss'] >= 0 ? 'graph-up text-success' : 'graph-down text-danger' ?>"></i>
                        <small class="<?= $overallStats['total_profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= $overallStats['total_profit_loss'] >= 0 ? 'Profit' : 'Loss' ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card glass chart-card h-100">
                <div class="card-header border-0 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                <i class="bi bi-graph-up me-2"></i>
                                P&L Evolution
                            </h5>
                            <p class="text-muted small mb-0">Last 12 months performance</p>
                        </div>
                        <div class="chart-controls">
                            <button class="btn btn-sm btn-outline-primary active" data-chart="line">
                                <i class="bi bi-graph-up"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-primary" data-chart="area">
                                <i class="bi bi-area-chart"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="profitLossChart" height="300"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card glass chart-card h-100">
                <div class="card-header border-0 pb-0">
                    <h5 class="card-title mb-1">
                        <i class="bi bi-pie-chart me-2"></i>
                        Win Rate Trend
                    </h5>
                    <p class="text-muted small mb-0">Monthly performance</p>
                </div>
                <div class="card-body">
                    <canvas id="winRateChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Table -->
    <div class="card glass">
        <div class="card-header border-0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-1">
                        <i class="bi bi-calendar3 me-2"></i>
                        Monthly Performance
                    </h5>
                    <p class="text-muted small mb-0">Detailed breakdown by month</p>
                </div>
                <div class="table-controls">
                    <button class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-download me-1"></i>
                        Export
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <?php if (!empty($monthlyData)): ?>
                <div class="table-responsive">
                    <table class="table table-hover modern-table">
                        <thead>
                            <tr>
                                <th class="border-0">
                                    <i class="bi bi-calendar me-1"></i>
                                    Period
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-journal me-1"></i>
                                    Studies
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-check-circle me-1"></i>
                                    Wins
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Losses
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-bar-chart me-1"></i>
                                    Trades
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-percent me-1"></i>
                                    Win Rate
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-currency-dollar me-1"></i>
                                    P&L
                                </th>
                                <th class="border-0 text-center">
                                    <i class="bi bi-calendar-event me-1"></i>
                                    Period
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($monthlyData as $data): ?>
                            <tr class="table-row-hover">
                                <td class="fw-semibold">
                                    <div class="d-flex align-items-center">
                                        <div class="month-indicator me-2"></div>
                                        <?= h($data['month_name']) ?> <?= h($data['year']) ?>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill"><?= number_format($data['total_studies']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success rounded-pill"><?= number_format($data['total_wins']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger rounded-pill"><?= number_format($data['total_losses']) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info rounded-pill"><?= number_format($data['total_trades']) ?></span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="progress me-2" style="width: 40px; height: 6px;">
                                            <div class="progress-bar <?= $data['avg_win_rate'] >= 50 ? 'bg-success' : 'bg-warning' ?>" 
                                                 style="width: <?= $data['avg_win_rate'] ?>%"></div>
                                        </div>
                                        <span class="badge <?= $data['avg_win_rate'] >= 50 ? 'bg-success' : 'bg-warning' ?> rounded-pill">
                                            <?= $data['avg_win_rate'] ?>%
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold <?= $data['total_profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                        <i class="bi bi-<?= $data['total_profit_loss'] >= 0 ? 'arrow-up' : 'arrow-down' ?> me-1"></i>
                                        <?= CurrencyHelper::formatForUser($data['total_profit_loss'], $student['currency'] ?? 'BRL') ?>
                                    </span>
                                </td>
                                <td class="text-center text-muted small text-period">
                                    <?= date('d/m', strtotime($data['first_study'])) ?> - <?= date('d/m', strtotime($data['last_study'])) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state text-center py-5">
                    <div class="empty-icon mb-4">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h4 class="text-muted mb-3">No Studies Found</h4>
                    <p class="text-muted mb-4">Start adding studies to see your performance analytics here.</p>
                    <a href="/studies/add" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>
                        Add First Study
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
/* Dashboard Specific Styles */
.stat-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
    transform: scaleX(0);
    transition: transform 0.3s ease;
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 1.5rem;
    color: var(--bs-primary);
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

.stat-label {
    color: var(--bs-gray-600);
    font-weight: 500;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.stat-trend {
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.chart-card {
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.chart-controls .btn {
    border-radius: 50%;
    width: 36px;
    height: 36px;
    padding: 0;
    margin-left: 0.25rem;
}

.chart-controls .btn.active {
    background: var(--bs-primary);
    border-color: var(--bs-primary);
    color: white;
}

.modern-table {
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead th {
    background: rgba(var(--bs-primary-rgb), 0.05);
    font-weight: 600;
    font-size: 0.875rem;
    color: var(--bs-gray-700);
    padding: 1rem 0.75rem;
}

.modern-table tbody tr {
    transition: all 0.2s ease;
}

.modern-table tbody tr:hover {
    background: rgba(var(--bs-primary-rgb), 0.02);
    transform: scale(1.01);
}

.table-row-hover td {
    padding: 1rem 0.75rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.month-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--primary-gradient);
}

.empty-state .empty-icon {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(var(--bs-primary-rgb), 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    font-size: 2rem;
    color: var(--bs-primary);
}

.table-controls .btn {
    border-radius: 20px;
    padding: 0.375rem 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .stat-number {
        font-size: 1.5rem;
    }
    
    .chart-controls {
        display: none;
    }
    
    .modern-table {
        font-size: 0.875rem;
    }
}
</style>

<script>
// Wait for everything to load
setTimeout(function() {
    // Check if Chart is available
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not available');
        return;
    }

    // Chart data
    const chartLabels = <?= json_encode($chartLabels) ?>;
    const chartProfitLoss = <?= json_encode($chartProfitLoss) ?>;
    const chartWinRate = <?= json_encode($chartWinRate) ?>;

    // Chart.js default configuration
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.color = '#6c757d';

    // P&L Chart
    const profitLossCtx = document.getElementById('profitLossChart');
    if (profitLossCtx) {
        const profitLossChart = new Chart(profitLossCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'P&L ($)',
                    data: chartProfitLoss,
                    borderColor: '#00ff88',
                    backgroundColor: 'rgba(0, 255, 136, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#00ff88',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 10,
                    pointHoverBackgroundColor: '#00cc6a',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#00ff88',
                        bodyColor: '#fff',
                        borderColor: '#00ff88',
                        borderWidth: 2,
                        cornerRadius: 12,
                        displayColors: false,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                return 'P&L: $' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#a0a0a0',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 255, 136, 0.1)',
                            lineWidth: 1
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            color: '#a0a0a0',
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            callback: function(value) {
                                return '$' + value.toFixed(0);
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });

        // Chart controls
        document.querySelectorAll('[data-chart]').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('[data-chart]').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const chartType = this.dataset.chart;
                profitLossChart.config.type = chartType;
                profitLossChart.update();
            });
        });
    }

    // Win Rate Chart
    const winRateCtx = document.getElementById('winRateChart');
    if (winRateCtx) {
        const winRateChart = new Chart(winRateCtx, {
            type: 'doughnut',
            data: {
                labels: ['Wins', 'Losses'],
                datasets: [{
                    data: [
                        <?= $overallStats['total_trades'] > 0 ? ($overallStats['overall_win_rate'] / 100 * $overallStats['total_trades']) : 0 ?>,
                        <?= $overallStats['total_trades'] > 0 ? ((100 - $overallStats['overall_win_rate']) / 100 * $overallStats['total_trades']) : 0 ?>
                    ],
                    backgroundColor: [
                        '#00ff88',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        '#00cc6a',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 3,
                    cutout: '70%',
                    hoverBackgroundColor: [
                        '#00cc6a',
                        'rgba(255, 99, 132, 0.9)'
                    ],
                    hoverBorderColor: [
                        '#00ff88',
                        'rgba(255, 99, 132, 1)'
                    ],
                    hoverBorderWidth: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            color: '#a0a0a0',
                            font: {
                                size: 13,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: '#00ff88',
                        bodyColor: '#fff',
                        borderColor: '#00ff88',
                        borderWidth: 2,
                        cornerRadius: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + percentage + '%';
                            }
                        }
                    }
                }
            }
        });
    }

    // Add smooth animations
    // Animate stat numbers
    document.querySelectorAll('.stat-number').forEach(el => {
        const finalValue = parseInt(el.textContent.replace(/[^0-9.-]/g, ''));
        let currentValue = 0;
        const increment = finalValue / 50;
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            el.textContent = el.textContent.includes('$') ? 
                '$' + Math.floor(currentValue).toLocaleString() :
                Math.floor(currentValue).toLocaleString() + (el.textContent.includes('%') ? '%' : '');
        }, 30);
    });
}, 1000); // Wait 1 second for everything to load
</script>