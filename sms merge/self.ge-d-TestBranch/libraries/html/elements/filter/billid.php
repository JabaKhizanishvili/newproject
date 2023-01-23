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
class FilterElementbillid extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'billid';
	public $StartYear = 2021;

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name, 0 );
		$Now = new PDate();
		$Month = $Now->toFormat( '%m' );
		$Year = $Now->toFormat( '%Y' );
		$Current = substr( $Year, 2, 2 ) . str_pad( $Month, 2, '0', STR_PAD_LEFT );
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Bill FILTER' ) );
		$EndYear = 2021;
		$Months = range( 1, 12 );
		$Years = range( $this->StartYear, $Year );
		$Bills = array();
		foreach ( $Years as $Year )
		{
			foreach ( $Months as $M )
			{
				$Date = PDate::Get( $Year . '-' . $M . '-10' );
				$BIll = substr( $Year, 2, 2 ) . str_pad( $M, 2, '0', STR_PAD_LEFT );
				PHP_EOL;
				if ( $BIll > $Current )
				{
					continue;
				}
				$Bills[$BIll] = array(
						'key' => $BIll,
						'period' => $Date->toFormat( '%B %Y' )
				);
			}
			$EndYear--;
		}
		foreach ( array_reverse( $Bills ) as $BIll )
		{
			$options[] = HTML::_( 'select.option', C::_( 'key', $BIll ), c::_( 'period', $BIll ) );
		}

		return HTML::_( 'select.genericlist', $options, $name, ' class="filter_droplist form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

}
