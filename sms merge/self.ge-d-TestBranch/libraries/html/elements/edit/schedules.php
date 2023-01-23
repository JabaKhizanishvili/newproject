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
class JElementSchedules extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'schedules';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$ORG = C::_( '_registry._default.data.ORG', $this->_parent );
		$Positions = $this->getSchedulesList( $ORG );
		$ID = $control_name . $name;
		$options = [];
		foreach ( $Positions as $Item )
		{
			$options[] = HTML::_( 'select.option', C::_( 'ID', $Item ), XTranslate::_( C::_( 'LIB_TITLE', $Item ) ) );
		}
		if ( is_array( $options ) )
		{
			reset( $options );
		}
		$html = '<input type="hidden" name="' . $control_name . '[' . $name . ']' . '[]" />';
		$html .= '<select name="' . $control_name . '[' . $name . ']' . '[]" id="' . $ID . '" ' . ' multiple ' . ' class="form-control kbd search-select">';
		$html .= HTMLSelect::Options( $options, 'value', 'text', $value, false );
		$html .= '</select>';
		return $html;
//		return HTML::_( 'select.genericlist', $options, '' . $name, ' multiple="multiple" size="10" ', 'value', 'text', $value, $id );

	}

	public function getSchedulesList( $org = 0 )
	{
		$where = '';
		if ( (int) $org > 0 )
		{
			$where .= ' and w.org = ' . DB::Quote( $org );
		}
		$Query = ' select '
						. ' pp.id, '
						. ' pp.lib_title '
						. ' from lib_staff_schedules pp where '
						. ' pp.id in( '
						. ' select sc.id '
						. ' from hrs_workers_sch w '
						. ' left join lib_staff_schedules sc on	sc.id = w.staff_schedule '
						. ' where w.id > 0 '
						. ' and sc.active = 1 '
						. ' and w.active = 1 '
						. $where
						. ' group by sc.id '
						. ' ) '
						. ' order by pp.lib_title '
		;
		return DB::LoadObjectList( $Query );

	}

}
