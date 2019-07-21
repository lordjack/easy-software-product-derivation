<?php

class Util
{

    const RECORD_TEMPLATE_PATH = __DIR__ . "/template/Record.class.php";
    const LIST_TEMPLATE_PATH = __DIR__ . "/template/List.class.php";
    const FORM_TEMPLATE_PATH = __DIR__ . "/template/Form.class.php";
    const DETALHE_TEMPLATE_PATH = __DIR__ . "/template/Detalhe.class.php";

    public static function successMsg( $msg )
    {

        echo '<div class="col s12">
                <div id="card-alert" class="card green">
                      <div class="card-content white-text">
                        <p><i class="fa fa-check"></i> SUCESSO : ' . $msg . '.</p>
                      </div>
                    </div>
               </div>';
    }

    public static function errorMsg( $msg )
    {
        echo '<div class="col s12">
                    <div id="card-alert" class="card red">
                          <div class="card-content white-text">
                            <p><i class="fa fa-exclamation-circle"></i> ALERTA : ' . $msg . '</p>
                          </div>
                      </div>
               </div>';

    }

    public static function createFile( $templateFile, $newFile )
    {

        if( copy( $templateFile, $newFile ) )
            return true;
        else
            return false;

    }

    public static function getItemsFromPOST( $post )
    {
        $data = [];

        $i = 0;
        $temp = [];

        foreach ( $post as $key => $value ) {

            $str = explode( "_", $key );

            if( $str[0] == "item" ) {

                $position = $str[sizeof($str) - 1];

                if( $position == $i ) {

                    $temp[$key] = $value;

                }else {

                    $data[$i] = $temp;
                    $temp = [];
                    $temp[$key] = $value;

                    $i++;

                }

            }

        }

        $data[$i] = $temp;
        return $data;

    }

    public static function getConfigFileDatabaseName()
    {

        $file = file_get_contents('../database/config.json');
        $config = json_decode($file, true);

        return $config['ini_file'];

    }

}
