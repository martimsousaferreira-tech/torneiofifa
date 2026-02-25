<?php 
session_start(); 
require_once "config.php"; 
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Torneio Expo FC26 - XXIX EXPOCOLGAIA</title>
    <meta name="description" content="Participa no Torneio Expo FC26 na XXIX EXPOCOLGAIA. Compete com os melhores jogadores no Colégio Gaia e ganha prémios incríveis!">
    <meta name="keywords" content="FC26, Torneio FIFA, EXPOCOLGAIA, Colégio Gaia, ITM, Gaming">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>⚽</text></svg>">
</head>
<body>
    <div class="bg-glows">
        <div class="glow glow-1"></div>
        <div class="glow glow-2"></div>
        <div class="glow glow-3"></div>
    </div>

    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">EXPO <span>FC26</span></div>
                
                <div class="mobile-menu-btn">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div class="nav-links">
                    <div class="main-nav">
                        <a href="#inicio" onclick="toggleMenu()">Início</a>
                        <a href="#regulamento" onclick="toggleMenu()">Regras</a>
                        <a href="#inscricao" onclick="toggleMenu()">Inscrição</a>
                        <a href="#premios" onclick="toggleMenu()">Prémios</a>
                        <a href="#classificacoes" onclick="toggleMenu()">Brackets</a>
                        <a href="#calendario" onclick="toggleMenu()">Calendário</a>
                        <a href="https://www.twitch.tv/torneioitm" target="_blank" style="color: #9146ff;"><i class="fab fa-twitch"></i> Live <span class="live-dot"></span></a>
                    </div>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <div class="user-pill-container" style="display: flex; gap: 0.8rem; align-items: center;">
                            <?php if(isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                                <a href="backoffice" class="btn-admin-nav" title="Painel de Controlo"><i class="fas fa-terminal"></i></a>
                            <?php endif; ?>
                            <div class="user-menu-pill">
                                <span class="user-name-label">Olá, <?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'])[0]); ?></span>
                                <a href="logout" class="btn-logout-circle" title="Terminar Sessão"><i class="fas fa-power-off"></i></a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login" class="btn btn-login">Login / Registar</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section id="inicio" class="hero">
        <div class="hero-content">
            <div class="hero-badge">12º ITM APRESENTA</div>
            <h1>TORNEIO<br>EXPO <span class="glow-text">FC26</span></h1>
            
            <?php
                $tot_reg_res = $conn->query("SELECT COUNT(*) as t FROM inscricoes");
                $total_registrations = $tot_reg_res->fetch_assoc()['t'] ?? 0;
            ?>
            <div class="registrations-counter">
                <i class="fas fa-users"></i> <span><?php echo $total_registrations; ?>/32 JOGADORES INSCRITOS</span>
            </div>

            <div class="hero-event-label">XXIX EXPOCOLGAIA</div>

            <div class="countdown-container">
                <div class="countdown-item"><span id="days">00</span><label>Dias</label></div>
                <div class="countdown-item"><span id="hours">00</span><label>Horas</label></div>
                <div class="countdown-item"><span id="minutes">00</span><label>Minutos</label></div>
                <div class="countdown-item"><span id="seconds">00</span><label>Segundos</label></div>
            </div>

            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="#regulamento" class="btn btn-login"><span>Saber Mais</span></a>
                <?php if(!isset($_SESSION['user_id'])): ?>
                    <a href="login" class="btn btn-outline"><span>Criar Conta</span></a>
                <?php else: ?>
                    <a href="#inscricao" class="btn btn-outline"><span>Inscrever Agora</span></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="scroll-indicator">
            <span>Scroll</span>
            <div class="mouse-icon"></div>
        </div>
    </section>

    <!-- Regulamento Section -->
    <section id="regulamento">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 4rem;">
                <h2 class="section-title">Regulamento Oficial</h2>
                <p style="color: var(--text-secondary);">Regras essenciais para garantir o fair-play</p>
            </div>
            <div class="rules-grid">
                <div class="rule-card">
                    <div class="rule-icon"><i class="fas fa-sitemap"></i></div>
                    <h3>Eliminatória Direta</h3>
                    <p>Sorteio de 32 jogadores em sistema de eliminatória única (bracket). Perdeu, estás fora da competição.</p>
                </div>
                <div class="rule-card">
                    <div class="rule-icon"><i class="fas fa-clock"></i></div>
                    <h3>Duração de 10m</h3>
                    <p>Cada partida será realizada no modo offline, com 4 minutos por parte. Em caso de empate, o jogo será decidido por penáltis.</p>
                </div>
                <div class="rule-card">
                    <div class="rule-icon"><i class="fas fa-vr-cardboard"></i></div>
                    <h3>Configurações</h3>
                    <p>Cada jogador deverá escolher uma equipa de um clube real e as equipas terão um rating de 95 (sendo proibidas seleções nacionais).</p>
                </div>
                <div class="rule-card">
                    <div class="rule-icon"><i class="fas fa-fist-raised"></i></div>
                    <h3>Fair Play</h3>
                    <p>Qualquer comportamento antidesportivo resultará em desqualificação imediata do torneio.</p>
                </div>
            </div>
            <div style="text-align: center; margin-top: 4rem; width: 100%;">
                <a href="regulamento.pdf" target="_blank" class="btn btn-outline" style="border-radius: 50px; padding: 12px 35px;">
                    <i class="fas fa-file-pdf"></i> Ver Regulamento Completo (PDF)
                </a>
            </div>
        </div>
    </section>

    <!-- Transmissão Section -->
    <section id="transmissao" style="background: #0e0e11; border-top: 1px solid var(--glass-border); border-bottom: 1px solid var(--glass-border);">
        <div class="container">
            <div class="live-stream-zone">
                <div class="live-content">
                    <span class="live-badge"><i class="fas fa-circle pulse-icon"></i> LIVE STREAM</span>
                    <h2>Acompanha em Direto</h2>
                    <p>Não percas nenhum golo! Estamos a transmitir os melhores jogos do torneio em direto na Twitch com comentários ao vivo.</p>
                    <div class="live-actions">
                        <a href="https://www.twitch.tv/torneioitm" target="_blank" class="btn btn-twitch">
                            <i class="fab fa-twitch"></i> Seguir na Twitch
                        </a>
                    </div>
                </div>
                <div class="live-player-wrapper" style="box-shadow: 0 0 50px rgba(145, 70, 255, 0.2); border-radius: 15px; overflow: hidden;">
                    <div id="twitch-embed"></div>
                </div>
                <script src="https://embed.twitch.tv/embed/v1.js"></script>
                <script type="text/javascript">
                  new Twitch.Embed("twitch-embed", {
                    width: "100%",
                    height: 480,
                    channel: "torneioitm",
                    layout: "video",
                    parent: [window.location.hostname]
                  });
                </script>
            </div>
        </div>
    </section>

    <!-- Prizes Section -->
    <section id="premios" style="background: #0e0e11;">
        <div class="container">
            <h2 class="section-title">Grandes <span class="glow-text">Prémios</span></h2>
            <div class="prizes-grid">
                <div class="prize-card">
                    <div class="prize-icon"><i class="fas fa-medal" style="color: #cd7f32;"></i></div>
                    <h3>3º Lugar</h3>
                    <p>Cartão Presente 10€</p>
                </div>
                <div class="prize-card winner">
                    <div class="prize-icon"><i class="fas fa-trophy" style="color: #ffd700; font-size: 4rem;"></i></div>
                    <h3 style="font-size: 2rem;">1º Lugar</h3>
                    <p style="font-size: 1.2rem; font-weight: 700;">Cartão Presente 50€</p>
                </div>
                <div class="prize-card">
                    <div class="prize-icon"><i class="fas fa-medal" style="color: #c0c0c0;"></i></div>
                    <h3>2º Lugar</h3>
                    <p>Cartão Presente 25€</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Inscrição Section -->
    <section id="inscricao" style="background: #09090b; position: relative; overflow: hidden;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 3rem;">
                <h2 class="section-title">Garante a tua <span class="glow-text">Vaga</span></h2>
                <p style="color: var(--text-secondary);">Inscrições limitadas a 32 jogadores por ordem de confirmação.</p>
            </div>

            <?php if(isset($_SESSION['user_id'])): 
                $uid = $_SESSION['user_id'];
                $check_reg = $conn->prepare("SELECT pago FROM inscricoes WHERE user_id = ?");
                $check_reg->bind_param("i", $uid);
                $check_reg->execute();
                $reg_res = $check_reg->get_result();
                $is_registered = $reg_res->fetch_assoc();

                if($is_registered): ?>
                    <div class="status-card" style="max-width: 600px; margin: 0 auto; background: rgba(74, 222, 128, 0.05); border: 1px solid var(--accent-color); padding: 2rem; border-radius: 20px; text-align: center;">
                        <i class="fas fa-check-circle" style="font-size: 3rem; color: var(--accent-color); margin-bottom: 1rem;"></i>
                        <h3 style="color: #fff; margin-bottom: 0.5rem;">Já estás inscrito!</h3>
                        <p style="color: var(--text-secondary);">
                            <?php if($is_registered['pago']): ?>
                                O teu pagamento foi confirmado. Prepara-te para o jogo!
                            <?php else: ?>
                                A tua inscrição está pendente de pagamento (2€). Entrega ao staff do Torneio.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php elseif($total_registrations >= 32): ?>
                    <div class="status-card" style="max-width: 600px; margin: 0 auto; background: rgba(239, 68, 68, 0.05); border: 1px solid #ef4444; padding: 3rem 2rem; border-radius: 20px; text-align: center; backdrop-filter: blur(10px);">
                        <div style="position: relative; display: inline-block; margin-bottom: 1.5rem;">
                            <i class="fas fa-lock" style="font-size: 4rem; color: #ef4444; text-shadow: 0 0 20px rgba(239, 68, 68, 0.4);"></i>
                        </div>
                        <h3 style="color: #fff; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 2px;">Inscrições Encerradas</h3>
                        <p style="color: var(--text-secondary); font-size: 1.1rem;">
                            Desculpa, mas já atingimos o limite máximo de 32 jogadores para este torneio.
                        </p>
                        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
                            <p style="color: var(--accent-color); font-weight: 700; font-size: 0.9rem;">FICA ATENTO PARA PRÓXIMAS EDIÇÕES!</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="form-container" style="max-width: 600px; margin: 0 auto;">
                        <form action="processar_inscricao.php" method="POST" class="tournament-form">
                            <div class="form-grid">
                                <div class="form-group fg-span-2">
                                    <label>Nome Completo (Máx 11 chars)</label>
                                    <input type="text" name="nome" value="<?php echo htmlspecialchars(substr($_SESSION['user_name'], 0, 11)); ?>" maxlength="11" required>
                                </div>
                                <div class="form-group">
                                    <label>Ano</label>
                                    <select name="ano" required>
                                        <option value="">Selecionar</option>
                                        <option value="10º">10º Ano</option>
                                        <option value="11º">11º Ano</option>
                                        <option value="12º">12º Ano</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Curso</label>
                                    <select name="turma_letra" required>
                                        <option value="">Selecionar</option>
                                        <option value="AM">AM</option>
                                        <option value="AGD">AGD</option>
                                        <option value="AQB">AQB</option>
                                        <option value="CGE">CGE</option>
                                        <option value="CM">CM</option>
                                        <option value="DP-AE">DP-AE</option>
                                        <option value="EIA">EIA</option>
                                        <option value="ETC">ETC</option>
                                        <option value="ITM">ITM</option>
                                        <option value="MDI">MDI</option>
                                        <option value="TDS">TDS</option>
                                        <option value="TSA">TSA</option>
                                        <option value="TSI">TSI</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Número de Aluno (1-30)</label>
                                    <input type="number" name="numero" placeholder="Ex: 5" min="1" max="30" required>
                                </div>
                                <div class="form-group">
                                    <label>Telemóvel</label>
                                    <input type="tel" name="telemovel" placeholder="9xxxxxxxx" pattern="[0-9]{9}" required title="Deve conter 9 dígitos">
                                </div>
                                <div class="form-group fg-span-2" style="display: flex; flex-direction: row; align-items: center; gap: 10px; cursor: pointer;">
                                    <input type="checkbox" id="agree_rules" name="agree_rules" required style="width: 20px; height: 20px; accent-color: var(--accent-color);">
                                    <label for="agree_rules" style="margin: 0; font-size: 0.9rem; color: var(--text-secondary);">
                                        Li e aceito o <a href="#regulamento" style="color: var(--accent-color); text-decoration: underline;">regulamento do torneio</a>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-login" style="width: 100%; margin-top: 2rem;">
                                <span>Confirmar Inscrição (2€)</span>
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <?php if($total_registrations >= 32): ?>
                    <div class="status-card" style="max-width: 600px; margin: 0 auto; background: rgba(239, 68, 68, 0.05); border: 1px solid #ef4444; padding: 3rem 2rem; border-radius: 20px; text-align: center; backdrop-filter: blur(10px);">
                        <div style="position: relative; display: inline-block; margin-bottom: 1.5rem;">
                            <i class="fas fa-lock" style="font-size: 4rem; color: #ef4444; text-shadow: 0 0 20px rgba(239, 68, 68, 0.4);"></i>
                        </div>
                        <h3 style="color: #fff; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 2px;">Inscrições Encerradas</h3>
                        <p style="color: var(--text-secondary); font-size: 1.1rem;">
                            O limite de 32 jogadores foi atingido.
                        </p>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; background: rgba(255,255,255,0.02); border: 1px dashed var(--glass-border); border-radius: 20px;">
                        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Precisas de ter conta para te inscreveres no torneio.</p>
                        <a href="login" class="btn btn-outline">Fazer Login / Criar Conta</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </section>

    <!-- Bracket Section -->
    <section id="classificacoes" style="padding-bottom: 12rem;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 3rem;">
                <h2 class="section-title">Bracket da <span class="glow-text">Competição</span></h2>
                <p style="color: var(--text-secondary);">O caminho até à Grande Final</p>
            </div>
            
            <div class="bracket-container">
                <div class="mobile-scroll-hint"><i class="fas fa-arrows-left-right"></i> Desliza para ver o bracket completo</div>
                
                <?php
                $res = $conn->query("SELECT m.*, p1.nome as p1_nome, p2.nome as p2_nome 
                                    FROM matches m 
                                    LEFT JOIN inscricoes p1 ON m.player1_id = p1.id 
                                    LEFT JOIN inscricoes p2 ON m.player2_id = p2.id 
                                    ORDER BY m.round ASC, m.id ASC");
                $matches_data = [];
                while($row = $res->fetch_assoc()) {
                    $matches_data[$row['round']][] = $row;
                }

                // Symmetrical split logic with fallback for empty data
                $left = []; $right = [];
                
                // Define the structure of a complete tournament (32 players = 16 matches in R1)
                $rounds_config = [1 => 16, 2 => 8, 3 => 4, 4 => 2];
                $complete_matches = [];

                foreach($rounds_config as $r => $count) {
                    for($i=0; $i<$count; $i++) {
                        $match = isset($matches_data[$r][$i]) ? $matches_data[$r][$i] : null;
                        $complete_matches[$r][] = $match;
                    }
                }

                $final = $matches_data[5][0] ?? null;
                $third = $matches_data[6][0] ?? null;

                foreach([1,2,3,4] as $r) {
                    $half = count($complete_matches[$r]) / 2;
                    $left[$r] = array_slice($complete_matches[$r], 0, $half);
                    // Right side should be reversed for symmetrical tree look
                    $right[$r] = array_reverse(array_slice($complete_matches[$r], $half));
                }

                    function renderMatch($m, $num) {
                        $w1 = ($m && $m['winner_id'] == $m['player1_id'] && $m['winner_id']) ? 'is-winner' : '';
                        $w2 = ($m && $m['winner_id'] == $m['player2_id'] && $m['winner_id']) ? 'is-winner' : '';
                        
                        $p1_nome = $m['p1_nome'] ?? 'A definir';
                        $p2_nome = $m['p2_nome'] ?? 'A definir';
                        
                        $p1_display = htmlspecialchars($p1_nome);
                        $p2_display = htmlspecialchars($p2_nome);
                        
                        // Shorten names for the bracket display
                        if(strlen($p1_display) > 11) $p1_display = substr($p1_display, 0, 10) . '.';
                        if(strlen($p2_display) > 11) $p2_display = substr($p2_display, 0, 10) . '.';

                        $html = '<div class="match-box ' . ($m ? '' : 'empty') . '">';
                        $html .= '<div class="match-number">#' . $num . '</div>';
                        $html .= '<div class="player-row '.$w1.'"><span title="'.htmlspecialchars($p1_nome).'">'.$p1_display.'</span><span class="player-score">'.(($m && $m['winner_id']) ? $m['score1'] : '-').'</span></div>';
                        $html .= '<div class="player-row '.$w2.'"><span title="'.htmlspecialchars($p2_nome).'">'.$p2_display.'</span><span class="player-score">'.(($m && $m['winner_id']) ? $m['score2'] : '-').'</span></div>';
                        $html .= '</div>';
                        return $html;
                    }

                    function getMatchID($round, $side, $idx) {
                        $starts = [1 => 0, 2 => 16, 3 => 24, 4 => 28];
                        $counts = [1 => 16, 2 => 8, 3 => 4, 4 => 2];
                        if ($side === 'left') {
                            return $starts[$round] + $idx + 1;
                        } else {
                            $half = $counts[$round] / 2;
                            return $starts[$round] + ($counts[$round] - 1 - $idx) + 1;
                        }
                    }
                ?>
                <div class="bracket-wrapper">
                    <div class="symmetrical-bracket">
                        <!-- LEFT SIDE -->
                        <div class="bracket-half left">
                            <?php for($r=1; $r<=4; $r++): ?>
                                <div class="bracket-column round-<?php echo $r; ?>">
                                    <?php if(isset($left[$r])) foreach($left[$r] as $idx => $m) echo renderMatch($m, getMatchID($r, 'left', $idx)); ?>
                                </div>
                            <?php endfor; ?>
                        </div>

                        <!-- CENTER (FINAL) -->
                        <div class="bracket-center">
                            <div class="trophy-display">
                                <i class="fas fa-trophy"></i>
                            </div>
                            <div class="final-box">
                                <span class="round-label">GRANDE FINAL</span>
                                <?php echo renderMatch($final, 32); ?>
                            </div>
                            <div class="third-place-box">
                                <span class="round-label">3º LUGAR</span>
                                <?php echo renderMatch($third, 31); ?>
                            </div>
                        </div>

                        <!-- RIGHT SIDE -->
                        <div class="bracket-half right">
                            <?php for($r=1; $r<=4; $r++): ?>
                                <div class="bracket-column round-<?php echo $r; ?>">
                                    <?php if(isset($right[$r])) foreach($right[$r] as $idx => $m) echo renderMatch($m, getMatchID($r, 'right', $idx)); ?>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                <?php ?>
            </div>
        </div>
    </section>

    <!-- Calendário Section -->
    <section id="calendario" style="background: #0e0e11;">
        <div class="container">
            <h2 class="section-title">Agenda oficial <span class="glow-text">ITM</span></h2>
            <p style="text-align: center; color: var(--accent-color); margin-bottom: 3rem; font-weight: 700;">
                <i class="fas fa-info-circle"></i> Nota: Irão decorrer 2 jogos em simultâneo por horário.
            </p>
            
            <div class="calendar-grid-wrapper">
                <!-- Quinta-Feira -->
                <div class="calendar-day-box">
                    <h3><i class="fas fa-calendar-day"></i> Quinta-Feira</h3>
                    <div class="day-pills">
                        <div class="period-label">TARDE</div>
                        <div class="time-item"><span>15:00 – 15:20</span> Jogo #1 e #16</div>
                        <div class="time-item"><span>15:20 – 15:40</span> Jogo #2 e #15</div>
                        <div class="time-item"><span>15:40 – 16:00</span> Jogo #3 e #14</div>
                        <div class="time-item"><span>16:00 – 16:20</span> Jogo #4 e #13</div>
                        <div class="time-item"><span>16:20 – 16:40</span> Jogo #5 e #12</div>
                        <div class="time-item"><span>16:40 – 17:00</span> Fim do Dia</div>
                    </div>
                </div>

                <!-- Sexta-Feira -->
                <div class="calendar-day-box">
                    <h3><i class="fas fa-calendar-day"></i> Sexta-Feira</h3>
                    <div class="day-pills">
                        <div class="period-label">MANHÃ</div>
                        <div class="time-item"><span>10:00 – 10:20</span> Jogo #6 e #11 (R1)</div>
                        <div class="time-item"><span>10:20 – 10:40</span> Jogo #7 e #10 (R1)</div>
                        <div class="time-item"><span>10:40 – 11:00</span> Jogo #8 e #9 (R1)</div>
                        <div class="time-item"><span>11:00 – 11:20</span> Jogo #17 e #24 (R2)</div>
                        <div class="time-item"><span>11:20 – 11:40</span> Jogo #18 e #23 (R2)</div>
                        <div class="time-item"><span>11:40 – 12:00</span> Jogo #19 e #22 (R2)</div>
                        
                        <div class="period-label">TARDE</div>
                        <div class="time-item"><span>15:00 – 15:20</span> Jogo #20 e #21 (R2)</div>
                        <div class="time-item"><span>15:20 – 15:40</span> Jogo #25 e #28 (Qt.)</div>
                        <div class="time-item"><span>15:40 – 16:00</span> Jogo #26 e #27 (Qt.)</div>
                        <div class="time-item"><span>16:00 – 16:20</span> Jogo #29 e #30 (Meias)</div>
                        <div class="time-item"><span>16:20 – 16:40</span> Jogo #31 (3º Lugar)</div>
                        <div class="time-item highlight-final"><span>16:40 – 17:00</span> Jogo #32 (FINAL)</div>
                        <div class="time-item" style="opacity: 0.6; border: none; background: none; justify-content: center;">Fim do Torneio</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq">
        <div class="container">
            <h2 class="section-title">Perguntas <span class="glow-text">Frequentes</span></h2>
            <div class="faq-grid" style="max-width: 800px; margin: 0 auto;">
                <div class="faq-item">
                    <div class="faq-question">Preciso de levar o meu comando? <i class="fas fa-plus"></i></div>
                    <div class="faq-answer">Recomendamos que tragas o teu próprio comando PS4/PS5 para maior conforto, embora tenhamos alguns disponíveis.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">Qual é o custo da inscrição? <i class="fas fa-plus"></i></div>
                    <div class="faq-answer">A inscrição tem um custo simbólico de 2€, que reverte para os prémios do torneio.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">Posso escolher qualquer equipa? <i class="fas fa-plus"></i></div>
                    <div class="faq-answer">Sim, podes escolher qualquer clube (menos seleções) disponível no jogo (modo Kick-off), sendo que todas as equipas vão ter um rating de 95.</div>
                </div>
                <div class="faq-item">
                    <div class="faq-question">Onde se realiza o torneio? <i class="fas fa-plus"></i></div>
                    <div class="faq-answer">O torneio decorre no pavilhão B do Colégio Gaia, durante a XXIX EXPOCOLGAIA.</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Partners Section -->
    <section id="parceiros" style="background: rgba(255,255,255,0.02); border-top: 1px solid var(--glass-border);">
        <div class="container">
            <h2 class="section-title" style="font-size: 1.5rem; margin-bottom: 2rem;">Main <span class="glow-text">Sponsors</span></h2>
            <div class="partners-list">
                <div class="partner-card">
                    <i class="fas fa-school"></i>
                    <span>Colégio Gaia</span>
                </div>
                <div class="partner-card">
                    <i class="fas fa-code"></i>
                    <span>Curso ITM</span>
                </div>
                <div class="partner-card">
                    <i class="fas fa-microchip"></i>
                    <span>ITM - Informática e Tecnologias Multimédia</span>
                </div>
                <div class="partner-card">
                    <i class="fas fa-graduation-cap"></i>
                    <span>12º Ano - 2026</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer style="background: #000; padding: 4rem 0; border-top: 1px solid var(--glass-border); text-align: center;">
        <div class="container">
            <div class="logo" style="font-size: 1.8rem; margin-bottom: 1.5rem;">EXPO <span>FC26</span></div>
            <p style="color: var(--text-secondary); max-width: 600px; margin: 0 auto 1.5rem; font-size: 0.9rem;">
                Organizado pelos alunos do 12º ano de ITM do Colégio Gaia para a XXIX EXPOCOLGAIA.
            </p>
            <div class="social-links" style="justify-content: center; margin-bottom: 2rem;">
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="https://www.twitch.tv/torneioitm" target="_blank"><i class="fab fa-twitch"></i></a>
            </div>
            <p style="color: #444; font-size: 0.8rem;">&copy; 2026 12ºITM. Todos os direitos reservados.</p>
        </div>
    </footer>

    <div id="toast-container" style="position: fixed; bottom: 2rem; right: 2rem; z-index: 9999; display: flex; flex-direction: column; gap: 0.5rem;"></div>
    <script src="js/script.js"></script>
    <script>
        <?php if(isset($_SESSION['toast'])): ?>
            document.addEventListener('DOMContentLoaded', () => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: "<?php echo $_SESSION['toast']['type']; ?>",
                    title: "<?php echo $_SESSION['toast']['msg']; ?>"
                });
            });
            <?php unset($_SESSION['toast']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['swal'])): ?>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    title: "<?php echo $_SESSION['swal']['title']; ?>",
                    text: "<?php echo $_SESSION['swal']['msg']; ?>",
                    icon: "<?php echo $_SESSION['swal']['type']; ?>",
                    background: '#18181b',
                    color: '#fff',
                    confirmButtonColor: 'var(--accent-color)'
                });
            });
            <?php unset($_SESSION['swal']); ?>
        <?php endif; ?>
    </script>
</body>
</html>
