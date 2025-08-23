# Base CRUD BJ

> **Projet de révision PHP** - Bibliothèque d'outils pour les examens de programmation web

## Description

Base CRUD BJ est une bibliothèque PHP éducative développée dans le contexte académique béninois, conçue pour accélérer le développement lors des examens de programmation web. Il couvre les paradigmes orienté objet et procédural, offrant des fonctionnalités courantes : CRUD, sécurité, gestion de fichiers et interface utilisateur.

Adapté aux contraintes des examens universitaires au Bénin, ce projet se concentre sur l'essentiel pour réussir rapidement les exercices pratiques PHP, que ce soit en OOP ou en procédural.

**Note :** La version procédurale est actuellement en cours de développement et sera disponible prochainement.

## Structure du Projet

```
base-crud-bj/
├── app/                    # Classes OOP principales
│   ├── Alert.php          # Génération d'alertes Bootstrap
│   ├── Config.php         # Gestionnaire de configuration
│   ├── Database.php       # Connexion base de données (Singleton)
│   ├── FileManager.php    # Gestion de fichiers sécurisée
│   ├── Messages.php       # Messages centralisés
│   ├── Model.php          # Modèle CRUD générique
│   └── Security.php       # Fonctions de sécurité
├── demo/                  # Démonstration complète
│   ├── demo.php           # Page d'accueil de la démo
│   ├── setup.sql     # Script SQL avec données d'exemple
│   ├── demo_etudiants_*.php # Gestion des étudiants
│   └── demo_filieres.php  # Gestion des filières
├── config/                # Configuration
│   ├── database.php       # Configuration base de données
│   └── upload.php         # Configuration upload de fichiers
├── uploads/               # Dossier de stockage des fichiers
├── index.php             # Point d'entrée principal
├── upload.php            # Page de gestion des fichiers
└── README.md             # Cette documentation
```

## Installation et Configuration

### Prérequis
- **PHP 8.0+**
- **MySQL/MariaDB** 
- **Serveur web** (Apache, Nginx, ou serveur intégré PHP)

### Configuration Base de Données

1. **Créer une base de données MySQL** :
```sql
CREATE DATABASE test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. **Configurer la connexion** dans `config/database.php` :
```php
return [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'dbname' => 'test',
    'port' => '3307',
    'charset' => 'utf8'
];
```

### Configuration Upload

Modifiez `config/upload.php` selon vos besoins :
```php
return [
    'directory' => __DIR__ . '/../uploads',
    'allowed_extensions' => ['pdf'],
    'max_size' => 2 * 1024 * 1024 // 2 Mo
];
```

## Utilisation

### Utilisation des Classes

```php
<?php
require_once 'app/Database.php';
require_once 'app/Model.php';
require_once 'app/Security.php';

use App\Database;
use App\Model;
use App\Security;

// Connexion à la base de données
$pdo = Database::connectTo();

// Modèle utilisateur
$userModel = new Model('users');

// Créer un utilisateur
$userData = [
    'nom' => Security::cleanInput($_POST['nom']),
    'email' => Security::cleanInput($_POST['email'])
];
$userModel->create($userData);

// Lire tous les utilisateurs
$users = $userModel->read();

// Fermer la connexion
Database::disconnect();
```

## Fonctionnalités

- **CRUD générique** - Opérations sur toute table avec le modèle Model
- **Connexion sécurisée** - PDO avec pattern Singleton et requêtes préparées
- **Sécurité** - Protection XSS, validation des données, hachage des mots de passe
- **Upload de fichiers** - Gestion sécurisée avec validation d'extensions et taille
- **Interface utilisateur** - Alertes Bootstrap et messages centralisés
- **Configuration** - Paramètres centralisés pour base de données et uploads

## Usage Éducatif et Examens

**Base CRUD BJ** est spécifiquement conçu comme outil d'apprentissage et de révision pour les examens de programmation PHP dans le contexte académique béninois.

### Utilisation autorisée en examen
- ✅ **Ressource locale** - Peut être utilisé hors ligne sur votre PC personnel
- ✅ **Code source personnel** - Développé comme outil d'étude individuel
- ✅ **Documentation intégrée** - Commentaires et exemples inclus
- ✅ **Adaptation libre** - Modifiable selon les besoins de l'exercice

### Responsabilité de l'utilisateur
- Vérifier les règles spécifiques de votre institution
- S'assurer de la conformité avec le règlement d'examen
- Utiliser comme base d'apprentissage, non comme solution finale

**Disclaimer :** L'auteur n'est pas responsable de l'usage fait de cet outil. Chaque utilisateur doit s'assurer de respecter les règles de son institution académique.

## Auteur

**Chrigene Vodounon** - Version 1.0
