<?php

/**
 * @version		$Id: Day.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
/**
 * Renders a Day element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementDay extends JElement
{

    /**
     * Element type
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Day';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $Start = 1;
        $End = 31;
        for($a = $Start; $a <= $End; $a ++)
        {
            $val = $a;
            $text = ' ' . $a . ' ';
            $options[] = HTML::_('select.option', $val, Text::_($text));
        }

        return HTML::_('select.genericlist', $options, '' . $control_name . '[' . $name . ']', '', 'value', 'text', $value, $control_name . $name);
    }

}
