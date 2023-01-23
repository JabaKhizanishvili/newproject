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
class JGridElementORGPlace extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ORGPlace';

	public function fetchElement( $row, $node, $group )
	{
		$Depts = $this->getOrgList();
		$key = trim( $node->attributes( 'key' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Text = '';
		if ( isset( $row->{$key} ) )
		{
			$Text = C::_( $row->{$key} . '.TITLE', $Depts );
			if ( $translate == 1 )
			{
				$Text = XTranslate::_( $Text );
			}
		}
		return $Text;

	}

	public function getOrgList()
	{
		static $ORGList = null;
		if ( is_null( $ORGList ) )
		{
			$Query = 'select '
							. ' id, '
							. ' t.lib_title title '
							. ' from lib_units t '
			;
//							. ' order by t. asc';
			$ORGList = (array) XRedis::getDBCache( 'lib_units', $Query, 'LoadObjectList', 'ID' );
		}
		return $ORGList;

	}

}
