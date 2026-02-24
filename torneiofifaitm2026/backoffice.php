<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: index.php");
    exit;
}

$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Score update
    if (isset($_POST['update_score'])) {
        $mid = $_POST['match_id'];
        $s1 = max(0, (int)$_POST['score1']);
        $s2 = max(0, (int)$_POST['score2']);
        
        if ($s1 == $s2) {
            $_SESSION['toast'] = ['msg' => "Não são permitidos empates em eliminatórias!", 'type' => 'error'];
            header("Location: backoffice.php?tab=brackets");
            exit;
        }

        $winner = ($s1 > $s2) ? $_POST['p1_id'] : $_POST['p2_id'];
        
        $stmt = $conn->prepare("UPDATE matches SET score1=?, score2=?, winner_id=? WHERE id=?");
        $stmt->bind_param("iiii", $s1, $s2, $winner, $mid);
        $stmt->execute();
        $_SESSION['toast'] = ['msg' => "Jogo #$mid atualizado!", 'type' => 'success'];
        header("Location: backoffice.php?tab=brackets");
        exit;
    }

    // Toggle Paid
    if (isset($_POST['toggle_paid'])) {
        $id = $_POST['reg_id'];
        $status = $_POST['pago'];
        $stmt = $conn->prepare("UPDATE inscricoes SET pago = ? WHERE id = ?");
        $stmt->bind_param("ii", $status, $id);
        $stmt->execute();
        $_SESSION['toast'] = ['msg' => "Pagamento atualizado!", 'type' => 'success'];
        header("Location: backoffice.php?tab=inscricoes");
        exit;
    }

    // Delete Registration
    if (isset($_POST['delete_reg'])) {
        $id = $_POST['reg_id'];
        $stmt = $conn->prepare("DELETE FROM inscricoes WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $_SESSION['toast'] = ['msg' => "Registo apagado!", 'type' => 'error'];
        header("Location: backoffice.php?tab=inscricoes");
        exit;
    }

    // Reset Tournament
    if (isset($_POST['generate_bracket'])) {
        $conn->query("TRUNCATE TABLE matches");
        $res = $conn->query("SELECT id FROM inscricoes");
        $players = [];
        while($r = $res->fetch_assoc()) $players[] = $r['id'];
        shuffle($players);
        for($i=0; $i<count($players); $i+=2) {
            $p1 = $players[$i];
            $p2 = $players[$i+1] ?? null;
            $stmt = $conn->prepare("INSERT INTO matches (round, player1_id, player2_id) VALUES (1, ?, ?)");
            $stmt->bind_param("ii", $p1, $p2);
            $stmt->execute();
        }
        $_SESSION['toast'] = ['msg' => "Bracket reiniciado!", 'type' => 'success'];
        header("Location: backoffice.php?tab=brackets");
        exit;
    }

    // Advance Round
    if (isset($_POST['advance_round'])) {
        // Look for current highest round in main tree (1-4)
        $current_round_res = $conn->query("SELECT MAX(round) as r FROM matches WHERE round < 5");
        $current_round = $current_round_res->fetch_assoc()['r'] ?? 0;
        
        if ($current_round == 0) {
            $_SESSION['toast'] = ['msg' => "Nenhum jogo encontrado!", 'type' => 'error'];
            header("Location: backoffice.php?tab=brackets");
            exit;
        }

        $res = $conn->query("SELECT id, player1_id, player2_id, winner_id FROM matches WHERE round = $current_round ORDER BY id ASC");
        $winners = [];
        $losers = [];
        while($r = $res->fetch_assoc()) {
            if($r['winner_id']) {
                $winners[] = $r['winner_id'];
                $losers[] = ($r['winner_id'] == $r['player1_id']) ? $r['player2_id'] : $r['player1_id'];
            }
        }

        if (count($winners) < 2) {
            $_SESSION['toast'] = ['msg' => "Vencedores insuficientes para avançar!", 'type' => 'error'];
        } else {
            $next_round = $current_round + 1;
            // standard advance (Winners to next round)
            for($i=0; $i<count($winners); $i+=2) {
                if (!isset($winners[$i+1]) && $next_round < 5) {
                    // Bye if odd number (shouldn't happen with 32)
                    $p1 = $winners[$i];
                    $p2 = null;
                } else {
                    $p1 = $winners[$i];
                    $p2 = $winners[$i+1] ?? null;
                }
                $stmt = $conn->prepare("INSERT INTO matches (round, player1_id, player2_id) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $next_round, $p1, $p2);
                $stmt->execute();
            }

            // If we just finished Round 4 (Semifinals), generate Round 6 (3rd Place) from losers
            if ($current_round == 4) {
                $p1_3rd = $losers[0] ?? null;
                $p2_3rd = $losers[1] ?? null;
                $stmt = $conn->prepare("INSERT INTO matches (round, player1_id, player2_id) VALUES (6, ?, ?)");
                $stmt->bind_param("ii", $p1_3rd, $p2_3rd);
                $stmt->execute();
            }

            $round_label = ($next_round == 5) ? "Final" : "Ronda $next_round";
            $_SESSION['toast'] = ['msg' => "$round_label gerada com sucesso!", 'type' => 'success'];
        }
        header("Location: backoffice.php?tab=brackets");
        exit;
    }
}

$active_tab = $_GET['tab'] ?? 'inscricoes';
$sort = $_GET['sort'] ?? 'id';
$dir = $_GET['dir'] ?? 'DESC';
$allowed_cols = ['id', 'nome', 'email', 'turma', 'pago'];
if (!in_array($sort, $allowed_cols)) $sort = 'id';
if (!in_array($dir, ['ASC', 'DESC'])) $dir = 'DESC';

function sortLink($col, $label, $active_tab) {
    global $sort, $dir;
    $new_dir = ($sort == $col && $dir == 'ASC') ? 'DESC' : 'ASC';
    $icon = "";
    if ($sort == $col) {
        $icon = $dir == 'ASC' ? ' <i class="fas fa-sort-up"></i>' : ' <i class="fas fa-sort-down"></i>';
    }
    return "<a href='?tab=$active_tab&sort=$col&dir=$new_dir' style='color:inherit; text-decoration:none;'>$label $icon</a>";
}
?>
<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Expo FC26</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .admin-header {
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--glass-border);
            padding: 15px 0;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;
        }

        .admin-section {
            background: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--glass-border);
            padding: 2.5rem;
            margin-bottom: 3rem;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .tab-nav {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 120px 0 40px;
        }

        .tab-btn {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            padding: 14px 28px;
            border-radius: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.85rem;
        }

        .tab-btn.active {
            background: var(--accent-color);
            color: #000;
            border-color: var(--accent-color);
            box-shadow: 0 0 20px var(--accent-glow);
        }

        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        .admin-table th {
            padding: 15px 20px;
            text-align: left;
            color: var(--accent-color);
            font-family: 'Rajdhani', sans-serif;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.8rem;
            background: transparent;
        }

        .admin-table td {
            padding: 20px;
            background: rgba(255,255,255,0.02);
            border-top: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
        }

        .admin-table tr td:first-child { border-left: 1px solid var(--glass-border); border-radius: 12px 0 0 12px; }
        .admin-table tr td:last-child { border-right: 1px solid var(--glass-border); border-radius: 0 12px 12px 0; }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid var(--glass-border);
            padding: 1.5rem;
            border-radius: 18px;
            text-align: center;
        }

        .stat-value { font-size: 2.2rem; font-weight: 800; color: var(--accent-color); font-family: 'Rajdhani', sans-serif; }
        .stat-label { font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 2px; }

        .message-box {
            background: rgba(74, 222, 128, 0.1);
            border: 1px solid var(--accent-color);
            color: #fff;
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 2rem;
            text-align: center;
            animation: fadeIn 0.5s ease-out;
        }

        .match-row {
            background: rgba(255,255,255,0.02);
            border: 1px solid var(--glass-border);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            display: grid;
            grid-template-columns: 1fr auto 1fr auto;
            align-items: center;
            gap: 2rem;
            transition: 0.3s;
        }

        .match-row:hover { background: rgba(255,255,255,0.04); border-color: var(--accent-color); }

        .score-input {
            width: 50px;
            height: 40px;
            background: #000;
            border: 1px solid #333;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            font-weight: 800;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>

    <header class="admin-header">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center;">
            <div class="logo">EXPO <span>FC26</span></div>
            <div style="display: flex; gap: 1rem;">
                <a href="index.php" class="btn btn-outline" style="padding: 8px 20px; font-size: 0.8rem; border-radius: 50px;"><i class="fas fa-eye"></i> SITE</a>
                <a href="logout.php" class="btn" style="background:#ef4444; color:#fff; padding: 8px 20px; font-size: 0.8rem; border-radius: 50px;"><i class="fas fa-power-off"></i> SAIR</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="tab-nav">
            <button class="tab-btn <?php echo $active_tab == 'inscricoes' ? 'active' : ''; ?>" onclick="location.href='?tab=inscricoes'">
                <i class="fas fa-user-friends"></i> Inscrições
            </button>
            <button class="tab-btn <?php echo $active_tab == 'brackets' ? 'active' : ''; ?>" onclick="location.href='?tab=brackets'">
                <i class="fas fa-sitemap"></i> Brackets & Jogos
            </button>
        </div>

        <?php if($message): ?>
            <div class="message-box"><?php echo $message; ?></div>
        <?php endif; ?>

        <?php if($active_tab == 'inscricoes'): ?>
            <div class="admin-section">
                <div class="stat-grid">
                    <?php
                    $tot_paid = $conn->query("SELECT COUNT(*) as t FROM inscricoes WHERE pago = 1")->fetch_assoc()['t'];
                    $tot_all = $conn->query("SELECT COUNT(*) as t FROM inscricoes")->fetch_assoc()['t'];
                    ?>
                    <div class="stat-card"><div class="stat-label">Inscritos</div><div class="stat-value"><?php echo $tot_all; ?></div></div>
                    <div class="stat-card"><div class="stat-label">Pagos</div><div class="stat-value"><?php echo $tot_paid; ?> / 32</div></div>
                    <div class="stat-card"><div class="stat-label">Vagas Livres</div><div class="stat-value"><?php echo max(0, 32 - $tot_paid); ?></div></div>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th><?php echo sortLink('id', 'ID', 'inscricoes'); ?></th>
                            <th><?php echo sortLink('nome', 'Nome', 'inscricoes'); ?></th>
                            <th><?php echo sortLink('email', 'E-mail', 'inscricoes'); ?></th>
                            <th><?php echo sortLink('turma', 'Turma', 'inscricoes'); ?></th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = $conn->query("SELECT * FROM inscricoes ORDER BY $sort $dir");
                        while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td style="color: var(--accent-color); font-weight: 700;">#<?php echo $row['id']; ?></td>
                                <td style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($row['nome']); ?></td>
                                <td style="color: var(--text-secondary);"><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><span style="background: rgba(255,255,255,0.05); padding: 5px 12px; border-radius: 6px; font-size: 0.8rem;"><?php echo htmlspecialchars($row['turma']); ?></span></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="reg_id" value="<?php echo $row['id']; ?>">
                                        <input type="hidden" name="pago" value="<?php echo $row['pago'] ? 0 : 1; ?>">
                                        <button type="submit" name="toggle_paid" style="background:none; border:none; cursor:pointer; font-size: 1.2rem;">
                                            <?php echo $row['pago'] ? '✅' : '❌'; ?>
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <button type="button" onclick="confirmDelete(<?php echo $row['id']; ?>)" style="background:none; border:none; cursor:pointer; color: #ef4444; font-size: 1rem;">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="admin-section">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; gap: 1rem;">
                    <h2 style="font-size: 1.8rem;">Gestão do Torneio</h2>
                    <div style="display: flex; gap: 1rem;">
                        <form method="POST" id="advance-round-form">
                            <button type="button" onclick="confirmAdvance()" name="advance_round_btn" class="btn" style="background: #2563eb; color: #fff; border-radius: 12px; clip-path: none; font-size: 0.8rem; padding: 12px 25px;">
                                <i class="fas fa-arrow-right"></i> AVANÇAR RONDA
                            </button>
                            <input type="hidden" name="advance_round" value="1">
                        </form>
                        <form method="POST" id="generate-bracket-form">
                            <button type="button" onclick="confirmReset()" name="generate_bracket_btn" class="btn btn-outline" style="background: var(--accent-color); color: #000; border-radius: 12px; clip-path: none; font-size: 0.8rem; padding: 12px 25px;">
                                <i class="fas fa-random"></i> REINICIAR
                            </button>
                            <input type="hidden" name="generate_bracket" value="1">
                        </form>
                    </div>
                </div>

                <div class="matches-list">
                    <?php
                    $res = $conn->query("SELECT m.*, p1.nome as p1_nome, p2.nome as p2_nome FROM matches m LEFT JOIN inscricoes p1 ON m.player1_id = p1.id LEFT JOIN inscricoes p2 ON m.player2_id = p2.id ORDER BY m.round DESC, m.id ASC");
                    $last_r = -1;
                    while($m = $res->fetch_assoc()): 
                        if ($m['round'] != $last_r):
                            $last_r = $m['round'];
                            $round_name = ($last_r == 5) ? "Final" : (($last_r == 6) ? "3º Lugar" : "Ronda $last_r");
                            echo "<h3 style='margin: 2rem 0 1rem; color: var(--accent-color); font-family: Rajdhani;'>$round_name</h3>";
                        endif;
                    ?>
                        <form method="POST" class="match-row">
                            <input type="hidden" name="match_id" value="<?php echo $m['id']; ?>">
                            <input type="hidden" name="p1_id" value="<?php echo $m['player1_id']; ?>">
                            <input type="hidden" name="p2_id" value="<?php echo $m['player2_id']; ?>">
                            
                            <div style="text-align: right; font-weight: 700; color: #fff;"><?php echo htmlspecialchars($m['p1_nome'] ?? 'BYE'); ?></div>
                            
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <input type="number" name="score1" value="<?php echo $m['score1']; ?>" min="0" class="score-input">
                                <span style="color: #444; font-weight: 800;">VS</span>
                                <input type="number" name="score2" value="<?php echo $m['score2']; ?>" min="0" class="score-input">
                            </div>
                            
                            <div style="font-weight: 700; color: #fff;"><?php echo htmlspecialchars($m['p2_nome'] ?? 'BYE'); ?></div>
                            
                            <button type="submit" name="update_score" class="btn" style="padding: 10px; border-radius: 10px; background: var(--accent-color); color: #000; min-height: 40px; clip-path: none;"><i class="fas fa-save"></i></button>
                        </form>
                    <?php endwhile; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php if(isset($_SESSION['toast'])): ?>
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
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
        </script>
        <?php unset($_SESSION['toast']); ?>
    <?php endif; ?>

    <script>
        function confirmAdvance() {
            Swal.fire({
                title: 'Avançar Ronda?',
                text: 'Certifique-se que todos os resultados da ronda atual foram inseridos.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, avançar!',
                cancelButtonText: 'Cancelar',
                background: '#18181b',
                color: '#fff',
                customClass: {
                    popup: 'swal2-dark-popup'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('advance-round-form').submit();
                }
            });
        }

        function confirmReset() {
             Swal.fire({
                title: 'Reiniciar Torneio?',
                text: 'Tem a certeza? Isto apagará todos os jogos atuais!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffd700',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, reiniciar!',
                cancelButtonText: 'Cancelar',
                background: '#18181b',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('generate-bracket-form').submit();
                }
            });
        }

        function confirmDelete(regId) {
            Swal.fire({
                title: 'Eliminar Inscrição?',
                text: 'Esta ação não pode ser revertida!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#333',
                confirmButtonText: 'Sim, eliminar!',
                cancelButtonText: 'Cancelar',
                background: '#18181b',
                color: '#fff'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="reg_id" value="${regId}">
                        <input type="hidden" name="delete_reg" value="1">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

</body>
</html>
