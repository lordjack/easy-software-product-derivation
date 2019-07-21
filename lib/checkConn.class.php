<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

//require "../vendor/autoload.php";
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Functions.php';

use Util\Config\Conn;
use Util\Functions;

class checkConn
{
    function __construct()
    {
        if (!empty($_POST['type'])) {

            Util\Functions::createConfigDB($_POST);

            $check = Conn\Connection::checkConn('configDB');

            echo $check;
        }else{
            Util\Functions::createConfigSSH($_POST);

            $check = Conn\Connection::checkConn();

            echo $check;
        }
    }
}

new checkConn();
