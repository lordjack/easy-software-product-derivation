<?php

namespace Util;

require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

use Features\FeatureModules;
use Util\Config\Conn;

//require_once 'database/Connection.class.php';
//require_once 'FeatureModules.class.php';

class Functions
{
    static public $dirConfig = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "";
    static public $dirConfigDB = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "scripts" . DIRECTORY_SEPARATOR . "";
    static public $dirConfigCodeGenerate = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . "code_generator" . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "";

    static function sksort(&$array, $subkey = "id", $sort_ascending = false)
    {
        if (count($array))
            $temp_array[key($array)] = array_shift($array);

        foreach ($array as $key => $val) {
            $offset = 0;
            $found = false;
            foreach ($temp_array as $tmp_key => $tmp_val) {
                if (!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey])) {
                    $temp_array = array_merge((array)array_slice($temp_array, 0, $offset),
                        array($key => $val),
                        array_slice($temp_array, $offset)
                    );
                    $found = true;
                }
                $offset++;
            }
            if (!$found) $temp_array = array_merge($temp_array, array($key => $val));
        }

        if ($sort_ascending) $array = array_reverse($temp_array);

        else $array = $temp_array;
    }

    static function generateJsonParam($param)
    {
        if (file_exists(self::$dirConfig . "ConfigDataTree.json")) {
            unlink(self::$dirConfig . "ConfigDataTree.json");
            //  unlink("database/db_product_config.db");
        }
        $jsonFile = '';
        $objFeatureGenerate = new FeatureModules();
        $jsonFile .= $objFeatureGenerate->getGenerateModule($param);

        file_put_contents(self::$dirConfig . "ConfigDataTree.json", $jsonFile);

    }

    static function createConfigProduct($param)
    {
        try {
            $sqlInsert = '';
            ###################### Config product ######################

            $product = (object)Functions::getEntity('product', $param['product_id']);

            $sqlInsert .= "INSERT INTO cf_product (id,name,description,url_repository,login_repository,password_repository) VALUES ('" . $param['product_id'] . "', '" . strtolower($product->name) . "', '" . strtoupper($product->description) . "','" . strtolower($product->url_repository) . "','" . $product->login_repository . "', '" . $product->password_repository . "');\n";

        } catch (PDOException $e) {
            print_r($e->getMessage());
        }
        ###################### Config product ######################

        ###################### Config group_menu ######################
        $sqlInsert .= "INSERT INTO cf_group_menu (name) VALUES ('CADASTRO');\n";
        $sqlInsert .= "INSERT INTO cf_group_menu (name) VALUES ('RELATÓRIO');\n";
        $sqlInsert .= "INSERT INTO cf_group_menu (name) VALUES ('GRÁFICO');\n";
        $sqlInsert .= "INSERT INTO cf_group_menu (name) VALUES ('ACOMPANHAMENTO');\n";
        ###################### Config group_menu ######################

        ###################### Config Module ######################
        try {
            foreach ($param['module_id'] as $item) {
                $module = (object)Functions::getEntity('module', $item);

                $sqlInsert .= "INSERT INTO cf_module (product_id,name,title) VALUES ('" . $param['product_id'] . "','" . strtoupper($module->name) . "','" . strtoupper($module->title) . "');\n";

            }
        } catch
        (PDOException $e) {
            print_r($e->getMessage());
        }
        ###################### Config Module ######################

        ###################### Config Feature ######################
        //set update para desabilitar todas as paginas dos modulos exceto o GU
        $sqlUpdate = "UPDATE pagina SET situacao = 'INATIVO' where pagina.modulo_id IN (select m.id from modulo m where m.nome != 'GU' );\n";

        $countFeature = 0;
        foreach ($param['feature_id'] as $itemF) {
            $feature = (object)Functions::getEntity('feature', $itemF);

            try {

                $sqlInsert .= "INSERT INTO cf_feature (module_id,title,name,type,group_menu_id) VALUES ('" . $feature->module_id . "','" . $feature->title . "','" . $feature->name . "','" . $feature->type . "','" . $feature->group_menu_id . "');\n";

            } catch (PDOException $e) {
                print_r($e->getMessage());
            }
            $countFeature++;
            ###################### Config feature_page ######################
            if (self::countEntity("feature_page", ['feature_id' => $itemF]) > 0) {

                $featurePagesArray = (Functions::getEntity('feature_page', '', 'feature_id = ' . $itemF));

                //verifica se e um array multidimencional conta a qtd, se não retorna zero
                if (array_sum(array_map("is_array", $featurePagesArray)) != 0) {
                    foreach ($featurePagesArray as $featurePage) {
                        try {

                            $sqlInsert .= "INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('" . $countFeature . "','" . $featurePage['name'] . "','" . $featurePage['controller'] . "');\n";

                            $sqlUpdate .= "UPDATE pagina SET situacao = 'ATIVO' WHERE arquivo = '" . $featurePage['controller'] . "';\n";

                        } catch (PDOException $e) {
                            print_r($e->getMessage());
                        }
                    }

                } else {
                    $featurePagesArray = array_unique($featurePagesArray); //remove os valores duplicados no array

                    try {
                        $sqlInsert .= "INSERT INTO cf_feature_page (feature_id,name,controller) VALUES ('" . $countFeature . "','" . $featurePagesArray['name'] . "','" . $featurePagesArray['controller'] . "');\n";

                        $sqlUpdate .= "UPDATE pagina SET situacao = 'ATIVO' WHERE arquivo = '" . $featurePagesArray['controller'] . "';\n";

                    } catch (PDOException $e) {
                        print_r($e->getMessage());
                    }


                }

            }
            ###################### Config feature_page ######################

        }

        ###################### Config Feature ######################

        $configDB = Functions::getConfigDB();

        if ($configDB->type == "pgsql") {
            $sqlUpdate .= "ALTER DATABASE " . $configDB->name . " SET datestyle TO ISO, DMY;";
        }

        $insert_config = self::$dirConfigDB . "insert_config_product_" . $configDB->type . ".sql";
        if (file_exists($insert_config)) {
            unlink($insert_config);
            file_put_contents($insert_config, $sqlInsert);
        } else {
            file_put_contents($insert_config, $sqlInsert);
        }
        if ($param['product_id'] == 1) {
            $enable_page = self::$dirConfigDB . "enable_pages_config_product_" . $configDB->type . "_ceres.sql";
            if (file_exists($enable_page)) {
                unlink($enable_page);
                file_put_contents($enable_page, $sqlUpdate);
            } else {
                file_put_contents($enable_page, $sqlUpdate);
            }
        }
    }

    static function getEntity($table, $id = null, $where = null, $order = null)
    {
        try {

            $where = !empty($where) ? $where : "";
            $conn = Conn\Connection::conectar();

            if (empty($id) && !empty($where)) {
                $sql = "SELECT * FROM {$table} WHERE {$where}";

            } elseif (!empty($id) && !empty($where)) {
                $sql = "SELECT * FROM {$table} WHERE id = {$id} AND {$where}";

            } elseif (!empty($id) && empty($where)) {
                $sql = "SELECT * FROM {$table} WHERE id = {$id}";

            } else {
                $sql = "SELECT * FROM {$table}";

            }
            if (!empty($order)) {
                $sql . " order by {$order}";
            }

            $sth = $conn->prepare($sql);

            if ($sth->execute()) {
                $result = $sth->fetchAll();

                if (count($result) > 1) {

                    $object = [];
                    foreach ($result as $row) {
                        $object[] = $row;
                    }

                } else {
                    $object = '';
                    foreach ($result as $row) {
                        $object = $row;
                    }
                }


                return $object;
            } else {
                return null;
            }
            $sth->close();
        } catch (\Exception $e) {
            print_r($e);
        }
    }

    static function countEntity($table, $foreign)
    {
        try {

            $foreign = !empty($foreign) ? $foreign : "";
            $conn = Conn\Connection::conectar();

            if (!empty($foreign)) {
                $sql = "SELECT * FROM {$table} WHERE " . key($foreign) . " = {$foreign[key($foreign)]}";

            } else {
                $sql = "SELECT * FROM {$table}";

            }

            $sth = $conn->prepare($sql);

            $sth->execute();

            return count($sth->fetchAll());

            $sth->close();

        } catch (\Exception $e) {
            print_r($e);
        }

    }

    static function createConfigSSH($param)
    {
        if (!empty($param)) {

            $config = '{
              "host": "' . $param['host'] . '",
              "user": "' . $param['user'] . '",
              "password": "' . $param['password'] . '",
              "port": "22",
              "dir": "' . $param['dir'] . '"
            }';

            file_put_contents(self::$dirConfig . "configSSH.json", $config);
        }
    }

    public
    static function getConfigSSH()
    {
        $file = file_get_contents(self::$dirConfig . "configSSH.json");
        $configJson = json_decode($file, true);

        return (object)$configJson;
    }

    public
    static function getConfigDB()
    {
        if (file_exists(self::$dirConfig . "configDB.json")) {

            $file = file_get_contents(self::$dirConfig . "configDB.json");
            $configJson = json_decode($file, true);

            return (object)$configJson;
        } else {
            return null;
        }
    }

    public
    static function getConfigProduct($product_id)
    {
        try {

            $conn = Conn\Connection::conectar('ConfigConnectionWizard');

            $sth = $conn->prepare("SELECT * FROM product WHERE id={$product_id}");

            if ($sth->execute()) {

                while ($row = $sth->fetchObject()) {
                    return $row;
                }
            } else {
                throw new \Exception('Product id é null ou inexistente');
            }
        } catch (PDOException $e) {
            print_r($e->getMessage());
        }

    }

    static function createConfigDB($param)
    {
        if (!empty($param)) {
            if (file_exists(self::$dirConfig . "configDB.json")) {
                unlink(self::$dirConfig . "configDB.json");
                unlink(self::$dirConfigCodeGenerate . "config.json");
            }

            $config = '{  "type": "' . $param['type'] . '",
                          "host": "' . $param['host'] . '",
                          "port": "' . ($param['type'] == 'pgsql' ? '5432' : '3306') . '",
                          "name": "' . $param['name'] . '",
                          "user": "' . $param['user'] . '",
                          "pass": "' . $param['password'] . '",
                          "ini_file": "' . (!empty($param['ini_file']) ? $param['ini_file'] : '') . '",
                          "docker_container": "' . $param['docker_container'] . '"
                      }';

            file_put_contents(self::$dirConfig . "configDB.json", $config);
            file_put_contents(self::$dirConfigCodeGenerate . "config.json", $config);
        }
    }

    static function createConfigDBiniProduct($param)
    {
        if (!empty($param)) {
            if (file_exists(self::$dirConfig . "pg_ceres.ini")) {
                unlink(self::$dirConfig . "pg_ceres.ini");
            }

            $config = 'host  =  "' . $param['host'] . '"
                       name  =  "' . $param['name'] . '"
                       port  =  "' . ($param['type'] == 'pgsql' ? '5432' : '3306') . '"
                       user  =  "' . $param['user'] . '"
                       pass  =  "' . $param['pass'] . '"
                       type  =  "' . $param['type'] . '"';

            file_put_contents(self::$dirConfig . "pg_ceres.ini", $config);
        }
    }


    static function removeByItemArray(&$array, $item)
    {
        foreach ($array as $key => $row) {
            if ($row == $item) {
                unset($array[$key]);
            }
        }
    }

    public static function successMsg($msg)
    {
        echo '<div class="col s12"><div id="card-alert" class="card green"><div class="card-content white-text"><p><i class="fa fa-check"></i> SUCESSO : ' . $msg . '.</p></div></div></div>';
    }

    public static function errorMsg($msg)
    {
        echo '<div class="col s12"><div id="card-alert" class="card red"><div class="card-content white-text"><p><i class="fa fa-exclamation"></i> ALERTA : ' . $msg . '</p></div></div></div>';

    }

    function count_dimension($Array, $count = 0)
    {
        if (is_array($Array)) {
            return count_dimension(current($Array), ++$count);
        } else {
            return $count;
        }
    }

    /**
     * Execute the given command by displaying console output live to the user.
     * @param  string  cmd          :  command to be executed
     * @return array   exit_status  :  exit status of the executed command
     *                  output       :  console output of the executed command
     */
    static function liveExecuteCommand($cmd)
    {

        while (@ ob_end_flush()) ; // end all output buffers if any

        $proc = popen("$cmd 2>&1 ; echo Exit status : $?", 'r');

        $live_output = "";
        $complete_output = "";

        while (!feof($proc)) {
            $live_output = fread($proc, 4096);
            $complete_output = $complete_output . $live_output;
            echo "$live_output";
            @ flush();
        }

        pclose($proc);

        // get exit status
        preg_match('/[0-9]+$/', $complete_output, $matches);

        // return exit status and intended output
        return array(
            'exit_status' => intval($matches[0]),
            'output' => str_replace("Exit status : " . $matches[0], '', $complete_output)
        );
    }

}