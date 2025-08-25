# BASE CRUD BJ

Bibliothèque PHP éducative et open source développée pour accélérer le développement lors des examens pratiques de programmation web au Bénin. Il inclut la plupart des fonctionnalités qui reviennent chaque année. Ce n'est pas un framework. Cependant, l'objectif est aussi de permettre au candidat de se concentrer sur le processus métier de son application.

**Note :** La version actuelle est écrite uniquement en programmation orientée objet. Une version procédurale est prévue. Toute contribution est la bienvenue.

## Quickstart

**Prérequis :** PHP 8.0+, MySQL/MariaDB et un serveur web

### 1. Configuration de la base de données

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

### 2. Configuration des uploads (optionnel)

Modifiez `config/upload.php` si nécessaire :
```php
return [
    'directory' => __DIR__ . '/../uploads',
    'allowed_extensions' => ['pdf'],
    'max_size' => 2 * 1024 * 1024 // 2 Mo
];
```

### 3. Personnaliser votre environnement
- **Supprimez librement** les fichiers/dossiers non nécessaires : `demo/`, `index2.php`, `upload.php`
- **Créez votre dossier projet** dans la racine : `mon-projet/`, `gestion-stock/`, etc.
- **Gardez** : `app/`, `config/`, `bootstrap-5.3.7-dist/`, `uploads/`
- **Configurez `index.php`** : Modifiez la variable `$PROJET_ACTUEL` pour pointer vers votre projet

### 4. Commencer votre projet

**Créez votre dossier et fichier principal :**
```php
<?php
// Dans votre nouveau fichier (ex: mon-projet/index.php)
require_once '../app/Database.php';
require_once '../app/Model.php';
require_once '../app/Security.php';

use App\Database;
use App\Model;
use App\Security;

// Votre code ici...
```

**Configurez la redirection dans `index.php` (racine) :**
```php
// Modifiez cette ligne pour pointer vers votre projet
$PROJET_ACTUEL = 'mon-projet/';  // ou 'gestion-stock/' etc.
```

Ainsi, `http://localhost/base-crud-bj/` redirigera automatiquement vers votre projet !

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
    'nom' => trim($_POST['nom']),
    'email' => trim($_POST['email'])
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
  - Protection contre les attaques XSS avec Security::escape()
  - Nettoyage des entrées utilisateur avec trim()
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
  - Troncature de texte pour l'affichage

---

**Outil pédagogique open source** - Respectez les règles de votre institution
