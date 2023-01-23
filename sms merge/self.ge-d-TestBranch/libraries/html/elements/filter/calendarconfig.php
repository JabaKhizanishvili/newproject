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
class FilterElementcalendarconfig extends FilterElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'calendarconfig';

	public function fetchElement( $name, $id, $node, $config )
	{
		$Config = explode( '.', $node->attributes( 'config_key' ) );
		$Change = (int) $node->attributes( 'change' );
		$Day = Helper::getConfig( C::_( '0', $Config ), C::_( '1', $Config ) ) + $Change;

		if ( $Day < 0 )
		{
			$Day = 6;
		}
		if ( $Day > 6 )
		{
			$Day = 0;
		}

		$format = ( $node->attributes( 'format' ) ? $node->attributes( 'format' ) : '%Y-%m-%d' );
		$class = $node->attributes( 'class' ) ? $node->attributes( 'class' ) : 'form-control';
		$value = trim( $this->GetConfigValue( $config['data'], $name, $node->attributes( 'default' ) ) );
		if ( empty( $value ) )
		{
			$value = $node->attributes( 'default' );
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
		return HTML::_( 'calendar', $value, $name, $id, $format, array( 'class' => $class, 'data-weekday' => $Day ) );

	}

}
