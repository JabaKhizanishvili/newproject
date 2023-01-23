<?php

class GraphTimesView extends View
{

    protected $_option = 'graphtimes';
    protected $_option_edit = 'graphtime';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'graphtimes.display';

    function display($tmpl = null)
    {
        /* @var $model GraphTimesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
