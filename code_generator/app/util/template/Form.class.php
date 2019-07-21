<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

class **FORM_CLASS_NAME** extends TPage
{

    private $form;

    public function __construct()
    {

        parent::__construct();

        $this->form = new BootstrapFormBuilder('form_**TABLE_NAME**');
        $this->form->setFormTitle( '**FORM_LABEL**' );
        $this->form->class = 'form_**TABLE_NAME**';

**FORM_FIELD_CREATION_LINE**
**FIELD_SIZE_LINE**
**FIELD_VALIDATION_LINE**

**FORM_FIELD_ADD_LINE**
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<i>* Campos obrigatórios</i>' ) ]);

        $this->form->addAction('Salvar', new TAction(array($this, 'onSave')), 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('**LIST_NAME**', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';

        parent::add($this->form);

    }

    function onSave($param = NULL)
    {

        try {

            TTransaction::open('**DB_CONFIG_FILE**');

            $this->form->validate();

            $object = $this->form->getData('**RECORD_NAME**');

            $object->usuarioalteracao = $_SESSION['usuario'];
            $object->dataalteracao = date("d/m/Y H:i:s");

            $object->store();

            TTransaction::close();

            $action_ok = new TAction( [ '**LIST_NAME**', "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action_ok );

        } catch (Exception $e) {

            new TMessage('error', $e->getMessage());

            TTransaction::rollback();

        }

    }

    function onEdit($param = NULL)
    {

        try {

            if (isset($param['key'])) {

                $key = $param['key'];

                TTransaction::open('**DB_CONFIG_FILE**');

                $object = new **RECORD_NAME**($key);

                $this->form->setData($object);

                TTransaction::close();

            }

        } catch (Exception $e) {


            new TMessage('error', '<b>Error</b> ' . $e->getMessage() . "<br/>");

            TTransaction::rollback();

        }

    }

}

?>