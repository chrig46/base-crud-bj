<?php
/**
 * Page d'information Open Source
 *
 * Projet focalisé sur un set POO simple pour aller vite à l'examen.
 * La partie procédurale est en cours et ouverte aux contributions.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Projet Open Source - Informations</title>
    <link href="bootstrap-5.3.7-dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container py-5">
        <h1 class="mb-3">Projet Open Source</h1>
        <p class="text-muted mb-4">
            Ce projet propose un <strong>set POO simple et réutilisable</strong> pour réaliser rapidement
            les exercices de l'examen. L'objectif est d'offrir une base claire, légère et facilement
            adaptable, sans fonctionnalités superflues.
        </p>

        <div class="mb-4">
            <h5>Pourquoi cette approche ?</h5>
            <ul class="mb-0">
                <li>Se concentrer sur l'essentiel pour l'examen (CRUD, PDO, structure propre).</li>
                <li>Code moderne et lisible, simple à étendre.</li>
                <li>Éviter la complexité inutile pour gagner du temps.</li>
            </ul>
        </div>

        <div class="mb-4">
            <h5>Partie procédurale</h5>
            <p class="mb-2">
                La version procédurale est <strong>en cours</strong>. Elle sera publiée dès qu'elle atteindra un état stable.
            </p>
            <p class="mb-0">
                Contributions bienvenues sur les points suivants : exemples procéduraux, corrections, tests et documentation.
            </p>
        </div>

        <div class="mb-4">
            <h5>Comment contribuer</h5>
            <ol class="mb-0">
                <li>Forkez le projet.</li>
                <li>Créez une branche : <code>feature/procedural-...</code></li>
                <li>Ajoutez vos changements (fonctions, pages, docs simples).</li>
                <li>Soumettez une Pull Request avec une description claire.</li>
            </ol>
        </div>

        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-outline-secondary">Retour à l'accueil</a>
            <a href="demo/demo.php" class="btn btn-dark">Voir la démo POO</a>
        </div>
    </div>

    <script src="bootstrap-5.3.7-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
