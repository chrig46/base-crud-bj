<?php
/**
 * Page de modification d'enregistrement
 * 
 * Permet de modifier les données d'un enregistrement existant
 * avec pré-remplissage du formulaire et validation
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
$etudiant = [];
$filieres = [];

// Vérification de l'ID
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: etudiants_list.php');
    exit;
}

// Traitement de la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['modifier'])) {
        try {
            $etudiantModel = new Model('etudiants', 'id');
        
        // Nettoyage des données saisies
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = trim($_POST['email']);
        $sexe = trim($_POST['sexe']);
        $age = (int)$_POST['age'];
        $filiere_id = (int)$_POST['filiere_id'];
        
        // Validation des données saisies
        $errors = [];
        if (empty($nom)) $errors[] = "Le nom est obligatoire";
        if (empty($prenom)) $errors[] = "Le prénom est obligatoire";
        if (empty($email)) $errors[] = "L'email est obligatoire";
        if (!Security::isValidEmail($email)) $errors[] = "L'email n'est pas valide";
        if ($age <= 0) $errors[] = "L'âge est obligatoire";
        if (empty($sexe)) $errors[] = "Le sexe est obligatoire";
        // if ($filiere_id <= 0) $errors[] = "Veuillez sélectionner une filière"; // Décommenter si filière obligatoire
        
            if (empty($errors)) {
                // Mise à jour de l'enregistrement en base
                $success = $etudiantModel->update($id, [
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'email' => $email,
                    'sexe' => $sexe,
                    'age' => $age,
                    'filiere_id' => $filiere_id > 0 ? $filiere_id : null
                ]);
            
                if ($success) {
                    $message = Alert::success("Enregistrement modifié avec succès !");
                } else {
                    $message = Alert::error("Erreur lors de la modification");
                }
            } else {
                $message = Alert::warning("Erreurs : " . implode(', ', $errors));
            }

        } catch (Exception $e) {
            $message = Alert::error('Erreur : ' . $e->getMessage());
        }
    }
}

// Chargement des données de l'enregistrement à modifier
try {
    $etudiantModel = new Model('etudiants', 'id');
    $etudiant = $etudiantModel->read($id);
    
    if (empty($etudiant)) {
        header('Location: etudiants_list.php');
        exit;
    }
} catch (Exception $e) {
    $message = Alert::error('Erreur lors du chargement : ' . $e->getMessage());
}

// Chargement des options pour la liste déroulante
try {
    $filiereModel = new Model('filieres', 'id');
    $filieres = $filiereModel->read();
} catch (Exception $e) {
    $filieres = [];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo - Modifier un étudiant</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Modifier un étudiant</h1>
        
        <!-- Navigation -->
        <div class="btn-group my-4" role="group">
            <a href="index.php" class="btn btn-outline-secondary">Accueil</a>
            <a href="etudiants_list.php" class="btn btn-outline-secondary">Liste étudiants</a>
            <a href="filieres.php" class="btn btn-outline-secondary">Gestion filières</a>
        </div>
        
        <!-- Messages -->
        <?= $message ?>
        
        <?php if (!empty($etudiant)): ?>
        <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom *</label>
                    <input type="text" class="form-control" id="nom" name="nom" 
                           value="<?= Security::escape($etudiant['nom']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="prenom" class="form-label">Prénom *</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" 
                           value="<?= Security::escape($etudiant['prenom']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= Security::escape($etudiant['email']) ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="age" class="form-label">Âge *</label>
                    <input type="number" class="form-control" id="age" name="age" 
                           min="16" max="99" value="<?= Security::escape($etudiant['age'] ?? '') ?>" required>
                </div>
                
                <div class="mb-3">
                    <label for="sexe" class="form-label">Sexe *</label>
                    <select class="form-select" id="sexe" name="sexe" required>
                        <option value="">-- Sélectionnez --</option>
                        <option value="M" <?= ($etudiant['sexe'] ?? '') == 'M' ? 'selected' : '' ?>>Masculin</option>
                        <option value="F" <?= ($etudiant['sexe'] ?? '') == 'F' ? 'selected' : '' ?>>Féminin</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="filiere_id" class="form-label">Filière</label>
                    <select class="form-select" id="filiere_id" name="filiere_id">
                        <option value="">-- Sélectionnez une filière --</option>
                        <?php foreach ($filieres as $filiere): ?>
                            <option value="<?= $filiere['id'] ?>" 
                                    <?= ($etudiant['filiere_id'] == $filiere['id']) ? 'selected' : '' ?>>
                                <?= Security::escape($filiere['nom']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="mb-3">
                    <button type="submit" name="modifier" class="btn btn-warning">Modifier</button>
                </div>
        </form>
        
        <?php else: ?>
        <div class="alert alert-danger">
            Enregistrement non trouvé ou erreur de chargement.
        </div>
        <?php endif; ?>
    </div>
    
</body>
</html>
