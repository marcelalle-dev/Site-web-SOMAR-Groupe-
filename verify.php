<?php
session_start();
// ============================================
// CONFIGURATION
// ============================================
$CONFIG = [
    'admin_email' => 'marcelalle3@gmail.com',
    'site_name' => 'SOMAR Groupe',
    'redirect_target' => 'index.html', // Page vers laquelle rediriger
    'enable_emails' => false, // Activer/désactiver les emails
    'max_visitors' => 1000, // Nombre maximum de visiteurs dans JSON
    'cookie_duration' => 30 * 24 * 60 * 60, // 30 jours en secondes
    'data_directory' => 'data',
    'logs_directory' => 'data/logs'
];
// ============================================
// VÉRIFICATION DE LA MÉTHODE
// ============================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php?error=invalid_method");
    exit();
}
// ============================================
// RÉCUPÉRATION ET NETTOYAGE DES DONNÉES
// ============================================
$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$telephone = isset($_POST['telephone']) ? trim($_POST['telephone']) : '';
$objectif = isset($_POST['objectif']) ? trim($_POST['objectif']) : '';
$niveau = isset($_POST['niveau']) ? trim($_POST['niveau']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
// ============================================
// VALIDATION
// ============================================
$errors = [];
// Validation des champs requis
if (empty($nom) || empty($email) || empty($telephone) || empty($objectif)) {
    $errors[] = 'empty';
}
// Validation email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'email';
}
// Validation téléphone (format international Bénin)
if (!empty($telephone) && !preg_match('/^(\+229|00229|229)?[0-9]{8,9}$/', str_replace(' ', '', $telephone))) {
    $errors[] = 'telephone';
}
// Protection contre les soumissions trop rapides (anti-spam)
if (isset($_SESSION['last_submission']) && (time() - $_SESSION['last_submission'] < 5)) {
    $errors[] = 'rate_limit';
}
// S'il y a des erreurs, rediriger
if (!empty($errors)) {
    header("Location: index.php?error=" . $errors[0]);
    exit();
}
// Mettre à jour le timestamp de la dernière soumission
$_SESSION['last_submission'] = time();
// ============================================
// CRÉATION DES DOSSIERS DE DONNÉES
// ============================================
if (!is_dir($CONFIG['data_directory'])) {
    mkdir($CONFIG['data_directory'], 0755, true);
}
if (!is_dir($CONFIG['logs_directory'])) {
    mkdir($CONFIG['logs_directory'], 0755, true);
}
// ============================================
// DONNÉES DE LA VISITE
// ============================================
$timestamp = time();
$date = date('Y-m-d', $timestamp);
$heure = date('H:i:s', $timestamp);
$ip = $_SERVER['REMOTE_ADDR'];
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu';
$referer = $_SERVER['HTTP_REFERER'] ?? 'Direct';
$visitorId = uniqid('visitor_', true);
// ============================================
// SAUVEGARDE DANS FICHIER CSV (Excel)
// ============================================
$csvFile = $CONFIG['data_directory'] . '/visiteurs.csv';
// Créer l'en-tête si le fichier n'existe pas
if (!file_exists($csvFile)) {
    $header = "ID;Date;Heure;IP;Nom;Email;Telephone;Objectif;Niveau;Message;Navigateur;Referer\n";
    file_put_contents($csvFile, $header, FILE_APPEND);
}
// Préparer la ligne CSV avec protection contre les injections
function escapeCSV($value) {
    return '"' . str_replace('"', '""', $value) . '"';
}
$csvLine = implode(';', [
    escapeCSV($visitorId),
    escapeCSV($date),
    escapeCSV($heure),
    escapeCSV($ip),
    escapeCSV($nom),
    escapeCSV($email),
    escapeCSV($telephone),
    escapeCSV($objectif),
    escapeCSV($niveau),
    escapeCSV($message),
    escapeCSV($userAgent),
    escapeCSV($referer)
]) . "\n";
// Écrire dans le fichier CSV
file_put_contents($csvFile, $csvLine, FILE_APPEND | LOCK_EX);
// ============================================
// SAUVEGARDE DANS FICHIER JSON
// ============================================
$jsonFile = $CONFIG['data_directory'] . '/visiteurs.json';
// Préparer les données du visiteur
$visitorData = [
    'id' => $visitorId,
    'timestamp' => $timestamp,
    'date' => $date,
    'heure' => $heure,
    'nom' => $nom,
    'email' => $email,
    'telephone' => $telephone,
    'objectif' => $objectif,
    'niveau' => $niveau,
    'message' => $message,
    'ip' => $ip,
    'user_agent' => $userAgent,
    'referer' => $referer,
    'user_agent_parsed' => [
        'browser' => getBrowserInfo($userAgent),
        'device' => getDeviceInfo($userAgent),
        'os' => getOSInfo($userAgent)
    ]
];
// Lire les données existantes
$allData = [];
if (file_exists($jsonFile)) {
    $jsonContent = file_get_contents($jsonFile);
    $allData = json_decode($jsonContent, true) ?: [];
}
// Limiter le nombre d'entrées
if (count($allData) >= $CONFIG['max_visitors']) {
    array_shift($allData); // Supprimer la plus ancienne
}
// Ajouter le nouveau visiteur
$allData[] = $visitorData;
// Sauvegarder
file_put_contents($jsonFile, json_encode($allData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
// ============================================
// SAUVEGARDE DANS FICHIER LOG DÉTAILLÉ
// ============================================
$logFile = $CONFIG['logs_directory'] . '/visiteurs_' . date('Y-m') . '.log';
$logEntry = "===========================================\n";
$logEntry .= "NOUVEAU VISITEUR - " . date('d/m/Y à H:i:s') . "\n";
$logEntry .= "===========================================\n";
$logEntry .= "ID: " . $visitorId . "\n";
$logEntry .= "Nom: " . $nom . "\n";
$logEntry .= "Email: " . $email . "\n";
$logEntry .= "Téléphone: " . $telephone . "\n";
$logEntry .= "Objectif: " . $objectif . "\n";
$logEntry .= "Niveau: " . $niveau . "\n";
$logEntry .= "Message: " . ($message ?: '(aucun)') . "\n";
$logEntry .= "IP: " . $ip . "\n";
$logEntry .= "Navigateur: " . getBrowserInfo($userAgent) . "\n";
$logEntry .= "OS: " . getOSInfo($userAgent) . "\n";
$logEntry .= "Device: " . getDeviceInfo($userAgent) . "\n";
$logEntry .= "Referer: " . $referer . "\n";
$logEntry .= "===========================================\n\n";
file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
// ============================================
// SAUVEGARDE DANS FICHIER STATISTIQUES
// ============================================
$statsFile = $CONFIG['data_directory'] . '/statistiques.json';
// Lire les statistiques existantes
$stats = [];
if (file_exists($statsFile)) {
    $statsContent = file_get_contents($statsFile);
    $stats = json_decode($statsContent, true) ?: [];
}
// Initialiser les statistiques du jour
if (!isset($stats[$date])) {
    $stats[$date] = [
        'total' => 0,
        'par_objectif' => [],
        'par_niveau' => [],
        'par_heure' => [],
        'par_navigateur' => []
    ];
}
// Mettre à jour les statistiques
$stats[$date]['total']++;
// Statistiques par objectif
if (!isset($stats[$date]['par_objectif'][$objectif])) {
    $stats[$date]['par_objectif'][$objectif] = 0;
}
$stats[$date]['par_objectif'][$objectif]++;
// Statistiques par niveau
if ($niveau) {
    if (!isset($stats[$date]['par_niveau'][$niveau])) {
        $stats[$date]['par_niveau'][$niveau] = 0;
    }
    $stats[$date]['par_niveau'][$niveau]++;
}
// Statistiques par heure
$heure_simplifiee = date('H', $timestamp) . 'h';
if (!isset($stats[$date]['par_heure'][$heure_simplifiee])) {
    $stats[$date]['par_heure'][$heure_simplifiee] = 0;
}
$stats[$date]['par_heure'][$heure_simplifiee]++;
// Statistiques par navigateur
$browser = getBrowserInfo($userAgent);
if (!isset($stats[$date]['par_navigateur'][$browser])) {
    $stats[$date]['par_navigateur'][$browser] = 0;
}
$stats[$date]['par_navigateur'][$browser]++;
// Statistiques globales
if (!isset($stats['global'])) {
    $stats['global'] = [
        'total_visiteurs' => 0,
        'premiere_visite' => $date,
        'derniere_visite' => $date,
        'jours_actifs' => []
    ];
}
$stats['global']['total_visiteurs']++;
$stats['global']['derniere_visite'] = $date;
// Ajouter le jour actif s'il n'existe pas
if (!in_array($date, $stats['global']['jours_actifs'])) {
    $stats['global']['jours_actifs'][] = $date;
    sort($stats['global']['jours_actifs']);
}
// Sauvegarder les statistiques
file_put_contents($statsFile, json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
// ============================================
// ENVOI D'EMAIL DE NOTIFICATION
// ============================================
if ($CONFIG['enable_emails']) {
    envoyerNotification($visitorData, $stats[$date]['total'], $stats['global']['total_visiteurs']);
}
// ============================================
// SESSION ET COOKIES
// ============================================
$_SESSION['visitor_verified'] = true;
$_SESSION['visitor_id'] = $visitorId;
$_SESSION['visitor_name'] = $nom;
$_SESSION['visitor_email'] = $email;
$_SESSION['visitor_objectif'] = $objectif;
$_SESSION['access_time'] = $timestamp;
// Cookies pour persistance
setcookie('somar_visitor', $visitorId, time() + $CONFIG['cookie_duration'], '/');
setcookie('somar_access', 'true', time() + $CONFIG['cookie_duration'], '/');
setcookie('somar_visitor_name', $nom, time() + $CONFIG['cookie_duration'], '/');
// ============================================
// PAGE INTERMÉDIAIRE DE REDIRECTION
// ============================================
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ Accès autorisé - SOMAR Groupe</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
       
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
       
        .container {
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
       
        .success-icon {
            font-size: 80px;
            color: #2ecc71;
            margin-bottom: 20px;
            animation: bounce 1s infinite alternate;
        }
       
        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(to right, #3498db, #2ecc71);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
       
        .welcome-text {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #ecf0f1;
        }
       
        .user-name {
            color: #f1c40f;
            font-weight: bold;
            font-size: 1.3rem;
        }
       
        .info-box {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 15px;
            padding: 20px;
            margin: 25px 0;
            border-left: 4px solid #3498db;
        }
       
        .info-box p {
            margin: 10px 0;
            display: flex;
            justify-content: space-between;
        }
       
        .info-label {
            color: #bdc3c7;
            font-weight: 500;
        }
       
        .info-value {
            color: white;
            font-weight: bold;
        }
       
        .loader-container {
            margin: 30px 0;
        }
       
        .loader {
            width: 60px;
            height: 60px;
            margin: 0 auto;
            position: relative;
        }
       
        .loader-spinner {
            width: 100%;
            height: 100%;
            border: 6px solid rgba(255, 255, 255, 0.1);
            border-top: 6px solid #3498db;
            border-radius: 50%;
            animation: spin 1.5s linear infinite;
        }
       
        .loader-text {
            margin-top: 15px;
            color: #95a5a6;
            font-size: 0.9rem;
        }
       
        .countdown {
            font-size: 1.5rem;
            font-weight: bold;
            color: #f1c40f;
            margin-top: 10px;
        }
       
        .manual-link {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 30px;
            background: rgba(52, 152, 219, 0.3);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: 2px solid rgba(52, 152, 219, 0.5);
        }
       
        .manual-link:hover {
            background: rgba(52, 152, 219, 0.6);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
       
        .stats {
            display: flex;
            justify-content: space-around;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
       
        .stat-item {
            text-align: center;
        }
       
        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2ecc71;
        }
       
        .stat-label {
            font-size: 0.8rem;
            color: #bdc3c7;
            margin-top: 5px;
        }
       
        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-10px); }
        }
       
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
       
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
       
        .container {
            animation: fadeIn 0.8s ease-out;
        }
       
        /* Responsive */
        @media (max-width: 600px) {
            .container {
                padding: 25px;
            }
           
            h1 {
                font-size: 2rem;
            }
           
            .info-box p {
                flex-direction: column;
                text-align: left;
                margin: 15px 0;
            }
           
            .stats {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✅</div>
       
        <h1>Formulaire Validé !</h1>
       
        <p class="welcome-text">
            Bienvenue <span class="user-name"><?php echo htmlspecialchars($nom); ?></span> !
            <br>Votre accès au site est maintenant activé.
        </p>
       
        <div class="info-box">
            <p>
                <span class="info-label">🎯 Objectif :</span>
                <span class="info-value"><?php echo htmlspecialchars($objectif); ?></span>
            </p>
            <p>
                <span class="info-label">📧 Email :</span>
                <span class="info-value"><?php echo htmlspecialchars($email); ?></span>
            </p>
            <p>
                <span class="info-label">📞 Téléphone :</span>
                <span class="info-value"><?php echo htmlspecialchars($telephone); ?></span>
            </p>
            <?php if ($niveau): ?>
            <p>
                <span class="info-label">📚 Niveau :</span>
                <span class="info-value"><?php echo htmlspecialchars($niveau); ?></span>
            </p>
            <?php endif; ?>
        </div>
       
        <div class="loader-container">
            <div class="loader">
                <div class="loader-spinner"></div>
            </div>
            <p class="loader-text">Préparation de votre accès au site...</p>
            <div class="countdown" id="countdown">3</div>
        </div>
       
        <div class="stats">
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats[$date]['total']; ?></div>
                <div class="stat-label">Visiteurs aujourd'hui</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo $stats['global']['total_visiteurs']; ?></div>
                <div class="stat-label">Total visiteurs</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo count($stats['global']['jours_actifs']); ?></div>
                <div class="stat-label">Jours d'activité</div>
            </div>
        </div>
       
        <a href="<?php echo $CONFIG['redirect_target']; ?>" class="manual-link" id="manualLink">
            <i class="fas fa-external-link-alt"></i> Accéder manuellement au site
        </a>
    </div>
   
    <script>
        // Stocker les données d'accès côté client
        localStorage.setItem('somar_access', 'true');
        localStorage.setItem('somar_visitor_id', '<?php echo $visitorId; ?>');
        localStorage.setItem('somar_visitor_name', '<?php echo addslashes($nom); ?>');
        localStorage.setItem('somar_visitor_email', '<?php echo addslashes($email); ?>');
        localStorage.setItem('somar_visitor_objectif', '<?php echo addslashes($objectif); ?>');
        localStorage.setItem('somar_access_time', '<?php echo $timestamp; ?>');
       
        sessionStorage.setItem('somar_access', 'true');
        sessionStorage.setItem('somar_visitor_verified', 'true');
       
        // Cookies de secours
        document.cookie = "somar_access=true; path=/; max-age=<?php echo $CONFIG['cookie_duration']; ?>";
        document.cookie = "somar_visitor=<?php echo $visitorId; ?>; path=/; max-age=<?php echo $CONFIG['cookie_duration']; ?>";
       
        // Compte à rebours
        let countdown = 3;
        const countdownElement = document.getElementById('countdown');
        const manualLink = document.getElementById('manualLink');
       
        function updateCountdown() {
            countdownElement.textContent = countdown;
           
            if (countdown > 0) {
                countdown--;
                setTimeout(updateCountdown, 1000);
            } else {
                // Redirection automatique
                window.location.href = '<?php echo $CONFIG['redirect_target']; ?>';
            }
        }
       
        // Démarrer le compte à rebours
        updateCountdown();
       
        // Fallback : si la redirection automatique échoue
        setTimeout(function() {
            if (window.location.href.indexOf('verify.php') > -1) {
                console.log('Redirection automatique échouée, tentative manuelle...');
                manualLink.style.display = 'block';
            }
        }, 5000);
       
                // Vérification du stockage
        document.addEventListener('DOMContentLoaded', function() {
            const accessStored = localStorage.getItem('somar_access');
            console.log('Accès stocké dans localStorage:', accessStored);
           
            if (!accessStored) {
                console.warn('⚠️ Impossible de stocker localStorage, utilisation des cookies uniquement');
            }
        });
    </script>
   
    <noscript>
        <style>
            .loader-container, .manual-link {
                display: none;
            }
           
            .container::after {
                content: "⚠️ JavaScript est désactivé. Veuillez activer JavaScript ou cliquer sur le lien manuel ci-dessus.";
                display: block;
                background: rgba(231, 76, 60, 0.2);
                color: #e74c3c;
                padding: 15px;
                border-radius: 10px;
                margin-top: 20px;
                border: 1px solid #e74c3c;
            }
        </style>
    </noscript>
   
    <!-- Font Awesome pour l'icône -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>
<?php
// ============================================
// FONCTIONS UTILITAIRES
// ============================================
/**
* Envoie une notification par email
*/
function envoyerNotification($visitorData, $todayCount, $totalCount) {
    global $CONFIG;
   
    $to = $CONFIG['admin_email'];
    $subject = "📊 Nouveau visiteur sur " . $CONFIG['site_name'] . " - " . date('d/m/Y H:i');
   
    $message = "
    <!DOCTYPE html>
    <html>
    <head>
        <title>Notification visiteur</title>
        <meta charset='UTF-8'>
        <style>
            body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 20px; }
            .container { max-width: 600px; background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(to right, #2c3e50, #3498db); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
            .info { margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #3498db; }
            .label { font-weight: bold; color: #2c3e50; }
            .stats { background: #e8f4fc; padding: 15px; border-radius: 8px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>🚀 Nouveau visiteur sur " . $CONFIG['site_name'] . "</h2>
                <p>" . date('d/m/Y à H:i:s') . "</p>
            </div>
           
            <div class='info'>
                <span class='label'>👤 Nom :</span> " . htmlspecialchars($visitorData['nom']) . "
            </div>
           
            <div class='info'>
                <span class='label'>📧 Email :</span> " . htmlspecialchars($visitorData['email']) . "
            </div>
           
            <div class='info'>
                <span class='label'>📞 Téléphone :</span> " . htmlspecialchars($visitorData['telephone']) . "
            </div>
           
            <div class='info'>
                <span class='label'>🎯 Objectif :</span> " . htmlspecialchars($visitorData['objectif']) . "
            </div>
           
            <div class='info'>
                <span class='label'>📚 Niveau :</span> " . htmlspecialchars($visitorData['niveau'] ?: 'Non spécifié') . "
            </div>
           
            <div class='info'>
                <span class='label'>💬 Message :</span><br>
                " . nl2br(htmlspecialchars($visitorData['message'] ?: 'Aucun message')) . "
            </div>
           
            <div class='stats'>
                <h3>📊 Statistiques</h3>
                <p>Visiteurs aujourd'hui : <strong>" . $todayCount . "</strong></p>
                <p>Total visiteurs : <strong>" . $totalCount . "</strong></p>
                <p>ID visiteur : <code>" . $visitorData['id'] . "</code></p>
            </div>
           
            <div style='margin-top: 30px; padding: 15px; background: #f8f9fa; border-radius: 5px;'>
                <p style='color: #7f8c8d; font-size: 14px;'>
                    <i>Ces données ont été automatiquement enregistrées dans le système.</i>
                </p>
            </div>
        </div>
    </body>
    </html>
    ";
   
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8\r\n";
    $headers .= "From: " . $CONFIG['site_name'] . " <no-reply@{$_SERVER['HTTP_HOST']}>\r\n";
    $headers .= "Reply-To: no-reply@{$_SERVER['HTTP_HOST']}\r\n";
    $headers .= "X-Priority: 1\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();
   
    @mail($to, $subject, $message, $headers);
}
/**
* Détecte le navigateur
*/
function getBrowserInfo($userAgent) {
    $browsers = [
        'Chrome' => 'Chrome',
        'Firefox' => 'Firefox',
        'Safari' => 'Safari',
        'Opera' => 'Opera|OPR',
        'Edge' => 'Edge|Edg',
        'IE' => 'MSIE|Trident'
    ];
   
    foreach ($browsers as $browser => $pattern) {
        if (preg_match("/$pattern/i", $userAgent)) {
            return $browser;
        }
    }
   
    return 'Autre';
}
/**
* Détecte l'OS
*/
function getOSInfo($userAgent) {
    $oses = [
        'Windows' => 'Windows',
        'Mac' => 'Macintosh|Mac OS',
        'Linux' => 'Linux',
        'Android' => 'Android',
        'iOS' => 'iPhone|iPad|iPod'
    ];
   
    foreach ($oses as $os => $pattern) {
        if (preg_match("/$pattern/i", $userAgent)) {
            return $os;
        }
    }
   
    return 'Inconnu';
}
/**
* Détecte le type d'appareil
*/
function getDeviceInfo($userAgent) {
    if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent)) {
        return 'Mobile';
    } elseif (preg_match('/Tablet|iPad/i', $userAgent)) {
        return 'Tablet';
    } else {
        return 'Desktop';
    }
}
exit();
?>