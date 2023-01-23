<?php

class Limits_HRView extends View
{

    protected $_option = 'limits_hr';
    protected $_order = 'lastname';
    protected $_dir = '0';
    protected $_space = 'Limits_HR.display';

    function display($tmpl = null)
    {
        /* @var $model Limits_HRModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
