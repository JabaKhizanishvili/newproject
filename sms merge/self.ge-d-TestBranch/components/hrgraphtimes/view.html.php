<?php

class hrgraphtimesView extends View
{

    protected $_option = 'hrgraphtimes';
    protected $_option_edit = 'hrgraphtime';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'hrgraphtimes.display';

    function display($tmpl = null)
    {
        /* @var $model hrgraphtimesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
