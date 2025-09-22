<?php

/**
 * @var array $study
 * @var array $students
 */
?>
<div class="studies form content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-chart-line"></i> Add Study</h3>
        <a href="/studies" class="btn btn-secondary">List Studies</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/studies/add" class="needs-validation" novalidate>
                <?php if (!isset($currentStudentId)): ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="student_id" class="form-label">Student</label>


                                <!-- Select normal para administradores -->
                                <select class="form-select" id="student_id" name="student_id" required>
                                    <option value="">Select a student</option>
                                    <?php if (!empty($students)): ?>
                                        <?php foreach ($students as $student): ?>
                                            <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Please select a student.
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="study_date" class="form-label">Study Date</label>
                            <input type="date" class="form-control" id="study_date" name="study_date" required>
                            <div class="invalid-feedback">
                                Please provide a valid study date.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="wins" class="form-label">Wins</label>
                            <input type="number" class="form-control" id="wins" name="wins" min="0" required>
                            <div class="invalid-feedback">
                                Please provide a valid number of wins.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="losses" class="form-label">Losses</label>
                            <input type="number" class="form-control" id="losses" name="losses" min="0" required>
                            <div class="invalid-feedback">
                                Please provide a valid number of losses.
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="profit_loss" class="form-label">Profit/Loss ($)</label>
                            <input type="number" class="form-control" id="profit_loss" name="profit_loss" step="0.01" required>
                            <div class="invalid-feedback">
                                Please provide a valid profit/loss amount.
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/studies" class="btn btn-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Bootstrap form validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>