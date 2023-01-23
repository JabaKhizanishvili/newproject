<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
	$RecDate = null;
		$RecDateDate = new PDate( C::_( 'EVENT_DATE', $Item ) );
		if ( $RecDateDate->toFormat( '%d-%m-%Y' ) != $RecDate )
		{
			$RecDate = $RecDateDate->toFormat( '%d-%m-%Y' );
			?>
<!--			<tr class="bulletin_report_item_head">-->
<!--				<td colspan="10" class="text-left bp">-->
<!--					<strong>-->
<!--						--><?php //echo $RecDateDate->toFormat( '%d-%m-%Y' ) . ', ' . Text::_( $RecDateDate->toFormat( '%A' ) ); ?><!--						-->
<!--					</strong>-->
<!--				</td>-->
<!--			</tr>-->
			<?php
		}
		$TIME_MIN = (C::_( 'TIME_MIN', $Item ) > 0) ? C::_( 'TIME_MIN', $Item ) : '';
		$private_number = C::_( 'PRIVATE_NUMBER', $Item ) ? ' \'' . C::_( 'PRIVATE_NUMBER', $Item ) : '';
		?>
		<tr>
            <td class="text-left"><?php echo XTranslate::_( C::_( 'EVENT_DATE_DAY', $Item ) ); ?></td>
			<td class="text-left"><?php echo XTranslate::_( C::_( 'WORKER', $Item ) ); ?></td>
			<td class="text-left"><?php echo $private_number ?></td>
			<td class="text-left"><?php echo XTranslate::_( C::_( 'ORG_NAME', $Item ) ); ?></td>
			<td class="text-left"><?php echo XTranslate::_( C::_( 'ORG_PLACE', $Item ) ); ?></td>
			<td class="text-left"><?php echo XTranslate::_( C::_( 'STAFF_SCHEDULE', $Item ) ); ?></td>
			<td class="text-left"><?php echo XTranslate::_( C::_( 'POSITION', $Item ) ); ?></td>
			<td><?php echo $TIME_MIN; ?></td>
			<td><?php echo XTranslate::_( C::_( 'TIME_COMMENT', $Item ) ); ?></td>
			<td><?php echo $RecDateDate->toFormat( '%H:%M:%S' ); ?></td>
			<!--<td><?php // echo C::_( 'PREV_EVENT_NAME', $Item );  ?></td>-->
			<!--<td><?php // echo C::_( 'PREV_EVENT_DATE', $Item );  ?></td>-->
		</tr>


