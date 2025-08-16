<?php
class Database
{
    private $host;
    private $database;
    private $username;
    private $password;
    private $connection;

    public function __construct()
    {
        $this->loadEnv();
    }

    // Read the .env file and get database settings
    private function loadEnv()
    {
        $envFile = __DIR__ . '/.env';

        if (!file_exists($envFile)) {
            throw new Exception('.env file not found');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                switch ($key) {
                    case 'DB_HOST':
                        $this->host = $value;
                        break;
                    case 'DB_NAME':
                        $this->database = $value;
                        break;
                    case 'DB_USER':
                        $this->username = $value;
                        break;
                    case 'DB_PASS':
                        $this->password = $value;
                        break;
                }
            }
        }
    }

    // Try to connect to the database
    public function connect()
    {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";

            $this->connection = new PDO($dsn, $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);

            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // Get the connection object
    public function getConnection()
    {
        return $this->connection;
    }
}
?>