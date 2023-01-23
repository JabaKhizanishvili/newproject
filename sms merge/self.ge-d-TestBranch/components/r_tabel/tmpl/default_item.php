<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
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
		?>
		<tr>
			<td class="text-left"><?php echo C::_( 'WORKER', $Item ); ?></td>
			<td><?php echo $TIME_MIN; ?></td>
			<td><?php echo $RecDateDate->toFormat( '%H:%M:%S' ); ?></td>
			<td><?php echo C::_( 'PREV_EVENT_DATE', $Item ); ?></td>
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
