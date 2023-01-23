<?php
defined('PATH_BASE') or die('Restricted access');
require_once 'table.php';

class change_passwordModel extends Model
{
    protected $Table = null;
    public $_errors = [];

    public function __construct($params)
    {
        $this->Table = new ProfileEditsTable();
        parent::__construct($params);

    }

    public function isValidData($data)
    {
        if (empty($data['U_PASSWORD'])) {
            $this->addError('Current Password is required field');
        }

        if (empty($data['NEW_PASSWORD'])) {
            $this->addError('New Password is required field');
        }

        if (empty($data['NEW_PASSWORD_CONFIRMATION'])) {
            $this->addError('New Password Confirmation is required field');
        }

        if ($data['NEW_PASSWORD'] !== $data['NEW_PASSWORD_CONFIRMATION']) {
            $this->addError('New password and confirm password does not match');
        }

        $user = Users::getUser(Users::GetUserID());

        if (md5($data['U_PASSWORD']) !== $user->U_PASSWORD) {
            $this->addError('Current password is incorrect');
        }

        return !$this->getFirstError();
    }

    public function getItem()
    {
        $id = Users::GetUserID();
        if (!empty($id)) {
            $this->Table->load($id);
        }
        return $this->Table;

    }

    public function SaveData($data)
    {
        $BindData = array();

        $Password = C::_('NEW_PASSWORD', $data, null);

        if (!empty($Password)) {
            $BindData['U_PASSWORD'] = md5($Password);
        }

        if (!$this->isValidData($data)) {
            return false;
        }

        if (!$this->Table->load(Users::GetUserID())) {
            return false;
        }

        if (!$this->Table->bind($BindData)) {
            return false;
        }

        if (!$this->Table->store()) {
            return false;
        }

        return $this->Table;

    }

    public function addError($message)
    {
        $this->_errors[] = Text::_($message);
    }

    public function getFirstError()
    {
        if (isset($this->_errors[0])) {
            return $this->_errors[0];
        }

        return null;
    }

}
