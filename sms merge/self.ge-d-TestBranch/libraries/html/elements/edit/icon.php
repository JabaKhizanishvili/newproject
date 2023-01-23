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
class JElementIcon extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'icon';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$size = ( $node->attributes( 'size' ) ? 'size="' . $node->attributes( 'size' ) . '"' : '' );
		$class = ( $node->attributes( 'class' ) ? 'class="' . $node->attributes( 'class' ) . '"' : 'class="text_area"' );
//		$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		$html = '';

		$html .= '<div class="sp-replacer sp-light icon_picker">'
						. '<div class="icon-pick">'
						. '<i class="icon-picked bi ' . $value . '"></i>'
						. '</div>'
						. '<div class="sp-dd">▼'
						. '</div>'
						. '</div>';

		$icons = '';
		$filename = X_PATH_BASE . DS . 'templates/css/fonts/bootstrap5_icons_pick.json';
		if ( file_exists( $filename ) )
		{
			$ICONS = json_decode( file_get_contents( $filename ) );
			foreach ( $ICONS as $ICON )
			{
				$icons .= '<i class="icon_p bi ' . $ICON . '"></i>';
			}
		}

		Helper::SetJS( '$(\'<div class="icon-picker" style="display:none;"><input type="text" class="icon-picker-search" placeholder="Search ..."><span class="icon-search bi bi-search"></span><div></div></div>\').appendTo(\'body\');' );
		Helper::SetJS( '$(\'' . $icons . '\').appendTo(\'.icon-picker div\');' );
		Helper::SetJS( '$(\'.icon_picker\').click(function(){
if($(\'.icon-picker\').css("display") == "none")
{
var offset = $(this).offset();
var left = offset.left;
var top = offset.top + 30;
$(\'.icon-picker\').css({\'left\':left+\'px\',\'top\':top+\'px\'}).show();
}
else
{
$(\'.icon-picker\').hide();
}
});' );
		Helper::SetJS( '$(\'.icon_p\').click(function(){
var CLASS = $(this).attr(\'class\').split(\' \');
var target = CLASS[CLASS.length -1];
$(\'.icon-picked\').attr({\'class\':\'\'}).addClass(\'icon-picked bi \'+target);
$("#' . $control_name . $name . '").attr({\'value\': target});
$(\'.icon-picker\').hide();
});
$(\'.icon-search\').click(function () {
    var search = $(\'.icon-picker-search\').val();
    $(\'.icon-picker div\').children().each(function () {
      var name = $(this).attr(\'class\').split(\'bi-\')[1];
      if (!name.includes(search))
      {
        $(this).hide();
      }
     else
     {
        $(this).show();
	}
    });
  });
	$(\'.icon-picker-search\').keydown(function (e) {
  if (e.keyCode == 13) {
    $(\'.icon-search\').click();
  }
});' );

		$html .= '<input type="text" name="' . $control_name . '[' . $name . ']" id="' . $control_name . $name . '" style="display:none;" value="' . $value . '" ' . $class . ' ' . $size . ' />';
		return $html;

	}

}