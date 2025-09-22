<!-- Contact Hero Section -->
<section class="py-5 bg-primary bg-gradient text-white">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold mb-3">Entre em Contato</h1>
                <p class="lead">Estamos aqui para ajudar você a maximizar seus resultados no trading</p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h2 class="h3 fw-bold text-primary">Fale Conosco</h2>
                            <p class="text-muted">Preencha o formulário abaixo e entraremos em contato em breve</p>
                        </div>
                        
                        <form id="contactForm" method="POST" action="/contato">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label fw-semibold">Nome Completo</label>
                                    <input type="text" class="form-control form-control-lg" id="name" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-semibold">E-mail</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-semibold">Telefone</label>
                                    <input type="tel" class="form-control form-control-lg" id="phone" name="phone">
                                </div>
                                <div class="col-md-6">
                                    <label for="subject" class="form-label fw-semibold">Assunto</label>
                                    <select class="form-select form-select-lg" id="subject" name="subject" required>
                                        <option value="">Selecione um assunto</option>
                                        <option value="duvidas">Dúvidas sobre a plataforma</option>
                                        <option value="suporte">Suporte técnico</option>
                                        <option value="comercial">Informações comerciais</option>
                                        <option value="sugestoes">Sugestões e melhorias</option>
                                        <option value="outros">Outros</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label fw-semibold">Mensagem</label>
                                    <textarea class="form-control" id="message" name="message" rows="5" 
                                              placeholder="Descreva sua dúvida, sugestão ou como podemos ajudá-lo..." required></textarea>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="privacy" name="privacy" required>
                                        <label class="form-check-label text-muted" for="privacy">
                                            Concordo com o tratamento dos meus dados pessoais conforme nossa política de privacidade
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 text-center">
                                    <button type="submit" class="btn btn-primary btn-lg px-5">
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
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="h3 fw-bold">Perguntas Frequentes</h2>
                    <p class="text-muted">Encontre respostas para as dúvidas mais comuns</p>
                </div>
                
                <div class="accordion" id="faqAccordion">
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq1">
                                Como funciona o BackTest?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                O BackTest é uma plataforma completa para registro e análise de operações de trading. 
                                Você registra suas operações, e o sistema gera relatórios detalhados com métricas 
                                de performance, gráficos e insights para melhorar seus resultados.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq2">
                                Meus dados estão seguros?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Sim! Utilizamos as melhores práticas de segurança, incluindo criptografia de dados, 
                                backup automático e acesso seguro. Seus dados são tratados com total confidencialidade 
                                e nunca são compartilhados com terceiros.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm mb-3">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq3">
                                Posso importar dados de outras plataformas?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
                                Estamos trabalhando em funcionalidades de importação de dados de diversas corretoras 
                                e plataformas de trading. Entre em contato para saber mais sobre as opções disponíveis 
                                para sua situação específica.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed fw-semibold" type="button" 
                                    data-bs-toggle="collapse" data-bs-target="#faq4">
                                Como posso começar a usar?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body text-muted">
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
</section>