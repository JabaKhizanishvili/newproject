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
class JGridElementUserRole extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'userrole';

	public function fetchElement( $row, $node, $group )
	{
		$Roles = $this->getRoles();
		$key = trim( $node->attributes( 'key' ) );
		$translate = trim( $node->attributes( 't' ) );
		$value = C::_( $key, $row );
		$Text = C::_( $value . '.LIB_TITLE', $Roles );
		if ( $translate == 1 )
		{
			$Text = XTranslate::_( $Text );
		}
		return $Text;

	}

	public function getRoles()
	{
		static $Types = null;
		if ( is_null( $Types ) )
		{
			$Query = 'select*from LIB_ROLES r where r.active = 1';
			$Types = DB::LoadObjectList( $Query, 'ID' );
		}
		return $Types;

	}

}
