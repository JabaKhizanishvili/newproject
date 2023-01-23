<?php

/**
 * @version		$Id: textarea.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a textarea element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementTextarea extends JElement
{

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'Textarea';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $rows = $node->attributes('rows');
        $cols = $node->attributes('cols');
        $class = ( $node->attributes('class') ? 'class="' . $node->attributes('class') . ' form-control"' : 'class="form-control"' );
        // convert <br /> tags so they are not visible when editing
        $value = str_replace('<br />', "\n", $value);
        $this->SetGeoKBD($node, $control_name . $name);
        return '<textarea name="' . $control_name . '[' . $name . ']" cols="' . $cols . '" rows="' . $rows . '" ' . $class . ' id="' . $control_name . $name . '" >' . $value . '</textarea>';
    }

}
