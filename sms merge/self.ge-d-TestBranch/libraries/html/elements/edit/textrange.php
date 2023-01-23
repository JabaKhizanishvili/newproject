<?php

/**
 * @version		$Id: text.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a text element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementTextRange extends JElement
{

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'textrange';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $names = explode(',', str_replace(' ', '', $node->attributes('names')));
        if(count($names) != count($value) && count($value) != 2)
        {
            return 'Error!!!';
        }
        $html = '<input type="text" name="' . $control_name . '[' . $names[0] . ']" id="' . $control_name . $names[0] . '" value="' . $value[0] . '" class="date_range" />';
        $html .= '<input type="text" name="' . $control_name . '[' . $names[1] . ']" id="' . $control_name . $names[1] . '" value="' . $value[1] . '" class="date_range"  />';
        return $html;
    }

}
