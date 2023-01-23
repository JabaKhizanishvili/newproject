<?php

class ChiefGraphsView extends View
{

    protected $_option = 'chiefgraphs';
    protected $_option_edit = 'graph';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'chiefgraphs.display';

    function display($tmpl = null)
    {
        /* @var $model ChiefGraphsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
