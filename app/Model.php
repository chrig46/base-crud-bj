<?php

namespace App;

use Exception;

/**
 * Classe Model - Modèle générique pour opérations CRUD
 *
 * Cette classe fournit des méthodes génériques pour manipuler n'importe quelle table
 * de la base de données (Create, Read, Update, Delete). Elle utilise PDO et la connexion
 * centralisée de Database. Le nom de la table et la clé primaire sont configurables.
 *
 * Utilisation :
 * $userModel = new Model('users', 'id');
 * $userModel->create(['nom' => 'Jean']);
 * $users = $userModel->read();
 *
 * @author Chrigene Vodounon
 * @version 1.0
 */
class Model
{
    /** @var \PDO Connexion PDO partagée */
    private static ?\PDO $pdo = null;
    /** @var string Nom de la table */
    private string $table;
    /** @var string Nom de la clé primaire */
    private string $primaryKey;

    /**
     * Constructeur du modèle générique
     *
     * @param string $table Nom de la table
     * @param string $primaryKey Nom de la clé primaire (obligatoire)
     * @throws Exception
     */
    public function __construct(string $table, string $primaryKey)
    {
        $this->table = $table;
        $this->primaryKey = $primaryKey;
        if (self::$pdo === null) {
            self::$pdo = Database::connectTo();
        }
    }

    /**
     * Insère une nouvelle ligne dans la table
     *
     * @param array $data Données à insérer (clé => valeur)
     * @return bool Succès ou échec de l'insertion
     */
    public function create(array $data): bool
    {
        $fields = implode(", ", array_keys($data));
        $values = ":" . implode(", :", array_keys($data));
        $sql = "INSERT INTO {$this->table} ($fields) VALUES ($values)";
        $statement = self::$pdo->prepare($sql);
        return $statement->execute($data);
    }

    /**
     * Récupère une ou toutes les lignes de la table
     *
     * @param int|null $id Valeur de la clé primaire (optionnel)
     * @param string|null $orderBy Clause ORDER BY (ex: "id DESC", "nom ASC") - par défaut "id DESC"
     * @return array Ligne(s) récupérée(s)
     */
    public function read(int $id = null, string $orderBy = null): array
    {
        if ($id) {
            $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :{$this->primaryKey}";
            $statement = self::$pdo->prepare($sql);
            $statement->execute([$this->primaryKey => $id]);
            return $statement->fetch() ?: [];
        } else {
            // Ordre par défaut : plus récents en premier
            $orderBy = $orderBy ?? "{$this->primaryKey} DESC";
            $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy}";
            $statement = self::$pdo->query($sql);
            return $statement->fetchAll();
        }
    }

    /**
     * Met à jour une ligne dans la table
     *
     * @param int $id Valeur de la clé primaire
     * @param array $data Données à mettre à jour
     * @return bool Succès ou échec de la mise à jour
     */
    public function update(int $id, array $data): bool {
        $fields = "";
        foreach ($data as $field => $value) {
            $fields .= "{$field} = :{$field}, ";
        }
        $fields = rtrim($fields, ", ");
        $sql = "UPDATE {$this->table} SET {$fields} WHERE {$this->primaryKey} = :{$this->primaryKey}";
        $statement = self::$pdo->prepare($sql);
        $data[$this->primaryKey] = $id;
        return $statement->execute($data);
    }

    /**
     * Supprime une ligne de la table
     *
     * @param int $id Valeur de la clé primaire
     * @return bool Succès ou échec de la suppression
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :{$this->primaryKey}";
        $statement = self::$pdo->prepare($sql);
        return $statement->execute([$this->primaryKey => $id]);
    }

    /**
     * Exécute une requête SQL personnalisée
     *
     * @param string $sql Requête SQL
     * @param array $params Paramètres de la requête
     * @param bool $fetchData Retourner les résultats (par défaut true)
     * @return array|bool Résultat de la requête ou booléen
     */
    public function query(string $sql, array $params = [], bool $fetchData = true): array|bool
    {
        $statement = self::$pdo->prepare($sql);
        $result = $statement->execute($params);
        if ($fetchData) {
            return $statement->fetchAll();
        } else {
            return $result;
        }
    }
}