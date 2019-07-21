<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);
class **LIST_CLASS_NAME** extends TPage
{
    private $form;
    private $datagrid;
    public function __construct()
{
    parent::__construct();
    $this->form = new BootstrapFormWrapper(new TQuickForm('list_**TABLE_NAME**'));
    $opcao = new TCombo('opcao');
    $nome  = new TEntry('nome');
    $items= array();
    $items['**SEARCH_ITEM_VALUE**'] = '**SEARCH_ITEM_LABEL**';
    $opcao->addItems($items);
    $opcao->setValue('**SEARCH_ITEM_VALUE**');
    $this->form->addQuickField( 'Selecione o campo', $opcao, '80%' );
    $this->form->addQuickField( 'Buscar', $nome, '80%' );
    $find_button = $this->form->addQuickAction( 'Buscar', new TAction(array($this, 'onSearch')), 'fa:search');
    $find_button->class = 'btn btn-sm btn-primary';
    $new_button = $this->form->addQuickAction( 'Novo' , new TAction(array('**FORM_NAME**', 'onEdit')), 'fa:file');
    $new_button->class = 'btn btn-sm btn-primary';
    $this->form->addQuickAction( 'Limpar Busca' , new TAction(array($this, 'onClear')), 'fa:eraser red');
    //DATAGRID ------------------------------------------------------------------------------------------
    $this->datagrid = new \Adianti\Wrapper\BootstrapDatagridWrapper(new \Adianti\Widget\Wrapper\TQuickGrid());
    **DATA_GRID_ITEMS_LINE**
$actionEdit = new TDataGridAction(array('**FORM_NAME**', 'onEdit'));
    $actionEdit->setLabel('Editar');
    $actionEdit->setImage( "fa:pencil-square-o blue fa-lg" );
    $actionEdit->setField('id');
    $actionDelete = new TDataGridAction(array($this, 'onDelete'));
    $actionDelete->setLabel('Deletar');
    $actionDelete->setImage( "fa:trash-o red fa-lg" );
    $actionDelete->setField('id');
    $this->datagrid->addAction($actionEdit);
    $this->datagrid->addAction($actionDelete);
    $this->datagrid->createModel();
    //FIM DATAGRID -----------------------------------------------------------------------------------------
    $container = new TVBox();
    $container->style = "width: 100%";
    $container->add( TPanelGroup::pack( '**LIST_LABEL**', $this->form ) );
    $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
    parent::add( $container );
}
    public function onClear() {
    if (TSession::getValue('filter_**RECORD_NAME**')) {
        TSession::setValue('filter_**RECORD_NAME**', null);
    }
    $this->onReload();
}
    public function onReload( $param = NULL )
{
    try {
        TTransaction::open('**DB_CONFIG_FILE**');
        $repository = new TRepository('**RECORD_NAME**');
        $criteria = new TCriteria();
        if (TSession::getValue('filter_**RECORD_NAME**')) {
            $filters = TSession::getValue('filter_**RECORD_NAME**');
            foreach ($filters as $filter) {
                $criteria->add($filter);
            }
        }
        $objects = $repository->load( $criteria, FALSE );
        $this->datagrid->clear();
        if ( !empty( $objects ) ) {
            foreach ( $objects as $object ) {
                $this->datagrid->addItem( $object );
            }
        }
        $criteria->resetProperties();
        TTransaction::close();
    } catch ( Exception $ex ) {
        TTransaction::rollback();
        new TMessage( "error",  $ex->getMessage()  );
    }
}
    public function onSearch()
{
    $data = $this->form->getData();
    try {
        if( !empty( $data->opcao ) && !empty( $data->nome ) ) {
            $filter = [];
            switch ( $data->opcao ) {
                default:
                    $filter[] = new TFilter( "LOWER(" . $data->opcao . ")", "LIKE", "NOESC:LOWER( '%" . $data->nome . "%' )" );
                    break;
            }
            TSession::setValue('filter_**RECORD_NAME**', $filter);
            $this->form->setData( $data );
            $this->onReload();
        } else {
            TSession::setValue('filter_**RECORD_NAME**', '');
            $this->onReload();
            $this->form->setData( $data );
            new TMessage( "error", "Selecione uma opção e informe os dados da busca corretamente!" );
        }
    } catch ( Exception $ex ) {
        TTransaction::rollback();
        $this->form->setData( $data );
        new TMessage( "error",  $ex->getMessage() .'.' );
    }
}
    public function onDelete( $param = NULL )
{
    if( isset( $param[ "key" ] ) ) {
        $action_ok = new TAction( [ $this, "Delete" ] );
        $action_cancel = new TAction( [ $this, "onReload" ] );
        $action_ok->setParameter( "key", $param[ "key" ] );
        new TQuestion( "Deseja remover o registro?", $action_ok, $action_cancel,  "Deletar");
    }
}
    function Delete( $param = NULL )
    {
        try {
            TTransaction::open('**DB_CONFIG_FILE**');
            $object = new **RECORD_NAME**($param['key']);
            $object->delete();
            TTransaction::close();
            $this->onReload();
            new TMessage( "info", "Registro deletado com sucesso!" );
        } catch ( Exception $ex ) {
            TTransaction::rollback();
            new TMessage( "error",  $ex->getMessage() .'.' );
        }
    }
    public function show()
{
    $this->onReload();
    parent::show();
}
}
?>