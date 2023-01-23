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
class PageElementGraphType extends PageElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'GraphType';

	public function fetchElement( $row, $node, $group )
	{
		$GraphTypes = $this->getGraphTypeList();
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$Text = '';
		if ( isset( $row->{$key} ) )
		{
			$Key = trim( stripslashes( $row->{$key} ) );
			$Text = C::_( $Key . '.LIB_TITLE', $GraphTypes );
			return $Text;
		}

	}

	public function getGraphTypeList()
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$Query = 'select t.id, t.lib_title from LIB_STANDARD_GRAPHS t where t.active > -1 order by t.lib_title asc';
			$Data = DB::LoadObjectList( $Query, 'ID' );
		}
		return $Data;

	}

}
