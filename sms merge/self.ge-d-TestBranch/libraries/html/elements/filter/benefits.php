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
class FilterElementBenefits extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'benefits';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$Depts = $this->getBenefits();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'select category' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = XTranslate::_( $dept->TITLE );
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control search-select" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getBenefits()
	{
		$query = 'select '
						. ' t.id, '
						. ' t.lib_title title '
						. ' from lib_f_benefits t '
						. ' where t.active > 0 '
						. ' order by t.lib_title ';
		return DB::LoadObjectList( $query );

	}

}
