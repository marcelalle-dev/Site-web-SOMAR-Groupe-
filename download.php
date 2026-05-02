<?php
// download.php - Téléchargement sécurisé des données
// Vérifier l'accès via la session admin
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    die('Accès non autorisé');
}
$csvFile = 'data/visiteurs.csv';
if (file_exists($csvFile)) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="visiteurs_somar_' . date('Y-m-d') . '.csv"');
    readfile($csvFile);
    exit();
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Erreur</title>
        <style>
            body { font-family: Arial; padding: 50px; text-align: center; }
            .error { color: #e74c3c; background: #ffeaea; padding: 20px; border-radius: 8px; }
        </style>
    </head>
    <body>
        <div class="error">
            <h2>⚠️ Fichier non trouvé</h2>
            <p>Le fichier de données n'existe pas encore.</p>
            <p>Il sera créé automatiquement après le premier visiteur.</p>
            <a href="admin.php">Retour au tableau de bord</a>
        </div>
    </body>
    </html>
    <?php
}
?>