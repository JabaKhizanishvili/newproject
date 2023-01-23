<?php

class calendarView extends View
{

    protected $_option = 'calendar';
    protected $_option_edit = 'calendar';
    protected $_order = 'type';
    protected $_dir = '0';
    protected $_space = 'calendar.display';

    function display($tmpl = null)
    {
        /* @var $model calendarModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        $this->assignRef('model', $model);
        parent::display($tmpl);
    }

}
