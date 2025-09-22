<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading">Actions</h4>
            <form method="post" action="/studies/delete/<?= h($study['id']) ?>" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete # <?= h($study['id']) ?>?');">
                <button type="submit" class="btn btn-danger mb-2">Delete</button>
            </form>
            <a href="/studies" class="btn btn-secondary mb-2">List Studies</a>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="studies form content">
            <div class="card">
                <div class="card-header">
                    <h4><i class="fas fa-edit"></i> Edit Market Replay Study</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="/studies/edit/<?= h($study['id']) ?>" class="needs-validation" novalidate>
                    <fieldset>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student</label>
                                    <select name="student_id" id="student_id" class="form-select" required>
                                        <option value="">Select a student</option>
                                        <?php foreach ($students as $id => $name): ?>
                                            <option value="<?= h($id) ?>" <?= $study['student_id'] == $id ? 'selected' : '' ?>><?= h($name) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="study_date" class="form-label">Study Date</label>
                                    <input type="date" name="study_date" id="study_date" class="form-control" value="<?= h($study['study_date']) ?>" required>
                                    <div class="form-text">The date when the study was conducted</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="wins" class="form-label">Wins</label>
                                    <input type="number" name="wins" id="wins" class="form-control" value="<?= h($study['wins']) ?>" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="losses" class="form-label">Losses</label>
                                    <input type="number" name="losses" id="losses" class="form-control" value="<?= h($study['losses']) ?>" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="profit_loss" class="form-label">Profit/Loss ($)</label>
                                    <input type="number" name="profit_loss" id="profit_loss" class="form-control" value="<?= h($study['profit_loss']) ?>" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Current Win Rate:</strong> <?= h($study['win_rate']) ?>% 
                            (<?= h($study['wins']) ?> wins out of <?= h($study['total_trades']) ?> total trades)
                        </div>
                    </fieldset>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="/studies" class="btn btn-secondary me-md-2">Cancel</a>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const winsInput = document.querySelector('input[name="wins"]');
    const lossesInput = document.querySelector('input[name="losses"]');
    
    function updateWinRate() {
        const wins = parseInt(winsInput.value) || 0;
        const losses = parseInt(lossesInput.value) || 0;
        const total = wins + losses;
        const winRate = total > 0 ? ((wins / total) * 100).toFixed(2) : 0;
        
        // Update or create win rate display
        let winRateDisplay = document.getElementById('win-rate-display');
        if (!winRateDisplay) {
            winRateDisplay = document.createElement('div');
            winRateDisplay.id = 'win-rate-display';
            winRateDisplay.className = 'alert alert-success mt-2';
            lossesInput.parentNode.appendChild(winRateDisplay);
        }
        
        winRateDisplay.innerHTML = `<strong>Updated Win Rate: ${winRate}%</strong> (${wins} wins out of ${total} total trades)`;
    }
    
    winsInput.addEventListener('input', updateWinRate);
    lossesInput.addEventListener('input', updateWinRate);
});
</script>