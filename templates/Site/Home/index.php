<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Análise Profissional de <span class="text-warning">Trading</span>
                </h1>
                <p class="lead mb-4">
                    Plataforma completa para acompanhar, analisar e otimizar seus resultados de trading. 
                    Transforme dados em insights valiosos para suas operações.
                </p>
                <div class="d-flex gap-3">
                    <a href="/login" class="btn btn-warning btn-lg">
                        <i class="fas fa-chart-line me-2"></i>Acessar Sistema
                    </a>
                    <a href="/sobre" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-info-circle me-2"></i>Saiba Mais
                    </a>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="text-center">
                    <i class="fas fa-chart-area display-1 text-warning opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center mb-5">
            <div class="col-12">
                <h2 class="display-5 fw-bold mb-3">Recursos Principais</h2>
                <p class="lead text-muted">Tudo que você precisa para análise profissional de trading</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-chart-line fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Dashboard Interativo</h5>
                        <p class="card-text text-muted">
                            Visualize seus resultados com gráficos interativos e métricas em tempo real.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-calculator fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">Análise de P&L</h5>
                        <p class="card-text text-muted">
                            Acompanhe lucros e perdas com análises detalhadas por período e mercado.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-globe fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title">Multi-Mercados</h5>
                        <p class="card-text text-muted">
                            Suporte para múltiplos mercados e moedas com conversão automática.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold text-primary">100+</h3>
                    <p class="text-muted">Traders Ativos</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold text-success">10K+</h3>
                    <p class="text-muted">Operações Analisadas</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold text-info">15+</h3>
                    <p class="text-muted">Mercados Suportados</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-item">
                    <h3 class="display-4 fw-bold text-warning">24/7</h3>
                    <p class="text-muted">Disponibilidade</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Pronto para Começar?</h2>
                <p class="lead mb-4">
                    Acesse nossa plataforma e comece a analisar seus resultados de trading hoje mesmo.
                </p>
                <a href="/login" class="btn btn-warning btn-lg">
                    <i class="fas fa-rocket me-2"></i>Começar Agora
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.hero-section {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
}

.feature-icon {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.stat-item {
    padding: 2rem 0;
}

.card {
    transition: transform 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
}

.main-content {
    padding-top: 76px; /* Account for fixed navbar */
}
</style>