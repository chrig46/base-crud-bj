<?php
/**
 * Page d'ajout d'un étudiant
 * 
 * Permet d'ajouter un nouvel étudiant avec sélection de filière
 * et validation des données saisies
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
$formData = [];

// Traitement de la soumission du formulaire
if ($_POST && isset($_POST['ajouter'])) {
    try {
        $etudiantModel = new Model('etudiants');
        
        // Nettoyage des données saisies
        $nom = Security::cleanInput($_POST['nom']);
        $prenom = Security::cleanInput($_POST['prenom']);
        $email = Security::cleanInput($_POST['email']);
        $age = (int)$_POST['age'];
        $sexe = Security::cleanInput($_POST['sexe']);
        $filiere_id = (int)$_POST['filiere_id'];
        
        // Validation des champs obligatoires
        $errors = [];
        if (empty($nom)) $errors[] = "Le nom est obligatoire";
        if (empty($prenom)) $errors[] = "Le prénom est obligatoire";
        if (empty($email)) $errors[] = "L'email est obligatoire";
        if (!Security::isValidEmail($email)) $errors[] = "L'email n'est pas valide";
        if (!empty($sexe) && !in_array($sexe, ['M', 'F'])) $errors[] = "Le sexe doit être M ou F";
        if ($filiere_id <= 0) $errors[] = "Veuillez sélectionner une filière";
        
        if (empty($errors)) {
            // Création de l'étudiant en base
            $success = $etudiantModel->create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'age' => $age > 0 ? $age : null,
                'sexe' => !empty($sexe) ? $sexe : null,
                'filiere_id' => $filiere_id
            ]);
            
            if ($success) {
                $message = Alert::success("Étudiant {$prenom} {$nom} ajouté avec succès !");
                $formData = []; // Vider le formulaire
            } else {
                $message = Alert::error("Erreur lors de l'ajout (email peut-être déjà utilisé)");
                $formData = $_POST;
            }
        } else {
            $message = Alert::warning("Erreurs : " . implode(', ', $errors));
            $formData = $_POST;
        }
        
    } catch (Exception $e) {
        $message = Alert::error('Erreur : ' . Security::escape($e->getMessage()));
        $formData = $_POST;
    }
}

// Chargement des filières pour la liste déroulante
try {
    $filiereModel = new Model('filieres');
    $filieres = $filiereModel->read();
} catch (Exception $e) {
    $filieres = [];
    $message = Alert::error('Erreur de chargement : ' . Security::escape($e->getMessage()));
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - Ajouter un étudiant</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Ajouter un étudiant</h1>
        
        <!-- Navigation -->
        <div class="btn-group my-4" role="group">
            <a href="demo.php" class="btn btn-outline-secondary">Accueil</a>
            <a href="demo_etudiants_list.php" class="btn btn-outline-secondary">Liste étudiants</a>
            <a href="demo_filieres.php" class="btn btn-outline-secondary">Gestion filières</a>
        </div>
        
        <!-- Messages -->
        <?= $message ?>
        
        <?php if (empty($filieres)): ?>
            <div class="alert alert-warning mt-4">
                <strong>Attention :</strong> Aucune filière trouvée. 
                <a href="demo_filieres.php">Créez d'abord des filières</a> avant d'ajouter des étudiants.
            </div>
        <?php else: ?>
        <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" class="form-control" id="nom" name="nom" 
                           value="<?= Security::escape($formData['nom'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom *</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" 
                           value="<?= Security::escape($formData['prenom'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= Security::escape($formData['email'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="age" class="form-label">Âge</label>
                    <input type="number" class="form-control" id="age" name="age" 
                           min="16" max="99" value="<?= Security::escape($formData['age'] ?? '') ?>">
                </div>
                
                <div class="mb-3">
                    <label for="sexe" class="form-label">Sexe</label>
                    <select class="form-select" id="sexe" name="sexe">
                        <option value="">-- Non spécifié --</option>
                        <option value="M" <?= (isset($formData['sexe']) && $formData['sexe'] == 'M') ? 'selected' : '' ?>>Masculin</option>
                        <option value="F" <?= (isset($formData['sexe']) && $formData['sexe'] == 'F') ? 'selected' : '' ?>>Féminin</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="filiere_id" class="form-label">Filière *</label>
                    <select class="form-select" id="filiere_id" name="filiere_id" required>
                        <option value="">-- Sélectionnez une filière --</option>
                        <?php foreach ($filieres as $filiere): ?>
                            <option value="<?= $filiere['id'] ?>" 
                                    <?= (isset($formData['filiere_id']) && $formData['filiere_id'] == $filiere['id']) ? 'selected' : '' ?>>
                                <?= Security::escape($filiere['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <button type="submit" name="ajouter" class="btn btn-success">Ajouter l'étudiant</button>
                    <button type="reset" class="btn btn-outline-danger">Effacer</button>
                </div>
        </form>
        
        <?php endif; ?>
        
        <!-- Note technique -->
        <div class="alert alert-info mt-4">
            <strong>Points techniques :</strong> Relations entre tables - Liste déroulante dynamique - Validation - Sécurité
        </div>
    </div>
    
</body>
</html>
