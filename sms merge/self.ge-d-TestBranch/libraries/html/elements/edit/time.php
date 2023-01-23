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
class JElementTime extends JElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Time';

	public function fetchElement( $name, $valuein, $node, $control_name )
	{
		$format = ( $node->attributes( 'format' ) ? $node->attributes( 'format' ) : '%H:%M' );
		$class = $node->attributes( 'class' ) ? $node->attributes( 'class' ) : 'form-control';
		$value = trim( $valuein );
		if ( !Xhelp::checkTime( $value ) )
		{
			$value = '';
		}
		if ( !empty( $value ) )
		{
			$date = new PDate( $value );
			$value = $date->toFormat( $format );
		}
		else
		{
			$value = '';
		}
		$id = $control_name . $name;
		$name = $control_name . '[' . $name . ']';
		Helper::SetJS( '$("#' . $id . ' input").mask("00:00", {placeholder:"00:00"});' );
		return HTML::_( 'time', $value, $name, $id, $format, array( 'class' => $class ) );

	}

}
