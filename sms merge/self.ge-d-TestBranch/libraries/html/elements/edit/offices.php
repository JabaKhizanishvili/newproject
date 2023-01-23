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
class JElementOffices extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Offices';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$Depts = $this->getOfficeList();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'OFFICE FILTER' ) );
		foreach ( $Depts as $dept )
		{
			$val = $dept->ID;
			$text = $dept->TITLE;
			$options[] = HTML::_( 'select.option', $val, $text );
		}
//		$js = '$(\'#' . $control_name . $name . '\').chosen();';
//		Helper::SetJS( $js );
		return HTML::_( 'select.genericlist', $options, '' . $control_name . '[' . $name . ']', ' class="form-control" ', 'value', 'text', $value, $control_name . $name );

	}

	public function getOfficeList()
	{
		$query = 'select '
						. ' t.id, '
						. ' t.description || \' - \' || t.address title '
						. ' from ccare.office t '
						. ' where t.id >0 '
						. ' order by t.description ';
		return DB::LoadObjectList( $query );

	}

}
