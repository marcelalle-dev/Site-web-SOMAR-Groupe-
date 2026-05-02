<?php
// ============================================
// CONFIGURATION ADMIN
// ============================================
$admin_password = 'AdminSomar2024'; // CHANGEZ CE MOT DE PASSE !
// Vérifier l'accès
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    if (isset($_POST['password'])) {
        if ($_POST['password'] === $admin_password) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_login_time'] = time();
        } else {
            $error = "Mot de passe incorrect";
        }
    }
   
    // Afficher le formulaire de connexion
    if (!isset($_SESSION['admin_logged_in'])) {
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Admin - Connexion</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body {
                    font-family: 'Segoe UI', sans-serif;
                    background: linear-gradient(135deg, #2c3e50, #3498db);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    padding: 20px;
                }
                .login-container {
                    width: 100%;
                    max-width: 400px;
                    background: white;
                    border-radius: 15px;
                    overflow: hidden;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
                    animation: fadeIn 0.5s ease;
                }
                .login-header {
                    background: linear-gradient(to right, #2c3e50, #3498db);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .login-header h1 {
                    font-size: 24px;
                    margin-bottom: 10px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 15px;
                }
                .login-body {
                    padding: 30px;
                }
                .error-message {
                    background: #ffeaea;
                    color: #e74c3c;
                    padding: 12px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                    text-align: center;
                    border-left: 4px solid #e74c3c;
                }
                .form-group {
                    margin-bottom: 20px;
                }
                .form-group label {
                    display: block;
                    margin-bottom: 8px;
                    color: #2c3e50;
                    font-weight: 600;
                    font-size: 14px;
                }
                .form-control {
                    width: 100%;
                    padding: 12px 15px;
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 16px;
                    transition: all 0.3s;
                }
                .form-control:focus {
                    outline: none;
                    border-color: #3498db;
                    box-shadow: 0 0 0 3px rgba(52,152,219,0.2);
                }
                .login-btn {
                    width: 100%;
                    padding: 14px;
                    background: linear-gradient(to right, #2c3e50, #3498db);
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                }
                .login-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(52,152,219,0.3);
                }
                .security-note {
                    margin-top: 20px;
                    padding: 12px;
                    background: #f8f9fa;
                    border-radius: 6px;
                    font-size: 12px;
                    color: #7f8c8d;
                    text-align: center;
                    border: 1px solid #e9ecef;
                }
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            </style>
        </head>
        <body>
            <div class="login-container">
                <div class="login-header">
                    <h1><i class="fas fa-lock"></i> Administration SOMAR</h1>
                    <p>Accès sécurisé au tableau de bord</p>
                </div>
                <div class="login-body">
                    <?php if (isset($error)): ?>
                        <div class="error-message">
                            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                   
                    <form method="POST">
                        <div class="form-group">
                            <label for="password">Mot de passe administrateur</label>
                            <input type="password" id="password" name="password"
                                   class="form-control" placeholder="Entrez le mot de passe" required>
                        </div>
                       
                        <button type="submit" class="login-btn">
                            <i class="fas fa-sign-in-alt"></i> Se connecter
                        </button>
                       
                        <div class="security-note">
                            <i class="fas fa-shield-alt"></i>
                            Accès réservé au personnel autorisé
                        </div>
                    </form>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit();
    }
}
// Vérifier la durée de session (8 heures maximum)
$session_duration = 8 * 60 * 60; // 8 heures
if (time() - $_SESSION['admin_login_time'] > $session_duration) {
    session_destroy();
    header("Location: admin.php");
    exit();
}
// ============================================
// FONCTIONS DE GESTION DES DONNÉES
// ============================================
function getVisitorStats() {
    $stats = [
        'total' => 0,
        'today' => 0,
        'this_week' => 0,
        'this_month' => 0,
        'by_objectif' => [],
        'by_niveau' => [],
        'recent_visitors' => []
    ];
   
    // Lire le fichier CSV
    $csvFile = 'data/visiteurs.csv';
    if (!file_exists($csvFile)) {
        return $stats;
    }
   
    $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $stats['total'] = count($lines) - 1; // Moins l'en-tête
   
    // Analyser les données
    $today = date('Y-m-d');
    $week_start = date('Y-m-d', strtotime('monday this week'));
    $month_start = date('Y-m-01');
   
    foreach ($lines as $index => $line) {
        if ($index === 0) continue; // Sauter l'en-tête
       
        $data = str_getcsv($line, ';');
        if (count($data) < 8) continue;
       
        $date = $data[0];
        $objectif = $data[6] ?? '';
        $niveau = $data[7] ?? '';
       
        // Aujourd'hui
        if ($date === $today) {
            $stats['today']++;
        }
       
        // Cette semaine
        if ($date >= $week_start) {
            $stats['this_week']++;
        }
       
        // Ce mois
        if (substr($date, 0, 7) === substr($today, 0, 7)) {
            $stats['this_month']++;
        }
       
        // Par objectif
        if ($objectif) {
            if (!isset($stats['by_objectif'][$objectif])) {
                $stats['by_objectif'][$objectif] = 0;
            }
            $stats['by_objectif'][$objectif]++;
        }
       
        // Par niveau
        if ($niveau) {
            if (!isset($stats['by_niveau'][$niveau])) {
                $stats['by_niveau'][$niveau] = 0;
            }
            $stats['by_niveau'][$niveau]++;
        }
       
        // 10 derniers visiteurs
        if (count($stats['recent_visitors']) < 10) {
            $stats['recent_visitors'][] = [
                'date' => $data[0] . ' ' . ($data[1] ?? ''),
                'nom' => $data[3] ?? '',
                'email' => $data[4] ?? '',
                'telephone' => $data[5] ?? '',
                'objectif' => $objectif
            ];
        }
    }
   
    return $stats;
}
function getRecentVisitors($limit = 50) {
    $visitors = [];
    $csvFile = 'data/visiteurs.csv';
   
    if (!file_exists($csvFile)) {
        return $visitors;
    }
   
    $lines = file($csvFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lines = array_reverse($lines); // Les plus récents d'abord
   
    $count = 0;
    foreach ($lines as $index => $line) {
        if ($index === 0) continue; // Sauter l'en-tête
        if ($count >= $limit) break;
       
        $data = str_getcsv($line, ';');
        if (count($data) >= 8) {
            $visitors[] = [
                'id' => $index,
                'date' => $data[0] ?? '',
                'heure' => $data[1] ?? '',
                'ip' => $data[2] ?? '',
                'nom' => $data[3] ?? '',
                'email' => $data[4] ?? '',
                'telephone' => $data[5] ?? '',
                'objectif' => $data[6] ?? '',
                'niveau' => $data[7] ?? '',
                'message' => $data[8] ?? ''
            ];
            $count++;
        }
    }
   
    return $visitors;
}
// Récupérer les statistiques
$stats = getVisitorStats();
$recentVisitors = getRecentVisitors(100);
// Gérer l'export CSV
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="visiteurs_somar_' . date('Y-m-d') . '.csv"');
   
    $csvFile = 'data/visiteurs.csv';
    if (file_exists($csvFile)) {
        readfile($csvFile);
    }
    exit();
}
// Gérer la déconnexion
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de bord - SOMAR Groupe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
       
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
       
        .admin-header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
       
        .admin-header h1 {
            font-size: 24px;
            display: flex;
            align-items: center;
            gap: 15px;
        }
       
        .admin-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }
       
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
       
        .btn-primary {
            background: #27ae60;
            color: white;
        }
       
        .btn-primary:hover {
            background: #219653;
            transform: translateY(-2px);
        }
       
        .btn-secondary {
            background: #e74c3c;
            color: white;
        }
       
        .btn-secondary:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }
       
        .btn-info {
            background: #3498db;
            color: white;
        }
       
        .btn-info:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }
       
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            padding: 30px;
        }
       
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
            transition: transform 0.3s;
            position: relative;
            overflow: hidden;
        }
       
        .stat-card:hover {
            transform: translateY(-5px);
        }
       
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(to right, #3498db, #2ecc71);
        }
       
        .stat-card h3 {
            font-size: 2.5rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }
       
        .stat-card p {
            color: #7f8c8d;
            font-size: 14px;
        }
       
        .stat-icon {
            font-size: 40px;
            color: #3498db;
            margin-top: 15px;
        }
       
        .charts-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 25px;
            padding: 0 30px 30px;
        }
       
        .chart-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }
       
        .chart-card h3 {
            margin-bottom: 20px;
            color: #2c3e50;
            display: flex;
            align-items: center;
            gap: 10px;
        }
       
        .table-container {
            padding: 0 30px 30px;
        }
       
        .data-table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }
       
        table {
            width: 100%;
            border-collapse: collapse;
        }
       
        thead {
            background: #2c3e50;
            color: white;
        }
       
        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
       
        td {
            padding: 12px 15px;
            border-bottom: 1px solid #eee;
        }
       
        tr:hover {
            background: #f9f9f9;
        }
       
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }
       
        .badge-cours { background: #e8f4fc; color: #3498db; }
        .badge-epreuves { background: #e8f8f0; color: #27ae60; }
        .badge-info { background: #fef9e7; color: #f39c12; }
        .badge-orientation { background: #f4ecf7; color: #8e44ad; }
        .badge-tutorat { background: #fef5e7; color: #e67e22; }
        .badge-autre { background: #f2f3f4; color: #7f8c8d; }
       
        .search-box {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
       
        .search-input {
            flex: 1;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 14px;
        }
       
        .search-btn {
            padding: 12px 20px;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
       
        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            padding: 20px;
        }
       
        .page-btn {
            padding: 8px 12px;
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            cursor: pointer;
        }
       
        .page-btn.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }
       
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
           
            .charts-container {
                grid-template-columns: 1fr;
            }
           
            .admin-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }
           
            .admin-actions {
                flex-wrap: wrap;
                justify-content: center;
            }
           
            table {
                display: block;
                overflow-x: auto;
            }
        }
       
        .session-info {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 5px;
        }
       
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
        }
       
        .empty-state i {
            font-size: 50px;
            margin-bottom: 20px;
            color: #bdc3c7;
        }
    </style>
</head>
<body>
    <div class="admin-header">
        <div>
            <h1><i class="fas fa-chart-line"></i> Tableau de bord SOMAR Groupe</h1>
            <div class="session-info">
                Connecté depuis <?php echo date('H:i', $_SESSION['admin_login_time']); ?>
                | Session expire à <?php echo date('H:i', $_SESSION['admin_login_time'] + $session_duration); ?>
            </div>
        </div>
        <div class="admin-actions">
            <a href="?export=csv" class="btn btn-primary">
                <i class="fas fa-download"></i> Exporter CSV
            </a>
            <a href="admin.php?refresh=1" class="btn btn-info">
                <i class="fas fa-sync-alt"></i> Actualiser
            </a>
            <a href="?logout=1" class="btn btn-secondary">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>
   
    <div class="stats-grid">
    <div class="stat-card">
        <h3><?php echo number_format($stats['total'], 0, ',', ' '); ?></h3>
        <p>Visiteurs totaux</p>
        <div class="stat-icon">
            <i class="fas fa-users"></i>
        </div>
    </div>
   
    <div class="stat-card">
        <h3><?php echo number_format($stats['today'], 0, ',', ' '); ?></h3>
        <p>Aujourd'hui</p>
        <div class="stat-icon">
            <i class="fas fa-calendar-day"></i>
        </div>
    </div>
   
    <div class="stat-card">
        <h3><?php echo number_format($stats['this_week'], 0, ',', ' '); ?></h3>
        <p>Cette semaine</p>
        <div class="stat-icon">
            <i class="fas fa-calendar-week"></i>
        </div>
    </div>
   
    <div class="stat-card">
        <h3><?php echo number_format($stats['this_month'], 0, ',', ' '); ?></h3>
        <p>Ce mois</p>
        <div class="stat-icon">
            <i class="fas fa-calendar-alt"></i>
        </div>
    </div>
</div>