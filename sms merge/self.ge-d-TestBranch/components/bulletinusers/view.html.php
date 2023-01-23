<?php

class bulletinusersView extends View
{

    protected $_option = 'bulletinusers';
    protected $_option_edit = 'bulletinuser';
    protected $_order = 't.rec_date';
    protected $_dir = '1';
    protected $_space = 'bulletinusers.display';

    function display($tmpl = null)
    {
        /* @var $model bulletinusersModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
