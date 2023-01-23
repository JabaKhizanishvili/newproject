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
class JElementAttributeChosen extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'attributechosen';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$options = array();

		$destinations = $node->attributes( 'destinations' );

		$Offices = $this->getAttributes( $destinations );

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

	public function getAttributes( $destinations )
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$Query = 'select la.id, la.lib_title
                    from lib_attributes la
                    where la.id > 0
                    and la.active = 1
                    and la.destination in (' . $destinations . ')
                    order by la.lib_title';
			$Data = (array) XRedis::getDBCache( 'lib_attributes', $Query, 'LoadObjectList' );
		}
		return $Data;

	}

}
