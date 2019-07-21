<?php
/**
 * Module Active Record
 * @author  Jackson Meires
 */
class Module extends TRecord
{
    const TABLENAME = 'module';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}


    private $product;

    /**
     * Method get_product_name
     * Sample of usage: $module->product->attribute;
     * @returns Product instance
     */
    public function get_product_name()
    {
        // loads the associated object
        if (empty($this->product))
            $this->product = new Product($this->product_id);

        // returns the associated object
        return $this->product->description;
    }

}