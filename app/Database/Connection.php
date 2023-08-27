<?php
declare(strict_types=1);

namespace App\Database;

use PDO;

/**
 * @mixin PDO
 */
class Connection {
    protected static Connection|null $instance  = null;

    protected PDO $pdo;

    protected function __construct(string $dsn, ?string $username, ?string $password)
    {
        $opt = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $this->pdo = new PDO($dsn, $username, $password, $opt);
    }

    protected function __clone() { }

    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    public static function getInstance(): Connection
    {
        if (!self::$instance) {
            self::$instance = new static(
                getenv('DB_DSN'),
                getenv('DB_USER'),
                getenv('DB_PASS'),
            );
        }
        return self::$instance;
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->pdo, $method], $args);
    }
}
