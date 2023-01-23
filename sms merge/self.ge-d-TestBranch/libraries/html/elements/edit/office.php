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
class JElementOffice extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Office';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Depts = $this->getOfficeList();
		$translate = trim( $node->attributes( 't' ) );
		$options[] = HTML::_( 'select.option', 0, Text::_( 'OFFICE FILTER' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = $dept->TITLE;
			if ( $translate == 1 )
			{
				$text = XTranslate::_( $text );
			}
			$options[] = HTML::_( 'select.option', $val, $text );
		}

		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getOfficeList()
	{
		$query = 'select '
						. ' id, '
						. ' t.lib_title title '
						. ' from lib_offices t '
						. ' where t.active=1 '
						. ' order by title asc';
		return DB::LoadObjectList( $query );

	}

}
