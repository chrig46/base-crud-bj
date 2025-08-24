# BASE CRUD BJ

Bibliothèque PHP éducative et open source développée pour accélérer le développement lors des examens pratiques de programmation web au Bénin. Il inclut la plupart des fonctionnalités qui reviennent chaque année. Ce n'est pas un framework. Cependant, l'objectif est aussi de permettre au candidat de se concentrer sur le processus métier de son application.

**Note :** La version actuelle est écrite uniquement en programmation orientée objet. Une version procédurale est prévue. Toute contribution est la bienvenue.

## Configuration

**Prérequis :** PHP 8.0+, MySQL/MariaDB et un serveur web

### Configuration de la base de données

Configurez la connexion dans `config/database.php` :
```php
return [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname' => 'test',
    'port' => '3307',    // ou 3306
    'charset' => 'utf8'
];
```

### Configuration des uploads

Modifiez `config/upload.php` si nécessaire :
```php
return [
    'directory' => __DIR__ . '/../uploads',
    'allowed_extensions' => ['pdf'],
    'max_size' => 2 * 1024 * 1024 // 2 Mo
];
```

## Utilisation

```php
<?php
require_once 'app/Database.php';
require_once 'app/Model.php';
require_once 'app/Security.php';

use App\Database;
use App\Model;
use App\Security;

// Modèle utilisateur (table 'users' avec clé primaire 'id')
$userModel = new Model('users', 'id');

// Créer un utilisateur
$userData = [
    'nom' => Security::cleanInput($_POST['nom']),
    'email' => Security::cleanInput($_POST['email'])
];
$userModel->create($userData);

// Lire tous les utilisateurs
$users = $userModel->read();

// Lire un utilisateur spécifique
$user = $userModel->read(1);

// Modifier un utilisateur
$userModel->update(1, ['nom' => 'Nouveau nom']);

// Supprimer un utilisateur
$userModel->delete(1);
```

## Fonctionnalités

- CRUD complet (Create, Read, Update, Delete)
  - Opérations automatiques sur toute table
  - Requêtes SQL personnalisees et sécurisées
- Sécurité et validation des données
  - Protection contre les attaques XSS
  - Nettoyage automatique des entrées utilisateur
  - Validation des champs obligatoires
  - Hachage sécurisé des mots de passe
- Configuration de la base de données et d'upload de fichiers
  - Paramètres centralisés dans config/
  - Validation des extensions et tailles de fichiers
- Interface utilisateur Bootstrap
  - Messages de succès, erreur et avertissement
  - Design responsive et moderne
- Fonctions utilitaires (Helper)
  - Formatage de données (tailles, dates, texte)
  - Génération de tokens et validation d'emails
  - Création de slugs et troncature de texte

---

**Outil pédagogique open source** - Respectez les règles de votre institution
