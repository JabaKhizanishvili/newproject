<?php

class OfficesView extends View
{

    protected $_option = 'offices';
    protected $_option_edit = 'office';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'offices.display';

    function display($tmpl = null)
    {
        /* @var $model OfficesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
