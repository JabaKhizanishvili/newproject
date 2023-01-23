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
class JElementTMPLEditor extends JElement
{
	protected $_name = 'TMPLEditor';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$rows = $node->attributes( 'rows' );
		$cols = $node->attributes( 'cols' );
		$class = ( $node->attributes( 'class' ) ? 'class="' . $node->attributes( 'class' ) . '"' : 'class="text_area"' );
		$value = stripslashes( $valueIN );
		$TMPL = XTMPL::GetInstance();
		$Keys = $TMPL->GetTerms();
		$js = '
			tinymce.init({
				selector: "#' . $control_name . $name . '",
				 plugins: \'importcss searchreplace autolink autosave save directionality code visualblocks visualchars fullscreen image link media template table charmap hr nonbreaking anchor toc advlist lists wordcount imagetools textpattern noneditable charmap quickbars emoticons\',
  imagetools_cors_hosts: [\'picsum.photos\'],
  menubar: \'file edit view insert format tools table help\',
  toolbar: \'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor backcolor removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image  link anchor codesample | ltr rtl\',
  toolbar_sticky: true,
  autosave_ask_before_unload: true,
  autosave_interval: "30s",
  autosave_prefix: "{path}{query}-{id}-",
  autosave_restore_when_empty: false,
  autosave_retention: "2m",
  image_advtab: true,
  importcss_append: true,
  height: 400,
  template_cdate_format: \'[Date Created (CDATE): %m/%d/%Y : %H:%M:%S]\',
  template_mdate_format: \'[Date Modified (MDATE): %m/%d/%Y : %H:%M:%S]\',
  image_caption: true,
  quickbars_selection_toolbar: \'bold italic | quicklink h2 h3 blockquote quickimage quicktable\',
  noneditable_noneditable_class: "mceNonEditable",
  toolbar_drawer: \'sliding\',
  contextmenu: "link  table"	
			});
';
		Helper::SetJSFile( 'templates/tinymce/tinymce.min.js' );
		Helper::SetJS( $js );
		$KeysHTML = '';
		foreach ( $Keys as $key )
		{
			$KeysHTML .= '<span class="tmpl_key" title="' . C::_( 'KEY', $key ) . '">' . C::_( 'LABEL', $key ) . '</span>';
		}
		return '<div class="cls"></div>'
						. '<div class="editor">'
						. '<textarea name="' . $control_name . '[' . $name . ']" cols="' . $cols . '" rows="' . $rows . '" ' . $class . ' id="' . $control_name . $name . '" >' . $value . '</textarea>'
						. '</div>'
						. '<div class="tmpl_keys">' . $KeysHTML . '</div>'
						. '<div class="cls"></div>'
						. '';

	}

}
