<?php
/**
 * @version		$Id: sql.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a SQL element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class PageElementSQL extends PageElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'SQL';

	public function fetchElement( $row, $node, $group )
	{
		$Key = $node->attributes( 'key' );
		$KeyField = $node->attributes( 'key_field' );
		$ValueField = $node->attributes( 'value_field' );
		$Value = C::_( $Key, $row );
		static $SqlData = array();
		if ( !isset( $SqlData[$Key] ) )
		{
			$SqlData[$Key] = DB::loadObjectList( $node->attributes( 'query' ), $KeyField );
		}
		return C::_( $Key . '.' . $Value . '.' . $ValueField, $SqlData );

	}

}
