<?php

class GlobalTimesView extends View
{

    protected $_option = 'globaltimes';
    protected $_option_edit = 'globaltime';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'globaltimes.display';

    function display($tmpl = null)
    {
        /* @var $model GlobalTimesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
