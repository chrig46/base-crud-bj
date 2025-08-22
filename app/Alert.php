<?php

namespace App;

/**
 * Classe Alert - Générateur d'alertes Bootstrap
 *
 * Cette classe génère des alertes HTML au format Bootstrap pour afficher
 * des messages de succès, d'erreur, d'information ou d'avertissement.
 * Elle s'intègre parfaitement avec la classe Messages pour un affichage cohérent.
 *
 * Utilisation :
 * echo Alert::success('Opération réussie');
 * echo Alert::error(Messages::get('crud_error'));
 * echo Alert::info('Information importante');
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */
class Alert
{
    /**
     * Génère une alerte de succès (verte)
     *
     * @param string $message Message à afficher
     * @param bool $dismissible Alerte fermable (par défaut true)
     * @return string HTML de l'alerte Bootstrap
     */
    public static function success(string $message, bool $dismissible = true): string
    {
        return self::generateAlert('success', $message, $dismissible);
    }

    /**
     * Génère une alerte d'erreur (rouge)
     *
     * @param string $message Message à afficher
     * @param bool $dismissible Alerte fermable (par défaut true)
     * @return string HTML de l'alerte Bootstrap
     */
    public static function error(string $message, bool $dismissible = true): string
    {
        return self::generateAlert('danger', $message, $dismissible);
    }

    /**
     * Génère une alerte d'information (bleue)
     *
     * @param string $message Message à afficher
     * @param bool $dismissible Alerte fermable (par défaut true)
     * @return string HTML de l'alerte Bootstrap
     */
    public static function info(string $message, bool $dismissible = true): string
    {
        return self::generateAlert('info', $message, $dismissible);
    }

    /**
     * Génère une alerte d'avertissement (jaune)
     *
     * @param string $message Message à afficher
     * @param bool $dismissible Alerte fermable (par défaut true)
     * @return string HTML de l'alerte Bootstrap
     */
    public static function warning(string $message, bool $dismissible = true): string
    {
        return self::generateAlert('warning', $message, $dismissible);
    }

    /**
     * Génère le HTML d'une alerte Bootstrap
     *
     * @param string $type Type d'alerte (success, danger, info, warning)
     * @param string $message Message à afficher
     * @param bool $dismissible Alerte fermable
     * @return string HTML complet de l'alerte
     */
    private static function generateAlert(string $type, string $message, bool $dismissible): string
    {
        $dismissibleClass = $dismissible ? ' alert-dismissible fade show' : '';
        $closeButton = $dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' : '';
        
        return sprintf(
            '<div class="alert alert-%s%s" role="alert">%s%s</div>',
            $type,
            $dismissibleClass,
            htmlspecialchars($message, ENT_QUOTES, 'UTF-8'),
            $closeButton
        );
    }
}
