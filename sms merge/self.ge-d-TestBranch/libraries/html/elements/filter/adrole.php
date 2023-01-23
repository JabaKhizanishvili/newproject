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
class FilterElementADRole extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'ADRole';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$List = $this->getAdRoleList();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'AD ROLE FILTER' ) );
		foreach ( $List as $Item )
		{
			$options[] = HTML::_( 'select.option', $Item->ROLE_AD_NAME, $Item->ROLE_AD_NAME . ' - ' . $Item->ROLE_NAME );
		}
		return HTML::_( 'select.genericlist', $options, '' . $name, ' class="form-control" style="width:100%;" ', 'value', 'text', $value, $id );

	}

	public function getAdRoleList()
	{
		$Query = 'select '
						. ' r.role_name, '
						. ' r.role_ad_name '
						. ' from user_manager.roles r'
						. ' order by  r.role_ad_name asc ';
		return DB::LoadObjectList( $Query );

	}

}
