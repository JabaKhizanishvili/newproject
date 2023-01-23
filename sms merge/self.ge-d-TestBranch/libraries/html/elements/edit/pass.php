<?php
/**
 * @version		$Id: password.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework

/**
 * Renders a password element
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
class JElementPass extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	protected $_name = 'Pass';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$class = ( $node->attributes( 'class' ) ? 'class="' . $node->attributes( 'class' ) . ' form-control"' : 'class="form-control"' );
		ob_start();
		?>
		<div class="form-group from-group-right">
			<input <?php echo $class; ?>  id="<?php echo $control_name . $name; ?>"  name="<?php echo $control_name . '[' . $name . ']'; ?>" type="password" value="<?php echo $value;?>" />
			<span class="pasdword-state from-group-addon-right">
				<i class="bi bi-eye-slash"></i>
			</span>
		</div>
		<?php
		$Content = ob_get_clean();
		return $Content;

	}

}
