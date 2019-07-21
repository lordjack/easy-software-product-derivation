<?php

include("content.php");
require_once 'lib/Functions.php';

use Util\Functions;
use Session\Session;

headerContent("");
breadcrumb(4);//cabecalho

$objSession = Session::getInstance();

\Util\Functions::createConfigDB($_REQUEST);
\Util\Functions::createConfigProduct($objSession->__get('product'));

$objSession->__set('product_id', $_REQUEST['product_id']);

$module_id = '';
$inputHidden = '';
foreach ($_REQUEST['module_id'] as $row) {
    $module[] = Functions::getEntity('module', $row);
    $inputHidden .= "<input type='hidden' name='module_id[]' value='" . $row . "' />";
    $module_id .= "&module_id[]=" . $row;
}

?>
    <script>
        function onOptionPage() {
            var code_generator = "<?php echo $objSession->__get('code_generator');?>";
            if (code_generator == 2) {
                document.getElementById("formStep04").action = 'code_generator/index.php';
            } else {
                document.getElementById("formStep04").action = 'step05.php';
            }
        }
    </script>
    <div class="container">
        <div class="row">
            <form id="formStep04" method="POST" name="index">
                <input type="hidden" name="product_id" value="<?php echo $_REQUEST['product_id']; ?>"/>
                <?php echo $inputHidden; ?>

                <!-- start content -->
                <div class="card hoverable" style="height: 580px;">
                    <div class="card-content">
                        <span class="card-title"><b>Configurar Acesso ao SSH</b> <i
                                    class="material-icons right">more_vert</i></span>

                        <div class="col s6">
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="host" type="text" name="host" class="validate"
                                           placeholder="200.000.000.000"
                                           required/>
                                    <label for="host">Host</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="user" type="text" name="user" class="validate" required/>
                                    <label for="user">Usuário</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <label for="password">Senha</label>
                                    <input id="password" name="password" type="password" placeholder="Password"
                                           class="validate" required/>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="dir" type="text" name="dir" class="validate"
                                           placeholder="/var/www/html/project"
                                           required>
                                    <label for="dir">Dir</label>
                                </div>
                            </div>
                        </div>
                        <!--
                        <div class="col s6" style="margin-top: 50px;">
                            <span class="flow-text"> <b>Cloud VPS (Virtual Private Service)</b></span>
                            <div class="row">
                                <div class="col s12">
                                    <i class="fa fa-amazon fa-5x" aria-hidden="true"></i>
                                    <div style="margin-top: -60px;margin-left: 167px;">
                                        <button type="button" class="btn #0091ea light-blue accent-4">Connect
                                            <i class="material-icons right">send</i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12" style="margin-top: 50px;">
                                    <i class="fa fa-google fa-5x" aria-hidden="true"></i>
                                    <div style="padding-left: 169px; margin-top: -55px;">
                                        <button type="button" class="btn #0091ea light-blue accent-4">Connect
                                            <i class="material-icons right">send</i></button>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="row">

                            <a class="waves-effect waves-light btn #f9a825 yellow darken-3"
                               href="#" onclick="checkConn()" style="margin-top: 150px;margin-left: 170px; ">
                                <i class="material-icons right">check</i>Testar Conexão</a>

                            <script src="lib/jquery3.3.1.min.js"></script>
                            <style>
                                #toast-container {
                                    top: auto !important;
                                    right: auto !important;
                                    bottom: 20%;
                                    left: 60%;
                                }
                            </style>
                            <script>
                                function checkConn() {
                                    $(document).ready(function () {

                                        dataForm = document.getElementById('formStep04');

                                        $.post("lib/checkConn.class.php", {
                                            host: dataForm.host.value,
                                            user: dataForm.user.value,
                                            dir: dataForm.dir.value,
                                            password: dataForm.password.value
                                        })
                                            .done(function (data) {
                                                if (data == 1) {
                                                    Materialize.toast('<b style="color: #FFF;">Conectado com sucesso</b>', 2000, 'green');
                                                    var element = document.getElementById("next");
                                                    element.classList.remove("disabled");
                                                } else {
                                                    Materialize.toast('<b style="color: #FFF;">Falha na conexão</b>', 2000, 'red');
                                                    var element = document.getElementById("next");
                                                    element.classList.add("disabled");
                                                }
                                            })
                                            .fail(function (data) {
                                                alert(data);
                                                Materialize.toast('<b style="color: #FFF;">Falha na conexão</b>', 2000, 'red');
                                            });
                                    });
                                }
                            </script>
                        </div>
                    </div><!-- /.card-content -->
                    <div class="col s12">
                        <div class="card-action">
                            <a class="waves-effect waves-light btn #0091ea light-blue accent-4"
                               href="step03.php?product_id=<?php echo $_REQUEST['product_id'] . $module_id; ?>">
                                <i class="material-icons right">arrow_back</i>Voltar</a>
                            <button id="next" type="submit" class="btn #4caf50 green disabled" onclick="onOptionPage()">
                                Próximo <i class="material-icons right">arrow_forward</i></button>
                        </div>
                    </div>
                </div><!-- /.card-content -->
            </form>
        </div>
    </div>
    </div>
<?php
footerContent("");
?>