<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

use Lib\Functions;

/**
 * Feature Tree Form Registration
 * @author  Jackson Meires
 */
class FeatureTreeForm extends TPage
{

    private $form;
    private $alertBox;
    private $tree;

    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();

        echo "<style> .tform .tformaction>td{ background-color: white !important; } </style>";

        $this->form = new \Adianti\Widget\Wrapper\TQuickForm('form_tree');
        $this->form->class = 'tform';
        $this->form->setFormTitle('Pesquisa e Formlário');

        // create the form fields
        $id = new TEntry('id');
        $id->style = "visibility: hidden";
        $level = new TEntry('level');
        $level->style = "visibility: hidden";

        $name = new TEntry('name');
        $level = new \Adianti\Widget\Form\THidden('level');
        $name_pai = new TEntry('name_pai');
        $pesquisar_feature = new TEntry('pesquisar_feature');

        $this->form->addQuickField('<h5><b>Pesquisar Feature', $pesquisar_feature, 200);
        $row = $this->form->addRow();
        $cell = $row->addCell(new TLabel(''));
        $cell->colspan = 2;
        $cell->style = 'height:5px; border-top: 1px solid gray;';
        $this->form->addQuickField('<h5><b>Feature Selecionada', $name_pai, 200);
        $this->form->addQuickField('<h5><b>Nova Feature', $name, 200);

        $product_id = filter_input(INPUT_GET, 'product_id');

        $action_save = new \Adianti\Control\TAction(array($this, 'onSave'));
        $action_save->setParameter('product_id', $product_id);

        $action_edit = new \Adianti\Control\TAction(array($this, 'onFeatureEdit'));
        $action_edit->setParameter('product_id', $product_id);

        $action_delete = new \Adianti\Control\TAction(array($this, 'onDelete'));
        $action_delete->setParameter('product_id', $product_id);

        $this->form->addQuickField('', $id, 0);
        $this->form->addQuickField('', $level, 0);
        $this->form->addQuickAction('Salvar', $action_save, 'fa:floppy-o')->class = 'btn btn-sm btn-primary';
        $this->form->addQuickAction('Editar', $action_edit, 'fa:edit blue')->class = 'btn btn';
        $this->form->addQuickAction('Deletar', $action_delete, 'fa:eraser white')->class = 'btn btn-danger';

        $div = new TElement('div');
        $div->class = 'row';

        $divCol1 = new TElement('div');
        $divCol1->class = 'col-sm-6';

        $divCol2 = new TElement('div');
        $divCol2->class = 'col-sm-6';

        $this->tree = new TElement('div');
        $this->tree->id = 'treeview-disabled';
        $this->tree->style = "box-shadow: 2px 3px 3px #A4A4A4;";

        $divForm = new TElement('div');
        $divForm->id = 'disabled-output';
        $divForm->add($this->form);

        $this->alertBox = new TElement('div');

        $divCol1->add($this->alertBox);
        $divCol1->add('<b>Árvore</b>');
        $divCol1->add($this->tree);
        $divCol2->add($this->form);

        $div->add($divCol1);
        $div->add($divCol2);

        $panel = new TPanelGroup('<b>Formulário Árvore de Feature</b>');
        $panel->add($div);

        $voltar = new \Adianti\Widget\Form\TButton('Voltar');
        $voltar->setLabel('Voltar');
        $voltar->setImage('fa:chevron-circle-left blue');
        $voltar->class = 'btn btn-default ';

        if (filter_input(INPUT_GET, 'key')) {
            $voltar->addFunction("__adianti_load_page('index.php?class=FeatureList&method=onSearch&key=" . filter_input(INPUT_GET, 'key') . "');");
        } else {
            $voltar->addFunction("__adianti_load_page('index.php?class=FeatureList');");
        }

        $panel->add("<br>");
        $panel->add($voltar);

        $panel->style = "box-shadow: 3px 5px 5px #A4A4A4;";

        $vbox = new TVBox;
        $vbox->add($panel);

        $vbox->class = 'col-sm-12 col-md-12 col-lg-9';

        parent::add($vbox);


    }


    public function onSave($param)
    {
        if (!empty($param['id']) && !empty($param['name'])) {
            try {
                $objectForm = $this->form->getData();

                TTransaction::open('db_conn');

                $keyPai = $objectForm->id;

                $object = new Feature();
                $objectPai = new Feature($keyPai);

                if ($objectForm->name) {

                    $object->title = $objectForm->name;
                    $object->name = Functions\Util::remover_caracter($objectForm->name);
                    $object->type = 'OPTIONAL';
                    $object->module_id = 1;
                    $object->group_menu_id = 1;
                    $object->level = $objectPai->level + 1;
                    $object->feature_id = $keyPai;
                    $object->store();

                    $param = $object->toArray();
                    $param['product_id'] = filter_input(INPUT_GET, 'product_id');

                    $this->onFeatureEdit($param);

                    $this->alertBox->add(new TAlert(' btn-success', 'Registro adicionado com sucesso!'));

                } else {

                    $objectPai->name = $objectForm->name_pai;
                    $objectPai->title = $objectForm->title;
                    $objectPai->name = $objectForm->name;
                    $objectPai->type = $objectForm->type;
                    $objectPai->group_menu_id = $objectForm->group_menu_id;
                    $objectPai->module_id = $objectForm->module_id;
                    $objectPai->store();

                    $this->alertBox->add(new TAlert(' btn-success', 'Registro alterado com sucesso!'));

                }

                TTransaction::close();

                $this->onLoadTreeView();

            } catch (Exception $e) {

                new TMessage('error', $e->getMessage());

                TTransaction::rollback();
            }
        } else {
            $this->form->setData($this->form->getData());
            if (empty($param['name'])) {
                new TMessage('error', 'Informe um nome da feature!');
            } else {
                new TMessage('error', 'Selecione um feature para adicionar a um item da arvore!');

            }
        }
    }


    public function onDelete($param)
    {
        if (!empty($param['id'])) {
            $this->form->setData($this->form->getData());

            $key = $param['id'];

            $action1 = new TAction(array($this, 'Delete'));

            $action1->setParameter('key', $key);
            $action1->setParameter('product_id', filter_input(INPUT_GET, 'product_id'));

            new TQuestion('Deseja realmente excluir o registro ?', $action1);
        } else {
            new TMessage('error', 'Selecione uma feature para ser removida');
        }
    }

    public function onFeatureEdit($param)
    {
        if (!empty($param['id'])) {
            $this->form->setData($this->form->getData());

            $key = $param['id'];
            $product_id = $param['product_id'];

            TScript::create("__adianti_load_page('index.php?class=FeatureForm&method=onEdit&key=$key&product_id=$product_id');");

        } else {
            new TMessage('error', 'Selecione uma feature para ser editada');
        }

    }

    public function Delete($param)
    {
        try {
            $key = $param['key'];
            $param['product_id'] = filter_input(INPUT_GET, 'product_id');

            TTransaction::open('db_conn');

            $object = new Feature($key);

            $object->delete();

            $this->alertBox->add(new TAlert(' btn-danger', 'Registro removido com sucesso!'));

            TTransaction::close();
            \Adianti\Core\AdiantiCoreApplication::loadPage('FeatureTreeForm', 'onReload', $param);
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }

    }

    public function onReload($param = null)
    {
        if (!TSession::getValue('feature_id')) {
            TSession::setValue('feature_id', filter_input(INPUT_GET, 'id'));

        }
        $this->onLoadTreeView();

    }

    public function onLoadTreeView()
    {

        try {

            TTransaction::open('db_conn');

            $repository = new \Adianti\Database\TRepository('Feature');

            $criteria = new \Adianti\Database\TCriteria();
            $criteria->add(new \Adianti\Database\TFilter('level', '=', 1));
            $criteria->add(new \Adianti\Database\TFilter('id', '=', TSession::getValue('feature_id')));

            $tree = "[";
            $objects = $repository->load($criteria);

            if ($objects) {

                foreach ($objects as $object) {


                    if ($this->haveChild($object->id)) {

                        $noh = "{
                                    text: '" . $object->title . "',
                                    level: " . $object->level . ",
                                    id: " . $object->id . ",
                                    color: 'white',
                                    backColor: 'green',
                                    nodes: [" . $this->node($object->id) . "]
                                },";

                    } else {

                        $noh = "{ 
                                    text: '" . $object->title . "',
                                    level: '" . $object->level . "',
                                    color: 'white',
                                    backColor: 'green',
                                    id: " . $object->id . "
                                },";

                    }

                    $tree .= $noh;

                }
            }


            $tree .= "]";

            $element = '

                    $(\'#name\').prop(\'disabled\', false);
                    $(\'#name_pai\').prop(\'disabled\', false);

                    var $disabledTree = $(\'#treeview-disabled\').treeview({
                      data: ' . $tree . ',
                      onNodeSelected: function (event, node) {
                        $(\'#level\').val( node.level );
                        $(\'#name_pai\').val( node.text );
                        $(\'#id\').val( node.id );
                                                
                        $(\'#name\').prop(\'disabled\', false);
                        $(\'#name_pai\').prop(\'disabled\', true);
                        $(\'#tbutton_adicionar\').text(\'Adicionar\');

                            if( node.level == 1 ){
                                $(\'#tbutton_remover\').hide();

                           }else{

                                $(\'#tbutton_remover\').show();

                           }
                      },
                        onNodeUnselected: function (event, node) {
                       
                           if( node.level != 1 ){
                                $(\'#name\').prop(\'disabled\', true);
                                $(\'#name_pai\').prop(\'disabled\', false);
                                $(\'#tbutton_adicionar\').text(\'Editar\');

                           }

                      }

                    });


                    var findExpandibleNodess = function() {
                      return $disabledTree.treeview(\'search\', [ $(\'#pesquisar_feature\').val(), { ignoreCase: false, exactMatch: false } ]);
                      
                    };
                    var expandibleNodes = findExpandibleNodess();

                    $(\'#pesquisar_feature\').on(\'keyup\', function (e) {
                        expandibleNodes = findExpandibleNodess();
                        $(\'.expand-node\').prop(\'disabled\', !(expandibleNodes.length >= 1));
                        
                    });

                    $disabledTree.treeview(\'expandAll\', { levels: 3, silent: false });
                  ';

            TScript::create($element);


            TTransaction::close();

        } catch (Exception $e) {
            new TMessage('erro', $e->getMessage());
        }


    }


    private function node($id)
    {

        $repository = new \Adianti\Database\TRepository('Feature');

        $criterio = new \Adianti\Database\TCriteria();
        $criterio->add(new \Adianti\Database\TFilter('feature_id', '=', $id));

        $objects = $repository->load($criterio);
        $noh = "";

        foreach ($objects as $object) {

            if ($this->haveChild($object->id)) {
                $noh .= "{
                        text: '" . $object->title . "',
                        level: '" . $object->level . "',
                        id: '" . $object->id . "',
                        nodes: [" . $this->node($object->id) . "]
                    },";

            } else {
                $noh .= "{ 
                        text: '" . $object->title . "',
                        level: '" . $object->level . "',
                        id: " . $object->id . "
                      },";

            }

        }

        return $noh;

    }


    private function haveChild($id)
    {

        $childs = false;

        $conn = TTransaction::get();

        //Numero de niveis que a Tree tem
        $sth = $conn->query('SELECT level FROM feature f WHERE f.feature_id = ' . $id . ' limit 1 ');

        foreach ($sth as $row) {
            $childs = true;
        }

        return $childs;

    }


    public function show()
    {
        $this->onReload();
        parent::show();
    }

}