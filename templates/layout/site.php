<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'BackTest - Sistema de Análise de Trading' ?></title>
    <meta name="description" content="<?= $description ?? 'Plataforma profissional para análise e acompanhamento de resultados de trading' ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Site CSS -->
    <link rel="stylesheet" href="/site/css/site.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark trading-navbar fixed-top" style="background: #000;">
        <div class="container">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="/">
                <img src="/site/images/logo-dark.png" class="img-logo" alt="">
            </a>
            
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link trading-nav-link" href="/">
                            <i class="fas fa-home me-1"></i>Início
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link trading-nav-link" href="/mentoria">
                            <i class="fas fa-handshake me-1"></i>Mentoria
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link trading-nav-link" href="/sobre">
                            <i class="fas fa-info-circle me-1"></i>Sobre
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link trading-nav-link" href="/contato">
                            <i class="fas fa-envelope me-1"></i>Contato
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link btn btn-outline-success btn-sm px-3 py-2 rounded-pill" href="/admin/students/dashboard">
                            <i class="fas fa-user-circle me-1"></i>Área do Aluno
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Flash Messages -->
        <?php if (isset($flash_messages)): ?>
            <?php foreach ($flash_messages as $message): ?>
                <div class="alert alert-<?= $message['type'] ?> alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($message['message']) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        
        <!-- Page Content -->
        <?= $content ?>
    </main>

    <!-- Footer -->
    <footer class="trading-footer py-5 mt-5 position-relative overflow-hidden">
        <!-- Animação de Candlesticks -->
        <div class="candlestick-container position-absolute w-100 h-100" id="footerCandlesticks"></div>
        
        <div class="container position-relative" style="z-index: 10;">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="footer-title mb-3">
                       7 <span style="color: #fff;">Trader</span>
                    </h5>
                    <p class="footer-subtitle mb-0">Aprenda com especialista em trading.</p>
                </div>
                <div class="col-md-6 text-md-end">

                    <p class="footer-copyright mb-0">
                        &copy; <?= date('Y') ?> 7 trader. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Site JS -->
    <script src="/site/js/site.js"></script>

</body>
</html>