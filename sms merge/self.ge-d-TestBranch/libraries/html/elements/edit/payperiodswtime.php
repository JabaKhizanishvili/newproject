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
class JElementPayPeriodsWTime extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'PayPeriodsWTime';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$worker = $this->_parent->get( 'WORKER' );
		$Depts = $this->getAccuracyPeriod( $worker );

		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$start = $dept->P_START;
			$pname = $dept->LIB_TITLE;
			$end = $dept->P_END;
			$text = $pname . ' / ' . explode( ' ', $start )[0] . ' - ' . explode( ' ', $end )[0];
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getAccuracyPeriod( $worker )
	{
		$Query = 'select '
						. ' pp.id, '
						. ' ap.lib_title, '
						. ' pp.p_start, '
						. ' pp.p_end '
						. ' from slf_worker sw '
						. ' left join lib_f_salary_types fs on fs.id = sw.salarytype '
						. ' left join lib_f_accuracy_periods ap on ap.id = fs.accuracy_period '
						. ' left join slf_pay_periods pp on pp.pid = ap.id '
						. ' where '
						. ' sw.id = ' . $worker
						. ' and pp.status in (0, 1) '
						. ' order by pp.p_start asc '
		;
		return DB::LoadObjectList( $Query );

	}

}
