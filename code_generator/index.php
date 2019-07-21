<?php
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

include("content.php");

headerContent("");

use Session\Session;

$objSession = Session::getInstance();

if (filter_input(INPUT_POST, 'option') == 3) {
    $objSession->__set('code_generator', filter_input(INPUT_POST, 'option'));
}

?>
    <script>
        function onOptionPage() {
            var code_generator = "<?php echo $objSession->__get('code_generator');?>";
            if (code_generator == 2) {
                document.getElementById("back").href = "../step05.php";
            } else {
                document.getElementById("back").href = "../index.php";
            }
        }
    </script>
    <div class="container">
        <div class="row">
            <!-- start content -->
            <form class="col s12" action="app/CodeSpecification.class.php" method="GET" id="index" name="index">
                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title activator grey-text text-darken-4"><b style="font-size: 20px;">Gerador de C&oacute;digo</b> <i
                                    class="material-icons right">more_vert</i></span>
                        <div class="row">
                            <div class="input-field col s12">
                                <label for="tableName">Nome da Tabela</label>
                                <input id="tableName" type="text" name="tableName" required/>
                            </div>
                        </div>

                    </div><!-- /.card-content -->
                    <div class="card-action">
                        <a id="back" class="waves-effect waves-light btn #0091ea light-blue accent-4" href="#"
                           onclick="onOptionPage()">
                            <i class="material-icons right">arrow_back</i>Voltar</a>
                        <button type="submit" class="btn #4caf50 green">Pr&oacute;ximo<i class="material-icons right">arrow_forward</i>
                        </button>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4"><b>Sobre</b><i class="material-icons right">close</i></span>
                        <p> Configurar acesso ao banco <br><br>
                            <a class="waves-effect waves-light btn #0091ea light-blue accent-4"
                               href="app/FormSettingDatabase.class.php"><i
                                        class="material-icons right">settings</i>Ir</a>
                        </p>
                    </div>
                </div><!-- /.row -->
            </form>
        </div>
    </div>
<?php
footerContent("");