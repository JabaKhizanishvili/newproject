<?php
require_once PATH_BASE . DS . 'libraries' . DS . 'Units.php';

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
class FilterElementBenefittypes extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'benefittypes';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$Benefits = $this->getBenefitTypes();

		$options[] = HTML::_( 'select.option', 0, Text::_( 'ORG FILTER' ) );
		foreach ( $Benefits as $Item )
		{
			$val = $Item->ID;
			$desc = $Item->LIB_DESC;
			$text = XTranslate::_( $Item->LIB_TITLE );
			$text .= $desc ? ' (' . $desc . ') ' : '';
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, $name, ' class="form-control" onchange="setFilter();" ', 'value', 'text', $value, $id );

	}

	public function getBenefitTypes()
	{
		$Query = 'select '
						. ' e.id, '
						. ' e.lib_title,'
						. ' e.lib_desc '
						. ' from LIB_F_BENEFIT_TYPES e '
						. ' where '
						. ' e.active = 1 '
						. ' order by e.lib_title asc';
		return DB::LoadObjectList( $Query, 'ID' );

	}

}
