<?php
/**
 * @version		$Id: Month.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is included in WSCMS
defined( 'PATH_BASE' ) or die( 'Restricted access' );

/**
 * Renders a Month element
 *
 * @package 	WSCMS.Framework
 * @subpackage	Parameter
 * @since		1.5
 */
class FilterElementMonth extends FilterElement
{
	/**
	 * Element name
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Month';

	public function fetchElement( $Name, $id, $Node, $config )
	{
		$class = $Node->attributes( 'class' ) ? $Node->attributes( 'class' ) : 'form-control';
		$value = trim( $this->GetConfigValue( $config['data'], $Name, $Node->attributes( 'default' ) ) );
		$size = ( $Node->attributes( 'size' ) ? ' size="' . $Node->attributes( 'size' ) . '" ' : '' );
		$Now = new PDate();
		$Start = 2019;
		$CurrentYear = (int) $Now->toFormat( '%Y' );
		$Range = range( $Start, $CurrentYear );
		$Months = $this->getMonths();
		$CurrentMonth = (int) $Now->toFormat( '%m' );
		$options = array();
		foreach ( $Range as $Year )
		{
			foreach ( $Months as $Key => $Value )
			{
				$ValMonth = intval( $Key );
				if ( $Year == $CurrentYear && $ValMonth > $CurrentMonth )
				{
					goto End;
				}
				$options[] = HTML::_( 'select.option', $Year . '-' . $Key, $Year . ' - ' . $Value );
			}
		}
		End:
		return HTML::_( 'select.genericlist', array_reverse( $options ), '' . $Name, $class . $size, 'value', 'text', $value, $id );

	}

	public function getMonths()
	{
		$Range = range( 1, 12 );
		$Return = array();
		foreach ( $Range as $Num )
		{
			if ( strlen( $Num ) == 1 )
			{
				$NumPad = '0' . $Num;
			}
			else
			{
				$NumPad = $Num;
			}
			$Return[$Num] = PDate::Get( '2019-' . $NumPad . '-01' )->toFormat( '%B' );
		}
		return $Return;

	}

}
