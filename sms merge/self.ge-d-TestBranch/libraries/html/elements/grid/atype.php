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
class JGridElementAtype extends JGridElement
{
	public function fetchElement( $row, $node, $group )
	{
		$Types = $this->getData();
		$key = trim( $node->attributes( 'key' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Value = C::_( $key, $row, null );
		$Text = C::_( $Value . '.LIB_TITLE', $Types );
		if ( $translate == 1 )
		{
			$Text = XTranslate::_( $Text );
		}
		return $Text;

	}

	public function getData()
	{
		$Query = 'select t.type, t.lib_title from LIB_APPLICATIONS_TYPES t where t.active > 0 order by t.lib_title asc';
		return DB::LoadObjectList( $Query, 'TYPE' );

	}

}
