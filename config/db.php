<?php
// Simple mysqli database settings.
define('DB_HOST', 'localhost');
define('DB_NAME', 'SCAPMS');
define('DB_USER', 'root');
define('DB_PASS', '');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

function get_db(): mysqli
{
    static $db = null;

    if ($db === null) {
        try {
            $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $db->set_charset('utf8mb4');
        } catch (mysqli_sql_exception $exception) {
            die('Database connection failed: ' . htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8'));
        }
    }

    return $db;
}

/**
 * Keeps the existing project pages working while the actual connection is mysqli.
 * New code should use get_db() directly.
 */
function get_pdo(): MysqliDatabase
{
    static $database = null;

    if ($database === null) {
        $database = new MysqliDatabase(get_db());
    }

    return $database;
}

class MysqliDatabase
{
    public function __construct(private mysqli $connection)
    {
    }

    public function prepare(string $sql): MysqliStatement
    {
        return new MysqliStatement($this->connection->prepare($sql));
    }

    public function query(string $sql): MysqliResult
    {
        return new MysqliResult($this->connection->query($sql));
    }

    public function lastInsertId(): int
    {
        return (int) $this->connection->insert_id;
    }

    public function beginTransaction(): void
    {
        $this->connection->begin_transaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollBack(): void
    {
        $this->connection->rollback();
    }
}

class MysqliStatement
{
    public function __construct(private mysqli_stmt $statement)
    {
    }

    public function execute(array $values = []): bool
    {
        if ($values !== []) {
            $types = '';
            foreach ($values as $value) {
                $types .= is_int($value) ? 'i' : (is_float($value) ? 'd' : 's');
            }

            $parameters = [$types];
            foreach ($values as $index => $value) {
                $parameters[] = &$values[$index];
            }
            $this->statement->bind_param(...$parameters);
        }

        return $this->statement->execute();
    }

    public function fetch(): ?array
    {
        $result = $this->statement->get_result();
        $row = $result->fetch_assoc();
        return $row ?: null;
    }

    public function fetchAll(): array
    {
        $result = $this->statement->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchColumn(int $column = 0): mixed
    {
        $row = $this->fetch();
        return $row === null ? false : array_values($row)[$column];
    }
}

class MysqliResult
{
    public function __construct(private mysqli_result $result)
    {
    }

    public function fetch(): ?array
    {
        $row = $this->result->fetch_assoc();
        return $row ?: null;
    }

    public function fetchAll(): array
    {
        return $this->result->fetch_all(MYSQLI_ASSOC);
    }

    public function fetchColumn(int $column = 0): mixed
    {
        $row = $this->fetch();
        return $row === null ? false : array_values($row)[$column];
    }
}
