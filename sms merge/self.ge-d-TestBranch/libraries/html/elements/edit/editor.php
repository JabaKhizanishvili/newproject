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
class JElementEditor extends JElement
{
	protected $_name = 'Editor';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$rows = $node->attributes( 'rows' );
		$cols = $node->attributes( 'cols' );
		$class = ( $node->attributes( 'class' ) ? 'class="' . $node->attributes( 'class' ) . '"' : 'class="text_area"' );
		// convert <br /> tags so they are not visible when editing
		$value = stripslashes( $valueIN );
//		$this->SetGeoKBD( $node, $control_name . $name );
		$js = '
			tinymce.init({
    selector: "#' . $control_name . $name . '",
    plugins: [
        "advlist autolink lists link image charmap print preview anchor",
        "searchreplace visualblocks code fullscreen",
        "insertdatetime media table contextmenu paste"
    ],
    toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image"
});
';
		Helper::SetJSFile( X_TEMPLATE . '/tinymce/tinymce.min.js' );
		Helper::SetJS( $js );
		return '</div>'
						. '<div class="cls"></div>'
						. '<div class="editor">'
						. '<textarea name="' . $control_name . '[' . $name . ']" cols="' . $cols . '" rows="' . $rows . '" ' . $class . ' id="' . $control_name . $name . '" >' . $value . '</textarea>'
						. '</div>'
						. '<div>';

	}

}
