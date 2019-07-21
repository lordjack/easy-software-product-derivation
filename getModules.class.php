
<?php
use Util\Config\Conn;

require_once 'database/Connection.class.php';

$conn = Conn\Connection::conectar();

$sth = $conn->prepare('SELECT * FROM module WHERE product_id = ?');

if ($sth->execute([$_GET['module_id']])) {
    $result = $sth->fetchAll();

    $tagSelect = "";
    $tagSelect = "<b style=\"font-size: 20px;\">Opções de Módulos</b>
                    <select multiple id='module_id' name='module_id[]' class='browser-default' required='required'>";
    foreach ($result as $row) {

        $tagSelect .= "<option value='{$row['id']}'>{$row['name']}</option>";
    }
}
$tagSelect .= "</select>";

echo $tagSelect;