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
class FilterElementMask extends FilterElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Mask';

	public function fetchElement( $name, $id, $node, $config )
	{
		$value = $this->GetConfigValue( $config['data'], $name );
		$class = ( $node->attributes( 'class' ) ? 'class="form-control ' . $node->attributes( 'class' ) . '"' : 'class="form-control"' );
		$Mask = $node->attributes( 'mask' );
		$DefVal = $node->attributes( 'default', 0 );
		if ( empty( $value ) )
		{
			$value = $DefVal;
		}
		$Placeholder = $node->attributes( 'placeholder' );
		Helper::SetJS( '$("#' . $id . '").mask("' . $Mask . '", {placeholder:"' . $Placeholder . '"});' );
		return '<input type = "text" name = "' . $name . '" id = "' . $id . '" value = "' . $value . '" ' . $class . ' />';

	}

}
