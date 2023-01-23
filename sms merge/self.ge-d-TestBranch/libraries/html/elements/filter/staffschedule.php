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
class FilterElementStaffschedule extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'staffschedule';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$Depts = $this->getSchedules();

		$Key = $node->attributes( 'elementid' );
		if ( !empty( $Key ) )
		{
			$org = C::_( 'data.' . $Key, $config, 0 );
			if ( (int) $org > 0 )
			{
				$Depts = $this->getSchedules( $org );
				$options[] = HTML::_( 'select.option', 0, Text::_( 'select category' ) );
			}
			else
			{
				$Depts = [];
				$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org' ) );
			}
		}
		else
		{
			$options[] = HTML::_( 'select.option', 0, Text::_( 'select category' ) );
		}

		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = XTranslate::_( $dept->LIB_TITLE );
			$ULevel = $dept->ULEVEL;
			if ( $ULevel < 0 )
			{
				$ULevel = 0;
			}
			if ( !empty( $dept->LIB_DESC ) )
			{
				$text .= '  (' . XTranslate::_( $dept->LIB_DESC ) . ') ';
			}
			$options[] = HTML::_( 'select.option', $val, str_repeat( '- ', $ULevel ) . $text );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control search-select" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getSchedules( $org = 0 )
	{
		$where = '';
		if ( (int) $org > 0 )
		{
			$where = ' and p.org =' . $org;
		}

		$Query = 'select '
						. ' p.id, p.lib_title, p.lib_desc, lu.ulevel, lu.lib_title unit '
						. ' from LIB_STAFF_SCHEDULES p '
						. ' inner join lib_units lu on lu.id = p.org_place '
						. ' where '
						. ' p.active > 0 '
						. $where
						. ' order by '
						. ' lu.lft asc, '
						. ' p.ordering asc'
		;
		return DB::LoadObjectList( $Query );

	}

}
