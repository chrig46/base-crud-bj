<?php

namespace App;

/**
 * Classe Messages - Gestionnaire de messages centralisé
 *
 * Cette classe centralise tous les messages d'erreur, de succès et d'information
 * de l'application. Elle permet de modifier facilement tous les textes depuis un
 * seul endroit et facilite la maintenance ou la traduction future.
 *
 * Utilisation :
 * echo Messages::get('upload_success');
 * echo Messages::get('crud_error');
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */
class Messages
{
    /** @var array Tableau des messages par catégorie */
    private static array $messages = [
        // Messages généraux
        'success' => "Opération réalisée avec succès.",
        'error' => "Une erreur est survenue.",

        // Messages d'upload de fichiers
        'upload_success' => "Fichier uploadé avec succès.",
        'upload_error' => "Erreur lors de l'upload du fichier.",
        'file_too_large' => "Le fichier est trop volumineux.",
        'file_type_not_allowed' => "Type de fichier non autorisé.",
        'file_not_found' => "Fichier non trouvé.",
        
        // Messages d'authentification
        'login_failed' => "Identifiants incorrects.",
        'login_success' => "Connexion réussie.",
        'logout_success' => "Déconnexion réussie.",
        
        // Messages CRUD (Create, Read, Update, Delete)
        'create_success' => "Ajout réussi.",
        'update_success' => "Modification réussie.",
        'delete_success' => "Suppression réussie.",
        'crud_error' => "Erreur lors de l'opération.",

        // Messages de base de données
        'db_connection_error' => "Erreur de connexion à la base de données.",
        'db_query_error' => "Erreur lors de l'exécution de la requête.",
        
        // Messages de validation
        'validation_error' => "Données invalides.",
        'required_field' => "Ce champ est obligatoire.",
        'invalid_email' => "Format d'email invalide."
    ];

    /**
     * Récupère un message par sa clé
     *
     * @param string $key Clé du message à récupérer
     * @return string Message correspondant ou message d'erreur par défaut
     */
    public static function get(string $key): string
    {
        return self::$messages[$key] ?? self::$messages['error'];
    }
}
