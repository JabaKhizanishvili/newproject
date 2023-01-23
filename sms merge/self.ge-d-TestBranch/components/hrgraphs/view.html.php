<?php

class HRGraphsView extends View
{

    protected $_option = 'hrgraphs';
    protected $_option_edit = 'graph';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'hrgraphs.display';

    function display($tmpl = null)
    {
        /* @var $model HRGraphsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
