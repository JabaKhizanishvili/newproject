<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

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
class PageElementunit extends PageElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'unit';

	public function fetchElement( $row, $node, $group )
	{

		static $Units = null;
		if ( is_null( $Units ) )
		{
			$Units = Units::getUnitList();
		}
		$Key = $node->attributes( 'key' );
		$Value = C::_( $Key, $row );
		return C::_( $Value . '.TITLE', $Units );

	}

}
