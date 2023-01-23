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
class JElementOfficesChosen extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'officesChosen';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$options = array();
		$Offices = $this->getOfficesList();
		$Value = explode( ',', $value );
		$ID = $control_name . $name;
		foreach ( $Offices as $Item )
		{
			$options[] = HTML::_( 'select.option', $Item->ID, XTranslate::_( $Item->LIB_TITLE ) );
		}
		if ( is_array( $options ) )
		{
			reset( $options );
		}
		$html = '<input type="hidden" name="' . $control_name . '[' . $name . ']' . '[]" />';
		$html .= '<select name="' . $control_name . '[' . $name . ']' . '[]" id="' . $ID . '" ' . ' multiple ' . ' class="form-control kbd search-select">';
		$html .= HTMLSelect::Options( $options, 'value', 'text', $Value, false );
		$html .= '</select>';
		return $html;

	}

	public function getOfficesList()
	{
		$Query = 'select w.id, w.lib_title
  from lib_offices w
 where w.id > 0
   and w.active = 1
 order by w.lib_title'
		;
		return DB::LoadObjectList( $Query );

	}

}
