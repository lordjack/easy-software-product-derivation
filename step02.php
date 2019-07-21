<?php
include("content.php");

use Util\Functions;
use Session\Session;

require_once 'lib/Functions.php';

headerContent("");
breadcrumb(2);//cabecalho

Util\Functions::generateJsonParam($_REQUEST);

$objSession = Session::getInstance();
$objSession->__set('product', $_REQUEST);

$module_id = '';
$inputHidden = '';
foreach ($_REQUEST['module_id'] as $row) {
    $module[] = Functions::getEntity('module', $row);
    $inputHidden .= "<input type='hidden' name='module_id[]' value='" . $row . "' />";
    $module_id .= "&module_id[]=" . $row;
}

?>
    <script src="lib/treeD3/d3.v3.min.js"></script>
    <script src="lib/treeD3/treeD3Model02.js"></script>
    <link type="text/css" rel="stylesheet"
          href="lib/treeD3/treeD3Model02.css" media="screen,projection"/>
    <div class="container">
        <div class="row">
            <form action="step03.php">
                <input type="hidden" name="product_id" value="<?php echo $_REQUEST['product_id']; ?>"/>
                <?php echo $inputHidden; ?>
                <!-- start content -->
                <div class="card hoverable">
                    <div class="card-content">

                        <span class="card-title"><b> Produto: <b style="color: red;"><?php echo $_REQUEST['product_name']; ?></b> - Gráfico Árvore de Features</b> <i
                                    class="material-icons right">more_vert</i></span>
                        <div id="tree-container"></div>
                        <p>&nbsp;</p>
                    </div><!-- /.card-content -->
                    <div class="card-action">
                        <a class="waves-effect waves-light btn #0091ea light-blue accent-4"
                           href="step01.php?product_id=<?php echo $_REQUEST['product_id'] . $module_id; ?>">
                            <i class="material-icons right">arrow_back</i>Voltar</a>
                        <button type="submit" class="btn #4caf50 green">Próximo<i class="material-icons right">arrow_forward</i>
                        </button>
                    </div>
                </div><!-- /.card-content -->
            </form>
        </div>
    </div>
    </div>
<?php
footerContent("");