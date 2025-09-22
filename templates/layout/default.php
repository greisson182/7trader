<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0a0a">
    <title>
        7 Trader
        <?= isset($title) ? ' - ' . $title : '' ?>
    </title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="/css/style.css" rel="stylesheet">
</head>

<body class="fade-in-up">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg glass fixed-top">
        <div class="container">
            <a href="/students/dashboard" class="navbar-brand">
                <img src="/images/logo-dark.png" alt="7Trader" class="logo logo-dark">
                <img src="/images/logo-light.png" alt="7Trader" class="logo logo-light">
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars text-white"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">

                    <?php if (isset($currentUser)): ?>
                        <?php if ($currentUser['role'] === 'student'): ?>
                            <li class="nav-item">
                                <a href="/students/dashboard" class="nav-link">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Painel
                                </a>
                            </li>
                        <?php elseif ($currentUser['role'] === 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="dashboardDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Painel
                                </a>
                                <ul class="dropdown-menu glass">
                                    <li><a class="dropdown-item" href="/students/admin-dashboard">
                                            <i class="bi bi-graph-up-arrow me-2"></i>
                                            Dashboard Administrativo
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <h6 class="dropdown-header">
                                            <i class="bi bi-person-gear me-1"></i>
                                            Selecionar Estudante
                                        </h6>
                                    </li>
                                    <li><a class="dropdown-item" href="/students">
                                            <i class="bi bi-list-ul me-2"></i>
                                            Ver Todos os Estudantes
                                        </a></li>
                                </ul>
                            <li class="nav-item">
                                <a href="/students" class="nav-link">
                                    <i class="bi bi-people me-1"></i>
                                    Estudantes
                                </a>
                            </li>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="/studies" class="nav-link">
                            <i class="bi bi-journal-text me-1"></i>
                            Estudos
                        </a>
                    </li>
                </ul>
                <ul class="navbar-nav">




                    <?php if (isset($currentUser)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= h($currentUser['username']) ?>
                                <span class="badge bg-primary ms-2"><?= ucfirst($currentUser['role']) ?></span>
                            </a>
                            <ul class="dropdown-menu glass dropdown-menu-end">
                                <li><a class="dropdown-item" href="/profile/edit">
                                        <i class="bi bi-person-gear me-2"></i>
                                        Editar Perfil
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="/logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        Sair
                                    </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="/login" class="nav-link">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Entrar
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="configDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear me-1"></i>
                            </a>
                            <ul class="dropdown-menu glass">
                                <li>
                                    <a class="dropdown-item" href="/markets">
                                        <i class="bi bi-currency-exchange me-2"></i>
                                        Mercados
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/students/add">
                                        <i class="bi bi-person-plus me-1"></i>
                                        Adicionar Estudante
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Theme Toggle -->
                    <li class="nav-item d-flex align-items-center">
                        <label class="theme-toggle">
                            <input type="checkbox" id="theme-toggle">
                            <span class="theme-slider">
                                <i class="bi bi-sun theme-icon sun-icon"></i>
                                <i class="bi bi-moon theme-icon moon-icon"></i>
                            </span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main" style="padding-top: 60px; margin-top: 80px;">
        <div class="container">
            <?php if (isset($flash) && !empty($flash)): ?>
                <div class="alert alert-info alert-dismissible fade show glass" role="alert">
                    <i class="bi bi-info-circle me-2"></i>
                    <?= h($flash) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="glass mt-5 py-4">
        <div class="container text-center">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start">
                    <p class="mb-0">
                        <i class="bi bi-graph-up-arrow me-2"></i>
                        <strong>7 Trader todos os direitos reservados.</strong>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">

                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/theme.js"></script>
    <script src="/js/value-colors.js"></script>

    <!-- Custom JavaScript for enhanced UX -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('show')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);

        // Add loading state to buttons on form submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Processando...';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('loading');
                }
            });
        });

        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.classList.add('btn-ripple');
        });

        // Add slide-in animation to buttons when they appear
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('btn-slide-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.btn').forEach(btn => {
            observer.observe(btn);
        });

        // Add success animation to primary buttons after successful actions
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('click', function() {
                setTimeout(() => {
                    this.classList.add('btn-success-pulse');
                    setTimeout(() => {
                        this.classList.remove('btn-success-pulse');
                    }, 600);
                }, 100);
            });
        });
    </script>
</body>

</html>