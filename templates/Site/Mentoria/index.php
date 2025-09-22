<?php
/**
 * @var \App\View\AppView $this
 * @var string $title
 * @var string $description
 */
?>

<!-- Hero Section -->
<section class="hero-section bg-gradient-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center min-vh-50">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    <i class="fas fa-handshake text-warning me-3"></i>
                    Mentoria Personalizada
                </h1>
                <p class="lead mb-4">
                    Acelere seu crescimento no trading com mentoria individual. 
                    Aprenda estratégias comprovadas e desenvolva disciplina para 
                    alcançar consistência nos mercados financeiros.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="#planos" class="btn btn-warning btn-lg px-4">
                        <i class="fas fa-rocket me-2"></i>Ver Planos
                    </a>
                    <a href="#depoimentos" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-star me-2"></i>Depoimentos
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <div class="mentor-image-container">
                    <img src="/site/images/mentor-hero.svg" alt="Mentor Trading" class="img-fluid mentor-hero-img">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefícios da Mentoria -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-dark mb-3">Por que escolher nossa mentoria?</h2>
                <p class="lead text-muted">Transforme seu conhecimento em resultados consistentes</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="benefit-card h-100 text-center p-4">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-user-tie fa-3x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Mentoria 1:1</h4>
                    <p class="text-muted">
                        Sessões individuais focadas nas suas necessidades específicas. 
                        Análise personalizada do seu perfil de risco e objetivos.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-card h-100 text-center p-4">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-chart-line fa-3x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Análise de Trades</h4>
                    <p class="text-muted">
                        Revisão detalhada das suas operações. Identificação de padrões 
                        e oportunidades de melhoria no seu trading.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="benefit-card h-100 text-center p-4">
                    <div class="benefit-icon mb-3">
                        <i class="fas fa-headset fa-3x text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Suporte Contínuo</h4>
                    <p class="text-muted">
                        Acesso direto ao mentor via WhatsApp. Suporte em tempo real 
                        durante o horário de mercado.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Planos de Mentoria -->
<section id="planos" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-dark mb-3">Planos de Mentoria</h2>
                <p class="lead text-muted">Escolha o plano ideal para seu nível de experiência</p>
            </div>
        </div>
        <div class="row g-4 justify-content-center">
            <!-- Plano Básico -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card h-100 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white text-center py-4">
                        <h3 class="fw-bold mb-0">Mentoria Básica</h3>
                        <p class="mb-0 opacity-75">Para iniciantes</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <span class="price-currency">R$</span>
                            <span class="price-amount">297</span>
                            <span class="price-period">/mês</span>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                2 sessões mensais de 1h
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Análise de até 10 trades
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Suporte via WhatsApp
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Material didático exclusivo
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Acesso ao grupo VIP
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent p-4">
                        <a href="#contato" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-rocket me-2"></i>Começar Agora
                        </a>
                    </div>
                </div>
            </div>

            <!-- Plano Premium -->
            <div class="col-lg-4 col-md-6">
                <div class="pricing-card h-100 border-0 shadow-lg position-relative">
                    <div class="popular-badge">
                        <span class="badge bg-warning text-dark fw-bold">MAIS POPULAR</span>
                    </div>
                    <div class="card-header bg-gradient-warning text-dark text-center py-4">
                        <h3 class="fw-bold mb-0">Mentoria Premium</h3>
                        <p class="mb-0 opacity-75">Para traders experientes</p>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <span class="price-currency">R$</span>
                            <span class="price-amount">497</span>
                            <span class="price-period">/mês</span>
                        </div>
                        <ul class="list-unstyled">
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                4 sessões mensais de 1h
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Análise ilimitada de trades
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Suporte prioritário 24/7
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Estratégias personalizadas
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Acesso a sinais exclusivos
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-check text-success me-2"></i>
                                Revisão de setup completo
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent p-4">
                        <a href="#contato" class="btn btn-warning w-100 btn-lg text-dark fw-bold">
                            <i class="fas fa-crown me-2"></i>Escolher Premium
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Sobre o Mentor -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 text-center mb-4 mb-lg-0">
                <div class="mentor-photo-container">
                    <img src="/site/images/mentor-profile.svg" alt="Mentor" class="mentor-photo img-fluid rounded-circle shadow-lg">
                </div>
            </div>
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold text-dark mb-4">Conheça seu Mentor</h2>
                <h3 class="text-primary mb-3">Carlos Silva</h3>
                <p class="lead text-muted mb-4">
                    Trader profissional há mais de 8 anos, especialista em análise técnica 
                    e gerenciamento de risco. Já treinou mais de 500 traders em todo o Brasil.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="mentor-stat">
                            <i class="fas fa-trophy text-warning me-2"></i>
                            <strong>8+ anos</strong> de experiência
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mentor-stat">
                            <i class="fas fa-users text-primary me-2"></i>
                            <strong>500+</strong> traders treinados
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mentor-stat">
                            <i class="fas fa-chart-line text-success me-2"></i>
                            <strong>85%</strong> de trades positivos
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="mentor-stat">
                            <i class="fas fa-medal text-info me-2"></i>
                            <strong>Certificado</strong> CPA-20
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Depoimentos -->
<section id="depoimentos" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold text-dark mb-3">O que dizem nossos alunos</h2>
                <p class="lead text-muted">Histórias reais de transformação</p>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card h-100 p-4 text-center">
                    <div class="stars mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="testimonial-text mb-4">
                        "Em 3 meses de mentoria, consegui organizar minha estratégia 
                        e aumentar minha consistência em 70%. Recomendo!"
                    </p>
                    <div class="testimonial-author">
                        <strong>Maria Santos</strong>
                        <small class="text-muted d-block">Trader há 2 anos</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card h-100 p-4 text-center">
                    <div class="stars mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="testimonial-text mb-4">
                        "O Carlos me ajudou a entender meus erros e desenvolver 
                        disciplina. Hoje tenho resultados muito mais estáveis."
                    </p>
                    <div class="testimonial-author">
                        <strong>João Oliveira</strong>
                        <small class="text-muted d-block">Trader profissional</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial-card h-100 p-4 text-center">
                    <div class="stars mb-3">
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                        <i class="fas fa-star text-warning"></i>
                    </div>
                    <p class="testimonial-text mb-4">
                        "Mentoria transformadora! Aprendi a gerenciar risco de forma 
                        profissional e meus resultados melhoraram drasticamente."
                    </p>
                    <div class="testimonial-author">
                        <strong>Ana Costa</strong>
                        <small class="text-muted d-block">Investidora</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Final -->
<section id="contato" class="py-5 bg-gradient-primary text-white">
    <div class="container text-center">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Pronto para acelerar seus resultados?</h2>
                <p class="lead mb-4">
                    Agende uma conversa gratuita de 30 minutos para conhecer nossa metodologia 
                    e descobrir como a mentoria pode transformar seu trading.
                </p>
                <div class="d-flex gap-3 justify-content-center flex-wrap">
                    <a href="https://wa.me/5511999999999" target="_blank" class="btn btn-warning btn-lg px-4">
                        <i class="fab fa-whatsapp me-2"></i>Agendar Conversa Gratuita
                    </a>
                    <a href="/contato" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-envelope me-2"></i>Enviar E-mail
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Hero Section */
.hero-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 60vh;
}

.mentor-hero-img {
    max-width: 400px;
    filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));
}

/* Benefit Cards */
.benefit-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.benefit-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.benefit-icon {
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Pricing Cards */
.pricing-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    transition: transform 0.3s ease;
    position: relative;
}

.pricing-card:hover {
    transform: translateY(-5px);
}

.popular-badge {
    position: absolute;
    top: -10px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
}

.price-currency {
    font-size: 1.5rem;
    font-weight: 600;
    vertical-align: top;
}

.price-amount {
    font-size: 3rem;
    font-weight: 700;
    color: #333;
}

.price-period {
    font-size: 1.2rem;
    color: #666;
}

/* Mentor Section */
.mentor-photo {
    width: 250px;
    height: 250px;
    object-fit: cover;
    border: 5px solid #fff;
}

.mentor-stat {
    padding: 10px 0;
    font-size: 1.1rem;
}

/* Testimonials */
.testimonial-card {
    background: white;
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.testimonial-card:hover {
    transform: translateY(-5px);
}

.testimonial-text {
    font-style: italic;
    color: #555;
    line-height: 1.6;
}

/* Gradients */
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }
    
    .mentor-hero-img {
        max-width: 300px;
    }
    
    .mentor-photo {
        width: 200px;
        height: 200px;
    }
}
</style>