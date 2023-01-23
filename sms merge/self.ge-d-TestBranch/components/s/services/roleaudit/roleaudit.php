<?php

class roleaudit
{

    public function GetService()
    {
        $ID = Request::getInt('id', 0);
        $html = '';
        if(!empty($ID))
        {
            $Menu = MenuConfig::getInstance();
            $Menus = $Menu->getAllMenuItems(false);
            $Query = 'SELECT t.*  FROM lib_roles t  WHERE t.ID = ' . (int) $ID;
            $Role = DB::LoadObject($Query);
            $html = '<div class="cell_loader">';
            $html .= '<div class="role_audit_data">';
            $data = array_flip(explode(',', $Role->LIB_REL_MENUS));
            foreach($Menus as $M)
            {
                $chk = ' checked="checked" disabled="disabled" ';
                if(!isset($data[$M->ID]))
                {
                    continue;
                }
                $html .= '<div>'
                        . '<input type="checkbox" ' . $chk . '/>'
                        . '<label>' . str_repeat(' - ', $M->LIB_LEVEL) . $M->LIB_TITLE . '</label>'
                        . $this->getTasks($M)
                        . '</div>'
                        . '<div class="cls"></div>';
            }
        }
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    public function getTasks($M)
    {
        static $Tasks = null;
        $RoleID = Request::getInt('id');
        if(is_null($Tasks) && $RoleID)
        {
            $Tasks = Helper::getRolesConfig($RoleID, 'MENU');
        }
        $XMLFile = PATH_BASE . DS . 'components' . DS . C::_('LIB_OPTION', $M) . DS . 'config.xml';
        $html = '';
        if(is_file($XMLFile))
        {
            $XMLDoc = Helper::loadXMLFile($XMLFile);
            $Columns = $XMLDoc->getElementByPath('tasks')->children();
            /* @var $Column SimpleXMLElements  */
            foreach($Columns as $Column)
            {
                $name = $Column->attributes('name');
                $chk = '';
                if(C::_($M->ID . '.PARAMS.' . $name, $Tasks, false))
                {
                    $chk = ' checked="checked" disabled="disabled" ';
                }
                else
                {
                    continue;
                }
                $html .= '<div class="cls"></div>'
                        . '<div class="role_tasks">'
                        . '<input type="checkbox" ' . $chk . ' />'
                        . '<label> - ' . Text::_($name) . '</label>'
                        . '</div>'
                ;
            }
        }
        return $html;
    }

}
