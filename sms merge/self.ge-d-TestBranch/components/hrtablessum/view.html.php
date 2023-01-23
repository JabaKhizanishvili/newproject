<?php

class hrtablessumView extends View
{

    protected $_option = 'hrtablessum';
    protected $_option_edit = 'hrtablessum';
    protected $_order = 'workername';
    protected $_dir = '0';
    protected $_space = 'hrtablessum.display';

    function display($tmpl = null)
    {
        /* @var $model hrtablessumModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
