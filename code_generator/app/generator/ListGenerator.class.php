<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require_once('../util/Util.class.php');

class ListGenerator
{

    private $tableName;
    private $recordName;
    private $listName;
    private $listTitle;
    private $formName;
    private $dataGridItems;

    private $filePath;

    function __construct($listName, $listTitle, $formName, $recordName, $tableName, $dataGridItems)
    {

        $this->listName = $listName;
        $this->listTitle = $listTitle;
        $this->formName = $formName;

        $this->recordName = $recordName;
        $this->tableName = $tableName;

        $this->dataGridItems = $dataGridItems;

       $this->filePath = '../files/control/' . $this->tableName . '/' . $this->listName . '.class.php';

    }

    public function generate()
    {

        $createdSuccess = false;

        if (Util::createFile(Util::LIST_TEMPLATE_PATH, $this->filePath)) {

            if ($this->writeList()) {

                $createdSuccess = true;

            }

        }

        return $createdSuccess;

    }

    private function writeList()
    {

        $codeWritten = false;

        $code = file_get_contents($this->filePath);

        $code = str_replace("**LIST_CLASS_NAME**", $this->listName, $code);
        $code = str_replace("**LIST_LABEL**", $this->listTitle, $code);

        $code = str_replace("**SEARCH_ITEM_VALUE**", $this->dataGridItems[0]["column"], $code);
        $code = str_replace("**SEARCH_ITEM_LABEL**", $this->dataGridItems[0]["label"], $code);

        $code = str_replace("**FORM_NAME**", $this->formName, $code);
        $code = str_replace("**TABLE_NAME**", $this->tableName, $code);
        $code = str_replace("**DB_CONFIG_FILE**", Util::getConfigFileDatabaseName(), $code);
        $code = str_replace("**RECORD_NAME**", $this->recordName, $code);

        $data_items = ListGenerator::createDatagridItems($this->dataGridItems);

        $code = str_replace("**DATA_GRID_ITEMS_LINE**", $data_items, $code);

        if (file_put_contents($this->filePath, $code))
            $codeWritten = true;

        return $codeWritten;

    }

    public static function createDatagridItems($items)
    {

        $itemsCode = '';

        foreach ($items as $item) {

            $itemsCode .= str_repeat(' ', 8) . "\$dg" . $item["column"] . " = new TDataGridColumn('" . $item["column"] . "', '" . $item["label"] . "', 'left', 200);\r\n";

        }

        $itemsCode .= "\r\n";

        foreach ($items as $item) {


            $itemsCode .= str_repeat(' ', 8) . "\$this->datagrid->addColumn(\$dg" . $item["column"] . ");\r\n";

        }

        return $itemsCode;

    }

}

?>