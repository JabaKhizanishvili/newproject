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
class JElementweekday extends JElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'weekday';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Days = [
				'Sunday',
				'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday'
		];

		$Class = $node->attributes( 'class' ) ? $node->attributes( 'class' ) : 'form-control';
		$options = array();
		$K = 0;
		foreach ( $Days as $Day )
		{
			$options[] = HTML::_( 'select.option', $K, Text::_( $Day ) );
			$K++;
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', $Class, 'value', 'text', $value, $control_name . $name );

	}

}
