<?php

class WorkersView extends View
{

    protected $_option = 'workers';
    protected $_option_edit = 'worker';
    protected $_order = 'lastname';
    protected $_dir = '0';
    protected $_space = 'Workers.display';

    function display($tmpl = null)
    {
        /* @var $model WorkersModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
