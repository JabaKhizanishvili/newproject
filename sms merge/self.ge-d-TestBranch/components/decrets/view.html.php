<?php

class DecretsView extends View
{

    protected $_option = 'decrets';
    protected $_option_edit = 'decret';
    protected $_order = 'ID';
    protected $_dir = '1';
    protected $_space = 'decrets.display';

    function display($tmpl = null)
    {
        /* @var $model DecretsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
