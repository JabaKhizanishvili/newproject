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
class FilterElementCategory extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Category';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = (int) $this->GetConfigValue( $config['data'], $name );
		if ( empty( $value ) )
		{
			$value = $node->attributes( 'default' );
		}
		$Depts = $this->getCategoryList();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Category FILTER' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = XTranslate::_( $dept->TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getCategoryList()
	{
		$query = 'select '
						. ' id, '
						. ' t.lib_title title '
						. ' from lib_categories t '
						. ' where t.active=1 '
						. ' order by title asc';
		return DB::LoadObjectList( $query );

	}

}
