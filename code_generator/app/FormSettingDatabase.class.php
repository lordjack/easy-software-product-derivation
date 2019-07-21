<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../content.php");

headerContent("../");

class FormSettingDatabase
{
    private $dir;

    function __construct()
    {
        $this->dir = dirname(__DIR__) . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "database" . DIRECTORY_SEPARATOR . "config.json";

        $objConfig = new stdClass();
        $objConfig->type = '';
        $objConfig->host = '';
        $objConfig->user = '';
        $objConfig->name = '';
        $objConfig->pass = '';
        $objConfig->ini_file = '';

        if (!empty($_POST)) {
            $this->createConfigDB($_POST);
            ?>
            <script src="../../lib/jquery3.3.1.min.js"></script>
            <style>
                #toast-container {
                    top: auto !important;
                    right: auto !important;
                    bottom: 50%;
                    left: 60%;
                }
            </style>
            <script>
                $(document).ready(function () {
                    Materialize.toast('<b style="color: #FFF;">Salvo com sucesso</b>', 2000, 'green');
                });
            </script><?php
        }

        $objConfig = $this->getConfigDB();

        ?>
        <div class="container">
            <div class="row">
                <form action="FormSettingDatabase.class.php" method="POST">

                    <!-- start content -->
                    <div class="card hoverable" style="height: 650px;">
                        <div class="card-content">
                        <span class="card-title"><b>Configuration Data Base</b> <i
                                    class="material-icons right">more_vert</i></span>
                            <div class="col s6">
                                <div class="row">
                                    <div class="input-field col s6">
                                        <input id="ini_file" type="text" name="ini_file" class="validate"
                                               placeholder="db_connection" value="<?php echo $objConfig->ini_file; ?>"
                                               required>
                                        <label for="ini_file">Ini File</label>
                                    </div>
                                    <div class="input-field col s6">
                                        <input id="type" type="text" name="type" class="validate"
                                               placeholder="pgsql or mysql" value="<?php echo $objConfig->type; ?>"
                                               required>
                                        <label for="type">Type</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="host" type="text" name="host" class="validate"
                                               placeholder="200.000.000.000" value="<?php echo $objConfig->host; ?>"
                                               required>
                                        <label for="host">Host</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="user" type="text" name="user" class="validate"
                                               value="<?php echo $objConfig->user; ?>"
                                               required>
                                        <label for="user">User</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="pass" name="pass" type="password"
                                               value="<?php echo $objConfig->pass; ?>"
                                               class="validate">
                                        <label for="pass">Password</label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input id="name" type="text" name="name" class="validate"
                                               value="<?php echo $objConfig->name; ?>"
                                               placeholder="db_name"
                                               required>
                                        <label for="name">Name</label>
                                    </div>
                                </div>
                            </div>
                            <p>&nbsp;</p>
                        </div><!-- /.card-content -->
                        <div class="col s12">
                            <div class="card-action" style="margin-top: -10px;">
                                <a class="waves-effect waves-light btn #0091ea light-blue accent-4"
                                   href="../index.php?">
                                    <i class="material-icons right">arrow_back</i>Voltar</a>
                                <button type="submit" class="btn #4caf50 green">SALVAR<i class="material-icons right">save</i>
                                </button>
                            </div>
                        </div>
                    </div><!-- /.card-content -->
                </form>
            </div>
        </div>
        </div>
        <?php
    }

    function createConfigDB($param)
    {
        if (!empty($param)) {
            if (file_exists($this->dir)) {
                unlink($this->dir);
            }

            $config = '{
                          "type": "' . $param['type'] . '",
                          "host": "' . $param['host'] . '",
                          "port": "' . ($param['type'] == 'pgsql' ? '5432' : '3306') . '",
                          "name": "' . $param['name'] . '",
                          "user": "' . $param['user'] . '",
                          "pass": "' . $param['pass'] . '",
                          "ini_file": "' . (!empty($param['ini_file']) ? $param['ini_file'] : '') . '"
                      }';

            file_put_contents($this->dir, $config);
        }
    }

    function getConfigDB()
    {
        if (file_exists($this->dir)) {
            $file = file_get_contents($this->dir);
            $configJson = json_decode($file, true);

            return (object)$configJson;
        } else {
            return null;
        }
    }


}

new FormSettingDatabase();

footerContent("../");