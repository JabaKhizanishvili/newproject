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
class JGridElementWorkerUnits extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'workerUnits';

	public function fetchElement( $row, $node, $group )
	{
		$return = '';
		$key = trim( $node->attributes( 'key' ) );
		$Option = C::_( '_option', $group );
		$org_plce = trim( Request::getState( $Option, 'org_place', false ) );
		$Value = C::_( $key, $row );
		$translate = trim( $node->attributes( 't' ) );
		$WorkerUnitsList = $this->getWorkerUnitsList( $org_plce );
		$units = (array) C::_( $Value, $WorkerUnitsList, [] );
		$HTML = [];
		foreach ( $units as $Item )
		{
			$HTML[] = '<div class="key_div">'
							. '<span class="key_val">'
							. XTranslate::_( $Item )
							. ' '
							. '</span>'
							. '</div>'
			;
		}
		return '<div class="key_row">' . implode( '', $HTML ) . '</div>';

	}

	public function getWorkerUnitsList( $org_plce = 0 )
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$query = 'select '
							. ' t.orgpid, '
							. ' lu.lib_title org_place '
							. ' from slf_worker t '
							. ' left join lib_staff_schedules s on s.id = t.staff_schedule '
							. ' left join lib_units lu on lu.id = s.org_place '
							. ' where t.active = 1 '
//							. ($org_plce > 0 ? ' and s.org_place = ' . $org_plce : '')
							. ' order by t.ORGPID ';
			$return = DB::LoadObjectList( $query );
			foreach ( $return as $val )
			{
				$Data[$val->ORGPID][] = $val->ORG_PLACE;
			}
		}
		return $Data;

	}

}
