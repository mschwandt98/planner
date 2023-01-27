<?php

namespace Planner\Classes;

use mysqli;
use mysqli_result;
use Planner\Enums\Tables;

if (!defined('ROOT')) {
    exit;
}

/**
 * Die Klasse Database stellt eine Verbindung zur Datenbank her und stellt
 * Methoden zum Ausführen von Queries zur Verfügung.
 */
class Database {

    /**
     * Die Singleton-Instanz der Datenbank.
     *
     * @var Database
     */
    private static ?Database $instance = null;

    /**
     * Die Verbindung zur Datenbank.
     *
     * @var mysqli
     */
    private mysqli $connection;

    /**
     * Privater Konstruktor, damit die Klasse nicht instanziiert werden kann.
     */
    private function __construct() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT);
    }

    /**
     * Gibt die Singleton-Instanz der Datenbank zurück.
     *
     * @return Database Die Instanz der Datenbank.
     */
    public static function getInstance(): Database {

        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Führt eine Query aus.
     *
     * @param string $query Die Query, die ausgeführt werden soll.
     *
     * @return mysqli_result|boolean
     */
    public function query(string $query): mysqli_result|bool {
        return $this->connection->query($query);
    }

    /**
     * Führt eine Query mit Parametern aus. Die Parameter werden je nach Typ in
     * der Query mit ? ersetzt.
     *
     * @param string                $query      Die Query mit ? als Platzhalter
     *                                          für die Parameter.
     * @param string|null           $types      Die Typen der Parameter.
     *                                          Mögliche Werte: i, d, s, b.
     *                                          i = integer
     *                                          d = double
     *                                          s = string
     *                                          b = blob
     * @param integer|float|string  ...$params  Die Parameter.
     *
     * @return boolean  Gibt true zurück, wenn die Query erfolgreich ausgeführt
     *                  wurde.
     */
    public function execute_query(string $query, string $types = null, int|float|string ...$params): bool {

        $stmt = $this->connection->prepare($query);

        if ($types !== null) {
            $stmt->bind_param($types, ...$params);
        }

        return $stmt->execute();
    }

    /**
     * Führt ein INSERT-Statement aus und gibt die ID des neuen Datensatzes
     * zurück.
     *
     * @param Tables $table Die Tabelle, in die der Datensatz eingefügt werden
     *                      soll.
     * @param array $data   Die Daten, die eingefügt werden sollen.
     * @param array $format Die Typen der Daten.
     *
     * Beispiel:
     * $data = [
     *     'name' => 'Max Mustermann',
     *     'age' => 20
     * ]
     * $format = [
     *     's',
     *     'i'
     * ]
     *
     * @return int  Die ID des neuen Datensatzes. Falls der Datensatz nicht
     *              eingefügt werden konnte oder die Tabelle keinen
     *              Auto-Inkrement-Schlüssel hat, wird 0 zurückgegeben.
     */
    public function insert(Tables $table, array $data, array $format): int {

        $query = "INSERT INTO $table->value (";
        $query .= implode(', ', array_keys($data));
        $query .= ') VALUES (';
        $query .= implode(', ', array_fill(0, count($data), '?'));
        $query .= ')';

        $this->execute_query($query, implode('', $format), ...array_values($data));

        return $this->connection->insert_id;
    }

    /**
     * Führt ein UPDATE-Statement aus.
     *
     * @param Tables $table         Die Tabelle, in der der Datensatz
     *                              aktualisiert werden soll.
     * @param array $data           Die Daten, die aktualisiert werden sollen.
     * @param array $format         Die Typen der Daten.
     * @param array $where          Die Bedingung, die erfüllt sein muss, damit
     *                              der Datensatz aktualisiert wird.
     * @param array $where_format   Die Typen der Bedingung.
     *
     * @return boolean  Gibt true zurück, wenn der Datensatz aktualisiert wurde.
     *                  Falls der Datensatz nicht aktualisiert werden konnte,
     *                  wird false zurückgegeben.
     */
    public function update(Tables $table, array $data, array $format, array $where, array $where_format): bool {

        $query = "UPDATE $table->value SET ";
        $query .= implode(' = ?, ', array_keys($data));
        $query .= ' = ? WHERE ';
        $query .= implode(' = ? AND ', array_keys($where));
        $query .= ' = ?';

        $params = array_merge(array_values($data), array_values($where));
        $types = implode('', array_merge($format, $where_format));

        return $this->execute_query($query, $types, ...$params);
    }
}
