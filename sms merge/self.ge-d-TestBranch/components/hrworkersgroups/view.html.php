<?php

class hrworkersgroupsView extends View
{

    protected $_option = 'hrworkersgroups';
    protected $_option_edit = 'hrworkersgroup';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'hrworkersgroups.display';

    function display($tmpl = null)
    {
        /* @var $model hrworkersgroupsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
