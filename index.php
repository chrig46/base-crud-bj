<?php
/**
 * Page d'accueil du projet PHP CRUD
 * 
 * Point d'entrée principal présentant les deux versions disponibles :
 * - Version OOP avec classes et namespaces
 * - Version procédurale avec fonctions
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */

// Chargement des classes nécessaires
require_once __DIR__ . '/app/Database.php';
require_once __DIR__ . '/app/Config.php';
require_once __DIR__ . '/app/Model.php';
require_once __DIR__ . '/app/Security.php';
require_once __DIR__ . '/app/FileManager.php';
require_once __DIR__ . '/app/Messages.php';
require_once __DIR__ . '/app/Alert.php';

// Import des namespaces
use App\Database;
use App\Config;
use App\Model;
use App\Security;
use App\FileManager;
use App\Messages;
use App\Alert;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EXAM - BASE CRUD PHP</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">EXAM - BASE CRUD PHP</h1>
        
        <!-- Messages de statut -->
        <?php
        try {
            // Affichage des messages d'état
            echo Alert::success(Messages::get('success'));
            echo Alert::info("Classes chargées avec succès");
            echo Alert::warning("Configurez votre base de données dans config/database.php");
            echo Alert::warning("Configurez aussi l'upload dans config/upload.php");
            
            // Test de connexion à la base de données
            $pdo = Database::connectTo();
            echo Alert::success("Connexion à la base de données réussie");
            
            // Initialisation du modèle pour les tests
            $userModel = new Model('users');
            
        } catch (Exception $e) {
            echo Alert::error('Erreur : ' . Security::escape($e->getMessage()));
        }
        ?>
                
        <!-- Versions disponibles -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <h4 class="text-dark">Version OOP</h4>
                    <p class="text-muted mb-3">Classes avec namespaces, architecture moderne</p>
                    <div class="d-grid">
                        <a href="demo/demo.php" class="btn btn-dark">Démo OOP</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="border rounded p-3 h-100">
                    <h4 class="text-success">Version Procédurale</h4>
                    <p class="text-muted mb-3">Fonctions simples, approche traditionnelle</p>
                    <div class="d-grid">
                        <a href="open_source.php" class="btn btn-success">Démo Procédurale (en cours)</a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Liens utiles -->
        <div class="text-center mt-4">
            <a href="upload.php" class="btn btn-outline-primary">Gestion des fichiers</a>
            <a href="open_source.php" class="btn btn-outline-secondary ms-2">À propos (Open Source)</a>
        </div>
        
        <!-- Note d'information -->
        <div class="alert alert-info mt-4">
            <strong>Note :</strong> Exécutez demo_setup.sql avant de tester les démonstrations.
        </div>
    </div>
    
    <?php
    // Nettoyage des ressources
    try {
        Database::disconnect();
    } catch (Exception $e) {
        // Connexion déjà fermée
    }
    ?>
    
    <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>