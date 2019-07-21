<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

class **DETALHE_CLASS_NAME** extends TPage
{

    private $form;
    private $datagrid;

    public function __construct()
    {

        parent::__construct();

        $this->form = new BootstrapFormBuilder('detalhe_**TABLE_NAME**');
        $this->form->setFormTitle('**DETALHE_LABEL**');
        $this->form->class = 'detalhe_**TABLE_NAME**';

**FORM_FIELD_CREATION_LINE**
**FIELD_SIZE_LINE**
**FIELD_VALIDATION_LINE**

        $action1 = new TAction(array($this, 'onSave'));
        $action1->setParameter('key', '' . filter_input(INPUT_GET, 'key') . '');
        $action1->setParameter('fk', '' . filter_input(INPUT_GET, 'fk') . '');

**FORM_FIELD_ADD_LINE**
        $this->form->addFields([new TLabel('')], [TElement::tag('label', '<i>* Campos obrigatórios</i>' ) ]);

        $this->form->addAction('Salvar', $action1, 'fa:save')->class = 'btn btn-sm btn-primary';
        $this->form->addAction('Voltar', new TAction(array('**CLASSE_PAI_NOME**', 'onReload')), 'fa:arrow-left')->class = 'btn btn-sm btn-primary';

        $this->datagrid = new \Adianti\Wrapper\BootstrapDatagridWrapper(new \Adianti\Widget\Wrapper\TQuickGrid());

        **DATA_GRID_ITEMS_LINE**
        $actionEdit = new TDataGridAction(array($this, 'onEdit'));
        $actionEdit->setButtonClass("btn btn-default");
        $actionEdit->setLabel('Editar');
        $actionEdit->setImage("fa:pencil-square-o blue fa-lg");
        $actionEdit->setField('id');
        $actionEdit->setFk('**FK_NAME**');

        $actionDelete = new TDataGridAction(array($this, 'onDelete'));
        $actionDelete->setButtonClass("btn btn-default");
        $actionDelete->setLabel('Deletar');
        $actionDelete->setImage("fa:trash-o red fa-lg");
        $actionDelete->setField('id');
        $actionDelete->setFk('**FK_NAME**');

        $this->datagrid->addAction($actionEdit);
        $this->datagrid->addAction($actionDelete);

        $this->datagrid->createModel();

        $container = new TVBox();
        $container->style = "width: 100%";

        $container->add($this->form);
        $container->add(TPanelGroup::pack(NULL, $this->datagrid));

        parent::add($container);

    }

    function onReload()
    {

        TTransaction::open('**DB_CONFIG_FILE**');

        $repository = new TRepository('**RECORD_NAME**');

        $criteria = new TCriteria;

        $criteria->add(new TFilter('**FK_NAME**', '=', filter_input(INPUT_GET, 'fk')));

        $objects = $repository->load($criteria, FALSE);

        $this->datagrid->clear();

        if (!empty($objects)) {

            foreach ($objects as $object) {

                $this->datagrid->addItem($object);

            }

        }

        TTransaction::close();

        $this->loaded = true;

    }

    function onDelete($param = NULL)
    {

        if (isset($param["key"])) {

            $param = [
                "key" => $param[ "key" ],
                "fk"  => $param[ "fk" ]
            ];

            $action_ok = new TAction([$this, "Delete"]);
            $action_cancel = new TAction([$this, "onReload"]);

            $action_ok->setParameters( $param );
            $action_cancel->setParameters( $param );

            $this->onReload( $param );

            new TQuestion("Deseja remover o registro?", $action_ok, $action_cancel);
        }

    }

    function Delete($param = NULL)
    {

        $key = $param['key'];

        try {

            TTransaction::open('**DB_CONFIG_FILE**');

            $object = new **RECORD_NAME**($key);
            $object->delete();

            $this->onReload( $param );

            new TMessage("info", "Registro deletado com sucesso!");

            TTransaction::close();

        } catch (Exception $ex) {

            new TMessage('error', $ex->getMessage());

            TTransaction::rollback();

        }

        $this->onReload();

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

            $action = new TAction( [ "**DETALHE_CLASS_NAME**", "onReload" ] );
            $action->setParameter( "fk", $param[ "fk" ] );

            new TMessage("info", "Registro salvo com sucesso!", $action);

        } catch (Exception $ex) {

            new TMessage('error', $ex->getMessage());
            TTransaction::rollback();

        }

    }

    function onEdit($param = NULL)
    {

        try {

            if (isset($param['key'])) {

                $key = $param['key'];

                TTransaction::open('**DB_CONFIG_FILE**');

                $obj = new **RECORD_NAME**($key);

                $this->form->setData($obj);

                TTransaction::close();

            }

        } catch (Exception $ex) {

            new TMessage("Error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage());

            TTransaction::rollback();

        }

    }

    function show()
    {

        $this->onReload();

        parent::show();

    }

}