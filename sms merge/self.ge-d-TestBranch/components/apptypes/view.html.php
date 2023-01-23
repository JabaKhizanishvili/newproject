<?php

class AppTypesView extends View
{

    protected $_option = 'apptypes';
    protected $_option_edit = 'apptype';
    protected $_order = 'type';
    protected $_dir = '0';
    protected $_space = 'apptypes.display';

    function display($tmpl = null)
    {
        /* @var $model AppTypesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
