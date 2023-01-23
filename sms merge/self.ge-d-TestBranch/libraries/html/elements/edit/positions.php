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
class JElementPositions extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'positions';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Positions = $this->getPositionsList();
		$ID = $control_name . $name;
		foreach ( $Positions as $Item )
		{
			$options[] = HTML::_( 'select.option', $Item->POSITION, $Item->POSITION );
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

	public function getPositionsList()
	{
		$Query = 'select w.position '
						. ' from hrs_workers w '
						. ' where w.id > 0 '
						. ' and w.active = 1 '
						. ' group by w.position '
						. ' order by w.position '
		;
		return DB::LoadObjectList( $Query );

	}

}
