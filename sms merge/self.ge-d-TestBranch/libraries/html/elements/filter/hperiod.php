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
class FilterElementhperiod extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'hperiod';

	public function fetchElement( $name, $id, $node, $config )
	{
		$Periods = $this->GetPeriods();
		$options = array();
		$value = $this->GetConfigValue( $config['data'], $name );
		$options[] = HTML::_( 'select.option', -1, Text::_( '-' ) );
		foreach ( $Periods as $Period )
		{
			$val = C::_( 'P', $Period );
			$text = C::_( 'P_START', $Period ) . ' - ' . C::_( 'P_END', $Period );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="filter_droplist form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function GetPeriods()
	{
		$Query = 'select '
						. ' k.p, '
						. ' k.p_start, '
						. ' k.p_end, '
						. ' k.p_cur '
						. ' from ( '
						. ' select '
						. ' to_char(t.start_date, \'ddmmyy\') || to_char(t.end_date, \'ddmmyy\') p, '
						. ' to_char(t.start_date, \'dd-mm-yyyy\') p_start, '
						. ' to_char(t.end_date, \'dd-mm-yyyy\') p_end, '
						. ' case when sysdate between t.start_date and t.end_date then 1 else 0 end p_cur '
						. ' from lib_user_holiday_limit t '
						. ' left join slf_worker w on w.orgpid = t.worker '
						. ' where '
						. ' t.start_date is not null '
						. ' and t.end_date is not null'
						. ' and w.active = 1 '
						. ' ) k '
						. ' group by k.p, k.p_start, k.p_end, k.p_cur'
						. ' order by p_cur desc, k.p_start desc '
		;
		return DB::LoadObjectList( $Query );

	}

}
