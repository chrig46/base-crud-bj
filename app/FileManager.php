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
 * $filename = FileManager::upload($_FILES['fichier']);
 * FileManager::delete($fileRecord, 'nom');
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */
class FileManager
{
    // Constantes pour les tailles de fichiers
    private const BYTES_IN_GB = 1073741824; // 1024^3
    private const BYTES_IN_MB = 1048576;    // 1024^2
    private const BYTES_IN_KB = 1024;       // 1024^1

    /**
     * Upload un fichier de façon sécurisée
     *
     * @param array $file Tableau $_FILES['input_name']
     * @return string|false Nom du nouveau fichier ou false en cas d'erreur
     */
    public static function upload(array $file): string|false
    {
        // Charger la configuration
        try {
            $uploadConfig = Config::get('upload');
            $destinationDir = $uploadConfig['directory'];
            $allowedExtensions = $uploadConfig['allowed_extensions'];
            $maxSize = $uploadConfig['max_size'];
        } catch (\Throwable $e) {
            return false;
        }

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
        $destinationDir = self::normalizePath($destinationDir);
        
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
        $baseName = self::safeFilename(pathinfo($originalName, PATHINFO_FILENAME));
        $newName = uniqid($baseName . '_', true) . '.' . $ext;
        $destination = $destinationDir . DIRECTORY_SEPARATOR . $newName;

        // Déplacer le fichier
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return false;
        }

        // Vérifier que le fichier a bien été créé
        if (!file_exists($destination)) {
            return false;
        }

        return $newName;
    }

    /**
     * Supprime le fichier physique d'un enregistrement
     *
     * Cette méthode reçoit l'enregistrement du fichier déjà lu depuis la BDD
     * et supprime uniquement le fichier physique. Elle construit automatiquement
     * le chemin en utilisant la configuration d'upload.
     *
     * @param array $fileRecord Enregistrement du fichier depuis la BDD
     * @param string $filenameField Nom du champ contenant le nom du fichier
     * @return bool Succès ou échec de la suppression du fichier physique
     */
    public static function delete(array $fileRecord, string $filenameField): bool
    {
        try {
            // Vérifier que l'enregistrement contient le nom du fichier
            if (empty($fileRecord) || !isset($fileRecord[$filenameField])) {
                return false;
            }

            // Construire le chemin du fichier physique
            $uploadConfig = Config::get('upload');
            $destinationDir = self::normalizePath($uploadConfig['directory']);
            $filePath = $destinationDir . DIRECTORY_SEPARATOR . $fileRecord[$filenameField];
            
            // Supprimer le fichier physique s'il existe
            return file_exists($filePath) ? unlink($filePath) : false;
            
        } catch (\Throwable $e) {
            return false;
        }
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
        
        // Vérifier que filesize() a réussi
        if ($bytes === false) {
            return '0 bytes';
        }
        
        if ($bytes >= self::BYTES_IN_GB) {
            return number_format($bytes / self::BYTES_IN_GB, 2) . ' GB';
        } elseif ($bytes >= self::BYTES_IN_MB) {
            return number_format($bytes / self::BYTES_IN_MB, 2) . ' MB';
        } elseif ($bytes >= self::BYTES_IN_KB) {
            return number_format($bytes / self::BYTES_IN_KB, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes === 1) {
            return '1 byte';
        }
        
        return '0 bytes';
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

    /**
     * Construit le chemin complet d'un fichier uploadé
     * 
     * Cette méthode centralise la construction du chemin vers un fichier uploadé
     * en utilisant la configuration.
     * 
     * @param string $filename Nom du fichier (stocké en base)
     * @return string|null Chemin complet vers le fichier ou null si erreur de config
     */
    public static function getUploadedFilePath(string $filename): ?string
    {
        try {
            $uploadConfig = Config::get('upload');
            return self::normalizePath($uploadConfig['directory']) . DIRECTORY_SEPARATOR . $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Retourne l'URL web d'un fichier uploadé
     * 
     * Cette méthode construit l'URL relative pour accéder au fichier depuis le web.
     * Utile pour afficher des images ou créer des liens de téléchargement.
     * 
     * @param string $filename Nom du fichier (stocké en base)
     * @return string|null URL relative vers le fichier ou null si erreur de config
     */
    public static function getUploadedFileUrl(string $filename): ?string
    {
        try {
            $uploadConfig = Config::get('upload');
            $fullPath = $uploadConfig['directory'];
            
            // Extraire seulement le nom du dossier final (ex: "uploads")
            $folderName = basename($fullPath);
            
            return $folderName . '/' . $filename;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Retourne la taille formatée d'un fichier uploadé
     * 
     * Cette méthode construit automatiquement le chemin du fichier à partir
     * de la configuration d'upload et retourne sa taille formatée.
     * 
     * @param string $filename Nom du fichier (stocké en base)
     * @return string Taille formatée (ex: "1.5 MB") ou "0 bytes" si fichier introuvable
     */
    public static function getUploadedFileSize(string $filename): string
    {
        $filePath = self::getUploadedFilePath($filename);
        if ($filePath === null) {
            return '0 bytes';
        }
        return self::humanFileSize($filePath);
    }

    /**
     * Normalise un chemin en supprimant les séparateurs finaux
     * 
     * @param string $path Chemin à normaliser
     * @return string Chemin normalisé
     */
    private static function normalizePath(string $path): string
    {
        return rtrim($path, '/\\');
    }
}
