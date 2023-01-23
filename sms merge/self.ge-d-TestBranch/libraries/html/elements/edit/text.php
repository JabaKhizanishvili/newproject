<?php
/**
 * @version		$Id: text.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a text element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementText extends JElement
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
		$Multi = (int) $node->attributes( 'multi' );
		$size = ( $node->attributes( 'size' ) ? 'size="' . $node->attributes( 'size' ) . '"' : '' );
		/*
		 * Required to avoid a cycle of encoding &
		 * html_entity_decode was used in place of htmlspecialchars_decode because
		 * htmlspecialchars_decode is not compatible with PHP 4
		 */
		$this->SetGeoKBD( $node, $control_name . $name );
		if ( $Multi )
		{
			if ( is_array( $valueIN ) )
			{
				$value = implode( '|', $valueIN );
			}
			else
			{
				$value = trim( $valueIN );
			}
			ob_start();
			$class = ( $node->attributes( 'class' ) ? $node->attributes( 'class' ) . ' form-control multi-clone"' : 'form-control multi-clone' );
			$return = '<div class="WorkersBlock">'
							. '<div class="WorkersContainer' . $name . '" id="' . $control_name . $name . '_container">'
							. '</div>'
							. '<div class="cls"></div>'
							. '<div class="">'
							. '<input type = "text" name = "" id = "' . $control_name . $name . '_input" class = "form-control" autocomplete="off"/>'
							. '</div>'
							. '<input type="hidden" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '_value" value="' . $value . '" class="H_' . $control_name . $name . '" />'
							. '</div>';

			$JS = 'SetMultiText("' . $control_name . $name . '");'
							. ' $("#' . $control_name . $name . '_input").blur(function(){'
							. 'AddMultiText("' . $control_name . $name . '");'
							. '});'
							. '$("#' . $control_name . $name . '_input").keypress(function(event){'
							. 'AddMultiTextKeyPress("' . $control_name . $name . '", event);'
							. '});'
			;

			Helper::SetJS( $JS );
			return $return;
		}
		else
		{
			$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
			$class = ( $node->attributes( 'class' ) ? 'class="text_area ' . $node->attributes( 'class' ) . ' form-control"' : 'class="form-control"' );
			return '<input type="text" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" value="' . $value . '" ' . $class . ' ' . $size . ' />';
		}

	}

}
