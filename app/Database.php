<?php

namespace App;

require_once 'Config.php';

use PDO;
use PDOException;

/**
 * Classe Database - Gestionnaire de connexion à la base de données
 * 
 * Cette classe gère la connexion PDO à une base de données MySQL en utilisant
 * le pattern Singleton pour éviter les connexions multiples. Elle charge
 * automatiquement la configuration depuis le fichier config/database.php.
 * 
 * Utilisation :
 * $pdo = Database::connectTo();
 * 
 * @author Chrigene Vodounon
 * @version 1.0
 */
class Database
{
    /** @var \PDO|null Instance PDO partagée (pattern Singleton) */
    private static ?\PDO $pdo = null;

    /**
     * Établit ou récupère la connexion à la base de données
     * 
     * Cette méthode utilise le pattern Singleton pour s'assurer qu'une seule
     * connexion PDO existe. Elle charge la configuration depuis config/database.php
     * et établit une connexion MySQL avec gestion d'erreurs.
     * 
     * @return \PDO Instance de connexion PDO
     * @throws \Exception Si la configuration est introuvable ou la connexion échoue
     * 
     * @example 
     * $pdo = Database::connectTo();
     * $stmt = $pdo->prepare("SELECT * FROM users");
     */
    public static function connectTo(): \PDO
    {
        // Si la connexion existe déjà, la retourner (pattern Singleton)
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        // Charger la configuration de la base de données
        $config = Config::get('database');

        // Établir la connexion PDO avec gestion d'erreurs
        try {
            // Construction du DSN (Data Source Name) pour MySQL
            $dsn = "mysql:host={$config['hostname']};dbname={$config['dbname']};port={$config['port']};charset={$config['charset']}";
            
            // Création de la connexion PDO avec options
            self::$pdo = new \PDO($dsn, $config['username'], $config['password'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // Exceptions pour les erreurs
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // Fetch associatif par défaut
                \PDO::ATTR_EMULATE_PREPARES => false // Vraies requêtes préparées
            ]);

            return self::$pdo;

        } catch (\PDOException $e) {
            throw new \Exception("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Ferme la connexion à la base de données
     * 
     * Cette méthode ferme explicitement la connexion PDO en remettant
     * la propriété statique à null. Utile pour libérer les ressources
     * dans les scripts long-running ou pour les tests unitaires.
     * 
     * @return void
     * 
     * @example
     * Database::disconnect();
     * // La prochaine connexion créera une nouvelle instance PDO
     */
    public static function disconnect(): void
    {
        self::$pdo = null;
    }
}