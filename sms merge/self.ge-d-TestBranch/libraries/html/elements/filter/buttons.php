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
class FilterElementButtons extends FilterElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Buttons';

    public function fetchElement($name, $id, $node, $config)
    {
        $submit_label = $node->attributes('submit_label') ? $node->attributes('submit_label') : 'Filter';
        $reset_label = $node->attributes('reset_label') ? $node->attributes('reset_label') : 'Clear';

        $html = '<button class="btn btn-primary" onclick="setFilter();" type="button">'
                . Text::_($submit_label)
                . '</button>';
        if($node->attributes('show_reset') != '0')
        {
            $html .= '<button class="btn btn-primary" onclick="resetFilter();" type="button" >' . Text::_($reset_label) . '</button>';
        }
        return $html;
    }

}
