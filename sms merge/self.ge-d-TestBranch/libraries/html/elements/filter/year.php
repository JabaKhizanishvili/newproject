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
class FilterElementYear extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Year';

	public function fetchElement( $name, $id, $node, $config )
	{
		$Now = new PDate();
		$StartYear = $Now->toFormat( '%Y' );
		$value = $this->GetConfigValue( $config['data'], $name, $StartYear );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'Year FILTER' ) );
		$EndYear = 2020;
		for ( $K = $StartYear; $K >= $EndYear; $K-- )
		{
			$val = $K;
			$text = $K . ' ' . Text::_( 'year' );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="filter_droplist form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

}