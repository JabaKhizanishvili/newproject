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
class JElementPayperiods extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'payperiods';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$print = $node->attributes( 'print' );
		$print_id = $print == 1 && !empty( $value ) ? $value : 0;
		$Depts = $this->getAccuracyPeriod( $print_id );

		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select Org Place' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$start = $dept->P_START;
			$pname = $dept->LIB_TITLE;
			$end = $dept->P_END;
			$text = $pname . ' / ' . explode( ' ', $start )[0] . ' - ' . explode( ' ', $end )[0];

			if ( $print_id > 0 )
			{
				echo '<input type="hidden" name="params[' . $name . ']" id="params' . $name . '" value="' . $value . '">';
				return '<div class="form-control"><strong>' . XTranslate::_( $text ) . '</strong></div>';
			}

			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getAccuracyPeriod( $id = 0 )
	{
		$Query = 'select '
						. ' pp.id, '
						. ' ap.lib_title, '
						. ' pp.p_start, '
						. ' pp.p_end '
						. ' from slf_pay_periods  pp '
						. ' left join lib_f_accuracy_periods ap on ap.id = pp.pid '
						. ' where '
						. ' pp.p_start < sysdate '
//						. ($id > 0 ? '' : ' and pp.status = 0 ')
						. ($id > 0 ? ' and pp.id = ' . (int) $id : '')
						. ' order by pp.id desc '
		;
		return DB::LoadObjectList( $Query );

	}

}
