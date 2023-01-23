<?php

class RolesView extends View
{

    protected $_option = 'roles';
    protected $_option_edit = 'role';
    protected $_order = 'ordering';
    protected $_dir = '0';
    protected $_space = 'roles.display';

    function display($tmpl = null)
    {
        /* @var $model RolesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
