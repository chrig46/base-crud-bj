<?php

namespace App;

use Throwable;

/**
 * Classe Config - Gestionnaire de configuration
 * 
 * Cette classe permet de charger et de mettre en cache les fichiers de configuration
 * depuis le dossier /config. Elle utilise un système de cache statique pour éviter
 * de recharger plusieurs fois le même fichier de configuration.
 * 
 * Utilisation :
 * $dbConfig = Config::get('database'); // Charge config/database.php
 * 
 * @author Chrigene Vodounon
 * @version 1.0
 */
class Config
{
    /** @var array Cache statique pour stocker les configurations déjà chargées */
    private static array $configs = [];

    /**
     * Récupère une configuration depuis un fichier
     * 
     * Cette méthode charge un fichier de configuration depuis le dossier /config
     * et le met en cache pour les prochains appels. Le fichier doit retourner un array.
     * 
     * @param string $file Nom du fichier de configuration (sans extension .php)
     * @return array Tableau de configuration
     * @throws \Exception Si le fichier de configuration n'existe pas
     * 
     * @example Config::get('database') // Charge config/database.php
     */
    public static function get(string $file): array
    {
        // Vérifier si la configuration est déjà en cache
        if (isset(self::$configs[$file])) {
            return self::$configs[$file];
        }

        // Construire le chemin vers le fichier de configuration
        $configPath = __DIR__ . '/../config/' . $file . '.php';

        // Vérifier que le fichier existe
        if (!file_exists($configPath)) {
            throw new \Exception("Le fichier de configuration '{$file}.php' n'existe pas dans le dossier config/");
        }

        // Charger et mettre en cache la configuration
        self::$configs[$file] = require $configPath;

        return self::$configs[$file];
    }

    /**
     * Récupère les informations d'upload formatées pour l'affichage
     * 
     * Cette méthode retourne une chaîne formatée avec les extensions autorisées
     * et la taille maximale des fichiers, prête pour l'affichage dans les formulaires.
     * 
     * @return string Informations d'upload formatées (ex: "JPG, PNG - Max 2 MB")
     * 
     * @example
     * echo Config::getUploadInfo(); // "JPG, PNG, PDF - Max 5 MB"
     */
    public static function getUploadInfo(): string
    {
        try {
            $uploadConfig = self::get('upload');
            $extensions = strtoupper(implode(', ', $uploadConfig['allowed_extensions']));
            $maxSize = Helper::formatBytes((int)$uploadConfig['max_size']);
            return "$extensions - Max $maxSize";
        } catch (Throwable $e) {
            return 'Max 2 MB';
        }
    }

    /**
     * Efface le cache de configuration
     * 
     * Cette méthode vide le cache statique des configurations, forçant
     * le rechargement des fichiers lors des prochains appels à get().
     * Utile pour les tests unitaires ou le rechargement dynamique.
     * 
     * @return void
     * 
     * @example
     * Config::clearCache();
     * // Les prochains appels à Config::get() rechargeront les fichiers
     */
    public static function clearCache(): void
    {
        self::$configs = [];
    }
}