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
class FilterElementKDoors extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'KDoors';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$List = $this->getKDoorsList();
//		$options[] = HTML::_( 'select.option', 0, Text::_( 'AD ROLE FILTER' ) );
		foreach ( $List as $Item )
		{
			$options[] = HTML::_( 'select.option', $Item->CODE, $Item->LIB_TITLE );
		}

		if ( is_array( $options ) )
		{
			reset( $options );
		}
		$html = '<input type="hidden" name="' . $name . '[]" />';
		$html .= '<select name="' . $name . '[]" id="' . $id . '" ' . ' multiple ' . ' class="form-control kbd search-select">';
		$html .= HTMLSelect::Options( $options, 'value', 'text', $value, false );
		$html .= '</select>';
		return $html;
//		return HTML::_( 'select.genericlist', $options, '' . $name, ' multiple="multiple" size="10" ', 'value', 'text', $value, $id );

	}

	public function getKDoorsList()
	{
		$Query = 'select '
						. ' t.code, '
						. ' o.lib_title || \' - \' || t.lib_title lib_title '
						. ' from LIB_DOORS t '
						. ' left join lib_offices o on o.id = t.office '
						. ' where '
						. ' t.active = 1 '
						. ' and t.office= ' . Helper::getConfig( 'hrs_kutaisi_office' )
						. ' order by lib_title';
		return DB::LoadObjectList( $Query );

	}

}
