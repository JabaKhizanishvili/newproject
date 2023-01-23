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
class JElementUserPhoto extends JElement
{
	/**
	 * Element type
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'UserPhoto';

	public function fetchElement( $name, $value, $node, $control_name )
	{

		$tmpPhoto = '';
		$src = '';
		$plho = '';
		
		$id = $control_name . $name;
		
		$Photos = explode(';', $value);

		$Img = Helper::ImgToBase64( PATH_UPLOAD . DS . C::_('1', $Photos, C::_('0',$Photos)) );
		if ( !empty( $Img ) )
		{
			$src = '<img alt="" src="' . $Img . '" />';
			if(count($Photos)>1){
				  $plho = '<div class="placeholder">'
								. '<h1>ფოტო დასადასტურებელია</h1>'
								. '</div>';
					$src = $src . $plho;
			}
		}

//		if(isset($_SERVER['HTTP_USER_AGENT']) && $_SERVER['HTTP_USER_AGENT'] == 'Debug')
//		{
//			echo '<pre><pre>';
//			print_r($Photos);
//			echo '</pre><b>FILE:</b> '.__FILE__.'     <b>Line:</b> '.__LINE__.'</pre>'."\n";
//		}		
//		
		$html = '<div class="uploadRow">'
						. '<div id="' . $id . 'Preview" class="uploadblock">'
						. $src
						. '</div>'
						. '</div>'
						. '<div class="cls"></div>'
						. '<div class="text-center">'
						. '<div class="btn-group">'
						. '<span class="btn btn-success btn-file">' . Text::_( 'Browse Image...' )
						. '<input type="file" id="' . $id . '" class="form-control-file" />'
						. '</span>'
//						. '<button class="btn btn-danger" type="button" onclick="resetPhoto(\'' . $id . '\');">' . Text::_( 'Clear' ) . '</button>'
						. '</div>'
						. '<input type="hidden" name="' . $control_name . '[' . $name . ']' . '" id="' . $id . 'Source" value="" />'
						. '</div>'
		;
		Helper::SetJS( 'setUpload("' . $id . '");' );
		return $html;

	}

}
