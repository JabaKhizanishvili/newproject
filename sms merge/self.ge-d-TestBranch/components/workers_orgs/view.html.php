<?php

class Workers_orgsView extends View
{

    protected $_option = 'workers_orgs';
    protected $_option_edit = 'worker_org';
    protected $_order = 'lastname';
    protected $_dir = '0';
    protected $_space = 'Workers_orgs.display';

    function display($tmpl = null)
    {
        /* @var $model WorkersModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
