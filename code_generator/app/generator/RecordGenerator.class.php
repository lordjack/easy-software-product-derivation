<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../util/Util.class.php');

class RecordGenerator
{

    private $tableName;
    private $recordName;

    private $filePath;

    function __construct($tableName, $recordName)
    {

        $this->tableName = $tableName;
        $this->recordName = $recordName;

        $this->filePath = '../files/model/' . $this->tableName . '/' . $this->recordName . '.class.php';

    }

    public function generate()
    {

        $createdSuccess = false;

        if (Util::createFile(Util::RECORD_TEMPLATE_PATH, $this->filePath)) {

            if (RecordGenerator::writeRecord()) {

                $createdSuccess = true;

            }

        }

        return $createdSuccess;

    }

    private function writeRecord()
    {

        $codeWritte = false;

        $code = file_get_contents($this->filePath);

        $code = str_replace("**RECORD_NAME**", $this->recordName, $code);
        $code = str_replace("**TABLE_NAME**", $this->tableName, $code);

        if (file_put_contents($this->filePath, $code))
            $codeWritte = true;

        return $codeWritte;

    }

}