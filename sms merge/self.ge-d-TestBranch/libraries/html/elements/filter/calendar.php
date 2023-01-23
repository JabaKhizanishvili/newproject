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
class FilterElementCalendar extends FilterElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Calendar';

	public function fetchElement( $name, $id, $node, $config )
	{
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
		Helper::SetJS( '$("#' . $id . ' input").mask("00-00-0000", {placeholder:"00-00-0000"});' );
		return HTML::_( 'calendar', $value, $name, $id, $format, array( 'class' => $class ) );

	}

}
