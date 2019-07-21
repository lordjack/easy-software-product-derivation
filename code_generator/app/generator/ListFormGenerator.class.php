<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('ListGenerator.class.php');
require_once('FormGenerator.class.php');
require_once('../database/Connection.class.php');
require_once('../util/Util.class.php');

class ListFormGenerator
{

    private $listName;
    private $listTitle;

    private $formName;
    private $formTitle;

    private $recordName;
    private $tableName;

    private $itemsPost;

    function __construct($listName, $listTitle, $formName, $formTitle, $recordName, $tableName, $itemsPost)
    {

        $this->listName = $listName;
        $this->listTitle = $listTitle;

        $this->formName = $formName;
        $this->formTitle = $formTitle;

        $this->recordName = $recordName;
        $this->tableName = $tableName;

        $this->itemsPost = $itemsPost;

    }

    public function generate()
    {

        $gridItems = ListFormGenerator::getAllDatagridItems($this->itemsPost);

        $listGenerator = new ListGenerator($this->listName, $this->listTitle, $this->formName, $this->recordName, $this->tableName, $gridItems);

        if ($listGenerator->generate()) {

            Util::successMsg('> ' . $this->listName . ' criado com sucesso.');

            $formItems = ListFormGenerator::getAllFormItems($this->itemsPost);

            $formGenerator = new FormGenerator($this->listName, $this->formName, $this->formTitle, $this->recordName, $this->tableName, $formItems);

            if ($formGenerator->generate()) {

                Util::successMsg('> ' . $this->formName . ' criado com sucesso.');

            } else {

                Util::errorMsg('> Erro ao criar  ' . $this->formName . '.');

            }

        } else {

            Util::errorMsg('> Erro ao criar  ' . $this->listName . '.');

        }
    }

    public static function getAllDatagridItems($items)
    {

        $data = array();

        foreach ($items as $key => $value) {

            if (array_key_exists("item_grid_" . $key, $value)) {

                $temp = array();

                $temp["column"] = $value["item_column_" . $key];
                $temp["label"] = $value["item_label_" . $key];

                array_push($data, $temp);

            }

        }

        return $data;

    }

    public static function getAllFormItems($items)
    {

        $data = array();

        foreach ($items as $key => $value) {

            if (array_key_exists("item_form_" . $key, $value)) {

                $temp = array();

                $temp["column"] = $value["item_column_" . $key];

                if (array_key_exists("item_label_" . $key, $value))
                    $temp["label"] = $value["item_label_" . $key];

                $temp["widget"] = $value["item_widget_" . $key];

                if (array_key_exists("item_length_" . $key, $value))
                    $temp["length"] = $value["item_length_" . $key];

                $temp["is_nullable"] = $value["item_is_nullable_" . $key];

                array_push($data, $temp);

            }

        }

        return $data;

    }

}

?>