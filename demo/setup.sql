-- Script SQL pour la demo Étudiants + Filières
-- Simple et efficace pour l'examen
-- Crée automatiquement la base de données et toutes les tables

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE test;

-- Table des filières
CREATE TABLE IF NOT EXISTS filieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Table des étudiants
CREATE TABLE IF NOT EXISTS etudiants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    age INT NOT NULL,
    sexe ENUM('M', 'F') NOT NULL,
    filiere_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (filiere_id) REFERENCES filieres(id) ON DELETE SET NULL
);

-- Table des fichiers uploadés
CREATE TABLE IF NOT EXISTS fichiers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,            -- nom de fichier stocké sur le serveur
    nom_original VARCHAR(255) NOT NULL,   -- nom original fourni par l'utilisateur
    titre VARCHAR(255) NOT NULL,          -- titre saisi dans le formulaire
    taille INT NOT NULL,                  -- taille du fichier en octets
    uploaded_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Données d'exemple pour les filières
INSERT INTO filieres (nom, description) VALUES
('Informatique', 'Sciences informatiques et programmation'),
('Mathématiques', 'Mathématiques appliquées et théoriques'),
('Physique', 'Physique fondamentale et appliquée'),
('Chimie', 'Chimie générale et analytique');

-- Données d'exemple pour les étudiants
INSERT INTO etudiants (nom, prenom, email, age, sexe, filiere_id) VALUES
('Vodounon', 'Chrigene', 'chrigene@univ.com', 22, 'M', 1),
('Dupont', 'Jean', 'jean.dupont@univ.com', 20, 'M', 1),
('Martin', 'Marie', 'marie.martin@univ.com', 21, 'F', 2),
('Bernard', 'Paul', 'paul.bernard@univ.com', 19, 'M', 3),
('Durand', 'Sophie', 'sophie.durand@univ.com', 23, 'F', 2),
('Moreau', 'Pierre', 'pierre.moreau@univ.com', 20, 'M', 4);
