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
class JGridElementNumber extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Number';

	public function fetchElement( $row, $node, $config )
	{
		$Key = $node->attributes( 'key' );
		$KeyValue = C::_( $Key, $row );
		$group = $this->GetConfigValue( $config, '_option', 'default' ) . '-' . $KeyValue;
		$this->GetConfigValue( $config, '_option', 'default' );
		$start = Request::getInt( 'start', 0 );
		static $number = array();
		if ( isset( $number[$group] ) )
		{
			++$number[$group];
			return '<b>' . $number[$group] . '<b>';
		}
		else
		{
			$number[$group] = $start;
			++$number[$group];
			return '<b>' . $number[$group] . '<b>';
		}

	}

}
