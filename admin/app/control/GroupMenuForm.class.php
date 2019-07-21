<?php
/**
 * GroupMenu Registration
 * @author  Jackson Meires
 */
class GroupMenuForm extends TStandardForm
{
    protected $form; // form
    protected $notebook;

    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormWrapper(new TQuickForm('form_group_menu'));
        $this->form->style = 'width: 100%';

        // defines the database
        parent::setDatabase('db_conn');

        // defines the active record
        parent::setActiveRecord('GroupMenu');

        // create the form fields
        $id = new THidden('id');
        $name = new TEntry('name');
        $product_id = new TDBCombo('product_id', 'db_conn', 'Product', 'id', 'name', 'name');

        $name->addValidation('Nome', new TRequiredValidator);
        $product_id->addValidation('Produto', new TRequiredValidator);

        // add the fields
        $this->form->addQuickField('ID', $id, '30%');
        $this->form->addQuickField('Nome <b style="color: red;">*</b>', $name, '70%');
        $this->form->addQuickField('Produto <b style="color: red;">*</b>', $product_id, '70%');
        $this->form->addQuickField('OBS', new \Adianti\Widget\Form\TLabel('<b style="color: red;">*</b> Campos Obrigatórios'), '70%');
        $id->setEditable(FALSE);

        // define the form action
        $btn = $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('Clear'), new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addQuickAction('Voltar', new TAction(array('GroupMenuList', 'onLoad')), '');

        // add the form to the page
        $panelForm = new TPanelGroup('Formulário de Grupo Menu');
        $panelForm->add($this->form);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($panelForm);

        parent::add($container);
    }
}
