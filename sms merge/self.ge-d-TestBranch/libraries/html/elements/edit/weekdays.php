
<?php

class JElementWeekdays extends JElement
{
	protected $_name = 'weekdays';

	public function fetchElement( $name, $value, $node, $control_name )
	{
		$data = array_flip( explode( ',', $value ) );
		$Controllers = [ '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday' ];
		$K = '';
		ob_start();
		?>
		<div class="groups_parent level_0 radio">
			<?php
			foreach ( $Controllers as $ID => $day )
			{
				$chk = '';
				if ( isset( $value ) )
				{
					foreach ( $value as $k => $id )
					{
						if ( $ID == $id )
						{
							$chk = ' checked="checked" ';
						}
					}
				}
				?>
				<div class="level_0 radio">
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
