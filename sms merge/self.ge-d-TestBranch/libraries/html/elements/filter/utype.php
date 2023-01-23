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
class FilterElementutype extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'utype';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$Data = $this->getTypes();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'type FILTER' ) );
		foreach ( $Data as $Item )
		{
			$val = $Item->ID;
			$text = XTranslate::_( $Item->TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getTypes()
	{
		$query = 'select '
						. ' id, '
						. ' t.lib_title title '
						. ' from lib_unittypes t '
						. ' where t.active=1 '
						. ' order by t.ordering asc';
		return DB::LoadObjectList( $query );

	}

}
