<?php

class WorkersModalView extends View
{

    protected $_option = 'workersmodal';
    protected $_option_edit = '';
    protected $_order = 'u.id';
    protected $_dir = '0';
    protected $_space = 'workersmodal.display';

    function display($tmpl = null)
    {
        /* @var $model WorkersModalModel */
        $model = $this->getModel();
        /* @var $data ModelReturn */
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
