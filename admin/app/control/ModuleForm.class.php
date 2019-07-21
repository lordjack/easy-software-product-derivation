<?php
/**
 * Module Registration
 * @author  Jackson Meires
 */
class ModuleForm extends TStandardForm
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

        $this->form = new BootstrapFormWrapper(new TQuickForm('form_product'));
        $this->form->style = 'width: 100%';

        // defines the database
        parent::setDatabase('db_conn');

        // defines the active record
        parent::setActiveRecord('Module');

        // create the form fields
        $id = new THidden('id');
        $product_id = new TDBCombo('product_id', 'db_conn', 'Product', 'id', 'name', 'name');
        $name = new TEntry('name');
        $description = new TEntry('description');
        $title = new TEntry('title');
        $imagem= new \Adianti\Widget\Form\TFile('imagem');

        $title->addValidation('Título', new TRequiredValidator);
        $name->addValidation('Nome', new TRequiredValidator);
        $product_id->addValidation('Produto', new TRequiredValidator);

        // add the fields
        $this->form->addQuickField('ID', $id, '30%');
        $this->form->addQuickField('Título <b style="color: red;">*</b>', $title, '70%');
        $this->form->addQuickField('Nome <b style="color: red;">*</b>', $name, '70%');
        $this->form->addQuickField('Produto <b style="color: red;">*</b>', $product_id, '70%');
        $this->form->addQuickField('Descrição', $description, '70%');
        $this->form->addQuickField('Imagem', $imagem, '70%');
        $this->form->addQuickField('OBS', new \Adianti\Widget\Form\TLabel('<b style="color: red;">*</b> Campos Obrigatórios'), '70%');

        $id->setEditable(FALSE);

        // define the form action
        $btn = $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('Clear'), new TAction(array($this, 'onEdit')), 'fa:eraser red');
        $this->form->addQuickAction('Voltar', new TAction(array('ModuleList', 'onLoad')), '');

        // add the form to the page
        $panelForm = new TPanelGroup('Formulário de Módulo');
        $panelForm->add($this->form);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($panelForm);

        parent::add($container);
    }
}
