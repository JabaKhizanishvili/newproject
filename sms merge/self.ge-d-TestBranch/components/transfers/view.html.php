<?php

class transfersView extends View
{

    protected $_option = 'transfers';
    protected $_option_edit = 'transfer';
    protected $_order = 't.rec_date';
    protected $_dir = '1';
    protected $_space = 'transfers.display';

    function display($tmpl = null)
    {
        /* @var $model transfersModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
