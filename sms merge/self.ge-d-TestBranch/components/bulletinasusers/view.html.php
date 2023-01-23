<?php

class bulletinasusersView extends View
{

    protected $_option = 'bulletinasusers';
    protected $_option_edit = 'bulletinasuser';
    protected $_order = 't.rec_date';
    protected $_dir = '1';
    protected $_space = 'bulletinasusers.display';

    function display($tmpl = null)
    {
        /* @var $model bulletinasusersModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
