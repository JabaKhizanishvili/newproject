<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
?>
<tr class="bulletin_report_item_head">
	<td colspan="10">
		<table border="0" class="table-custom" >
			<thead>
				<tr>
					<td class="text-left Georgian2">
						<?php echo $Worker . ' - ' . $ORG . ' - ' . $staff_schedule; ?>
					</td>
					<td>
						<strong>
							<?php echo $Department; ?>
						</strong>
					</td>
					<td>
						<strong>
							<?php echo $Section; ?>	
						</strong>
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
		if ( $RecDateDate->toFormat( '%d-%m-%Y' ) != $RecDate )
		{
			$RecDate = $RecDateDate->toFormat( '%d-%m-%Y' );
			?>
			<tr>
				<td colspan="10" class="text-left bp">
					<strong>
						<?php echo $RecDateDate->toFormat( '%d-%m-%Y' ) . ', ' . Text::_( $RecDateDate->toFormat( '%A' ) ); ?>						
					</strong>
				</td>
			</tr>
			<?php
		}
		$TIME_MIN = (C::_( 'TIME_MIN', $Item ) > 0) ? C::_( 'TIME_MIN', $Item ) : '';
		$TIME_COMMENT = C::_( 'TIME_COMMENT', $Item, '' );
		$U_COMMENT = C::_( 'U_COMMENT', $Item );
		$C_RESOLUTION = C::_( 'C_RESOLUTION', $Item );
		?>
		<tr>
			<td class="text-right"><?php echo XTranslate::_( C::_( 'EVENT_NAME', $Item ) ); ?></td>
			<td><?php echo $RecDateDate->toFormat( '%H:%M:%S' ); ?></td>
			<td><?php echo $TIME_MIN; ?></td>
			<td>
				<?php
				if ( $TIME_MIN )
				{
					echo (empty( $U_COMMENT )) ? $TIME_COMMENT : $U_COMMENT;
				}
				else
				{
					echo $TIME_COMMENT;
				}
				?>
			</td>
			<td><?php
				if ( C::_( 'C_RESOLUTION', $Item, 0 ) == 1 )
				{
					echo Text::_( 'ADEQUATE' );
				}
				else if ( C::_( 'C_RESOLUTION', $Item, 0 ) == 2 )
				{
					echo Text::_( 'INADEQUATE' ) . ' ( ' . C::_( 'C_COMMENT', $Item, '' ) . ' )';
				}
				?></td>
			<td><?php
				echo XTranslate::_( C::_( 'CHIEF', $Item ) );
				?></td>
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
