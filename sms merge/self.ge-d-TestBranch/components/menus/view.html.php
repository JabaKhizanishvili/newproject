<?php

class MenusView extends View
{

    protected $_option = 'menus';
    protected $_option_edit = 'menu';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'menus.display';

    function display($tmpl = null)
    {
        /* @var $model MenusModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
