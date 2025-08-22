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

// Suppression de fichier
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['file_id'])) {
    try {
        $fileId = (int)$_POST['file_id'];
        $fileModel = new Model('fichiers');
        
        // Récupérer les informations du fichier avant suppression
        $fichier = $fileModel->read($fileId);
        
        if ($fichier && is_array($fichier) && !empty($fichier)) {
            $uploadConfig = Config::get('upload');
            $filePath = $uploadConfig['directory'] . '/' . $fichier['nom'];
            
            // Supprimer le fichier physique s'il existe
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Supprimer l'enregistrement de la base de données
            $success = $fileModel->delete($fileId);
            
            if ($success) {
                echo Alert::success("Fichier supprimé avec succès : " . Security::escape($fichier['titre']));
            } else {
                echo Alert::error("Erreur lors de la suppression en base de données");
            }
        } else {
            echo Alert::error("Fichier introuvable");
        }
    } catch (Throwable $e) {
        echo Alert::error('Erreur lors de la suppression : ' . Security::escape($e->getMessage()));
    }
}
// Upload de fichier avec informations supplémentaires
elseif ($_POST && isset($_FILES['fichier']) && $_FILES['fichier']['error'] === UPLOAD_ERR_OK) {
    try {
        $uploadConfig = Config::get('upload');
        $filename = FileManager::upload($_FILES['fichier'], $uploadConfig['directory'], $uploadConfig['allowed_extensions'], $uploadConfig['max_size']);
        
        if ($filename) {
            // Récupérer les informations du formulaire
            $titre = Security::cleanInput($_POST['titre'] ?? '');
            
            // Sauvegarder en base de données
            $fileModel = new Model('fichiers');
            $success = $fileModel->create([
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
    } catch (Throwable $e) {
        echo Alert::error('Erreur : ' . Security::escape($e->getMessage()));
    }
} elseif ($_POST) {
    echo Alert::error("Erreur lors de l'upload du fichier");
}

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
                    <label class="form-label">Fichier</label>
                    <input type="file" name="fichier" class="form-control" required>
                    <?php
                    try {
                        $uploadConfig = Config::get('upload');
                        $extensions = strtoupper(implode(', ', $uploadConfig['allowed_extensions']));
                        $maxSize = Helper::formatBytes((int)$uploadConfig['max_size']);
                        echo "<div class='form-text'>$extensions - Max $maxSize</div>";
                    } catch (Throwable $e) {
                        echo "<div class='form-text'>Formats autorisés - Max 2 MB</div>";
                    }
                    ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" class="form-control" required>
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
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $fileModel = new Model('fichiers');
                        $fichiers = $fileModel->read();
                        
                        if ($fichiers) {
                            foreach ($fichiers as $fichier) {
                                $uploadConfig = Config::get('upload');
                                $filePath = $uploadConfig['directory'] . '/' . $fichier['nom'];
                                $size = FileManager::humanFileSize($filePath);
                                
                                echo "<tr>";
                                echo "<td>" . Security::escape($fichier['titre']) . "</td>";
                                echo "<td>" . Security::escape($fichier['nom_original']) . "</td>";
                                echo "<td>$size</td>";
                                echo "<td>" . Security::escape($fichier['uploaded_at']) . "</td>";
                                echo "<td>";
                                echo "<form method='POST' style='display:inline;' onsubmit='return confirm(\"Êtes-vous sûr de vouloir supprimer ce fichier ?\")'>";
                                echo "<input type='hidden' name='action' value='delete'>";
                                echo "<input type='hidden' name='file_id' value='" . $fichier['id'] . "'>";
                                echo "<button type='submit' class='btn btn-danger btn-sm'>Supprimer</button>";
                                echo "</form>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center'>Aucun fichier</td></tr>";
                        }
                    } catch (Throwable $e) {
                        echo "<tr><td colspan='5' class='text-center text-danger'>Erreur BDD : " . Security::escape($e->getMessage()) . "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        
                
    </div>
    <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
