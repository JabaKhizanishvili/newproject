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
class JElementTMPLArea extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'TMPLArea';

	public function fetchElement( $name, $valueIN, $node, $control_name )
	{
		$TMPL = XTMPL::GetInstance();
		$Keys = $TMPL->GetTerms();

		$rows = $node->attributes( 'rows' );
		$cols = $node->attributes( 'cols' );
		$class = ( $node->attributes( 'class' ) ? 'class="' . $node->attributes( 'class' ) . ' form-control"' : 'class="form-control"' );
		$value = str_replace( '<br />', "\n", $valueIN );
		$this->SetGeoKBD( $node, $control_name . $name );
		ob_start();
		?>
		<div class="tmpl_container">
			<textarea name="<?php echo $control_name . '[' . $name . ']'; ?>" cols="<?php echo $cols; ?>" rows="<?php echo $rows; ?>" <?php echo $class; ?> id="<?php echo $control_name . $name; ?>" ><?php echo $value; ?></textarea>
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
