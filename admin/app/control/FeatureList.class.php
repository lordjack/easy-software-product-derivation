<?php

/**
 * FeatureList Listing
 * @author  Jackson Meires
 */
class FeatureList extends \Adianti\Control\TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;

    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();


        TSession::setValue('feature_id', "");
        TSession::setValue('product_id', "");

        // creates the form
        $this->form = new BootstrapFormWrapper(new TQuickForm('form_search_feature'));

        // create the form fields
        $filter = new TEntry('name');
        $filter_product_id = new \Adianti\Widget\Wrapper\TDBCombo('product_id', 'db_conn', 'Product', 'id', 'name', 'name');
       // $filter_product_id->setValue(1);
        $filter_module_id = new TCombo('module_id');
        $filter->setValue(TSession::getValue('Feature_name'));

        $change_action = new TAction(array($this, 'onChangeAction'));
        $filter_product_id->setChangeAction($change_action);

        $filter_product_id->addValidation('Produto', new TRequiredValidator);
        $filter_module_id->addValidation('Módulo', new TRequiredValidator);

        $this->form->addQuickField('Produto', $filter_product_id, '70%');
        $this->form->addQuickField('Módulo', $filter_module_id, '70%');
        $this->form->addQuickField('Nome', $filter, '70%');

        $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('New'), new TAction(array('FeatureForm', 'onEdit')), 'fa:plus-circle green');

        $this->datagrid = new \Adianti\Wrapper\BootstrapDatagridWrapper(new \Adianti\Widget\Wrapper\TQuickGrid());
        $this->datagrid->style = "width: 100%";

        $this->datagrid->addQuickColumn('ID', 'id', 'center', '10%');
        $this->datagrid->addQuickColumn('Nome', 'title', 'left', '30%');
        $this->datagrid->addQuickColumn('Module', 'module_name', 'left', '20%');
        $this->datagrid->addQuickColumn('Product', 'product_name', 'left', '20%');

        $action_save = new \Adianti\Widget\Datagrid\TDataGridAction(array('FeatureTreeForm', 'onReload'));
        $action_save->setField('id');
        $action_save->setFields(['product_id']);

        $this->datagrid->addQuickAction(_t('Edit'), $action_save, 'id', 'fa:edit blue');
        $this->datagrid->addQuickAction(_t('Delete'), new TDataGridAction(array($this, 'onDelete')), 'id', 'fa:trash red');

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panelForm = new TPanelGroup(('Listagem de Features'));
        $panelForm->add($this->form);

        $panelGrid = new TPanelGroup;
        $panelGrid->add($this->datagrid);
        $panelGrid->addFooter($this->pageNavigation);

        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($panelForm);
        $container->add($panelGrid);

        parent::add($container);
    }

    function onLoad()
    {

    }

    /**
     * Action to be executed when the user changes the combo_change field
     */
    public static function onChangeAction($param)
    {

        TTransaction::open('db_conn');
        $repository = new TRepository('Module');
        $criteria = new \Adianti\Database\TCriteria();

        $criteria->add(new \Adianti\Database\TFilter('product_id', '=', $param['product_id']));
        $criteria->setProperty('order', 'name');

        $collection = $repository->load($criteria, FALSE);

        $arrayModule = [];
        if ($collection) {
            foreach ($collection as $object) {//description
                $arrayModule[$object->id] = $object->name;

            }
        }
        TTransaction::close();

        //  $obj = new StdClass;
        TSession::setValue('product_id', $param['product_id']);
        //  $obj->product_id = $param['product_id']);
        //  TForm::sendData('form_search_feature', $obj);

        TCombo::reload('form_search_feature', 'module_id', $arrayModule);
    }

    function onSearch($param = NULL)
    {
        try {
            $data = $this->form->getData();

            $this->form->validate();

            if (!empty($param['key'])) {
                $filter[] = new TFilter('id', '=', $param['key']);

                TTransaction::open('db_conn');
                $objFeature = new Feature($param['key']);
                $objModule = new Module($objFeature->module_id);
                TTransaction::close();

                $data->product_id = $objModule->product_id;

            } elseif (isset($data->name)) {
                $filter[] = new TFilter('name', 'like', "NOESC:LOWER( '%" . $data->name . "%' )");
                $filter[] = new TFilter('module_id', '=', $data->module_id);

            }

            $module_id = (!empty($data->module_id) ? $data->module_id : $objFeature->module_id);

            $this->onChangeAction(['product_id' => $data->product_id]);
            TScript::create("$(document).ready( function() { $('select[name=" . 'module_id' . "]').val(" . $module_id . "); });");
            $this->form->setData($data);

            TSession::setValue('Feature_filter', $filter);

            $param = array();
            $param['offset'] = 0;
            $param['first_page'] = 1;
            $this->onReload($param);
        } catch (Exception $e) {
            new \Adianti\Widget\Dialog\TMessage('error', $e->getMessage());
        }
    }

    function onReload($param = NULL)
    {
        try {
            TTransaction::open('db_conn');

            $repository = new TRepository('Feature');
            $limit = 10;

            $criteria = new TCriteria;

            if (empty($param['order'])) {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }

            if (TSession::getValue('Feature_filter')) {
                $filters = TSession::getValue('Feature_filter');
                foreach ($filters as $filter) {
                    $criteria->add($filter);
                }
            }

            $criteria->add(new \Adianti\Database\TFilter('level', '=', '1'));

            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);

            $objects = $repository->load($criteria);

            $this->datagrid->clear();
            if ($objects) {
                foreach ($objects as $object) {
                    $this->datagrid->addItem($object);
                }
            }

            $criteria->resetProperties();
            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit

            TTransaction::close();
            $this->loaded = true;
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }

    function onDelete($param)
    {
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param);

        new TQuestion('Do you really want to delete ?', $action);
    }

    function Delete($param)
    {
        try {
            $key = $param['id'];

            TTransaction::open('db_conn');
            $object = new Feature($key);
            $object->delete();
            TTransaction::close();

            $this->onReload($param);

            new TMessage('info', "Record Deleted");
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());

            TTransaction::rollback();
        }
    }

}
