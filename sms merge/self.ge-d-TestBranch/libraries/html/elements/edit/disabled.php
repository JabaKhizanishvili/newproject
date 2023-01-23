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
class JElementDisabled extends JElement
{

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */
    protected $_name = 'disabled';

    public function fetchElement($name, $value, $node, $control_name)
    {
        $size = ( $node->attributes('size') ? 'size="' . $node->attributes('size') . '"' : '' );
        $class = ( $node->attributes('class') ? 'class="' . $node->attributes('class') . '"' : 'class="text_area"' );
        /*
         * Required to avoid a cycle of encoding &
         * html_entity_decode was used in place of htmlspecialchars_decode because
         * htmlspecialchars_decode is not compatible with PHP 4
         */
        $value = htmlspecialchars(html_entity_decode($value, ENT_QUOTES), ENT_QUOTES);

        return '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $value . '"><input type="text" id="dis_' . $control_name . $name . '" disabled="disabled" value="' . $value . '" ' . $class . ' ' . $size . ' />';
    }

}
