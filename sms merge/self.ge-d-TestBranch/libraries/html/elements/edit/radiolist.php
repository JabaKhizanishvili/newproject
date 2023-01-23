
<?php

class JElementRadiolist extends JElement
{
	protected $_name = 'radiolist';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		ob_start();
		?>
		<div class="groups_parent level_0 radio">
			<?php
			$O = 0;
			foreach ( $node->children() as $option )
			{
				$ID = $option->attributes( 'value' );
				$day = $option->data();
				$O++;

				$chk = '';
				if ( isset( $value ) )
				{
					foreach ( (array) $value as $k => $id )
					{
						if ( $ID == $id )
						{
							$chk = ' checked="checked" ';
						}
					}
				}
				?>
				<div class="level_0 radio">
					<?php
					echo $O == 1 ? '<input type="hidden"   checked="checked" name="' . $control_name . '[' . $name . '][]" id="' . $control_name . $name . '_-1' . '" value="-1" />' : '';
					?>
					<input type="checkbox"  <?php echo $chk; ?> name="<?php echo $control_name; ?>[<?php echo $name; ?>][]" id="<?php echo $control_name . $name . '_' . $ID; ?>" value="<?php echo $ID; ?>" />
					<label for="<?php echo $control_name . $name . '_' . $ID; ?>">
						<?php echo Text::_( $day ); ?>
					</label>
					<div class="cls"></div>
				</div>
			<?php }
			?>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;

	}

}
