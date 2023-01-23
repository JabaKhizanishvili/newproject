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
class JGridElementUnitType extends JGridElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'UnitType';

	public function fetchElement( $row, $node, $group )
	{
		$List = $this->getUnitTypeList();
		$key = trim( $node->attributes( 'key' ) );
		$translate = trim( $node->attributes( 't' ) );
		$Value = C::_( $key, $row );
		$Text = C::_( $Value . '.TITLE', $List, Text::_( 'ORG' ) );
		if ( $translate == 1 )
		{
			$Text = XTranslate::_( $Text );
		}
		return $Text;

	}

	public function fetchElemessnt( $name, $value, $node, $control_name )
	{
		$List = $this->getUnitTypeList();
		foreach ( $List as $Item )
		{
			$options[] = HTML::_( 'select.option', $Item->ID, $Item->TITLE );
		}
		Helper::SetJS( 'setADActior(\'' . $control_name . $name . '\');' );
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" onchange="setADActior(\'' . $control_name . $name . '\');" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getUnitTypeList()
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$Query = 'select '
							. ' ut.id, '
							. ' ut.lib_title title'
							. ' from lib_unittypes ut '
							. ' where ut.active > -1'
							. ' order by  ut.ordering  asc ';
			$Data = DB::LoadObjectList( $Query, 'ID' );
		}
		return $Data;

	}

}
