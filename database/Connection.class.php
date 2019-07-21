<?php

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/

namespace Util\Config\Conn;

require_once 'LoadConfig.class.php';

use phpseclib\Net\SSH2;
use Util\Config;
use \PDO;
use Exception;
use Util\Functions;

class Connection
{
    private static $conn;

    public static function conectar($db_config = 'ConfigConnectionWizard')
    {

        $file = file_get_contents(dirname(__DIR__) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . $db_config . ".json");

        $configJson = json_decode($file, true);

        $host = $configJson['host'];
        $port = $configJson['port'];
        $name = $configJson['name'];
        $user = $configJson['user'];
        $pass = $configJson['pass'];
        $type = $configJson['type'];

        /*
        try {
            // each database driver has a different instantiation process
            switch ($type) {
                case 'pgsql':
                    $port = $port ? $port : '5432';
                    $conn = new PDO("pgsql:dbname={$name};user={$user}; password={$pass};host=$host;port={$port}");
                    break;
                case 'mysql':
                    $port = $port ? $port : '3306';
                    $conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass);

                    // $conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                    break;
                case 'sqlite':
                    $conn = new PDO("sqlite:{$name}");
                    $conn->query('PRAGMA foreign_keys = ON'); // referential integrity must be enabled
                    break;

            }
        } catch (PDOException $e) {
            if ($e->getCode() == 1049) {
                echo "Erro no banco de dados.";
            } else {
                echo $e->getMessage();
            }
        }
        */
        try {
            switch ($type) {
                case 'pgsql':
                    $port = $port ? $port : '5432';
                    $conn = new \PDO("pgsql:dbname={$name};user={$user}; password={$pass};host=$host;port={$port}");
                    break;
                case 'mysql':
                    $conn = $type . ":host=" . $host . ";dbname=" . $name;
                    $conn = new \PDO($conn, $user, $pass);
                    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);;
                    break;
                case 'sqlite':
                    //  $nameDB = __DIR__ . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $name);
                    $conn = new \PDO("sqlite:{$name}");
                    $conn->query('PRAGMA foreign_keys = ON'); // referential integrity must be enabled
                    break;
                default:
                    throw new Exception('Driver not found' . ': ' . $type);
                    break;
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 1049) {
                echo "Erro no banco de dados.";
            } else {
                echo $e->getMessage();
            }
        }
        return $conn;
    }

    public function connect()
    {
        $config = Config\LoadConfig::get();
        $type = $config->getType();
        $host = $config->getHost();
        $port = $config->getPort();
        $name = $config->getName();
        $user = $config->getUser();
        $pass = $config->getPass();

        switch ($type) {

            case "pgsql":

                $port = $port ? $port : "5432";
                $pdo = new \PDO(
                    "{$type}:host={$host};port={$port};dbname={$name};", $user, $pass
                );

                break;

            case "mysql":

                $port = $port ? $port : "3306";
                $pdo = new \PDO(
                    "{$type}:host={$host};port={$port};dbname={$name};", $user, $pass,
                    [PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"]
                );

                break;
        }

        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    public static function get()
    {
        if (null === static::$conn) {
            static::$conn = new static();
        }

        return static::$conn;
    }

    protected function __construct()
    {
    }

    private function __clone()
    {
    }

    public static function checkConn($configDB = null)
    {

        if (!empty($configDB)) {

            try {
                $check = false;

                $config = Config\LoadConfig::get($configDB);
                $databaseName = $config->getName();

                $conn = Connection::conectar($configDB);
                $stmt = $conn->prepare("SELECT 1 FROM pg_database WHERE datname = :database;");

                $stmt->execute([
                    ':database' => $databaseName
                ]);

                $stmt->execute();

                if (!empty(count($stmt->fetchAll()))) {
                    $check = true;
                }

                return $check;

                $stmt->close();

            } catch (Exception $e) {
                return 0;
            }
        } else {
            try {
                $objConfigSSH = Functions::getConfigSSH();

                $ssh = new SSH2("" . $objConfigSSH->host . "");

                if (!$ssh->login("" . $objConfigSSH->user . "", "" . $objConfigSSH->password . "")) {
                    throw new \Exception('Login Failed in SSH');
                }

                $ssh->disconnect();

                return 1;

            } catch (\Exception $e) {
                return 0;
            }
        }


    }

    private function __wakeup()
    {
    }
}
