<?php

class LabelGenerator
{

    private static $i;

    public static function label( $columnName, $i ) {

        LabelGenerator::$i = $i;

        if($columnName == 'id' || $columnName == 'dataalteracao' || $columnName == 'usuarioalteracao')
            return '';

        $columnLabel = str_replace( "_", " ", $columnName );
        $columnLabel = str_replace( " id", "", $columnLabel );
        $columnLabel = ucwords( $columnLabel, " " );

        return LabelGenerator::createInput( $columnLabel );

    }

    private static function createInput( $columnLabel ) {

        return '<input type="text" name="item_label_' . LabelGenerator::$i .'" value="' . $columnLabel . '" />';

    }

    public static function className( $table ) {

        $className = ucwords( $table, "_" );
        $className = str_replace( "_", "", $className );

        return $className;

    }

}