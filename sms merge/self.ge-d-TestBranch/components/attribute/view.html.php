<?php

class attributeView extends View
{
    protected $_option = 'attributes';
    protected $_option_edit = 'attribute';

    function display($tmpl = null)
    {
        $params = (object)get_object_vars($this);
        $model = $this->getModel($params);
        $task = Request::getVar('task', '');
        $data = array();
        switch ($task) {
            case 'save':
                $data = Request::getVar('params', array());
                if ($data['ID'] == '0') {
                    $data['ID'] = '-0';
                }

                if ($model->SaveData($data)) {
                    $link = '?option=' . $this->_option;
                    XError::setMessage('Data Saved!');
                    Users::Redirect($link);
                }
                XError::setError('data_incorrect');
                break;
            case 'apply':
                $data = Request::getVar('params', array());
                $ID = $model->SaveData($data);
                if ($ID) {
                    $link = '?option=' . $this->_option_edit . '&task=edit&nid[]=' . $ID;
                    XError::setMessage('Data Saved!');
                    Users::Redirect($link);
                }
                XError::setError('data_incorrect');
                break;
            case 'copy':
                $data = $model->getItem();
                $key = $data->getKeyName();
                $data->{$key} = null;
                break;
            case 'delete':
                $link = '?option=' . $this->_option;
                $data = Request::getVar('nid', array());
                if (empty($data)) {
                    XError::setError('items_not_selected');
                    Users::Redirect($link);
                }

                $dataDeleted = $model->D_Delete($data);

                if ($dataDeleted['success']) {
                    XError::setMessage('Data Deleted!');
                    Users::Redirect($link);
                }

                XError::setError($dataDeleted['errorMessage']);
                Users::Redirect($link);
                break;

            case 'cancel':
                $data = Request::getVar('params', array());
                $link = '?option=' . $this->_option;
                XError::setInfo('Action Canceled!');
                Users::Redirect($link);
                break;
            case 'changestate':
                $model->ChangeState();
                $link = '?option=' . $this->_option;
                XError::setMessage('Status Changed!');
                Users::Redirect($link);
                break;

            default:
                $data = $model->getItem();
                break;
        }
        if (!is_object($data)) {
            $data = (object)$data;
        }
        $this->assignRef('data', $data);
        parent::display($tmpl);

    }

}
