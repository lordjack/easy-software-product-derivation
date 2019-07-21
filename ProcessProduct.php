<?php
/**
 * Created by PhpStorm.
 * User: Jackson Meires
 * Date: 03/02/2018
 * Time: 09:05
 */

namespace Util;

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/lib/Functions.php';
require_once __DIR__ . '/lib/Git.php';
require_once(__DIR__ . '/lib/git-php-master/src/GitRepository.php');

use GitWrapper\GitWrapper;
use phpseclib\Net\SSH2;
use Session\Session;

class ProcessProduct
{
    private $ssh;
    private $objConfigSSH;
    private $objConfigDB;
    private $objConfigProduct;
    private static $msg;
    private static $host;
    private static $sudo;
    private static $setTime;

    public function init($jsonProduct)
    {
        $jsonProduct = json_decode($jsonProduct);

        self::$setTime = 600;
        self::$msg = '';

        set_time_limit(self::$setTime);
        ini_set('max_execution_time', self::$setTime);
        ini_set("default_socket_timeout", self::$setTime);
        define('NET_SSH2_LOGGING', SSH2::LOG_COMPLEX);

        $this->objConfigDB = Functions::getConfigDB();
        $this->objConfigProduct = Functions::getConfigProduct($jsonProduct->id);
        Functions::createConfigDBiniProduct((array)$this->objConfigDB);

        $this->connSSH();

    }

    function connSSH()
    {
        try {
            $this->objConfigSSH = Functions::getConfigSSH();

            $this->ssh = new SSH2("" . $this->objConfigSSH->host . "");
            self::$host = $this->objConfigSSH->host;
            self::$sudo = "echo " . $this->objConfigSSH->password . " | sudo -S ";

            if (!$this->ssh->login("" . $this->objConfigSSH->user . "", "" . $this->objConfigSSH->password . "")) {
                throw new \Exception('Login Failed in SSH');
            }

            $this->cloneProduct();

        } catch (\Exception $e) {
            self::$msg .= $e->getMessage();
        }
    }

    static function getHost()
    {
        $objConfigSSH = Functions::getConfigSSH();

        return $objConfigSSH->host;
    }

    static function getDBType()
    {
        $objConfigSSH = Functions::getConfigDB();

        return $objConfigSSH->type;
    }

    function cloneProduct()
    {
        try {
            $log = '';

            $wrapper = new GitWrapper();
            $wrapper->setTimeout(self::$setTime);

            $this->objConfigProduct->name = strtolower($this->objConfigProduct->name);

            $createLog = 'cd ' . $this->objConfigSSH->dir . ' && ' . self::$sudo . ' chmod -R 777 wizard_lps/log';
            $log .= $this->run($createLog);

            $copy = 'cd ' . $this->objConfigSSH->dir . ' && mkdir ' . $this->objConfigProduct->name . ' && ' . self::$sudo . ' chmod -R 777 ' . $this->objConfigProduct->name;
            $log .= $this->run($copy);

            $log .= $wrapper->cloneRepository("" . $this->objConfigProduct->url_repository . "", '' . $this->objConfigSSH->dir . $this->objConfigProduct->name . '');

            if (file_exists("log/git.log")) {
                unlink("log/git.log");
            }
            if (file_exists("log/restore_db.log")) {
                unlink("log/restore_db.log");
            }
            if (!empty($this->objConfigProduct->name_file_database)) {
                $this->dumpDB();
            }

            $objSession = Session::getInstance();
            if ($objSession->__get('code_generator') == 2) {
                $this->copyFilesGenerator();
            }

            clearstatcache();
            file_put_contents("log/git.log", $log);

        } catch (Exception $e) {
            self::$msg .= $e->getMessage();
        }
    }

    function copyFilesGenerator()
    {
        $log = '';
        ##### copy page #####
        if ($this->objConfigProduct->name == "bloglps") {
            $dir_app_product = $this->objConfigSSH->dir . $this->objConfigProduct->name . '/admin/app/';
        } else {
            $dir_app_product = $this->objConfigSSH->dir . $this->objConfigProduct->name . '/app/';
        }

        $db_conn = 'cd ' . $this->objConfigSSH->dir . ' && ' . self::$sudo . ' cp -ar wizard_lps/code_generator/app/files/* ' . $dir_app_product;
        $log .= $this->run($db_conn);
        ##### copy page #####

        clearstatcache();
        file_put_contents("log/copy_files_generator.log", $log);
    }

    function dumpDB()
    {
        try {
            if (!empty($this->objConfigDB->name)) {

                $log = '';
                $sqlFileNameDump = $this->objConfigProduct->name_file_database; // dump_db_name.sql

                if ($this->objConfigDB->type == 'pgsql') {

                    if (empty($this->objConfigDB->docker_container)) {

                        ############# Commands PSQL #############
                        $pgsqlPassword = " export PGPASSWORD=" . $this->objConfigDB->pass . "";
                        $psqlCommand = $pgsqlPassword . " && psql -h " . $this->objConfigDB->host . " -U " . $this->objConfigDB->user . " -d " . $this->objConfigDB->name . " -f ";

                        $dirFileWizard = "cd " . $this->objConfigSSH->dir . "wizard_lps" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "scripts" . DIRECTORY_SEPARATOR . "";
                        $dirFileProduct = "cd " . $this->objConfigSSH->dir . $this->objConfigProduct->name . DIRECTORY_SEPARATOR . $this->objConfigProduct->dir_file_database;
                        $commandDump = $psqlCommand . $sqlFileNameDump . "";
                        ############# Commands PSQL #############

                        $db_restore = $dirFileProduct . " && " . self::$sudo . " chmod 777 " . $sqlFileNameDump . " && " . $commandDump;
                        $log .= $this->run($db_restore);

                        $db_create = $dirFileWizard . " && " . self::$sudo . " chmod 777 create_db_config_product_pgsql.sql | " . $psqlCommand . " create_db_config_product_pgsql.sql";
                        $log .= $this->run($db_create);

                        $db_insert = $dirFileWizard . " && " . self::$sudo . " chmod 777 insert_config_product_pgsql.sql | " . $psqlCommand . " insert_config_product_pgsql.sql";
                        $log .= $this->run($db_insert);

                        $db_update = $dirFileWizard . " && " . self::$sudo . " chmod 777 enable_pages_config_product_pgsql_ceres.sql | " . $psqlCommand . " enable_pages_config_product_pgsql_ceres.sql";
                        $log .= $this->run($db_update);

                    } else {

                        ############# Docker Commands PSQL #############
                        $psqlCommand = " docker exec -i " . $this->objConfigDB->docker_container . " psql -U " . $this->objConfigDB->user . " -d " . $this->objConfigDB->name . "";
                        $commandDump = " cat " . $sqlFileNameDump . " | " . $psqlCommand;

                        $dirFileWizard = "cd " . $this->objConfigSSH->dir . "wizard_lps" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "scripts" . DIRECTORY_SEPARATOR . "";
                        $dirFileProduct = "cd " . $this->objConfigSSH->dir . $this->objConfigProduct->name . DIRECTORY_SEPARATOR . $this->objConfigProduct->dir_file_database;
                        ############# Docker Commands PSQL #############

                        $db_restore = $dirFileProduct . " && " . self::$sudo . " chmod 777 " . $sqlFileNameDump . " && " . $commandDump;
                        $log .= $this->run($db_restore);

                        $db_create = $dirFileWizard . " && " . self::$sudo . " chmod 777 create_db_config_product_pgsql.sql && cat create_db_config_product_pgsql.sql | " . $psqlCommand;
                        $log .= $this->run($db_create);

                        $db_insert = $dirFileWizard . " && " . self::$sudo . " chmod 777 insert_config_product_pgsql.sql && cat insert_config_product_pgsql.sql | " . $psqlCommand;
                        $log .= $this->run($db_insert);

                        $db_update = $dirFileWizard . " && " . self::$sudo . " chmod 777 enable_pages_config_product_pgsql_ceres.sql && cat enable_pages_config_product_pgsql_ceres.sql | " . $psqlCommand;
                        $log .= $this->run($db_update);
                    }


                } else if ($this->objConfigDB->type == 'mysql') {
                    echo $this->ssh->exec("cd " . $this->objConfigSSH->dir . $this->objConfigProduct->name . "/database/ && sudo mysql -h localhost -U " . $this->objConfigDB->user . "  " . $this->objConfigDB->name . " -f db_teste.sql");

                } else if ($this->objConfigDB->type == 'sqlite') {
                    ############# SQlite Commands #############
                    $dirFileWizard = $this->objConfigSSH->dir . "wizard_lps" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "scripts" . DIRECTORY_SEPARATOR . "";
                    $dirFileProduct = $this->objConfigSSH->dir . $this->objConfigProduct->name . DIRECTORY_SEPARATOR . $this->objConfigProduct->dir_file_database;

                    $sqLiteCommand = " sqlite3 " . $dirFileProduct . $sqlFileNameDump;
                    ############# SQlite Commands #############

                    $db_restore = "cd " . $dirFileProduct . " && " . self::$sudo . " chmod 777 " . $sqlFileNameDump;
                    $log .= $this->run($db_restore);

                    $db_create = "cd " . $dirFileWizard . " && " . self::$sudo . " chmod 777 create_db_config_product_sqlite.sql && cat create_db_config_product_sqlite.sql | " . $sqLiteCommand;
                    $log .= $this->run($db_create);

                    $db_insert = "cd " . $dirFileWizard . " && " . self::$sudo . " chmod 777 insert_config_product_sqlite.sql && cat insert_config_product_sqlite.sql | " . $sqLiteCommand;
                    $log .= $this->run($db_insert);

                }

                if ($this->objConfigProduct->name == "cereslps") {
                    ##### copy page #####
                    $dir_config_product = $this->objConfigSSH->dir . $this->objConfigProduct->name . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;

                    $db_conn = 'cd ' . $this->objConfigSSH->dir . ' && ' . self::$sudo . ' mv -f wizard_lps' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'pg_ceres.ini ' . $dir_config_product;
                    $log .= $this->run($db_conn);
                    ##### copy page #####
                }
                clearstatcache();
                file_put_contents("log/restore_db.log", $log);
            }

        } catch (Exception $e) {
            self::$msg .= $e->getMessage();
        }
    }

    function run($command)
    {
        $this->ssh->write($command . "\n");
        $log = $this->ssh->read();

        return $log;
    }

    /**
     * @return string
     */
    public static function getMsg()
    {
        return self::$msg;
    }

    /**
     * set string
     */
    public function setMsg($msg)
    {
        return self::$msg = $msg;
    }

}

$objPP = new ProcessProduct();

if (!empty($_GET['json'])) {
    $objPP->init($_GET['json']);
} else {
    $objPP->setMsg('JSON is null');
}