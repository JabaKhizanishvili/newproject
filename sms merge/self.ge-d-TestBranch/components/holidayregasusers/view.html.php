<?php

class HolidayRegAsUsersView extends View
{

    protected $_option = 'holidayregasusers';
    protected $_option_edit = 'holidayregasuser';
    protected $_order = 't.rec_date';
    protected $_dir = '1';
    protected $_space = 'holidayregasusers.display';

    function display($tmpl = null)
    {
        /* @var $model HolidayRegAsUsersModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
