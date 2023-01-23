<?php

class overtimesView extends View
{

    protected $_option = 'overtimes';
    protected $_option_edit = 'overtime';
    protected $_order = 't.start_date';
    protected $_dir = '1';
    protected $_space = 'overtimes.display';

    function display($tmpl = null)
    {
        /* @var $model overtimesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
