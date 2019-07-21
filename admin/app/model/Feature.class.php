<?php

/**
 * Feature Active Record
 * @author  Jackson Meires
 */
class Feature extends TRecord
{
    const TABLENAME = 'feature';
    const PRIMARYKEY = 'id';
    const IDPOLICY = 'serial'; // {max, serial}

    private $module;
    private $feature_pages;

    /**
     * Method addFeaturePage
     * Add a FeaturePage to the Customer
     * @param $object Instance of FeaturePage
     */
    public function addFeaturePage(FeaturePage $object)
    {
        $this->feature_pages[] = $object;
    }

    /**
     * Method getFeaturePages
     * Return the Customer' FeaturePage's
     * @return Collection of FeaturePage
     */
    public function getFeaturePages()
    {
        return $this->feature_pages;
    }

    /**
     * Method get_module_name
     * Sample of usage: $feature->module->attribute;
     * @returns Module instance
     */
    public function get_module_name()
    {
        // loads the associated object
        if (empty($this->module))
            $this->module = new Module($this->module_id);

        // returns the associated object
        return $this->module->name;
    }

    /**
     * Method get_product_name
     * Sample of usage: $module->product->attribute;
     * @returns Module instance
     */
    public function get_product_name()
    {
        // returns the associated object
        return (new Module($this->module_id))->product_name;
    }

    /**
     * Method get_product_name
     * Sample of usage: $module->product->attribute;
     * @returns Module instance
     */
    public function get_product_id()
    {
        // returns the associated object
        return (new Module($this->module_id))->product_id;
    }

    /**
     * Delete the object and its aggregates
     * @param $id object ID
     */
    public function delete($id = NULL)
    {
        // delete the related Feature objects
        $id = isset($id) ? $id : $this->id;

        // delete Feature related objects
        Feature::where('feature_id', '=', $id)->delete();

        parent::deleteComposite('FeaturePage', 'feature_id', $id);

        // delete the object itself
        parent::delete($id);
    }

    /**
     * Reset aggregates
     */
    public function clearParts()
    {
        $this->feature_pages = [];
    }

    /**
     * Store the object and its aggregates
     */
    public function store()
    {
        // store the object itself
        parent::store();

        parent::saveComposite('FeaturePage', 'feature_id', $this->id, $this->feature_pages);
    }


    /**
     * Load the object and its aggregates
     * @param $id object ID
     */
    public function load($id)
    {
        $this->feature_pages = parent::loadComposite('FeaturePage', 'feature_id', $id);

        // load the object itself
        return parent::load($id);
    }
}