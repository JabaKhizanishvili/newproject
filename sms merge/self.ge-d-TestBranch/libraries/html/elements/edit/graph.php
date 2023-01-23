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
class JElementGraph extends JElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Graph';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $Query = 'select '
                . ' t.id, '
                . ' t.lib_title '
                . ' from lib_workers_groups t '
                . ' WHERE t.active =1 '
                . ' AND t.owner = ' . Users::GetUserID()
                . ' order by lib_title asc';
        $data = DB::LoadObjectList($Query);
        foreach($data as $dat)
        {
            $val = $dat->ID;
            $text = $dat->LIB_TITLE;
            $options[] = HTML::_('select.option', $val, $text);
        }

        return HTML::_('select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $name);
    }

}
