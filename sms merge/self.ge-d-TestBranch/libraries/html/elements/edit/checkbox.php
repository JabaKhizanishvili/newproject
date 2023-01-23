<?php
/**
 * @version		$Id: Checkbox.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a radio element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementCheckbox extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Checkbox';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$options = array();
		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			$text = $option->data();
			$options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
		}

		return HTML::_( 'select.checkbox', $options, '' . $control_name . '[' . $name . ']', '', 'value', 'text', $value, $control_name . $name );

	}

}
