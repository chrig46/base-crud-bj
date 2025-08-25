<?php

namespace App;

/**
 * Classe Helper - Méthodes utilitaires génériques
 *
 * Cette classe contient des méthodes statiques utilitaires qui peuvent être utilisées
 * dans différentes parties de l'application : formatage de données, conversions,
 * calculs, etc. Elle évite la duplication de code et centralise les fonctions communes.
 *
 * Utilisation :
 * $size = Helper::formatBytes(2097152); // "2 MB"
 * $slug = Helper::slugify("Mon Titre"); // "mon-titre"
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */
class Helper
{
    /**
     * Formate une valeur en octets en taille lisible par l'humain
     *
     * @param int $bytes Nombre d'octets
     * @return string Taille formatée (ex: "2 MB", "1.5 KB")
     */
    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        }
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        if ($bytes > 1) {
            return $bytes . ' bytes';
        }
        if ($bytes === 1) {
            return '1 byte';
        }
        return '0 bytes';
    }


    /**
     * Tronque un texte à une longueur donnée
     *
     * @param string $text Texte à tronquer
     * @param int $length Longueur maximale
     * @param string $suffix Suffixe à ajouter (ex: "...")
     * @return string Texte tronqué
     */
    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length - strlen($suffix)) . $suffix;
    }

    /**
     * Formate une date en français
     *
     * @param string $date Date au format Y-m-d H:i:s
     * @param string $format Format de sortie (par défaut : d/m/Y à H:i)
     * @return string Date formatée
     */
    public static function formatDate(string $date, string $format = 'd/m/Y à H:i'): string
    {
        try {
            $dateTime = new \DateTime($date);
            return $dateTime->format($format);
        } catch (\Exception $e) {
            return $date; // Retourner la date originale en cas d'erreur
        }
    }


}
