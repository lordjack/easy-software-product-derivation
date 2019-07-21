<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

include("content.php");

headerContent("");
breadcrumb();//cabecalho

use Util\Functions;

require_once 'lib/Functions.php';

$productArray = Functions::getEntity('product', null, null, 'name');
?>
    <script src="lib/jquery3.3.1.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#product_id").on("change", function () {
                $("#module_id").load("getModules.class.php?module_id=" + $('#product_id').val());
            });
        });

        function onSkipOptionPage() {
            var option = document.querySelector('input[name="option"]:checked').value;
            if (option == '2') {
                document.getElementById("index").action = 'step01.php';
            } else if (option == '3') {
                document.getElementById('product_id').required = false;
                document.getElementById('module_id').required = false;
                document.getElementById("index").action = 'code_generator/index.php';
            } else {
                document.getElementById("index").action = 'step01.php';
            }
        }
    </script>
    <div class="container">
        <div class="row">
            <!-- start content -->
            <form class="col s12" method="POST" id="index" name="index">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title activator grey-text text-darken-4"><b style="font-size: 20px;">Opções de Produtos</b> <i
                                    class="material-icons right">more_vert</i></span>
                        <div class="row">
                            <div class="col s12 m3">
                                <select id="product_id" name="product_id" class="browser-default" required>
                                    <option value="-1" disabled="disabled" selected="selected">::Escolha a sua opção::
                                    </option>
                                    <?php
                                    $aux = '';
                                    foreach ($productArray as $row) {
                                        if (is_array($row)) {
                                            echo "<option value=" . $row['id'] . ">" . $row['description'] . "</option>";
                                        } else if ($productArray['id'] != $aux) {
                                            echo "<option value=" . $productArray['id'] . ">" . $productArray['description'] . "</option>";
                                            $aux = $productArray['id'];
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col s12 m3">
                                <div id="module_id"></div>
                            </div>
                        </div>
                        <p>&nbsp;</p>
                        <div class="row">
                            <input class="#0091ea light-blue accent-4" checked name="option" type="radio"
                                   id="customize" value="1"/>
                            <label for="customize"><b style="color: red;">(i)</b> N&atilde;o gerar CRUDs durante a
                                deriva&ccedil;&atilde;o do produto e implanta&ccedil;&atilde;o</label>
                        </div>
                        <div class="row">
                            <input class="#0091ea light-blue accent-4" name="option" type="radio"
                                   id="customize2" value="2"/>
                            <label for="customize2"><b style="color: red;">(ii)</b> Gerar CRUDs de alguma
                                tabela do produto durante a deriva&ccedil;&atilde;o e implanta&ccedil;&atilde;o</label>
                        </div>
                        <div class="row">
                            <input class="#0091ea light-blue accent-4" name="option" type="radio"
                                   id="customize3" value="3"/>
                            <label for="customize3"><b style="color: red;">(iii)</b> Ir direto para o gera&ccedil;&atilde;o
                                de CRUDs</label>
                        </div>

                    </div><!-- /.card-content -->
                    <div class="card-action">
                        <button type="submit" class="btn #4caf50 green" onclick="onSkipOptionPage()">Próximo<i
                                    class="material-icons right">arrow_forward</i>
                        </button>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Sobre<i class="material-icons right">close</i></span>
                        <p>Here is some more information about this product that is Wizard for Clone-and-own with
                            Software Product Line, your object is helps development system, clone and deploy the
                            application easy.</p>
                    </div>
                </div><!-- /.row -->
            </form>
        </div>
    </div>
<?php
footerContent("");