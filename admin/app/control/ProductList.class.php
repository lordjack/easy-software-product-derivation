<?php

/**
 * ProductList Listing
 * @author  <your name here>
 */
class ProductList extends TStandardList
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

        parent::setDatabase('db_conn'); // defines the database
        parent::setActiveRecord('Product'); // defines the active record
        parent::setFilterField('name'); // defines the filter field
        parent::setDefaultOrder('id', 'desc');

        // creates the form
        $this->form = new BootstrapFormWrapper(new TQuickForm('form_search_product'));

        // create the form fields
        $filter = new TEntry('name');
        $filter->setValue(TSession::getValue('product_name'));

        // add the fields
        $this->form->addQuickField('Nome:', $filter, '70%');

        // define the form action
        $btn = $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('New'), new TAction(array('ProductForm', 'onEdit')), 'fa:plus-circle green');

        // creates a DataGrid
        $this->datagrid = new BootstrapDatagridWrapper(new TQuickGrid);
        $this->datagrid->style = "width: 100%";

        // creates the datagrid columns
        $this->datagrid->addQuickColumn('ID', 'id', 'center', '10%');
        $this->datagrid->addQuickColumn(_t('Name'), 'name', 'left', '30%');
        $this->datagrid->addQuickColumn('Description', 'description', 'left', '70%');

        // add the actions to the datagrid
        $this->datagrid->addQuickAction(_t('Edit'), new TDataGridAction(array('ProductForm', 'onEdit')), 'id', 'fa:edit blue');
        $this->datagrid->addQuickAction(_t('Delete'), new TDataGridAction(array($this, 'onDelete')), 'id', 'fa:trash red');

        // create the datagrid model
        $this->datagrid->createModel();

        // creates the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());

        $panelForm = new TPanelGroup(('Listagem de Produtos'));
        $panelForm->add($this->form);

        $panelGrid = new TPanelGroup;
        $panelGrid->add($this->datagrid);
        $panelGrid->addFooter($this->pageNavigation);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($panelForm);
        $container->add($panelGrid);

        // add the container inside the page
        parent::add($container);
    }

    function onLoad()
    {

    }
}
