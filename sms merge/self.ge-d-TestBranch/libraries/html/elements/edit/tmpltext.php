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
class JElementTMPLText extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'TMPLText';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$TMPL = XTMPL::GetInstance();
		$Keys = $TMPL->GetTerms();

		$size = ( $node->attributes( 'size' ) ? 'size="' . $node->attributes( 'size' ) . '"' : '' );
		$class = ( $node->attributes( 'class' ) ? 'class="text_area ' . $node->attributes( 'class' ) . ' form-control"' : 'class="form-control"' );
		$value = htmlspecialchars( html_entity_decode( $valueIN, ENT_QUOTES ), ENT_QUOTES );
		$this->SetGeoKBD( $node, $control_name . $name );
		ob_start();
		?>
		<div class="tmpl_container">
			<input type="text" name="<?php echo $control_name . '[' . $name . ']'; ?>" id="<?php echo $control_name . $name; ?>" value="<?php echo $value; ?>" <?php echo $class; ?>  <?php echo $size; ?> />
		</div>
		<div class="tmpl_keys">
			<?php
			foreach ( $Keys as $K )
			{
				?>
				<span class="tmpl_key" onclick="$('#<?php echo $control_name . $name; ?>').insertText('<?php echo C::_( 'KEY', $K ); ?>');">
					<?php echo C::_( 'LABEL', $K ); ?>
				</span>
				<?php
			}
			?>
		</div>
		<?php
		$Content = ob_get_clean();

		return $Content;

	}

}
