<?php

class UserGraphsView extends View
{

    protected $_option = 'usergraphs';
    protected $_option_edit = 'graph';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'usergraphs.display';

    function display($tmpl = null)
    {
        /* @var $model UserGraphsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
