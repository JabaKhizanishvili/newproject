<?php
/**
 * @version		$Id: hidden.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a hidden element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementHidden extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Hidden';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if ( is_array( $value ) )
		{
			$value = implode( '|', $value );
		}

		$class = ( $node->attributes( 'class' ) ? 'class="' . $node->attributes( 'class' ) . '"' : 'class="text_area"' );

		return '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . htmlspecialchars( $value, ENT_QUOTES ) . '" ' . $class . ' />';

	}

	public function fetchTooltip( $label, $description, $xmlElement, $control_name = '', $name = '' )
	{
		return false;

	}

}
