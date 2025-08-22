<?php

namespace App;

/**
 * Classe FileManager - Gestionnaire de fichiers
 *
 * Cette classe fournit des méthodes statiques pour gérer les fichiers de façon sécurisée :
 * upload, suppression, vérification d'extension, formatage de taille, et nettoyage de noms.
 * Elle utilise les bonnes pratiques de sécurité pour éviter les failles d'upload.
 *
 * Utilisation :
 * $filename = FileManager::upload($_FILES['fichier'], 'uploads/', ['jpg', 'png']);
 * FileManager::delete('uploads/fichier.jpg');
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */
class FileManager
{
    /**
     * Upload un fichier de façon sécurisée
     *
     * @param array $file Tableau $_FILES['input_name']
     * @param string $destinationDir Dossier de destination (doit exister)
     * @param array $allowedExtensions Extensions autorisées (ex: ['jpg', 'png'])
     * @param int $maxSize Taille maximale en octets (par défaut 2MB)
     * @return string|false Nom du nouveau fichier ou false en cas d'erreur
     */
    public static function upload(array $file, string $destinationDir, array $allowedExtensions = [], int $maxSize = 2097152): string|false
    {
        // Vérifications de base
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        if (!isset($file['size']) || $file['size'] > $maxSize || $file['size'] <= 0) {
            return false;
        }

        if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
            return false;
        }

        // Vérifier l'extension
        $originalName = $file['name'] ?? '';
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!empty($allowedExtensions) && !in_array($ext, $allowedExtensions)) {
            return false;
        }

        // Normaliser le chemin de destination (multiplateforme)
        $destinationDir = rtrim($destinationDir, '/\\');
        
        // Créer le dossier de destination s'il n'existe pas
        if (!is_dir($destinationDir)) {
            if (!mkdir($destinationDir, 0755, true)) {
                return false;
            }
        }

        // Vérifier les permissions d'écriture
        if (!is_writable($destinationDir)) {
            return false;
        }

        // Générer un nom unique et sécurisé
        $newName = uniqid('file_', true) . '.' . $ext;
        $destination = $destinationDir . DIRECTORY_SEPARATOR . $newName;

        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            // Debug : Pourquoi l'upload échoue
            error_log("Upload failed: " . $file['tmp_name'] . " -> " . $destination);
            return false;
        }

        // Vérifier que le fichier a bien été créé
        if (!file_exists($destination)) {
            error_log("File not created: " . $destination);
            return false;
        }

        return $newName;
    }

    /**
     * Supprime un fichier
     *
     * @param string $filePath Chemin vers le fichier
     * @return bool Succès ou échec de la suppression
     */
    public static function delete(string $filePath): bool
    {
        return file_exists($filePath) ? unlink($filePath) : false;
    }

    /**
     * Vérifie si l'extension d'un fichier est autorisée
     *
     * @param string $filename Nom du fichier
     * @param array $allowedExtensions Extensions autorisées
     * @return bool True si l'extension est autorisée
     */
    public static function checkExtension(string $filename, array $allowedExtensions): bool
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, $allowedExtensions);
    }

    /**
     * Retourne la taille d'un fichier en format lisible
     *
     * @param string $filePath Chemin vers le fichier
     * @return string Taille formatée (ex: "1.5 MB")
     */
    public static function humanFileSize(string $filePath): string
    {
        // Vérifier que le fichier existe avant de calculer sa taille
        if (!file_exists($filePath)) {
            return '0 bytes';
        }
        
        $bytes = filesize($filePath);
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }
        return $bytes;
    }


    /**
     * Génère un nom de fichier sécurisé
     *
     * @param string $filename Nom du fichier original
     * @return string Nom nettoyé et sécurisé
     */
    public static function safeFilename(string $filename): string
    {
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
        return substr($filename, 0, 255);
    }
}
