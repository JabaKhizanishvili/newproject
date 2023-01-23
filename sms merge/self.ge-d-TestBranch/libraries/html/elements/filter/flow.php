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
class FilterElementFlow extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'flow';

	public function fetchElement( $name, $id, $node, $config )
	{
		$Data = TaskHelper::getFlows( -1, 'LIB_TITLE', '>' );
		$options = array();
		$options[] = HTML::_( 'select.option', -1, Text::_( 'Flow Filter' ) );
		foreach ( $Data as $item )
		{
			$options[] = HTML::_( 'select.option', $item->ID, stripslashes( $item->LIB_TITLE ), 'value', 'text' );
		}
		$value = Collection::get( 'data.' . $name, $config );
//		return HTML::_( 'select.genericlist', $options, $name, ' onchange="setFilter();" style="width:200px;" ', 'value', 'text', $value, $id );
//		$js = '$(\'#' . $id . '\').chosen();';
//		Helper::SetJS( $js );
		return HTML::_( 'select.genericlist', $options, $name, ' onchange="setFilter();"  class="form-control" ', 'value', 'text', $value, $id );

	}

}
