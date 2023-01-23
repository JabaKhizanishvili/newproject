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
class FilterElementUnit extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Unit';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = trim( $this->GetConfigValue( $config['data'], $name, $node->attributes( 'default' ) ) );
		$Key = $node->attributes( 'elementid', 'org' );
		$Option = $node->attributes( 'admin' );
		$active = $node->attributes( 'active' );
		$Admin = '';
		if ( !empty( $Option ) )
		{
			$Admin = Helper::CheckTaskPermision( 'admin', $Option );
		}

		$org = C::_( 'data.' . $Key, $config, 0 );
		$options = array();
		if ( $org > 0 )
		{
			$Depts = [];
			$Base = [];
			if ( $Admin )
			{
				$Depts = Units::getMyUnitList( $org, Users::GetUserID() );
			}
			else
			{
				$Depts = $this->getUnitList( $org, $active );
			}

			$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
			foreach ( $Depts as $dept )
			{
				$val = $dept->ID;
				if ( !in_array( $val, $Base ) )
				{
					$text = XTranslate::_( $dept->TITLE );
					$ULevel = $dept->ULEVEL;
					$options[] = HTML::_( 'select.option', $val, str_repeat( '- ', $ULevel ) . $text );
					$Base[] = $val;
				}
			}
		}
		else
		{
			$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org' ) );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control search-select" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getUnitList( $ORG = 0, $active = 0 )
	{
		static $List = array();
		$Query = 'Select '
						. ' u.ID,'
						. ' u.lib_title title,'
						. ' u.ulevel '
						. ' from lib_units u'
						. ' where '
						. ' u.active ' . ($active ? ' = ' . (int) $active : '> -1')
						. ($ORG ? ' and u.org = ' . $ORG : '')
						. ' order by lft asc ';

		if ( !count( $List ) )
		{
			$List = XRedis::getDBCache( 'lib_units', $Query, 'LoadObjectList', 'ID' );
//			$List = DB::LoadObjectList( $Query, 'ID' );
		}
		return $List;

	}

}
