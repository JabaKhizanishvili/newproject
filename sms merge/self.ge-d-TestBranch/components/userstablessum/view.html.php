<?php

class userstablessumView extends View
{

    protected $_option = 'userstablessum';
    protected $_option_edit = 'userstablessum';
    protected $_order = 'workername';
    protected $_dir = '0';
    protected $_space = 'userstablessum.display';

    function display($tmpl = null)
    {
        /* @var $model userstablessumModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
