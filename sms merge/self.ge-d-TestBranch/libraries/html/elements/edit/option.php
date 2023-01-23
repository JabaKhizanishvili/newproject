<?php

/**
 * @version		$Id: list.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a list element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementOption extends JElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'option';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $Menu = MenuConfig::getInstance();
        $Menus = $Menu->getAllMenuItems(false, ' where m.active=1 ');
        $Options = $this->GetOptions();
        $options[] = HTML::_('select.option', '', Text::_('Separator'));
        $UsedOptions = array();
        foreach($Menus as $M)
        {
            if($M->ACTIVE == -2 || $M->LIB_OPTION == $value)
            {
                continue;
            }
            $UsedOptions[$M->LIB_OPTION] = $M->LIB_OPTION;
        }
       
        foreach($Options as $OPT)
        {
            if(isset($UsedOptions[$OPT]))
            {
                continue;
            }
            $val = $OPT;
            $text = ucfirst($OPT);
            $options[] = HTML::_('select.option', $val, $text);
        }

        return HTML::_('select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name);
    }

    public function GetOptions()
    {
        $data = Folder::folders(PATH_BASE . DS . 'components');
        return $data;
    }

}
