<?php

class OfficialTypesView extends View
{

    protected $_option = 'officialtypes';
    protected $_option_edit = 'officialtype';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'officialtypes.display';

    function display($tmpl = null)
    {
        /* @var $model OfficialTypesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
