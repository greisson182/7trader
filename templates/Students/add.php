<?php
/**
 * @var array $student
 */
?>
<div class="students form content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-user-plus"></i> Add Student</h3>
        <a href="/students" class="btn btn-secondary">List Students</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/students/add" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="invalid-feedback">
                        Please provide a valid name.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">
                        Please provide a valid email.
                    </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/students" class="btn btn-secondary me-md-2">Cancel</a>
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