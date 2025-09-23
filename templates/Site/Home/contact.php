<style>
        /* Estilos específicos da página de contato */
        .hero-section {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);
            position: relative;
            overflow: hidden;
            padding: 120px 0 80px;
        }

        .financial-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 30%, rgba(0, 233, 68, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(0, 233, 68, 0.08) 0%, transparent 50%),
                linear-gradient(45deg, transparent 49%, rgba(0, 233, 68, 0.02) 50%, transparent 51%);
            animation: patternMove 20s linear infinite;
        }

        @keyframes patternMove {
            0% { transform: translateX(0) translateY(0); }
            25% { transform: translateX(-10px) translateY(-5px); }
            50% { transform: translateX(10px) translateY(5px); }
            75% { transform: translateX(-5px) translateY(10px); }
            100% { transform: translateX(0) translateY(0); }
        }

        .market-status {
            background: linear-gradient(135deg, rgba(0, 233, 68, 0.2), rgba(0, 233, 68, 0.1));
            border: 1px solid rgba(0, 233, 68, 0.3);
            color: #00e944;
            padding: 8px 20px;
            border-radius: 25px;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
            animation: statusPulse 3s ease-in-out infinite;
        }

        @keyframes statusPulse {
            0%, 100% { box-shadow: 0 0 10px rgba(0, 233, 68, 0.3); }
            50% { box-shadow: 0 0 20px rgba(0, 233, 68, 0.5); }
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff 0%, #00e944 50%, #ffffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(0, 233, 68, 0.5);
            margin-bottom: 20px;
            animation: titleGlow 4s ease-in-out infinite;
        }

        @keyframes titleGlow {
            0%, 100% { 
                filter: drop-shadow(0 0 10px rgba(0, 233, 68, 0.3));
            }
            50% { 
                filter: drop-shadow(0 0 25px rgba(0, 233, 68, 0.6));
            }
        }

        .section-subtitle {
            font-size: 1.3rem;
            color: #b8b8b8;
            font-weight: 400;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto;
        }

        .financial-features-bg {
            background: linear-gradient(135deg, #0f0f0f 0%, #1a1a1a 50%, #0f0f0f 100%);
            position: relative;
            overflow: hidden;
        }

        .financial-features-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 30% 20%, rgba(0, 233, 68, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 70% 80%, rgba(0, 233, 68, 0.03) 0%, transparent 50%);
            animation: bgMove 15s ease-in-out infinite;
        }

        @keyframes bgMove {
            0%, 100% { opacity: 0.5; }
            50% { opacity: 0.8; }
        }

        .modern-card {
            background: linear-gradient(135deg, rgba(26, 26, 26, 0.9) 0%, rgba(15, 15, 15, 0.9) 100%);
            border: 1px solid rgba(0, 233, 68, 0.2);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 
                0 10px 30px rgba(0, 0, 0, 0.3),
                0 0 20px rgba(0, 233, 68, 0.1);
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.4),
                0 0 30px rgba(0, 233, 68, 0.2);
            border-color: rgba(0, 233, 68, 0.4);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #00e944 0%, #00b836 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 30px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 233, 68, 0.3);
        }

        .btn-primary-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 233, 68, 0.4);
            background: linear-gradient(135deg, #00ff4d 0%, #00d63f 100%);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #00e944;
            box-shadow: 0 0 0 0.2rem rgba(0, 233, 68, 0.25);
        }

        /* FAQ Specific Styles */
        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, rgba(0, 233, 68, 0.2), rgba(0, 233, 68, 0.1)) !important;
            color: #00e944 !important;
            border-color: rgba(0, 233, 68, 0.3) !important;
            box-shadow: 0 0 15px rgba(0, 233, 68, 0.2) !important;
        }

        .accordion-button:focus {
            border-color: rgba(0, 233, 68, 0.4);
            box-shadow: 0 0 0 0.25rem rgba(0, 233, 68, 0.25);
        }

        .accordion-button::after {
            filter: brightness(0) saturate(100%) invert(64%) sepia(98%) saturate(1000%) hue-rotate(100deg) brightness(1.2) contrast(1);
        }

        .glow-text {
            text-shadow: 
                0 0 10px rgba(0, 233, 68, 0.5),
                0 0 20px rgba(0, 233, 68, 0.3),
                0 0 30px rgba(0, 233, 68, 0.2);
            animation: textGlow 3s ease-in-out infinite;
        }

        @keyframes textGlow {
            0%, 100% { 
                text-shadow: 
                    0 0 10px rgba(0, 233, 68, 0.5),
                    0 0 20px rgba(0, 233, 68, 0.3),
                    0 0 30px rgba(0, 233, 68, 0.2);
            }
            50% { 
                text-shadow: 
                    0 0 15px rgba(0, 233, 68, 0.7),
                    0 0 25px rgba(0, 233, 68, 0.5),
                    0 0 35px rgba(0, 233, 68, 0.3);
            }
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2.5rem;
            }
            
            .hero-section {
                padding: 80px 0 60px;
            }
        }
    </style>

<!-- Contact Hero Section -->
<section class="hero-section text-white py-5 position-relative overflow-hidden" id="banner">
    <div class="container position-relative">
        <div class="row justify-content-center text-center min-vh-50">
            <div class="col-lg-8">
                <div class="hero-content">
                    <div class="market-status mb-3">
                        <span class="badge bg-success pulse-animation">
                            <i class="fas fa-envelope me-1"></i>ESTAMOS AQUI PARA VOCÊ
                        </span>
                    </div>
                    <h1 class="section-title neon mb-4">
                        Entre em <span class="text-success glow-text">Contato</span><br>
                        <span class="text-white">Estamos aqui para ajudar você a maximizar seus resultados no trading</span>
                    </h1>
                    <p class="section-subtitle mb-4 fade-in-up">
                        Nossa equipe está pronta para esclarecer suas dúvidas e ajudá-lo a escolher 
                        a melhor estratégia para acelerar seus resultados no mercado financeiro.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Background Pattern -->
    <div class="financial-pattern"></div>
</section>

<!-- Contact Form Section -->
<section class="py-5 financial-features-bg">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="modern-card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="h3 fw-bold text-white">
                                <i class="fas fa-comments me-2 text-success"></i>Fale Conosco
                            </h2>
                            <p class="text-light opacity-75">Preencha o formulário abaixo e entraremos em contato em breve</p>
                        </div>
                        
                        <form id="contactForm" method="POST" action="/contato">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold text-light">Nome Completo</label>
                                    <input type="text" class="form-control form-control-lg bg-dark text-light border-secondary" id="name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold text-light">E-mail</label>
                                    <input type="email" class="form-control form-control-lg bg-dark text-light border-secondary" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold text-light">Telefone</label>
                                    <input type="tel" class="form-control form-control-lg bg-dark text-light border-secondary" id="phone" name="phone">
                                </div>
                                <div class="col-md-6">
                                    <label for="subject" class="form-label fw-semibold text-light">Assunto</label>
                                    <select class="form-select form-select-lg bg-dark text-light border-secondary" id="subject" name="subject" required>
                                        <option value="">Selecione um assunto</option>
                                        <option value="duvidas">Dúvidas sobre a plataforma</option>
                                        <option value="suporte">Suporte técnico</option>
                                        <option value="comercial">Informações comerciais</option>
                                        <option value="sugestoes">Sugestões e melhorias</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-semibold text-light">Mensagem</label>
                                    <textarea class="form-control bg-dark text-light border-secondary" id="message" name="message" rows="5" 
                                              placeholder="Descreva sua dúvida, sugestão ou como podemos ajudá-lo..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                        <label class="form-check-label text-light opacity-75" for="privacy">
                                            Concordo com o tratamento dos meus dados pessoais conforme nossa política de privacidade
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary-modern btn-lg px-5">
                                        <i class="fas fa-paper-plane me-2"></i>Enviar Mensagem
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Info Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-envelope fa-2x text-white"></i>
                    </div>
                    <h4 class="h5 fw-bold">E-mail</h4>
                    <p class="text-muted mb-0">contato@backtest.com.br</p>
                    <p class="text-muted">suporte@backtest.com.br</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-success bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-phone fa-2x text-white"></i>
                    </div>
                    <h4 class="h5 fw-bold">Telefone</h4>
                    <p class="text-muted mb-0">(11) 9999-9999</p>
                    <p class="text-muted">Seg à Sex: 9h às 18h</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-warning bg-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" 
                         style="width: 80px; height: 80px;">
                        <i class="fas fa-clock fa-2x text-white"></i>
                    </div>
                    <h4 class="h5 fw-bold">Horário de Atendimento</h4>
                    <p class="text-muted mb-0">Segunda à Sexta</p>
                    <p class="text-muted">09:00 às 18:00</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5 financial-features-bg">
    <div class="container">
        <!-- FAQ Hero -->
        <div class="row justify-content-center mb-5">
            <div class="col-lg-8 text-center">
                <div class="market-status mb-3">
                    <i class="fas fa-question-circle me-2"></i>
                    <span>FAQ - Perguntas Frequentes</span>
                </div>
                <h2 class="section-title mb-4">
                    Tire suas <span class="text-success glow-text">Dúvidas</span><br>
                    <span class="text-white">Encontre respostas para as perguntas mais comuns</span>
                </h2>
                <p class="section-subtitle">
                    Nossa equipe preparou as respostas para as dúvidas mais frequentes 
                    sobre a plataforma e nossos serviços.
                </p>
            </div>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item modern-card border-0 shadow-lg mb-4">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold bg-dark text-light border-0" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq1">
                                <i class="fas fa-chart-line me-3 text-success"></i>
                                Como funciona o BackTest?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body bg-dark text-light border-top border-secondary">
                                O BackTest é uma plataforma completa para registro e análise de operações de trading. 
                                Você registra suas operações, e o sistema gera relatórios detalhados com métricas 
                                de performance, gráficos e insights para melhorar seus resultados.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item modern-card border-0 shadow-lg mb-4">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold bg-dark text-light border-0" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq2">
                                <i class="fas fa-shield-alt me-3 text-success"></i>
                                Meus dados estão seguros?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body bg-dark text-light border-top border-secondary">
                                Sim! Utilizamos as melhores práticas de segurança, incluindo criptografia de dados, 
                                backup automático e acesso seguro. Seus dados são tratados com total confidencialidade 
                                e nunca são compartilhados com terceiros.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item modern-card border-0 shadow-lg mb-4">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold bg-dark text-light border-0" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq3">
                                <i class="fas fa-download me-3 text-success"></i>
                                Posso importar dados de outras plataformas?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body bg-dark text-light border-top border-secondary">
                                Estamos trabalhando em funcionalidades de importação de dados de diversas corretoras 
                                e plataformas de trading. Entre em contato para saber mais sobre as opções disponíveis 
                                para sua situação específica.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item modern-card border-0 shadow-lg">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold bg-dark text-light border-0" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq4">
                                <i class="fas fa-rocket me-3 text-success"></i>
                                Como posso começar a usar?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body bg-dark text-light border-top border-secondary">
                                É muito simples! Entre em contato conosco através do formulário acima ou pelos 
                                nossos canais de atendimento. Nossa equipe irá orientá-lo sobre o processo de 
                                cadastro e primeiros passos na plataforma.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Background Pattern -->
    <div class="financial-pattern"></div>
</section>