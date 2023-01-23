<?php

class Access_managersView extends View
{

    protected $_option = 'access_managers';
    protected $_option_edit = 'access_manager';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'access_manager.display';

    function display($tmpl = null)
    {
        /* @var $model RolesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
