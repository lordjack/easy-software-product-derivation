<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

set_time_limit(600);

require __DIR__ . '/vendor/autoload.php';
include __DIR__ . "/content.php";
require_once __DIR__ . '/lib/Functions.php';

use Session\Session;
use Util\Functions;

headerContent("");
breadcrumb(5); //cabecalho

$objSession = Session::getInstance();
$product_id = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : $objSession->__get('product_id');

if (!empty($product_id)) {
    $product = (object)array_unique(Functions::getEntity('product', $product_id));
    $json = json_encode($product);
} else {
    ?>
    <script>
        $(document).ready(function () {
            $('#process').append('<div class="col s12"><div id="card-alert" class="card red"><div class="card-content white-text"><p><i class="fa fa-exclamation"></i> ALERTA : Product_id is null</p></div></div></div>');
        });
    </script>
    <?php
}

$module_id = '';
$inputHidden = '';
if (!empty($_REQUEST['module_id'])) {

    foreach ($_REQUEST['module_id'] as $row) {
        $module[] = Functions::getEntity('module', $row);
        $inputHidden .= "<input type='hidden' name='module_id[]' value='" . $row . "' />";
        $module_id .= "&module_id[]=" . $row;
    }
}
//---------------- Conection SSH --------------------
if (!empty($_REQUEST['host'])) {

    Functions::createConfigSSH($_REQUEST);
}
?>
    <script src="lib/jquery3.3.1.min.js"></script>
    <script>
        function appReady() {
            var appContainer = document.getElementById("appContainer");
            var verLog = document.getElementById("btn-ver-log");
            var abrirAplicacao = document.getElementById("btn-abrir-aplicacao");

            while (appContainer.firstChild) {
                appContainer.removeChild(appContainer.firstChild);
                document.getElementById('success').style.display = 'block';

                verLog.classList.remove("disabled");
                abrirAplicacao.classList.remove("disabled");
            }
        }

        $(document).ready(function () {
            $.ajax('ProcessProduct.php?json=<?php echo $json;?>')
                .done(function (data) {
                    // ###### simulate async loading of your app scripts... 2000 ######
                    setTimeout(appReady, 1);
                    if (!$.trim(data)) {
                        $('#success').append('<?php Functions::successMsg('O produto <b style="color: #FFF;">' . $product->description . '</b> foi configurado e Implantado'); ?>');
                        $('#files').append('<?php /*
                            if (\Util\ProcessProduct::getDBType() == 'sqlite') {
                                $dir = \Util\ProcessProduct::getHost() . '/wizard_lps/database/scripts/';
                                $dirCreate = $dir . 'create_db_config_product_sqlite.sql';
                                $dirInsert = $dir . 'insert_config_product_sqlite.sql';
                                echo "<a href=\"http://{$dirCreate}\" class=\"waves-effect waves-light btn #ffca28 amber lighten-1\" target=\"_blank\"><i class=\"material-icons right\">open_in_new</i>Create Table</a>";
                                echo "<a href=\"http://{$dirInsert}\" style=\"margin-left: 30px;\" class=\"waves-effect waves-light btn #ffca28 amber lighten-1\" target=\"_blank\"><i class=\"material-icons right\">open_in_new</i>Insert Table</a>";
                            }*/
                            ?>');

                    } else {
                        $('#process').append('<div class="col s12"><div id="card-alert" class="card red"><div class="card-content white-text"><p><i class="fa fa-exclamation"></i> ALERTA : ' + data + '</p></div></div></div>');
                    }

                })
                .fail(function (data) {
                    $('#process').append('<div class="col s12"><div id="card-alert" class="card red"><div class="card-content white-text"><p><i class="fa fa-exclamation"></i> ALERTA : ' + data + '</p></div></div></div>');
                });
        });
    </script>
    <div class="container">
        <div class="row">
            <!-- start content -->
            <form class="col s12" method="POST" id="form" name="form">
                <input type="hidden" name="product_id" value="<?php echo $_REQUEST['product_id']; ?>"/>
                <?php echo $inputHidden; ?>
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title activator grey-text text-darken-4"><b style="font-size: 20px;">   <span
                                        class="flow-text"> <b>Processo de Implanta&ccedil;&atilde;o</b></span></b> <i
                                    class="material-icons right">more_vert</i></span>
                        <div class="row">
                            <div class="col s12">
                                <div class="row">
                                    <div class="icol s12">
                                        <div id="appContainer">
                                            <div class="progress">
                                                <div class="indeterminate"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 ">
                                <div id="success">
                                    <h5 id="process">
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 ">
                                <div id="downloads">
                                    <p id="files" style="padding-left: 30%;">
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p>&nbsp;</p>
                    </div><!-- /.card-content -->
                    <div class="card-action">
                        <a class="waves-effect waves-light btn #0091ea light-blue accent-4"
                           href="step04.php?product_id=<?php echo $_REQUEST['product_id'] . $module_id; ?>">
                            <i class="material-icons right">arrow_back</i>Voltar</a>
                        <a class="waves-effect waves-light btn #e53935 red darken-1"
                           href="http://<?php echo $_SERVER['HTTP_HOST'] . '/wizard_lps/'; ?>"> <i
                                    class="material-icons right">done</i>Finalizar</a>

                        <a id="btn-abrir-aplicacao" class="waves-effect waves-light btn #4caf50 green disabled"
                           href="http://<?php echo \Util\ProcessProduct::getHost() . DIRECTORY_SEPARATOR . strtolower($product->name) ?>"
                           target="_blank">
                            <i class="material-icons right">open_in_new</i>Abrir Aplicação</a>
                        <button id="btn-ver-log" onclick="openLog()" class="btn #f9a825 yellow darken-3 disabled">
                            <i class="material-icons right">open_in_new</i>Ver Logs
                        </button>

                        <script>
                            function openLog() {
                                window.open('http://<?php echo \Util\ProcessProduct::getHost() . '/wizard_lps/log/restore_db.log'; ?>', '_blank');
                                window.open('http://<?php echo \Util\ProcessProduct::getHost() . '/wizard_lps/log/git.log'; ?>', '_blank');
                            }
                        </script>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Ajuda<i class="material-icons right">close</i></span>
                        <p>É possível acompanhar os logs que foram execultados atravez do console do navegador.</p>
                        <p>Caso ocorra algum erro durante o clone do produto, é recomendado que exclua a pasta do
                            projeto de forma recursiva e inicie a etapa 05 novamente.</p>
                        <p>Caso ocorra algum erro durante o dump do banco de dados, é recomendado que delete o banco e
                            crie um novo banco.</p>
                        <p>Caso esteja usando o SQLite a execução do script do banco de dados deve ser feita
                            manualmente, os Scripts gerados se encontram no diretorio da wizard_lps/database/scripts/
                            create_db_config_product_sqlite.sql e insert_config_product_sqlite.sql</p>
                    </div>
                </div><!-- /.row -->
            </form>
        </div>
    </div>
<?php
footerContent("");
