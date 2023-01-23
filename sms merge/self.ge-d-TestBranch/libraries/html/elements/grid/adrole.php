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
class JGridElementAdRole extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'AdRole';

	public function fetchElement( $row, $node, $config )
	{
		$List = $this->getAdRoleList();
		$key = trim( $node->attributes( 'key' ) );
		$length = trim( $node->attributes( 'length' ) );
		$limit_type = trim( $node->attributes( 'limit_type' ), 0 );
		$ID = C::_( $key, $row, null );
		$Text = C::_( $ID . '.ROLE_AD_NAME', $List ) . ' - ' . C::_( $ID . '.ROLE_NAME', $List );
		return Helper::MakeToolTip( $Text, $length, $limit_type );

	}

	public function getAdRoleList()
	{
		$Query = 'select '
						. ' r.role_name, '
						. ' r.role_ad_name '
						. ' from user_manager.roles r'
						. ' order by  r.role_ad_name asc ';
		return DB::LoadObjectList( $Query, 'ROLE_AD_NAME' );

	}

}
