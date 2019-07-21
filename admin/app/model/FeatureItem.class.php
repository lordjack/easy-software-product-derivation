<?php

/**
 * FeatureItem Active Record
 * @author  Jackson Meires
 */
class FeatureItem extends TRecord
{
    const TABLENAME = 'feature_item';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'max'; // {max, serial}

    private $feature;


    /**
     * Method get_feature_name
     * Sample of usage: $feature_item->feature->attribute;
     * @returns Module instance
     */
    public function get_feature_name()
    {
        // loads the associated object
        if (empty($this->feature))
            $this->feature = new Feature($this->feature_id);

        // returns the associated object
        return $this->feature->name;
    }


}