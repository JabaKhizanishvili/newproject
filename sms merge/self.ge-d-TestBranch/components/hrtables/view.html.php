<?php

class hrtablesView extends View
{

    protected $_option = 'hrtables';
    protected $_option_edit = 'hrtable';
    protected $_order = 'w.firstname';
    protected $_dir = '1';
    protected $_space = 'hrtables.display';

    function display($tmpl = null)
    {
        /* @var $model hrtablesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
