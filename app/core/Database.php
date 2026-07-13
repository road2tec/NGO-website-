<?php
/**
 * Database - PDO singleton with helper methods.
 * All queries use prepared statements.
 */
class Database
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo === null) {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
            try {
                self::$pdo = new PDO($dsn, DB_USER, DB_PASS, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                if (APP_ENV === 'development') {
                    die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
                }
                die('Database connection failed. Please check config/config.php');
            }
        }
        return self::$pdo;
    }

    /** Run a query, return the PDOStatement */
    public static function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /** Fetch all rows */
    public static function all(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /** Fetch one row or null */
    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Fetch a single scalar value */
    public static function value(string $sql, array $params = [])
    {
        return self::query($sql, $params)->fetchColumn();
    }

    /** Insert helper: table + assoc data, returns last insert id */
    public static function insert(string $table, array $data): int
    {
        $cols = array_keys($data);
        $sql  = "INSERT INTO `$table` (`" . implode('`,`', $cols) . "`) VALUES ("
              . implode(',', array_fill(0, count($cols), '?')) . ")";
        self::query($sql, array_values($data));
        return (int) self::pdo()->lastInsertId();
    }

    /** Update helper: table + data + where clause with params */
    public static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = implode(',', array_map(fn($c) => "`$c`=?", array_keys($data)));
        $stmt = self::query("UPDATE `$table` SET $set WHERE $where",
            array_merge(array_values($data), $whereParams));
        return $stmt->rowCount();
    }

    /** Delete helper */
    public static function delete(string $table, string $where, array $params = []): int
    {
        return self::query("DELETE FROM `$table` WHERE $where", $params)->rowCount();
    }
}
