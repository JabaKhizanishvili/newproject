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
class JElementDateRange extends JElement
{

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'daterange';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $names = explode(',', $node->attributes('names'));

        if(count($names) != count($value) && count($value) != 3)
        {
            return 'Error!!!';
        }
        $options[] = HTML::_('select.option', 'month', 'Month');
        $options[] = HTML::_('select.option', 'day', 'Day');

        $html = '<input type="text" name="' . $control_name . '[' . $names[0] . ']" id="' . $control_name . $names[0] . '" value="' . $value[0] . '" class="date_range" />';
        $html .= '<input type="text" name="' . $control_name . '[' . $names[1] . ']" id="' . $control_name . $names[1] . '" value="' . $value[1] . '" class="date_range"  />';
        $html .= HTML::_('select.genericlist', $options, $control_name . '[' . $names[2] . ']', ' style="width:60px;" ', 'value', 'text', $value[2], $control_name . '[' . $names[2] . ']');
        return $html;
    }

}
