<?php

class WorkersGroupsView extends View
{

    protected $_option = 'workersgroups';
    protected $_option_edit = 'workersgroup';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'workersgroups.display';

    function display($tmpl = null)
    {
        /* @var $model WorkersGroupsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
