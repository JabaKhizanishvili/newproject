<?php
/**
 * @version		$Id: sql.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a SQL element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class FilterElementSQL extends FilterElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'SQL';

	public function fetchElement( $name, $id, $node, $config )
	{
		$key = ($node->attributes( 'key_field' ) ? $node->attributes( 'key_field' ) : 'value');
		$val = ($node->attributes( 'value_field' ) ? $node->attributes( 'value_field' ) : $name);
		$class = ($node->attributes( 'class' ) ? $node->attributes( 'class' ) : '');
		$data = $data = DB::loadObjectList( $node->attributes( 'query' ) );

		$submit = ' onchange="setFilter();" ';
		if ( $node->attributes( 'disable_submit' ) == 1 )
		{
			$submit = '';
		}

		$select_label = ($node->attributes( 'select_label' ) ? $node->attributes( 'select_label' ) : '');
		if ( $select_label )
		{
			$Start = array(
					$key => '0',
					$val => Text::_( $select_label )
			);
			array_unshift( $data, $Start );
		}
		$value = $this->GetConfigValue( $config['data'], $name );

		foreach ( $data as $arrray )
		{
			if ( is_array( $arrray ) )
			{
				$arrray[$val] = XTranslate::_( $arrray[$val] );
			}

			if ( is_object( $arrray ) )
			{
				$arrray->$val = XTranslate::_( $arrray->$val );
			}
		}

		return HTML::_( 'select.genericlist', $data, $name, 'class="form-control' . $class . '" ' . $submit . ' ', $key, $val, $value, $id );

	}

}