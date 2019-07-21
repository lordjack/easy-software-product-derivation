<?php

/**
 * Product Registration
 * @author  Jackson Meires
 */
class ProductForm extends TStandardForm
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
        parent::setActiveRecord('Product');

        // create the form fields
        $id = new THidden('id');
        $name = new TEntry('name');
        $description = new TEntry('description');
        $urlRepository = new TEntry('url_repository');
        $loginRepository = new TEntry('login_repository');
        $passwordRepository = new TPassword('password_repository');
        $logo = new TFile('logo');
        $dirDatabase = new TEntry('dir_file_database');
        $nameDatabase = new TEntry('name_file_database');

        $urlRepository->setProperty('placeholder', 'https://user@domain.com/username/repository.git');
        $dirDatabase->setProperty('placeholder', 'app/database/');
        $nameDatabase->setProperty('placeholder', 'dump_db_ceres.sql');

        $description->addValidation('Título', new TRequiredValidator);
        $name->addValidation('Nome', new TRequiredValidator);
        $urlRepository->addValidation('URL Repositório', new TRequiredValidator);
        $loginRepository->addValidation('Login Repositório', new TRequiredValidator);
        $passwordRepository->addValidation('Password Repositório', new TRequiredValidator);

        // add the fields
        $this->form->addQuickField('ID', $id, '30%');
        $this->form->addQuickField('Título <b style="color: red;">*</b>', $description, '70%');
        $this->form->addQuickField('Nome <b style="color: red;">*</b>', $name, '70%');
        $this->form->addQuickField('URL Repositório <b style="color: red;">*</b>', $urlRepository, '70%');
        $this->form->addQuickField('Login Repositório <b style="color: red;">*</b>', $loginRepository, '70%');
        $this->form->addQuickField('Password Repositório <b style="color: red;">*</b>', $passwordRepository, '70%');
        $this->form->addQuickField('Logo', $logo, '70%');
        $this->form->addQuickField('Nome Arquivo do BD', $nameDatabase, '70%');
        $this->form->addQuickField('Diretório Arquivo do BD', $dirDatabase, '70%');
        $this->form->addQuickField('OBS', new \Adianti\Widget\Form\TLabel('<b style="color: red;">*</b> Campos Obrigatórios'), '70%');
        $id->setEditable(FALSE);

        // define the form action
        $btn = $this->form->addQuickAction(_t('Save'), new TAction([$this, 'onSave']), 'fa:floppy-o');
        $btn->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction(_t('Clear'), new TAction([$this, 'onEdit']), 'fa:eraser red');
        $this->form->addQuickAction('Voltar', new TAction(['ProductList', 'onLoad']), '');

        // add the form to the page
        $panelForm = new TPanelGroup('Formulário de Produto');
        $panelForm->add($this->form);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($panelForm);

        parent::add($container);
    }
}
