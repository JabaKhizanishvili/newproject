<?php

class newgraphtimesView extends View
{

    protected $_option = 'newgraphtimes';
    protected $_option_edit = 'newgraphtime';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'newgraphtimes.display';

    function display($tmpl = null)
    {
        /* @var $model newgraphtimesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
