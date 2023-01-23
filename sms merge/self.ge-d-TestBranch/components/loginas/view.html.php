<?php

class LoginASView extends View
{

    protected $_option = 'loginas';
    protected $_option_edit = 'loginas';
    protected $_order = 'lastname';
    protected $_dir = '0';
    protected $_space = 'LoginAS.display';

    function display($tmpl = null)
    {
        /* @var $model LoginASModel */
        $LoginAsUserID = C::_('nid.0', $_GET, false);
        if($LoginAsUserID)
        {
            return Users::LoginAsUser($LoginAsUserID);
        }
        /* @var $model LoginASModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
