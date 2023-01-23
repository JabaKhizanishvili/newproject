<?php
/**
 * @version		$Id: Quarter.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is included in WSCMS
defined( 'PATH_BASE' ) or die( 'Restricted access' );

/**
 * Renders a Quarter element
 *
 * @package 	WSCMS.Framework
 * @subpackage	Parameter
 * @since		1.5
 */
class FilterElementQuarter extends FilterElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Quarter';

	public function fetchElement( $Name, $id, $Node, $config )
	{
		$format = ( $Node->attributes( 'format' ) ? $Node->attributes( 'format' ) : '%Y-%m-%d' );
		$class = $Node->attributes( 'class' ) ? $Node->attributes( 'class' ) : 'form-control';
		$value = trim( $this->GetConfigValue( $config['data'], $Name, $Node->attributes( 'default' ) ) );
		$size = ( $Node->attributes( 'size' ) ? ' size="' . $Node->attributes( 'size' ) . '" ' : '' );
		$Now = new PDate();
		$Start = 2017;
		$CurrentYear = $Now->toFormat( '%Y' );
		$Range = range( $Start, $CurrentYear );
		$Quarters = $this->getQuarters();
		$CurrentQuarter = Helper::getCurrentQuarter();
		foreach ( $Range as $Year )
		{
			foreach ( $Quarters as $Key => $Value )
			{
				if ( $Year == $CurrentYear && $Key > $CurrentQuarter )
				{
					break;
				}
				$options[] = HTML::_( 'select.option', $Year . '-' . $Key, $Year . ' - ' . $Value );
			}
		}
		return HTML::_( 'select.genericlist', array_reverse( $options ), '' . $Name, $class . $size, 'value', 'text', $value, $id );

	}

	public function getQuarters()
	{
		return array(
				'1' => 'I',
				'2' => 'II',
				'3' => 'III',
				'4' => 'IV',
		);

	}

}
