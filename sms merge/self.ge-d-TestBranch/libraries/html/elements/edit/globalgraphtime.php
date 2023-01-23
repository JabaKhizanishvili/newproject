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
class JElementGlobalGraphTime extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'globalgraphtime';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Query = 'select '
						. ' t.id, '
						. ' t.lib_title '
						. ' from lib_graph_times t '
						. ' WHERE t.active =1 '
						. ' AND t.owner = 0 '
						. ' UNION ALL '
						. ' SELECT t.id, '
						. ' t.lib_title FROM '
						. ' lib_graph_times t '
						. ' WHERE '
						. ' t.active = 1 '
						. ' AND t.type = 1 '
						. ' order by lib_title asc'
		;
		$data = DB::LoadObjectList( $Query );
		$options = array();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Select Graph Time' ) );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'HOLIDAY' ) );
		foreach ( $data as $dat )
		{
			$val = $dat->ID;
			$text = XTranslate::_( $dat->LIB_TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $name );

	}

}
