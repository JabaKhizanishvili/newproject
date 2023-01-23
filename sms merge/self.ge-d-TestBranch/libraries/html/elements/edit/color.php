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
class JElementColor extends JElement
{

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'color';

    public function fetchElement($name, $valueIN, $node, $control_name)
    {
        $size = ( $node->attributes('size') ? 'size="' . $node->attributes('size') . '"' : '' );
        $class = ( $node->attributes('class') ? 'class="' . $node->attributes('class') . '"' : 'class="text_area"' );
        $value = htmlspecialchars(html_entity_decode($valueIN, ENT_QUOTES), ENT_QUOTES);
        $js = '$("#' . $control_name . $name . '").spectrum({'
                . ' color: "' . $value . '", '
                . ' showInput: true, '
                . ' showInitial: true, '
                . ' preferredFormat: "hex" '
                . '});';
        Helper::SetJS($js, false);
        return '<input type="text" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $value . '" ' . $class . ' ' . $size . ' />';
    }

}
