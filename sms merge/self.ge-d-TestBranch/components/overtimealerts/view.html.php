<?php

class overtimealertsView extends View
{

    protected $_option = 'overtimealerts';
    protected $_option_edit = 'overtimealert';
    protected $_order = 't.start_date';
    protected $_dir = '1';
    protected $_space = 'overtimealerts.display';

    function display($tmpl = null)
    {
        /* @var $model overtimealertsModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
