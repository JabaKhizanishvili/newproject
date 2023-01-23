<?php
defined('PATH_BASE') or die('Restricted access');
require_once 'table.php';

class attributeModel extends Model
{
    protected $Table = null;

    public function __construct($params)
    {
        $this->Table = new attributesTable();
        parent::__construct($params);

    }

    public function getItem()
    {
        $id = Request::getVar('nid', array());
        if (isset($id[0]) && $id[0] != '') {
            $this->Table->load($id[0]);
        }

        return $this->Table;

    }

    public function SaveData($data)
    {
        $id = parent::SaveData($data);

        if (!$id) {
            return false;
        }

        return $id;
    }

    public function D_Delete($data)
    {
        if (empty($data)) {
            return [
                'success' => false,
                'errorMessage' => XTranslate::_('Data_Not_Deleted')
            ];
        }

        $attributeIds = implode(',', $data);

        $checkRelExistQuery = 'select * from rel_attributes ra where ra.attribute_id in (' . $attributeIds . ')';

        if (DB::LoadList($checkRelExistQuery)) {
            return [
                'success' => false,
                'errorMessage' => XTranslate::_('UNIT CANNOT BE DELETED WHICH IS USED IN OTHER OPERATIONS!')
            ];
        }

        $query = 'update lib_attributes t set t.active = -1 where t.id in (' . $attributeIds . ')';

        if (DB::Update($query)) {
            return ['success' => true];
        }

        return [
            'success' => false,
            'errorMessage' => XTranslate::_('Data_Not_Deleted')
        ];
    }

}
