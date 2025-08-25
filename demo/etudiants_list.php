<?php
/**
 * Page de liste d'enregistrements
 * 
 * Affiche la liste des enregistrements avec leurs relations
 * via jointure SQL et permet les actions CRUD
 */

// Chargement des classes nécessaires
require_once __DIR__ . '/../app/Database.php';
require_once __DIR__ . '/../app/Model.php';
require_once __DIR__ . '/../app/Security.php';
require_once __DIR__ . '/../app/Alert.php';

use App\Database;
use App\Model;
use App\Security;
use App\Alert;

$message = '';

// Traitement de la suppression d'un enregistrement
if ($_POST && isset($_POST['supprimer'])) {
    try {
        $etudiantModel = new Model('etudiants', 'id');
        $id = (int)$_POST['id'];
        
        if ($id > 0) {
            $success = $etudiantModel->delete($id);
            if ($success) {
                $message = Alert::success("Enregistrement supprimé avec succès !");
            } else {
                $message = Alert::error("Erreur lors de la suppression");
            }
        }
    } catch (Exception $e) {
        $message = Alert::error('Erreur : ' . $e->getMessage());
    }
}

// Chargement des enregistrements avec jointure
try {
    $etudiantModel = new Model('etudiants', 'id');
    
    // Requête avec LEFT JOIN pour récupérer les relations (inclut étudiants sans filière)
    $sql = "SELECT e.id, e.nom, e.prenom, e.email, e.age, e.sexe, e.created_at, 
                   f.nom as filiere_nom, f.id as filiere_id
            FROM etudiants e 
            LEFT JOIN filieres f ON e.filiere_id = f.id
            ORDER BY e.id DESC";
    
    $etudiants = $etudiantModel->query($sql);
    
} catch (Exception $e) {
    $etudiants = [];
    $message = Alert::error('Erreur de chargement : ' . $e->getMessage());
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - Liste des étudiants</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Liste des étudiants</h1>
        
        <!-- Navigation -->
        <div class="btn-group my-4" role="group">
            <a href="index.php" class="btn btn-outline-secondary">Accueil</a>
            <a href="etudiants_add.php" class="btn btn-outline-secondary">Ajouter étudiant</a>
            <a href="filieres.php" class="btn btn-outline-secondary">Gestion filières</a>
        </div>
        
        <!-- Messages -->
        <?= $message ?>
        
        <!-- Compteur -->
        <p class="mt-4"><strong><?= count($etudiants) ?></strong> enregistrement(s)</p>
        
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Sexe</th>
                    <th>Filière</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($etudiants)): ?>
                    <?php foreach ($etudiants as $etudiant): ?>
                    <tr>
                        <td><strong><?= Security::escape($etudiant['id']) ?></strong></td>
                        <td><?= Security::escape($etudiant['nom']) ?></td>
                        <td><?= Security::escape($etudiant['prenom']) ?></td>
                        <td><?= Security::escape($etudiant['email']) ?></td>
                        <td><?= $etudiant['sexe'] ? ($etudiant['sexe'] == 'M' ? 'M' : 'F') : '-' ?></td>
                        <td><?= $etudiant['filiere_nom'] ? Security::escape($etudiant['filiere_nom']) : '<em>Non spécifiée</em>' ?></td>
                        <td>
                            <a href="etudiants_edit.php?id=<?= $etudiant['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer cet enregistrement ?')">
                                <input type="hidden" name="id" value="<?= $etudiant['id'] ?>">
                                <button type="submit" name="supprimer" class="btn btn-sm btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <em>Aucun enregistrement trouvé.</em>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <!-- Note technique -->
        <div class="alert alert-info mt-4">
            <strong>Points techniques :</strong> Jointure SQL (JOIN) - CRUD complet - Sécurité (échappement) - Relations entre tables
        </div>
    </div>
    
</body>
</html>
