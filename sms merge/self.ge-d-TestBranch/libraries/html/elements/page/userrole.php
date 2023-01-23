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
class PageElementUserRole extends PageElement
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
		$Key = $node->attributes( 'key' );
		$Value = C::_( $Key, $row );
		return C::_( $Value . '.LIB_TITLE', $Roles ); //[$row->{$Key} ][''];

	}

	public function getRoles()
	{
		static $Roles = null;
		if ( is_null( $Roles ) )
		{
			$query = ' select t.id, t.lib_title, t.lib_desc, t.active from lib_roles t where (t.active > -1) order by t.ordering asc';
			$Roles = DB::LoadObjectList( $query, 'ID' );
		}
		return $Roles;

	}

}
