<?php

namespace App;

/**
 * Classe Security - Utilitaires de sécurité
 *
 * Cette classe fournit des méthodes statiques essentielles pour sécuriser une application PHP :
 * protection XSS, nettoyage d'entrées, hachage de mots de passe et validations de base.
 * Elle utilise les fonctions natives PHP pour une sécurité optimale sans dépendances.
 *
 * Utilisation :
 * $safe = Security::escape($userInput);
 * $hash = Security::hashPassword('motdepasse');
 * $valid = Security::isValidEmail($email);
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */
class Security
{
    /**
     * Échappe les caractères spéciaux pour éviter les attaques XSS
     *
     * @param string $data Données à échapper
     * @return string Données sécurisées pour l'affichage HTML
     */
    public static function escape(string $data): string
    {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }


    /**
     * Hache un mot de passe de façon sécurisée
     *
     * @param string $password Mot de passe en clair
     * @return string Hash sécurisé du mot de passe
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Vérifie un mot de passe contre son hash
     *
     * @param string $password Mot de passe en clair
     * @param string $hash Hash stocké en base
     * @return bool True si le mot de passe correspond
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Vérifie qu'une donnée n'est pas vide
     *
     * @param string $data Donnée à vérifier
     * @return bool True si la donnée n'est pas vide
     */
    public static function isNotEmpty(string $data): bool
    {
        return !empty(trim($data));
    }

    /**
     * Valide un format d'email
     *
     * @param string $email Adresse email à valider
     * @return bool True si l'email est valide
     */
    public static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
}
