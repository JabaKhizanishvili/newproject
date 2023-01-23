<?php

class HolidaysView extends View
{

    protected $_option = 'holidays';
    protected $_option_edit = 'holiday';
    protected $_order = 'lib_month';
    protected $_dir = '0';
    protected $_space = 'holidays.display';

    function display($tmpl = null)
    {
        /* @var $model HolidaysModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
