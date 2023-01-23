<?php
/**
 * @version		$Id: text.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a IPelement
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementIP extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Text';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$size = ( $node->attributes( 'size' ) ? 'size="' . $node->attributes( 'size' ) . '"' : '' );
		$class = ( $node->attributes( 'class' ) ? 'class="text_area ' . $node->attributes( 'class' ) . ' form-control"' : 'class="form-control"' );
		$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		$this->SetGeoKBD( $node, $control_name . $name );
		$JS = '$("#' . $control_name . $name . '").mask("0ZZ.0ZZ.0ZZ.0ZZ", { placeholder:"___.___.___.___", translation: {"Z": {pattern: /[0-9]/, optional: true}}});';
		Helper::SetJS( $JS );
		return '<input type="text" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $value . '" ' . $class . ' ' . $size . ' />';

	}

}
