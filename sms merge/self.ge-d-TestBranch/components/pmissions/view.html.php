<?php

class pmissionsView extends View
{

    protected $_option = 'pmissions';
    protected $_option_edit = 'pmission';
    protected $_order = 't.start_date';
    protected $_dir = '1';
    protected $_space = 'pmissions.display';

    function display($tmpl = null)
    {
        /* @var $model pmissionsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
