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
class JElementMulty extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'multy';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		if ( $value )
		{
			$value = explode( ',', $value );
		}
		$class = ( $node->attributes( 'class' ) ? ' class="' . $node->attributes( 'class' ) . '" ' : ' class="form-control multySelector" ' );
		$size = ( $node->attributes( 'size' ) ? ' size="' . $node->attributes( 'size' ) . '" ' : '' );
		$options = array();
		foreach ( $node->children() as $option )
		{
			$val = $option->attributes( 'value' );
			$text = $option->data();
			$options[] = HTML::_( 'select.option', $val, Text::_( $text ) );
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . '][]', $class . $size . ' multiple ', 'value', 'text', $value, $control_name . $name );

	}

}
