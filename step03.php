<?php

include("content.php");
require_once 'lib/Functions.php';

use Util\Functions;
use Util\Config\Conn;

headerContent("");
breadcrumb(3);//cabecalho

$module_id = '';
$inputHidden = '';
foreach ($_REQUEST['module_id'] as $row) {
    $module[] = Functions::getEntity('module', $row);
    $inputHidden .= "<input type='hidden' name='module_id[]' value='" . $row . "' />";
    $module_id .= "&module_id[]=" . $row;
}

?>
    <script>

        function onSkip() {
            var skip = document.getElementById("skip");
            var element = document.getElementById("next");
            if (skip.checked == true) {
                document.getElementById('type').required = false;
                document.getElementById('host').required = false;
                document.getElementById("name").required = false;
                document.getElementById("user").required = false;
                document.getElementById("password").required = false;

                element.classList.remove("disabled");
            } else {
                document.getElementById('type').required = true;
                document.getElementById('host').required = true;
                document.getElementById("name").required = true;
                document.getElementById("user").required = true;
                document.getElementById("password").required = true;

                element.classList.add("disabled");
            }

        }

        function onSkipDocker() {
            var skipDocker = document.getElementById("skipDocker");
            if (skipDocker.checked == true) {
                document.getElementById('docker_container').style.display = 'block';
                document.getElementById('lb_docker_container').style.display = 'block';

            } else {
                document.getElementById('docker_container').style.display = 'none';
                document.getElementById('lb_docker_container').style.display = 'none';

            }
        }

        function onSkipSQLite(selectObject) {

            var element = document.getElementById("next");
            var type = selectObject.value;

            if (type == 'sqlite') {
                document.getElementById('host').required = false;
                document.getElementById("user").required = false;
                document.getElementById("password").required = false;

                element.classList.remove("disabled");
            } else {
                document.getElementById('type').required = true;
                document.getElementById('host').required = true;
                document.getElementById("name").required = true;
                document.getElementById("user").required = true;
                document.getElementById("password").required = true;

                element.classList.add("disabled");
            }
        }
    </script>
    <div class="container">
        <div class="row">
            <form id="formStep03" action="step04.php" method="POST">

                <input type="hidden" name="product_id" value="<?php echo $_REQUEST['product_id']; ?>"/>
                <?php echo $inputHidden; ?>
                <!-- start content -->
                <div class="card hoverable" style="height: 650px;">
                    <div class="card-content">
                        <span class="card-title"><b>Configurar Banco de Dados</b> <i
                                    class="material-icons right">more_vert</i></span>
                        <div class="col s6">
                            <div class="row">
                                <div class="col s12">
                                    <label>Tipo</label>
                                    <select id="type" name="type" class="browser-default validate"
                                            onchange="onSkipSQLite(this)">
                                        <option value="0" selected="selected">::Escolha a sua opção::</option>
                                        <option value="pgsql">PostgreSQL</option>
                                        <option value="mysql">MySQL</option>
                                        <option value="sqlite">SQLite</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="host" type="text" name="host" class="validate"
                                           placeholder="200.000.000.000"
                                           required>
                                    <label for="host">Host</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="user" type="text" name="user" class="validate" required>
                                    <label for="user">Usuário</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="password" name="password" type="password" class="validate" required>
                                    <label for="password">Senha</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="input-field col s12">
                                    <input id="name" type="text" name="name" class="validate" placeholder="db_name"
                                           required>
                                    <label for="name">Nome</label>
                                </div>
                                <p>
                                    <input id="skip" type="checkbox" name="skip" class="filled-in" onclick="onSkip()">
                                    <label for="skip">Pular</label>
                                </p>
                            </div>
                        </div><!--
                        <div class="col s6" style="margin-top: 50px;">
                            <span class="flow-text"> <b>Cloud VPS (Virtual Private Service)</b></span>
                            <div class="row">
                                <div class="col s12">
                                    <i class="fa fa-amazon fa-5x" aria-hidden="true"></i>
                                    <div style="margin-top: -60px;margin-left: 167px;">
                                        <button type="button" class="btn #0091ea light-blue accent-4">Conectar
                                            <i class="material-icons right">send</i></button>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col s12" style="margin-top: 50px;">
                                    <i class="fa fa-google fa-5x" aria-hidden="true"></i>
                                    <div style="padding-left: 169px; margin-top: -55px;">
                                        <button type="button" class="btn #0091ea light-blue accent-4">Conectar
                                            <i class="material-icons right">send</i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        -->
                    </div>
                    <div class="col s6">
                        <div class="row">
                            <h5 style="margin-top: -60px;">Usa um Conteiner Docker?</h5>
                            <div class="input-field col s12">
                                <input id="docker_container" type="text" name="docker_container" class="validate"
                                       placeholder="ID ou Nome" style="display: none;"
                                >
                                <label id="lb_docker_container" for="docker_container" style="display: none;">Docker
                                    Container</label>
                            </div>
                            <div class="row">
                                <div class="switch" style="margin-left: 25px;">
                                    <label>
                                        Não
                                        <input type="checkbox" id="skipDocker" name="skipDocker"
                                               onclick="onSkipDocker()">
                                        <span class="lever"></span>
                                        Sim
                                    </label>
                                </div>
                            </div>
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

                                            dataForm = document.getElementById('formStep03');

                                            $.post("lib/checkConn.class.php", {
                                                type: dataForm.type.value,
                                                host: dataForm.host.value,
                                                user: dataForm.user.value,
                                                name: dataForm.name.value,
                                                password: dataForm.password.value,
                                                docker_container: dataForm.docker_container.value
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
                            <div class="row">
                                <div class="col s12">
                                    <h4 style="text-align: center">OBS para Postgres</h4>
                                    <p style="text-align: center"> O banco de dados deve esta com o datestyle DMY<br>
                                        Use o seguinte comando para mudar, se for o caso.<br><br>
                                        ALTER DATABASE db_name SET datestyle TO ISO, DMY;
                                    </p>
                                </div>
                            </div>
                        </div>
                        <p>&nbsp;</p>
                    </div>
                    <!-- /.card-content -->
                    <div class="col s12">
                        <div class="card-action" style="margin-top: -10px;">
                            <a class="waves-effect waves-light btn #0091ea light-blue accent-4"
                               href="step02.php?product_id=<?php echo $_REQUEST['product_id'] . $module_id; ?>">
                                <i class="material-icons right">arrow_back</i>Voltar</a>
                            <button id="next" type="submit" class="btn #4caf50 green disabled">Próximo<i
                                        class="material-icons right">arrow_forward</i>
                            </button>
                        </div>
                    </div>
                </div><!-- /.card-content -->
            </form>
        </div>
    </div>
    </div>
<?php
footerContent("");

