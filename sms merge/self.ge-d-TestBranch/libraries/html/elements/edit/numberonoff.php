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
class JElementNumberonoff extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'numberonoff';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$lbl_onoff = $node->attributes( 'onoff_lbl' );
		$size = ( $node->attributes( 'size' ) ? 'size="' . $node->attributes( 'size' ) . '"' : '' );
		$class = ( $node->attributes( 'class' ) ? 'class="text_area ' . $node->attributes( 'class' ) . ' form-control"' : 'class="form-control"' );
		$Min = ( $node->attributes( 'min' ) ? 'min="' . $node->attributes( 'min' ) . '"' : '' );
		$Max = ( $node->attributes( 'max' ) ? 'max="' . $node->attributes( 'max' ) . '"' : '' );
		$Step = ( $node->attributes( 'step' ) ? 'step="' . $node->attributes( 'step' ) . '"' : '' );
		/*
		 * Required to avoid a cycle of encoding &
		 * html_entity_decode was used in place of htmlspecialchars_decode because
		 * htmlspecialchars_decode is not compatible with PHP 4
		 */
		$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		$decN = $node->attributes( 'dec' );
		if ( $decN )
		{
			$value = Xhelp::strNumber( $value, $decN );
		}
		$this->SetGeoKBD( $node, $control_name . $name );

		$rand = rand( 1, 20 );
		Helper::SetJS(
						'$("#params' . $name . $rand . '").click(function(){'
						. ' var ramess = $("#params' . $name . $rand . '"); '
						. ' var sets = $("#params' . $name . '");'
						. ' if(ramess.val() == 1)'
						. ' { '
						. '   ramess.val("0");'
						. '   sets.attr("disabled", false);'
						. '   sets.show();'
						. ' }'
						. ' else'
						. ' { '
						. '   ramess.val("1");'
						. '   sets.attr("disabled", true);'
						. '   sets.hide();'
						. ' } '
						. '});'
		);

		$html = '<div class="radio" style="margin-bottom:10px !important;">';
		$html .= '<input type="checkbox" name="' . $control_name . '[' . $name . '_ONOFF]" id="params' . $name . $rand . '" value="" >';
		$html .= '<label id="params' . $name . $rand . '" for="params' . $name . $rand . '" style="display: inline-block;">' . Text::_( $lbl_onoff ) . '</label>';
		$html .= '</div>';
		$html .= '<input type = "number" name = "' . $control_name . '[' . $name . ']" id = "' . $control_name . $name . '" value = "' . $value . '" ' . $class . ' ' . $size . $Min . $Max . $Step . ' />';
		return $html;

	}

}
