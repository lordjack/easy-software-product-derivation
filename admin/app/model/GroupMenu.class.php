<?php
/**
 * GroupMenu Active Record
 * @author  Jackson Meires
 */
class GroupMenu extends TRecord
{
    const TABLENAME = 'group_menu';
    const PRIMARYKEY= 'id';
    const IDPOLICY =  'serial'; // {max, serial}

    /**
     * Method get_product_name
     * Sample of usage: $module->product->attribute;
     * @returns Product instance
     */
    public function get_product_name()
    {
        // loads the associated object
        if (!empty($this->product_id))
            $this->product = new Product($this->product_id);

        // returns the associated object
        return $this->product->description;
    }
}