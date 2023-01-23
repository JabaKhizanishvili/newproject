<?php

class wmissionsView extends View
{

    protected $_option = 'wmissions';
    protected $_option_edit = 'wmission';
    protected $_order = 't.start_date';
    protected $_dir = '1';
    protected $_space = 'wmissions.display';

    function display($tmpl = null)
    {
        /* @var $model pmissionsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
