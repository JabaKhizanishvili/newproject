<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
?>
<tr>
	<td colspan="10">
		<table border="0" class="table-custom" >
			<thead>
				<tr>
					<td class="text-left Georgian2">
						<?php echo $Worker; ?>
					</td>
				</tr>
			</thead>
		</table>
	</td>
</tr>
<?php
if ( count( $Items ) )
{
	$RecDate = null;
	foreach ( $Items as $Item )
	{
		$RecDateDate = new PDate( C::_( 'EVENT_DATE', $Item ) );
		$TIME_MIN = (C::_( 'TIME_MIN', $Item ) > 0) ? C::_( 'TIME_MIN', $Item ) : '';
		$ID = C::_( 'ID', $Item );
		$TIME_COMMENT = C::_( 'TIME_COMMENT', $Item );
		$C_RESOLUTION = C::_( 'C_RESOLUTION', $Item );
		$C_COMMENT = C::_( 'C_COMMENT', $Item );
		?>
		<tr>
			<td class="text-right"><?php echo C::_( 'EVENT_NAME', $Item ); ?></td>
			<td><?php echo $RecDateDate->toFormat( '%H:%M:%S' ); ?></td>
			<td><?php echo $RecDateDate->toFormat( '%d-%m-%Y' ); ?></td>
			<td><?php echo $TIME_MIN; ?></td>
			<td><?php echo $TIME_COMMENT; ?></td>
			<td class="text-left" ><?php echo C::_( 'U_COMMENT', $Item ); ?></td>
			<td class="text-left"><?php echo C::_( 'U_COMMENT_DATE', $Item ); ?></td>
			<td>
				<?php
				$options = array();
				$options[] = HTML::_( 'select.option', 1, Text::_( 'adequate' ) );
				$options[] = HTML::_( 'select.option', 2, Text::_( 'inadequate' ) );
				echo HTML::_( 'select.radiolist', $options, 'params[' . $ID . '][C_RESOLUTION]', '  data-rel="params_' . $ID . '_C_COMMENT" class="c_resolution_data" ', 'value', 'text', $C_RESOLUTION, 'params_' . $ID . '_C_RESOLUTION' );
				?>
			</td>
			<td>
				<div class="hidden " id="params_<?php echo $ID; ?>_C_COMMENT" >
					<input type="text" value="<?php echo $C_COMMENT; ?>" name="params[<?php echo $ID; ?>][C_COMMENT]" class="form-control kbd" />
					<span class="input-group-addon">
						<i class="bi bi-asterisk form_must_fill"></i>
					</span>
				</div>
			</td>
		</tr>
		<?php
	}
}
?>
<tr>
	<td colspan="10">
		<br />
	</td>
</tr>
