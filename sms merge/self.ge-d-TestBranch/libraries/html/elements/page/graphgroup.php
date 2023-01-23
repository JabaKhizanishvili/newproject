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
class PageElementGraphGroup extends PageElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'GraphGroup';

	public function fetchElement( $row, $node, $group )
	{
		$GraphGroups = $this->getGraphGroupList();
		/* @var $node SimpleXMLElements */
		$key = trim( $node->attributes( 'key' ) );
		$Text = '';
		if ( isset( $row->{$key} ) )
		{
			$Key = trim( stripslashes( $row->{$key} ) );
			$Text = C::_( $Key . '.LIB_TITLE', $GraphGroups );
			return $Text;
		}

	}

	public function getGraphGroupList()
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$Query = 'select t.id, t.lib_title, t.lib_desc from LIB_WORKERS_GROUPS t where t.active > -1 order by t.lib_title asc';
			$Data = DB::LoadObjectList( $Query, 'ID' );
		}
		return $Data;

	}

}
