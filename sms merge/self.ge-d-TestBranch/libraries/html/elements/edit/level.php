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
class JElementLevel extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Level';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$class = ( $node->attributes( 'class' ) ? ' class="' . $node->attributes( 'class' ) . '" ' : ' class="form-control" ' );
		$size = ( $node->attributes( 'size' ) ? ' size="' . $node->attributes( 'size' ) . '" ' : '' );
		$options = array();
		$Days = range( 0, 100 );
		foreach ( $Days as $Day )
		{
			$val = $Day;
			$text = Text::_( 'Level' ) . ' - ' . $Day;
			;
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', $class . $size, 'value', 'text', $value, $control_name . $name );

	}

}
