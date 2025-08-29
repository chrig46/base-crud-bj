<?php
/**
 * Fichier upload.php - Upload de fichiers avec métadonnées
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */

// Chargement des classes nécessaires
require_once __DIR__ . '/app/Database.php';
require_once __DIR__ . '/app/Model.php';
require_once __DIR__ . '/app/Config.php';
require_once __DIR__ . '/app/FileManager.php';
require_once __DIR__ . '/app/Security.php';
require_once __DIR__ . '/app/Alert.php';
require_once __DIR__ . '/app/Helper.php';

use App\Database;
use App\Model;
use App\Config;
use App\FileManager;
use App\Security;
use App\Alert;
use App\Helper;

// Traitement des actions POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        
        // Suppression de fichier
        if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['file_id'])) {
            $fileId = (int)$_POST['file_id'];
            $fileModel = new Model('fichiers', 'id');
            
            // Récupérer les informations du fichier avant suppression
            $fichier = $fileModel->read($fileId);
            
            if (is_array($fichier) && !empty($fichier)) {
                // Utiliser FileManager::delete() pour supprimer le fichier physique
                $fileDeleted = FileManager::delete($fichier, 'nom'); // Colonne de nom de fichier db a specifier
                
                // Supprimer l'enregistrement de la base de données
                $success = $fileModel->delete($fileId);
                
                if ($success) {
                    if ($fileDeleted) {
                        echo Alert::success("Fichier supprimé avec succès : " . Security::escape($fichier['titre']));
                    } else {
                        echo Alert::warning("Enregistrement supprimé mais le fichier physique n'a pas pu être supprimé");
                    }
                } else {
                    echo Alert::error("Erreur lors de la suppression en base de données");
                }
            } else {
                echo Alert::error("Fichier introuvable");
            }
        }
        
        // Upload de fichier avec informations supplémentaires
        if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
            $filename = FileManager::upload($_FILES['fichier']);
            
            if ($filename) {
                // Récupérer les informations du formulaire lorsque le fichier est bien uploadé
                $titre = trim($_POST['titre']);
                
                // Sauvegarder en base de données
                $fileModel = new Model('fichiers', 'id');
                $success = $fileModel->create([
                    // 'nom' est imperatif - Contient le nom du fichier stocké sur le server
                    'nom' => $filename,
                    'nom_original' => $_FILES['fichier']['name'],
                    'titre' => $titre,
                    'taille' => $_FILES['fichier']['size'],
                    'uploaded_at' => date('Y-m-d H:i:s')
                ]);
                
                if ($success) {
                    echo Alert::success("Fichier uploadé avec succès : " . $filename);
                } else {
                    echo Alert::warning("Fichier uploadé mais erreur BDD");
                }
            } else {
                echo Alert::error("Échec de l'upload");
            }
        } else {
            echo Alert::error("Erreur lors de l'upload du fichier");
        }
    } catch (Throwable $e) {
        echo Alert::error('Erreur : ' . Security::escape($e->getMessage()));
    }
}

// Chargement des fichiers pour l'affichage
$fichiers = [];
$fichiersError = '';
try {
    $fileModel = new Model('fichiers', 'id');
    $fichiers = $fileModel->read() ?: [];
} catch (Throwable $e) {
    $fichiersError = 'Erreur BDD : ' . Security::escape($e->getMessage());
}

// Recuperation des informations d'upload pour l'affichage - Si souhaitable
$uploadInfo = Config::getUploadInfo();


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload de Fichiers</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Upload de Fichiers</h1>
                
        <!-- Formulaire -->
        <div class="mb-4">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="fichier" class="form-label">Fichier</label>
                    <input type="file" name="fichier" id="fichier" class="form-control" required>
                    <div class="form-text"><?= $uploadInfo ?></div>
                </div>
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" id="titre" required>
                </div>
                <button type="submit" class="btn btn-success w-100">Uploader</button>
            </form>
        </div>
                
        <!-- Liste des fichiers -->
        <h5 class="mb-3">Fichiers uploadés</h5>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Nom original</th>
                        <th>Taille</th>
                        <th>Date</th>
                        <th>Aperçu</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($fichiersError): ?>
                        <tr>
                            <td colspan="6" class="text-center text-danger"><?= $fichiersError ?></td>
                        </tr>
                    <?php elseif (empty($fichiers)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Aucun fichier</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($fichiers as $fichier): ?>
                            <tr>
                                <td><?= Security::escape($fichier['titre']) ?></td>
                                <td><?= Security::escape($fichier['nom_original']) ?></td>
                                <td><?= FileManager::getUploadedFileSize($fichier['nom']) ?></td>
                                <td><?= Security::escape($fichier['uploaded_at']) ?></td>
                                <td>
                                    <?php 
                                    $fileUrl = FileManager::getUploadedFileUrl($fichier['nom']);
                                    $extension = strtolower(pathinfo($fichier['nom_original'], PATHINFO_EXTENSION));
                                    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];
                                    ?>
                                    <?php if (in_array($extension, $imageExtensions)): ?>
                                        <img src="<?= $fileUrl ?>" style="max-height: 60px; max-width: 80px;" class="img-thumbnail" alt="Aperçu">
                                    <?php else: ?>
                                        <span class="text-muted">Pas d'aperçu</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce fichier ?')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="file_id" value="<?= $fichier['id'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
                
    </div>
    <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
