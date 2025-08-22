<?php
/**
 * Page de gestion des filières
 * 
 * Gestion complète des filières avec modals Bootstrap
 * et opérations CRUD sur une seule page
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

// Traitement des actions CRUD
if ($_POST) {
    try {
        $filiereModel = new Model('filieres');
        
        // Ajout d'une filière
        if (isset($_POST['ajouter'])) {
            $nom = Security::cleanInput($_POST['nom']);
            $description = Security::cleanInput($_POST['description']);
            
            if (!empty($nom)) {
                $success = $filiereModel->create([
                    'nom' => $nom,
                    'description' => $description
                ]);
                
                if ($success) {
                    $message = Alert::success("Filière '{$nom}' ajoutée avec succès !");
                } else {
                    $message = Alert::error("Erreur lors de l'ajout de la filière");
                }
            } else {
                $message = Alert::warning("Le nom de la filière est obligatoire");
            }
        }
        
        // Modification d'une filière
        if (isset($_POST['modifier'])) {
            $id = (int)$_POST['id'];
            $nom = Security::cleanInput($_POST['nom']);
            $description = Security::cleanInput($_POST['description']);
            
            if ($id > 0 && !empty($nom)) {
                $success = $filiereModel->update($id, [
                    'nom' => $nom,
                    'description' => $description
                ]);
                
                if ($success) {
                    $message = Alert::success("Filière '{$nom}' modifiée avec succès !");
                } else {
                    $message = Alert::error("Erreur lors de la modification");
                }
            } else {
                $message = Alert::warning("Données invalides pour la modification");
            }
        }
        
        // Suppression d'une filière
        if (isset($_POST['supprimer'])) {
            $id = (int)$_POST['id'];
            if ($id > 0) {
                $success = $filiereModel->delete($id);
                if ($success) {
                    $message = Alert::success("Filière supprimée avec succès !");
                } else {
                    $message = Alert::error("Erreur lors de la suppression");
                }
            }
        }
        
    } catch (Exception $e) {
        $message = Alert::error('Erreur : ' . Security::escape($e->getMessage()));
    }
}

// Chargement des filières
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
    <title>Demo - Gestion des filières</title>
    <link href="../bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>Gestion des filières</h1>
        
        <!-- Navigation -->
        <div class="btn-group my-4" role="group">
            <a href="demo.php" class="btn btn-outline-secondary">Accueil</a>
            <a href="demo_etudiants_list.php" class="btn btn-outline-secondary">Liste étudiants</a>
            <a href="demo_etudiants_add.php" class="btn btn-outline-secondary">Ajouter étudiant</a>
        </div>
        
        <!-- Messages -->
        <?= $message ?>
        
        <!-- Bouton d'ajout et compteur -->
        <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
            <p class="mb-0"><strong><?= count($filieres) ?></strong> filière(s) disponible(s)</p>
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#ajouterModal">
                Ajouter une filière
            </button>
        </div>
        
        <!-- Tableau des filières -->
        <?php if (!empty($filieres)): ?>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($filieres as $filiere): ?>
                <tr>
                    <td><strong><?= Security::escape($filiere['id']) ?></strong></td>
                    <td><?= Security::escape($filiere['nom']) ?></td>
                    <td><?= Security::escape(substr($filiere['description'] ?? '', 0, 60)) ?><?= strlen($filiere['description'] ?? '') > 60 ? '...' : '' ?></td>
                    <td>
                        <button type="button" class="btn btn-sm btn-warning" 
                                onclick="modifierFiliere(<?= $filiere['id'] ?>, '<?= Security::escape($filiere['nom']) ?>', '<?= Security::escape($filiere['description'] ?? '') ?>')">
                            Modifier
                        </button>
                        <button type="button" class="btn btn-sm btn-danger" 
                                onclick="supprimerFiliere(<?= $filiere['id'] ?>, '<?= Security::escape($filiere['nom']) ?>')">
                            Supprimer
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div class="alert alert-info mt-4">
                Aucune filière trouvée. <button type="button" class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#ajouterModal">Ajouter la première filière</button>
            </div>
        <?php endif; ?>
        
        <!-- Note technique -->
        <div class="alert alert-info mt-4">
            <strong>Points techniques :</strong> Modals Bootstrap - CRUD complet - Interface moderne - Gestion d'événements JavaScript
        </div>
    </div>
    
    <!-- Modal Ajouter -->
    <div class="modal fade" id="ajouterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter une filière</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="ajout_nom" class="form-label">Nom de la filière *</label>
                            <input type="text" class="form-control" id="ajout_nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="ajout_description" class="form-label">Description</label>
                            <textarea class="form-control" id="ajout_description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="ajouter" class="btn btn-success">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Modifier -->
    <div class="modal fade" id="modifierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier la filière</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="modif_id" name="id">
                        <div class="mb-3">
                            <label for="modif_nom" class="form-label">Nom de la filière *</label>
                            <input type="text" class="form-control" id="modif_nom" name="nom" required>
                        </div>
                        <div class="mb-3">
                            <label for="modif_description" class="form-label">Description</label>
                            <textarea class="form-control" id="modif_description" name="description" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="modifier" class="btn btn-warning">Modifier</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Supprimer -->
    <div class="modal fade" id="supprimerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Supprimer la filière</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" id="suppr_id" name="id">
                        <p>Êtes-vous sûr de vouloir supprimer la filière <strong id="suppr_nom"></strong> ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" name="supprimer" class="btn btn-danger">Supprimer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fonction pour modifier une filière
        function modifierFiliere(id, nom, description) {
            document.getElementById('modif_id').value = id;
            document.getElementById('modif_nom').value = nom;
            document.getElementById('modif_description').value = description;
            
            var modal = new bootstrap.Modal(document.getElementById('modifierModal'));
            modal.show();
        }
        
        // Fonction pour supprimer une filière
        function supprimerFiliere(id, nom) {
            document.getElementById('suppr_id').value = id;
            document.getElementById('suppr_nom').textContent = nom;
            
            var modal = new bootstrap.Modal(document.getElementById('supprimerModal'));
            modal.show();
        }
        
        // Vider les formulaires quand les modals se ferment
        document.getElementById('ajouterModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('ajout_nom').value = '';
            document.getElementById('ajout_description').value = '';
        });
    </script>
</body>
</html>
