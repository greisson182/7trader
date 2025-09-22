<?php
/**
 * @var array $student
 */
?>
<div class="students form content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-user-plus"></i> Adicionar Estudante</h3>
        <a href="/students" class="btn btn-secondary btn-with-icon">
            <i class="fas fa-list"></i>
            <span>Listar Estudantes</span>
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/students/add" class="needs-validation" novalidate>
                <div class="mb-3">
                    <label for="name" class="form-label">Nome</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <div class="invalid-feedback">
                        Por favor, forneça um nome válido.
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="invalid-feedback">
                        Por favor, forneça um email válido.
                    </div>
                </div>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/students" class="btn btn-secondary me-md-2 btn-with-icon">
                        <i class="fas fa-times"></i>
                        <span>Cancelar</span>
                    </a>
                    <button type="submit" class="btn btn-primary btn-with-icon">
                        <i class="fas fa-paper-plane"></i>
                        <span>Enviar</span>
                    </button>
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