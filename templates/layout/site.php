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
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-chart-line me-2"></i>BackTest
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Início</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="cursosDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-graduation-cap me-1"></i>Cursos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="cursosDropdown">
                            <li><a class="dropdown-item" href="/cursos/basico">
                                <i class="fas fa-play-circle me-2 text-primary"></i>Trading Básico
                            </a></li>
                            <li><a class="dropdown-item" href="/cursos/avancado">
                                <i class="fas fa-chart-line me-2 text-success"></i>Trading Avançado
                            </a></li>
                            <li><a class="dropdown-item" href="/cursos/profissional">
                                <i class="fas fa-trophy me-2 text-warning"></i>Trading Profissional
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/cursos">
                                <i class="fas fa-list me-2"></i>Ver Todos os Cursos
                            </a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/mentoria">
                            <i class="fas fa-handshake me-1"></i>Mentoria
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/sobre">Sobre</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contato">Contato</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/admin/students/dashboard">
                            <i class="fas fa-sign-in-alt me-1"></i>Área do Aluno
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
                        <i class="fas fa-chart-line me-2 text-success"></i>BackTest
                    </h5>
                    <p class="footer-subtitle mb-0">Plataforma profissional para análise de trading</p>
                    <p class="footer-description mt-2 mb-0">
                        Transforme seus dados em insights poderosos para o mercado financeiro
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-stats mb-3">
                        <div class="d-flex justify-content-md-end justify-content-start gap-4 flex-wrap">
                            <div class="stat-item">
                                <div class="stat-value text-success">📈</div>
                                <div class="stat-label">Trading</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value text-warning">📊</div>
                                <div class="stat-label">Analytics</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value text-info">🎯</div>
                                <div class="stat-label">Results</div>
                            </div>
                        </div>
                    </div>
                    <p class="footer-copyright mb-0">
                        &copy; <?= date('Y') ?> BackTest. Todos os direitos reservados.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Footer Styles -->
    <style>
        .trading-footer {
            background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #0a0a0a 100%);
            color: #ffffff;
            border-top: 2px solid rgba(0, 255, 136, 0.3);
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.5);
        }

        .footer-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #00ff88;
            text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
            animation: footerGlow 3s ease-in-out infinite;
        }

        .footer-subtitle {
            color: #b0b0b0;
            font-size: 1.1rem;
            font-weight: 500;
        }

        .footer-description {
            color: #888;
            font-size: 0.95rem;
            font-style: italic;
        }

        .footer-copyright {
            color: #666;
            font-size: 0.9rem;
        }

        .footer-stats .stat-item {
            text-align: center;
        }

        .footer-stats .stat-value {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .footer-stats .stat-label {
            font-size: 0.8rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Animação Realista de Candlesticks no Footer */
        .candlestick-container {
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 1;
            height: 100%;
            overflow: hidden;
        }

        .footer-candlestick {
            position: absolute;
            width: 8px;
            border-radius: 2px;
            opacity: 0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }

        .footer-candlestick.green {
            background: linear-gradient(to bottom, #00ff88 0%, #00d4aa 30%, #00b894 70%, #00ff88 100%);
            border: 1px solid #00ff88;
        }

        .footer-candlestick.red {
            background: linear-gradient(to bottom, #ff4757 0%, #ff3742 30%, #e84393 70%, #ff4757 100%);
            border: 1px solid #ff4757;
        }

        .footer-candlestick::before,
        .footer-candlestick::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            background: inherit;
            border-radius: 1px;
        }

        .footer-candlestick::before {
            top: -12px;
            height: 12px;
        }

        .footer-candlestick::after {
            bottom: -12px;
            height: 12px;
        }

        /* Animações de Mercado Realistas */
        @keyframes marketRise {
            0% {
                transform: translateY(80px) scale(0.8);
                opacity: 0;
            }
            15% {
                opacity: 0.7;
                transform: translateY(60px) scale(0.9);
            }
            30% {
                transform: translateY(40px) scale(1);
                opacity: 0.8;
            }
            50% {
                transform: translateY(20px) scale(1.1);
                opacity: 0.9;
            }
            70% {
                transform: translateY(10px) scale(1);
                opacity: 0.8;
            }
            85% {
                transform: translateY(-10px) scale(0.9);
                opacity: 0.6;
            }
            100% {
                transform: translateY(-30px) scale(0.7);
                opacity: 0;
            }
        }

        @keyframes marketFall {
            0% {
                transform: translateY(80px) scale(0.8);
                opacity: 0;
            }
            15% {
                opacity: 0.7;
                transform: translateY(70px) scale(0.9);
            }
            30% {
                transform: translateY(60px) scale(1);
                opacity: 0.8;
            }
            50% {
                transform: translateY(50px) scale(1.1);
                opacity: 0.9;
            }
            70% {
                transform: translateY(40px) scale(1);
                opacity: 0.8;
            }
            85% {
                transform: translateY(20px) scale(0.9);
                opacity: 0.6;
            }
            100% {
                transform: translateY(-10px) scale(0.7);
                opacity: 0;
            }
        }

        @keyframes marketVolatility {
            0% {
                transform: translateY(80px) scale(0.8) rotate(0deg);
                opacity: 0;
            }
            20% {
                transform: translateY(50px) scale(1) rotate(5deg);
                opacity: 0.8;
            }
            40% {
                transform: translateY(30px) scale(1.1) rotate(-3deg);
                opacity: 0.9;
            }
            60% {
                transform: translateY(20px) scale(1) rotate(2deg);
                opacity: 0.8;
            }
            80% {
                transform: translateY(10px) scale(0.9) rotate(-1deg);
                opacity: 0.6;
            }
            100% {
                transform: translateY(-20px) scale(0.7) rotate(0deg);
                opacity: 0;
            }
        }

        @keyframes footerGlow {
            0%, 100% {
                text-shadow: 0 0 20px rgba(0, 255, 136, 0.5);
            }
            50% {
                text-shadow: 0 0 30px rgba(0, 255, 136, 0.8), 0 0 40px rgba(0, 255, 136, 0.6);
            }
        }

        /* Responsividade */
        @media (max-width: 768px) {
            .footer-title {
                font-size: 1.5rem;
                text-align: center;
            }
            
            .footer-stats {
                text-align: center !important;
                margin-top: 1rem;
            }
            
            .footer-stats .d-flex {
                justify-content: center !important;
            }
        }
    </style>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Site JS -->
    <script src="/site/js/site.js"></script>
    
    <!-- Footer Candlestick Animation com Simulação de Mercado -->
    <script>
        // Simulador de Mercado Realista
        class MarketSimulator {
            constructor() {
                this.currentTrend = 'neutral'; // 'bullish', 'bearish', 'neutral', 'volatile'
                this.trendDuration = 0;
                this.maxTrendDuration = 15; // segundos
                this.price = 100; // preço base
                this.priceHistory = [];
                this.volatility = 0.5;
                
                this.updateTrend();
            }
            
            updateTrend() {
                const trends = ['bullish', 'bearish', 'neutral', 'volatile'];
                const weights = [0.3, 0.3, 0.2, 0.2]; // probabilidades
                
                let random = Math.random();
                let cumulativeWeight = 0;
                
                for (let i = 0; i < trends.length; i++) {
                    cumulativeWeight += weights[i];
                    if (random <= cumulativeWeight) {
                        this.currentTrend = trends[i];
                        break;
                    }
                }
                
                this.trendDuration = 0;
                this.maxTrendDuration = Math.random() * 20 + 10; // 10-30 segundos
                
                // Ajustar volatilidade baseada na tendência
                switch(this.currentTrend) {
                    case 'volatile':
                        this.volatility = Math.random() * 0.8 + 0.7; // 0.7-1.5
                        break;
                    case 'bullish':
                    case 'bearish':
                        this.volatility = Math.random() * 0.4 + 0.3; // 0.3-0.7
                        break;
                    default:
                        this.volatility = Math.random() * 0.3 + 0.2; // 0.2-0.5
                }
            }
            
            getMarketData() {
                this.trendDuration++;
                
                if (this.trendDuration >= this.maxTrendDuration) {
                    this.updateTrend();
                }
                
                // Calcular mudança de preço baseada na tendência
                let priceChange = 0;
                const baseChange = (Math.random() - 0.5) * this.volatility;
                
                switch(this.currentTrend) {
                    case 'bullish':
                        priceChange = baseChange + (Math.random() * 0.3 + 0.1);
                        break;
                    case 'bearish':
                        priceChange = baseChange - (Math.random() * 0.3 + 0.1);
                        break;
                    case 'volatile':
                        priceChange = (Math.random() - 0.5) * this.volatility * 2;
                        break;
                    default: // neutral
                        priceChange = baseChange * 0.5;
                }
                
                this.price += priceChange;
                this.priceHistory.push(this.price);
                
                // Manter histórico limitado
                if (this.priceHistory.length > 50) {
                    this.priceHistory.shift();
                }
                
                return {
                    trend: this.currentTrend,
                    price: this.price,
                    change: priceChange,
                    volatility: this.volatility,
                    isGreen: priceChange >= 0
                };
            }
        }
        
        // Instância do simulador
        const marketSim = new MarketSimulator();
        
        // Função para criar candlesticks estáticos
        function createStaticFooterCandlesticks() {
            const container = document.getElementById('footerCandlesticks');
            if (!container) return;
            
            // Criar candlesticks estáticos para decoração
            const candlestickPositions = [10, 20, 30, 40, 50, 60, 70, 80, 90];
            const candlestickTypes = [true, false, true, false, true, true, false, true, false]; // true = green, false = red
            const candlestickHeights = [22, 28, 20, 32, 24, 30, 26, 18, 25];
            
            candlestickPositions.forEach((position, index) => {
                const candlestick = document.createElement('div');
                const isGreen = candlestickTypes[index];
                const height = candlestickHeights[index];
                
                candlestick.className = `footer-candlestick ${isGreen ? 'green' : 'red'} static`;
                candlestick.style.left = position + '%';
                candlestick.style.height = height + 'px';
                candlestick.style.position = 'absolute';
                candlestick.style.bottom = '0';
                candlestick.style.animation = 'none'; // Remove todas as animações
                candlestick.style.opacity = '0.5'; // Deixa mais sutil como decoração
                
                container.appendChild(candlestick);
            });
        }
        
        // Iniciar candlesticks estáticos quando a página carregar
        document.addEventListener('DOMContentLoaded', function() {
            // Criar candlesticks estáticos apenas uma vez
            createStaticFooterCandlesticks();
            
            // Debug: mostrar tendência atual no console (remover em produção)
            setInterval(() => {
                const data = marketSim.getMarketData();
                console.log(`Tendência: ${data.trend}, Preço: ${data.price.toFixed(2)}, Volatilidade: ${data.volatility.toFixed(2)}`);
            }, 5000);
        });
    </script>
</body>
</html>