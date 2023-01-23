<?php
/**
 * @version		$Id: Phone.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a Phone element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementMask extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'mask';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$size = ( $node->attributes( 'size' ) ? 'size="' . $node->attributes( 'size' ) . '"' : '' );
		$class = ( $node->attributes( 'class' ) ? 'class="text_area ' . $node->attributes( 'class' ) . ' form-control"' : 'class="form-control" ' );
		$Mask = $node->attributes( 'mask' );

		$Placeholder = $node->attributes( 'placeholder' );
		/*
		 * Required to avoid a cycle of encoding &
		 * html_entity_decode was used in place of htmlspecialchars_decode because
		 * htmlspecialchars_decode is not compatible with PHP 4
		 */
		$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		$this->SetGeoKBD( $node, $control_name . $name );
		Helper::SetJS( '$("#' . $control_name . $name . '").mask("' . $Mask . '", {placeholder:"' . $Placeholder . '"});' );
		return '<input type = "text" name = "' . $control_name . '[' . $name . ']" id = "' . $control_name . $name . '" value = "' . $value . '" ' . $class . ' ' . $size . ' />';

	}

}
