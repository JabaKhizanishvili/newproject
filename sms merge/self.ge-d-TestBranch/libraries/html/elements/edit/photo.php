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
class JElementPhoto extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Photo';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$src = '';
		$description = XTranslate::_( 'დასაშვებია მხოლოდ სურათის (.jpg, .jpeg, .png) ფორმატის ფაილის ატვირთვა' );
		if ( preg_match( '/^data\:/i', $value ) )
		{
			$Img = $value;
		}
		else
		{
			$Img = Helper::ImgToBase64( PATH_UPLOAD . DS . $value );
		}
		if ( !empty( $Img ) )
		{
			$src = '<img alt="" src="' . $Img . '" />';
		}
		$id = $control_name . $name;
		$html = ''
						. '<div class="col-md-12 nopadding">'
						. '<div class="col-md-6 container-fluid">'
						. '<div class="col-12 nopadding">'
						. '<span class="btn btn-success btn-file">' . Text::_( 'Browse Image...' )
						. '<i class="bi bi-camera btn-ico"></i>'
						. '<input type="file" id="' . $id . '" class="form-control-file" />'
						. '</span>'
						. '</div>'
						. '<div class="row col-md-12 nopadding">'
						. '<div class="col-md-8 nopadding">'
						. '<div class="text-center imgdiv1">'
						. '<div id="' . $id . 'Preview" class="uploadblock">'
						. '<div class="rightDiagLine"></div>'
						. $src
						. '<div class="leftDiagLine"></div>'
						. '</div>'
						. '</div>'
						. '<div class="cls"></div>'
						. '<div class="text-center">'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']' . '" id="' . $id . 'Source" value="' . $Img . '" />'
						. '</div>'
						. '</div>'
						. '<div class="col-md-4 nopadding">'
						. '<button class="btn btn-danger imgdiv1_clear" type="button" onclick="resetPhoto(\'' . $id . '\');">' . Text::_( 'Clear' ) . '</button>'
						. '</div>'
						. '</div>'
						. '</div>'
						. '<div class="col-md-6">'
						. '<div class="form_desc red_desc_parent">'
						. '<i class="bi bi-exclamation-lg exclamation-ico"></i>'
						. '<span class="form_param_desc red_desc">'
						. $description
						. '</span>'
						. '</div>'
						. '</div>'
						. '</div>'
		;
		Helper::SetJS( 'setUpload("' . $id . '");' );
		return $html;

	}

}
