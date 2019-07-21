<?php

/**
 * Feature Form Registration
 * @author  Jackson Meires
 */
class FeatureForm extends \Adianti\Control\TWindow
{
    protected $form; // form
    private $feature_pages;

    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        parent::setTitle('Formulário de Feature');
        $this->form = new \Adianti\Wrapper\BootstrapFormBuilder('form_feature');
        $this->form->style = 'width: 100%';

        // sample values:
        // 600, 400 (absolute size)
        // 0.6, 0.4 (relative size 60%, 40%)
        parent::setSize(600, 630); // (600, 400)

        // absolute left, top
        // parent::setPosition( 100, 100 );

        // create the form fields
        $id = new THidden('id');
        $title = new TEntry('title');
        $name = new TEntry('name');
        $level = new THidden('level');
        $type = new TCombo('type');
        $group_menu = new TDBCombo('group_menu_id', 'db_conn', 'GroupMenu', 'id', 'name', 'name');
        $module_id = new TCombo('module_id');

        $arrayType = [];
        $arrayType['MANDATORY'] = 'MANDATORY';
        $arrayType['OPTIONAL'] = 'OPTIONAL';
        $arrayType['OR'] = 'OR';
        $arrayType['ALTERNATIVE'] = 'ALTERNATIVE';
        $type->addItems($arrayType);

        TTransaction::open('db_conn');

        // creates repository
        $repository = new TRepository('Module');
        $criteria = new TCriteria;
        if (filter_input(INPUT_GET, 'product_id')) {
            $criteria->add(new \Adianti\Database\TFilter('product_id', '=', filter_input(INPUT_GET, 'product_id')));
        }
        $criteria->setProperty('order', 'id');

        $collection = $repository->load($criteria, FALSE);

        $arrayModule = [];
        if ($collection) {
            foreach ($collection as $object) {
                $arrayModule[$object->id] = $object->name . ' - ' . $object->product_name;

            }
        }
        TTransaction::close();

        $module_id->addItems($arrayModule);

        $title->addValidation('Título', new TRequiredValidator);
        $name->addValidation('Nome', new TRequiredValidator);
        $module_id->addValidation('Módulo', new TRequiredValidator);
        $type->addValidation('Módulo', new TRequiredValidator);
        $group_menu->addValidation('Grupo', new TRequiredValidator);

        // add the fields
        $this->form->addFields([$id]);
        $this->form->addFields([$level]);
        $this->form->addFields([new TLabel('Título <b style="color: red;">*</b>')], [$title]);
        $this->form->addFields([new TLabel('Nome <b style="color: red;">*</b>')], [$name]);
        $this->form->addFields([new TLabel('Módulo <b style="color: red;">*</b>')], [$module_id]);
        $this->form->addFields([new TLabel('Tipo <b style="color: red;">*</b>')], [$type]);
        $this->form->addFields([new TLabel('Grupo <b style="color: red;">*</b>')], [$group_menu]);
        $this->form->addFields([new TLabel('OBS')], [new \Adianti\Widget\Form\TLabel('<b style="color: red;">*</b> Campos Obrigatórios')]);

        $this->form->addFields([new TLabel('')]);
        $this->form->addFields([new TLabel('<b style="color: red; font-size: 16px;">Páginas da Feature</b>')]);

        $name_page = new TEntry('name_page[]');
        $name_page->setSize(120);

        $name_controller = new TEntry('name_controller[]');
        $name_controller->setSize(120);

        $this->feature_pages = new \Adianti\Widget\Form\TFieldList();
        $this->feature_pages->addField('<b>Nome da Página</b>', $name_page);
        $this->feature_pages->addField('<b>Nome Controller</b>', $name_controller);
        $this->feature_pages->enableSorting();

        $this->form->addField($name_page);
        $this->form->addField($name_controller);

        $this->form->addContent([$this->feature_pages]);

        $action_save = new TAction(array($this, 'onSave'));
        $action_save->setParameter('product_id', filter_input(INPUT_GET, 'product_id'));

        // define the form action
        $btn = $this->form->addAction('Salvar', $action_save, 'fa:save');
        $btn->class = 'btn btn-sm btn-primary';
        // add the form to the page
        $panelForm = new TPanelGroup();
        $panelForm->add($this->form);

        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 100%';
        $container->add($panelForm);

        parent::add($container);
    }

    public function onSave($param = null)
    {

        try {
            $object = $this->form->getData('Feature');

            TTransaction::open('db_conn');

            if (empty($object->level)) {
                $object->level = 1;
            }

            if (empty($object->id)) {
                $prefixe_module = (new Module($object->module_id))->name;
                $object->name = strtolower($prefixe_module) . '_' . $object->name;
            }

            if (!empty($param['name_page']) AND is_array($param['name_page'])) {
                foreach ($param['name_page'] as $row => $name_page) {

                    if ($name_page) {
                        $objFP = new FeaturePage();
                        $objFP->name = $name_page;
                        $objFP->controller = $param['name_controller'][$row];

                        // add the FeaturePage to the Feature
                        $object->addFeaturePage($objFP);
                    }
                }
            }

            $object->store();

            TTransaction::close();

            if (!empty(filter_input(INPUT_GET, 'product_id'))) {

                $action = new TAction(array('FeatureTreeForm', 'onReload'));
                $action->setParameter('product_id', filter_input(INPUT_GET, 'product_id'));
                $action->setParameter('id', $object->id);
                $action->setParameter('key', $object->id);

                $_REQUEST['key'] = $object->id;

                new TMessage('info', 'Registro salvo com sucesso', $action);
            } else {
                $action = new TAction(array('FeatureList', 'onReload'));
                new TMessage('info', 'Registro salvo com sucesso', $action);

            }

        } catch (Exception $e) {
            $this->form->setData($this->form->getData());

            new TMessage('error', $e->getMessage());

            TTransaction::rollback();
        }
    }

    /**
     * method onEdit()
     * Executed whenever the user clicks at the edit button
     */
    function onEdit($param)
    {
        try {
            if (isset($param['key'])) {
                $key = $param['key'];

                TTransaction::open('db_conn');

                $object = new Feature($key);
                // load the feature_pages (composition)
                $feature_pages = $object->getFeaturePages();

                if ($feature_pages) {
                    $this->feature_pages->addHeader();
                    foreach ($feature_pages as $objFP) {
                        $fature_detail = new stdClass;
                        $fature_detail->name_page = $objFP->name;
                        $fature_detail->name_controller = $objFP->controller;

                        $this->feature_pages->addDetail($fature_detail);
                    }

                    $this->feature_pages->addCloneAction();
                } else {
                    $this->onClear($param);
                }

                $this->form->setData($object);

                TTransaction::close();
            } else {
                $this->onClear($param);
            }
        } catch (Exception $e) {
            new TMessage('error', $e->getMessage());

            TTransaction::rollback();
        }
    }


    /**
     * Clear form
     */
    public function onClear($param)
    {
        $this->form->clear();

        $this->feature_pages->addHeader();
        $this->feature_pages->addDetail(new stdClass);
        $this->feature_pages->addCloneAction();
    }
}
