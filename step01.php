<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

include("content.php");

use Util\Functions;
use Session\Session;

require_once 'database/Connection.class.php';
require_once 'lib/Functions.php';

headerContent("");
breadcrumb();//cabecalho

$product_id = !empty($_REQUEST['product_id']) ? $_REQUEST['product_id'] : '';
$module_id = !empty($_REQUEST['module_id']) ? $_REQUEST['module_id'] : '';

$product = Functions::getEntity('product', $product_id);

$module = [];
$feature = [];
$inputHidden = '';
foreach ($module_id as $row) {
    $module[] = Functions::getEntity('module', $row);
    $inputHidden .= "<input type='hidden' name='module_id[]' value='" . $row . "' />";
}
$objSession = Session::getInstance();
$objSession->__set('code_generator', filter_input(INPUT_POST, 'option'));
?>
    <div class="container">
        <div class="row">
            <!-- start content -->
            <form class="col s12" action="step02.php" method="POST">

                <input type="hidden" name="product_id"
                       value="<?php echo !empty($product['id']) ? $product['id'] : ''; ?>"/>
                <input type="hidden" name="product_name"
                       value="<?php echo !empty($product['name']) ? $product['name'] : ''; ?>"/>
                <?php echo $inputHidden; ?>

                <div class="card hoverable">
                    <div class="card-content">
                        <span class="card-title"><b>Selecione as Features do Produto: <b
                                        style="color: red;"><?php echo !empty($product['name']) ? $product['name'] : ''; ?></b> </b> <i
                                    class="material-icons right">more_vert</i></span>
                        <div class='row'>
                            <div class='row' style="margin-left: 20px;">
                                <div class='col s2 m5'>
                                    <input type="checkbox" id="checkAllForm" class="filled-in" name="checkAllForm"/>
                                    <label for="checkAllForm">Selecionar todas features da árvore</label>
                                </div>
                            </div>
                            <?php
                            foreach ($module as $i => $row) {
                                ?>
                                <div class='col s12'>
                                    <?php
                                    echo "<b class='flow-text'><b>Módulo: " . $module[$i]['name'] . "</b></p>";
                                    $featuresArray = Functions::getEntity('feature', null, "module_id=" .
                                        $module[$i]['id'] . " and level=1 order by group_menu_id, type");
                                    $group = "";

                                    foreach ($featuresArray as $row) {

                                        $name_group_menu = ((object)Functions::getEntity('group_menu', $row['group_menu_id']));

                                        $lb_group = $row['group_menu_id'] != $group ? "<p class='flow-text'><b>Menu: " . $name_group_menu->name . "</b></p>" : "";

                                        echo($lb_group);
                                        echo "<ul class=\"collection\">";
                                        $objectFeature = new \Features\FeatureModules();
                                        echo $objectFeature->onLoadTreeForm($row['id']);
                                        echo "</ul>";

                                        $group = $row['group_menu_id'];
                                    }
                                    ?>
                                </div>
                                <blockquote style="color: #0D47A1">
                                    <p>&nbsp;</p>
                                </blockquote>
                            <?php }
                            echo "</div>";
                            ?>
                            <p>&nbsp;</p>
                        </div><!-- /.card-content -->
                        <div class="card-action">
                            <a class="waves-effect waves-light btn #0091ea light-blue accent-4" href="index.php">
                                <i class="material-icons right">arrow_back</i>Voltar</a>
                            <button type="submit" class="btn #4caf50 green">Próximo<i
                                        class="material-icons right">arrow_forward</i>
                            </button>
                        </div>
                    </div><!-- /.row -->

            </form>
        </div>
    </div>
<?php
footerContent("");
################# Check All itens form table #################
echo '<script>
    $("#checkAllForm").click(function () {
         $(".checkForm").prop("checked", $(this).prop("checked"));
     }); 
    </script>';
################# Check All itens form table #################