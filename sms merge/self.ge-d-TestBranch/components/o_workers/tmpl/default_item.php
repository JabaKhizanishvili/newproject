<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$Worker = XTranslate::_( C::_( '0.WFIRSTNAME', $Items ), 'person' );
$Worker .= ' ' . XTranslate::_( C::_( '0.WLASTNAME', $Items ), 'person' );
$ORG = !empty( C::_( '0.ORG_NAME', $Items ) ) ? ' - ' . XTranslate::_( C::_( '0.ORG_NAME', $Items ) ) : '';
$SCHEDULE = !empty( C::_( '0.STAFF_SCHEDULE', $Items ) ) ? ' - ' . XTranslate::_( C::_( '0.STAFF_SCHEDULE', $Items ) ) : '';
?>
<tr class="bulletin_report_item_head">
	<td colspan="10" class="text-left Georgian2">
		<?php echo $Worker . $ORG . $SCHEDULE; ?>
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
		$ID = C::_( 'ID', $Item );
		$TIME_COMMENT = C::_( 'TIME_COMMENT', $Item );
		?>
		<tr>
			<td class="text-right"><?php echo XTranslate::_( C::_( 'EVENT_NAME', $Item ) ); ?></td>
			<td><?php echo $RecDateDate->toFormat( '%H:%M:%S' ); ?></td>
			<td>
				<input type="text" value="<?php echo $TIME_MIN; ?>" name="params[<?php echo $ID; ?>][TIME_MIN]" class="form-control" />
			</td>
			<td>
				<input type="text" value="<?php echo $TIME_COMMENT; ?>" name="params[<?php echo $ID; ?>][TIME_COMMENT]" class="form-control kbd" />
			</td>
		</tr>
		<?php
	}
}
