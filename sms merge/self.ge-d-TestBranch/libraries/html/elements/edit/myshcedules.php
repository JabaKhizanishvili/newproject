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
class JElementMyshcedules extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'myshcedules';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Depts = $this->mySchedules();
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Select category' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = XTranslate::_( $dept->LIB_TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control search-select" ', 'value', 'text', $value, $control_name . $name );

	}

	public function mySchedules()
	{
		$Q = 'select '
						. ' w.id, '
						. ' w.org_name ||\' - \'|| sc.lib_title lib_title'
						. ' from hrs_workers_sch w '
						. ' left join lib_staff_schedules sc on sc.id = w.staff_schedule '
						. ' where '
						. ' w.parent_id = ' . Users::GetUserID()
						. ' and w.active = 1 '
						. ' and sc.active = 1 '
		;
		return DB::LoadObjectList( $Q );

	}

}
