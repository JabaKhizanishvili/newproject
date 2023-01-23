<?php

class WorkerHolidaysView extends View
{

    protected $_option = 'workerholidays';
    protected $_option_edit = 'workerholiday';
    protected $_order = 't.start_date';
    protected $_dir = '1';
    protected $_space = 'workerholidays.display';

    function display($tmpl = null)
    {
        /* @var $model WorkerHolidaysModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
