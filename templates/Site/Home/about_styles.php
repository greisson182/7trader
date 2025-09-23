<style>
        /* Hero Section Styles */
        .hero-section {
            background: linear-gradient(135deg, #000 0%, #1a1a1a 50%, #000 100%);
            min-height: 60vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .financial-pattern {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(0, 233, 68, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 233, 68, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(0, 233, 68, 0.1) 0%, transparent 50%);
            animation: patternMove 20s ease-in-out infinite;
        }

        @keyframes patternMove {
            0%, 100% { transform: translateX(0) translateY(0); }
            25% { transform: translateX(-10px) translateY(-5px); }
            50% { transform: translateX(10px) translateY(5px); }
            75% { transform: translateX(-5px) translateY(10px); }
        }

        .market-status {
            display: inline-block;
            animation: statusPulse 2s ease-in-out infinite;
        }

        @keyframes statusPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .section-title {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.2;
            text-shadow: 
                0 0 20px rgba(0, 233, 68, 0.3),
                0 0 40px rgba(0, 233, 68, 0.2),
                0 0 60px rgba(0, 233, 68, 0.1);
            animation: titleGlow 4s ease-in-out infinite;
        }

        @keyframes titleGlow {
            0%, 100% { 
                text-shadow: 
                    0 0 20px rgba(0, 233, 68, 0.3),
                    0 0 40px rgba(0, 233, 68, 0.2),
                    0 0 60px rgba(0, 233, 68, 0.1);
            }
            50% { 
                text-shadow: 
                    0 0 30px rgba(0, 233, 68, 0.5),
                    0 0 50px rgba(0, 233, 68, 0.3),
                    0 0 70px rgba(0, 233, 68, 0.2);
            }
        }

        .section-subtitle {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            line-height: 1.6;
        }

        /* Background Sections */
        .financial-features-bg {
            background: linear-gradient(135deg, #000 0%, #1a1a1a 50%, #000 100%);
            position: relative;
            animation: bgMove 25s ease-in-out infinite;
        }

        @keyframes bgMove {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Modern Cards */
        .modern-card {
            background: rgba(26, 26, 26, 0.9);
            border: 1px solid rgba(0, 233, 68, 0.2);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 233, 68, 0.4);
            box-shadow: 0 10px 30px rgba(0, 233, 68, 0.2);
        }

        /* Button Styles */
        .btn-primary-modern {
            background: linear-gradient(135deg, #00e944, #00b536);
            border: none;
            border-radius: 50px;
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
            background: linear-gradient(135deg, #00ff4d, #00cc3a);
        }

        /* Glow Text Effect */
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