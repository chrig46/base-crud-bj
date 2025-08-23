<?php
/**
 * Demo - Page d'accueil simple
 * 
 * Navigation simple pour la démonstration Étudiants + Filières
 * Parfait pour présenter rapidement le projet lors d'un examen
 */

// Chargement des classes pour tester la connexion
require_once __DIR__ . '/../app/Database.php';
require_once __DIR__ . '/../app/Model.php';
require_once __DIR__ . '/../app/Alert.php';

use App\Database;
use App\Model;
use App\Alert;

$message = '';
$stats = ['etudiants' => 0, 'filieres' => 0];

// Test de connexion et récupération des statistiques
try {
    $etudiantModel = new Model('etudiants', 'id');
    $filiereModel = new Model('filieres', 'id');
    
    $etudiants = $etudiantModel->read();
    $filieres = $filiereModel->read();
    
    $stats['etudiants'] = count($etudiants);
    $stats['filieres'] = count($filieres);
    
    $message = Alert::success("✅ Connexion à la base de données réussie !");
    
} catch (Exception $e) {
    $message = Alert::error("❌ Erreur de connexion : Exécutez d'abord le script setup.sql");
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DEMO PHP OOP - Étudiants & Filières</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="text-center mb-4">
            <h1 class="display-3 fw-bold">DEMO PHP OOP</h1>
            <p class="lead">Exemple d'utilisation du set de classes PHP OOP</p>
            <div class="alert alert-primary mt-3">
                <strong>Concept :</strong> Un set de classes réutilisables (database, model, security, alert...) 
                pour éviter de réécrire du code long pendant l'examen. Cette demo montre comment les utiliser efficacement.
            </div>
        </div>
        
        <!-- Messages -->
        <?= $message ?>
        
        <!-- Dashboard -->
        <div class="row mt-4">
            <!-- Card Étudiants -->
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gestion des étudiants</h5>
                        <p class="card-text text-muted mb-2">Pages séparées avec CRUD complet et relations</p>
                        <p class="card-text"><strong><?= $stats['etudiants'] ?></strong> étudiant(s) inscrit(s)</p>
                        <div class="d-grid">
                            <a href="etudiants_list.php" class="btn btn-success btn-sm">Tester</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card Filières -->
            <div class="col-md-6 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Gestion des filières</h5>
                        <p class="card-text text-muted mb-2">Modals Bootstrap sur une seule page</p>
                        <p class="card-text"><strong><?= $stats['filieres'] ?></strong> filière(s) disponible(s)</p>
                        <div class="d-grid">
                            <a href="filieres.php" class="btn btn-success btn-sm">Tester</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card Set de classes PHP OOP -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Set de classes réutilisables</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-success">Classes principales du set</h6>
                                <ul class="list-unstyled">
                                    <li>• <strong>Database</strong> - connexion PDO avec singleton</li>
                                    <li>• <strong>Model</strong> - CRUD générique pour toute table</li>
                                    <li>• <strong>Security</strong> - validation et échappement</li>
                                    <li>• <strong>Alert</strong> - messages utilisateur stylés</li>
                                </ul>
                                
                                <h6 class="text-success">Avantages du set</h6>
                                <ul class="list-unstyled">
                                    <li>• Évite de réécrire les bases</li>
                                    <li>• Code testé et fonctionnel</li>
                                    <li>• Structure organisée</li>
                                    <li>• Adaptable selon les besoins</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-success">Utilisation dans cette demo</h6>
                                <ul class="list-unstyled">
                                    <li>• <code>new Model('etudiants', 'id')</code> - CRUD automatique</li>
                                    <li>• <code>Security::cleanInput()</code> - nettoyage</li>
                                    <li>• <code>Security::escape()</code> - protection XSS</li>
                                    <li>• <code>Alert::success()</code> - messages</li>
                                </ul>
                                
                                <h6 class="text-success">Concepts démontrés</h6>
                                <ul class="list-unstyled">
                                    <li>• Relations entre tables (étudiants ↔ filières)</li>
                                    <li>• Jointures SQL avec le model</li>
                                    <li>• Modals Bootstrap + JavaScript</li>
                                    <li>• Interface responsive moderne</li>
                                </ul>
                            </div>
                        </div>
                        <div class="alert alert-success mt-3">
                            <strong>Stratégie d'examen :</strong> Avec ce set de classes, vous pouvez créer rapidement n'importe quelle application 
                            PHP OOP sans perdre de temps à réécrire les bases. Concentrez-vous sur la logique métier et l'interface.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
