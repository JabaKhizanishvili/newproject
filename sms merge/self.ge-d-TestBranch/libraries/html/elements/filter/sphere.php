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
class FilterElementSphere extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Sphere';

	public function fetchElement( $name, $SphereID, $node, $config )
	{
		$app = array( '0' => Text::_( 'Sphere Filter' ) );
		$List = SalaryHelper::getSphereList();
		$options = array();
		foreach ( $app as $val => $text )
		{
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		foreach ( $List as $item )
		{
			$val = $item->SID;
			$text = $item->LIB_TITLE;
			$options[] = HTML::_( 'select.option', $val, $text, 'value', 'text' );
		}
		$value = $this->GetConfigValue( $config['data'], $name );
		return HTML::_( 'select.genericlist', $options, $name, ' onchange="setFilter();" class="form-control" ', 'value', 'text', $value, $SphereID );

	}

}
