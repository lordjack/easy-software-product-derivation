<?php
/**
 * Created by PhpStorm.
 * User: Jackson Meires
 * Date: 10/02/2018
 * Time: 11:11
 */

namespace Features;

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);


use PDO;
use Util\Config\Conn;
use Util\Functions;

require_once 'database/Connection.class.php';

class FeatureModules
{

    private $feature_array;

    public function onLoadTreeJSON($feature_id)
    {
        try {
            $conn = Conn\Connection::conectar();

            //Numero de niveis que a Tree tem
            $sth = $conn->prepare('SELECT * FROM feature f WHERE f.id = :id');
            $sth->bindValue(':id', $feature_id, PDO::PARAM_INT);
            $sth->execute();

            $objects = $sth->fetchAll(PDO::FETCH_OBJ);

            $noh = '';
            $tree = '';

            if ($objects) {
                foreach ($objects as $object) {
                    if (in_array($object->id, $this->feature_array)) {

                        Functions::removeByItemArray($this->feature_array, $object->id);

                        if ($this->haveChild($object->id)) {

                            $noh = "{\"name\": \"$object->title\",
                                 \"children\": [ 
                                                " . $this->nodeJSON($object->id) . "
                                               ]
                             },";
                        } else {
                            $noh = " { \"name\": \"$object->title\", \"size\": 10 },";
                        }

                    }

                    $tree .= $noh;
                }
            }
            return $tree;

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }
    }

    private
    function nodeJSON($id)
    {
        $conn = Conn\Connection::conectar();

        //Numero de niveis que a Tree tem
        $sth = $conn->prepare('SELECT * FROM feature f WHERE f.feature_id = :id');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();

        $nohLoad = "";

        $objects = $sth->fetchAll(PDO::FETCH_OBJ);

        foreach ($objects as $object) {
            if (in_array($object->id, $this->feature_array)) {

                Functions::removeByItemArray($this->feature_array, $object->id);

                if ($this->haveChild(!empty($object->id))) {
                    $nohLoad .= "{\"name\": \"$object->title\",
                                 \"children\": [ 
                                                " . $this->nodeJSON($object->id) . "
                                               ]
                             },";

                } else {
                    $nohLoad .= " { \"name\": \"$object->title\", \"size\": 10 },";
                }
            }
        }

        $noh = substr($nohLoad, 0, -1);//remove a ultima virgula
        return $noh;
    }

    public
    function onLoadTreeForm($feature_id)
    {
        try {

            $conn = Conn\Connection::conectar();

            //Numero de niveis que a Tree tem
            $sth = $conn->prepare('SELECT * FROM feature f WHERE f.id = :id');
            $sth->bindValue(':id', $feature_id, PDO::PARAM_INT);
            $sth->execute();

            $objects = $sth->fetchAll(PDO::FETCH_OBJ);

            $tree = '';

            if ($objects) {
                foreach ($objects as $object) {
                    if ($this->haveChild($object->id)) {
                        $noh = '
                                <li class="collection-item" >
                                    <input id="' . $object->name . '"
                                           name=\'feature_id[]\'
                                           type=\'checkbox\' ' . ($object->type == "MANDATORY" ? "checked=\"checked\" onclick=\"return false;\"" : "") . '
                                           value=' . $object->id . ' class=\'checkForm filled-in\' />
                                    <label for=' . $object->name . ' ' . ($object->type == "MANDATORY" ? "style=\"font-weight: bold; color:black;\" " : "") . '>' . ($object->title) . '</label>
                                </li>
                                ' . $this->nodeForm($object->id);
                    } else {
                        $noh = '
                                 <li class="collection-item">
                                        <input id="' . $object->name . '"
                                               name=\'feature_id[]\'
                                               type=\'checkbox\' ' . ($object->type == "MANDATORY" ? "checked=\"checked\" onclick=\"return false;\"" : "") . '
                                               value=' . $object->id . ' class=\'checkForm filled-in\'/>
                                        <label for=' . $object->name . ' ' . ($object->type == "MANDATORY" ? "style=\"font-weight: bold; color:black;\" " : "") . '>' . ($object->title) . '</label>
                                 </li>
                                ';
                    }

                    $tree .= $noh;
                }
            }
            return $tree;

        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
        }


    }

    private
    function nodeForm($id)
    {
        $conn = Conn\Connection::conectar();

        //Numero de niveis que a Tree tem
        $sth = $conn->prepare('SELECT * FROM feature f WHERE f.feature_id = :id');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();

        $noh = "";

        $objects = $sth->fetchAll(PDO::FETCH_OBJ);

        foreach ($objects as $object) {
            if ($this->haveChild(!empty($object->id))) {
                $noh .= "<li class=\"collection-item\" style='margin-left: {$this->levelCSS($object->level)}px;'>
                                    <input id=" . $object->name . "
                                           name='feature_id[]'
                                           type='checkbox' " . ($object->type == 'MANDATORY' ? 'checked=\'checked\' onclick=\'return false;\' ' : "") . '
                                           value=' . $object->id . ' class=\'checkForm filled-in\'/>
                                    <label for=' . $object->name . ' ' . ($object->type == "MANDATORY" ? "style=\"font-weight: bold; color:black;\" " : "") . '>' . ($object->title) . '</label>
                            </li>
                            ' . $this->nodeForm($object->id);
            } else {
                $noh .= "<li class=\"collection-item\" >
                                    <input id=" . $object->name . "
                                           name='feature_id[]'
                                           type='checkbox'  " . ($object->type == 'MANDATORY' ? 'checked=\'checked\' onclick=\'return false;\' ' : "") . '
                                             value=' . $object->id . ' class=\'checkForm filled-in\'/>
                                    <label for=' . $object->name . ' ' . ($object->type == "MANDATORY" ? "style=\"font-weight: bold; color:black;\" " : "") . '>' . ($object->title) . '</label>
                            </li>';
            }
        }

        return $noh;
    }

    private
    function haveChild($id)
    {

        $childs = false;
        $conn = Conn\Connection::conectar();

        //Numero de niveis que a Tree tem
        $sth = $conn->prepare('SELECT level FROM feature f WHERE f.feature_id = :id limit 1 ');
        $sth->bindValue(':id', $id, PDO::PARAM_INT);
        $sth->execute();
        $result = $sth->fetchAll(PDO::FETCH_OBJ);

        if ($result) {
            foreach ($result as $row) {
                $childs = true;
            }
        }

        return $childs;

    }

    private
    function levelCSS($level)
    {
        switch ($level) {
            case 1:
                return "25";
            case 2:
                return "50";
            case 3:
                return "75";
            case 4:
                return "100";
            case 5:
                return "125";
            default:
                return "0";
        }
    }

    function onOrderFeatureToModule($param)
    {
        $arrayFeatureOrder = [];
        foreach ($param as $feature_id) {
            $feature = ((object)Functions::getEntity('feature', $feature_id));
            $arrayFeatureOrder[] = array('feature_id' => $feature->id, 'module_id' => $feature->module_id);
        }

        Functions::sksort($arrayFeatureOrder, "module_id", true);

        $arrayFeature = [];
        foreach ($arrayFeatureOrder as $item) {
            $arrayFeature[] = $item['feature_id'];
        }

        return $arrayFeature;

    }

    function getGenerateModule($param)
    {
        if (!empty($param)) {

            $this->feature_array = $param['feature_id'];

            $tree = '{
              "name": "' . $param['product_name'] . '",
                    "children": [';

            $this->onOrderFeatureToModule($this->feature_array);

            $arrayFeatureOrder = [];
            foreach ($this->feature_array as $feature_id) {
                $feature = ((object)Functions::getEntity('feature', $feature_id));
                $arrayFeatureOrder[] = array('feature_id' => $feature->id, 'module_id' => $feature->module_id);
            }

            //ver quantos modulos tem nas features
            $moduleArray = [];
            $module = '';
            foreach ($this->feature_array as $item) {

                $feature = Functions::getEntity('feature', $item);
                if ($feature['module_id'] != $module) {
                    $moduleArray [] = $feature['module_id'];
                    $module = $feature['module_id'];
                }

            }

            $treeModule = '';
            $treeLoad = '';

            //adicionar features por modulos
            foreach (array_unique($moduleArray) as $module_id) {

                $moduleArrayName = Functions::getEntity('module', $module_id);

                $treeModule .= '{ "name": "' . $moduleArrayName['name'] . '",
                                        "children": [';

                foreach ($arrayFeatureOrder as $item) {

                    $feature = Functions::getEntity('feature', $item['feature_id']);

                    if ($module_id == $feature['module_id']) {

                        $treeLoad .= $this->onLoadTreeJSON($item['feature_id']);

                    }
                }

                $treeModule .= substr($treeLoad, 0, -1);//remove a ultima virgula;
                $treeLoad = '';
                $treeModule .= ']},';
            }

            $tree .= substr($treeModule, 0, -3);//remove 3 ultimos caracteres;


            //fechamento do modulo
            $tree .= ']
                }';

            //fechamento do todo
            $tree .= ']
                }';

            $this->feature_array = [];

            return $tree;

        }
    }

}