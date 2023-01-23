<?php
/**
 * @version		$Id: calendar.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is included in WSCMS
defined( 'PATH_BASE' ) or die( 'Restricted access' );

/**
 * Renders a calendar element
 *
 * @package 	WSCMS.Framework
 * @subpackage	Parameter
 * @since		1.5
 */
class JElementRangeNumber extends JElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'RangeNumber';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Start = (int) $node->attributes( 'start' );
		$End = (int) $node->attributes( 'end' );
		$Placeholder = (int) $node->attributes( 'placeholder' );
		$Class = $node->attributes( 'class' ) ? $node->attributes( 'class' ) : 'form-control';

		$Range = range( $Start, $End );
		$Step = (int) $node->attributes( 'step' );
		if ( $Step > 0 )
		{
			$Range = range( $Start, $End, $Step );
		}

		$options = array();
		if ( $Placeholder == 1 )
		{
			$options[] = HTML::_( 'select.option', -1, Text::_( 'select category' ) );
		}
		foreach ( $Range as $Num )
		{
			$val = $Num;
			$text = $Num;
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', $Class, 'value', 'text', $value, $control_name . $name );

	}

}
