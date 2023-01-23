<?php

class attributesView extends View
{

    protected $_option = 'attributes';
    protected $_option_edit = 'attribute';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'attributes.display';

    function display($tmpl = null)
    {
        /* @var $model AppTypesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
