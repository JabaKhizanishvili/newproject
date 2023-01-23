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
class FilterElementRegion extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Region';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = trim( $this->GetConfigValue( $config['data'], $name, $node->attributes( 'default' ) ) );

		$Locations = Gis::getLocationList();
		
		
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'REGION FILTER' ) );
		foreach ( $Locations as $Item )
		{
			$options[] = HTML::_( 'select.option', $Item->WID, $Item->Name );
		}
		if ( is_array( $options ) )
		{
			reset( $options );
		}

		$html = HTML::_( 'select.genericlist', $options, '' . $name, ' class="form-control search-select" ', 'value', 'text', $value, $id );
		return $html;
	}

}
