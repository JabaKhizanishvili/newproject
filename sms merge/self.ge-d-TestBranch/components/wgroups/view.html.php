<?php

class wgroupsView extends View
{

    protected $_option = 'wgroups';
    protected $_option_edit = 'wgroup';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'wgroups.display';

    function display($tmpl = null)
    {
        /* @var $model wgroupsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
