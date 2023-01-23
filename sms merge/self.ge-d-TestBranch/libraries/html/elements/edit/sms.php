<?php
/**
 * @version		$Id: textarea.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a textarea element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementSMS extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'sms';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$rows = $node->attributes( 'rows', 5 );
		$cols = $node->attributes( 'cols', 35 );
		$class = ( $node->attributes( 'class' ) ? 'class="' . $node->attributes( 'class' ) . ' form-control"' : 'class="form-control"' );
		// convert <br /> tags so they are not visible when editing
		$value = str_replace( '<br />', "\n", $valueIN );
		$this->SetGeoKBD( $node, $control_name . $name );
		$Texts = Helper::getConfig( 'sms_templates' );
		$Data = explode( '@', $Texts );
		$Templates = '';
		$JS = ' var SMSTmpl_data =[];';
		$First = null;
		foreach ( $Data as $Key => $Text )
		{
			$Text = trim( $Text );
			if ( empty( $Text ) )
			{
				continue;
			}
			if ( empty( $First ) )
			{
				$First = $Text;
			}
			$JS .= ' SMSTmpl_data[' . $Key . '] = "' . preg_replace( "/[\n\r]+/", '\n', htmlspecialchars( $Text ) ) . '";';
			$Templates .= '<div class="sms_template_item"><a href="javascript:void(0);" onclick="$(\'#' . $control_name . $name . '\').val(SMSTmpl_data[' . $Key . ']);" >' . preg_replace( "/[\n\r]+/", '<br />', $Text ) . ' </a></div>';
		}
		if ( empty( $value ) )
		{
			$value = $First;
		}
		Helper::SetJS( $JS, false );
		return '<textarea name = "' . $control_name . '[' . $name . ']" cols = "' . $cols . '" rows = "' . $rows . '" ' . $class . ' id = "' . $control_name . $name . '" >' . $value . '</textarea><div class="cls"></div>' . $Templates;

	}

}
