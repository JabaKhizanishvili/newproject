<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
?>
<tr class="bulletin_report_item_head">
	<td colspan="10">
		<table border="0" class="table-custom" >
			<thead>
				<tr>
					<td class="text-left Georgian2">
						<?php echo $Worker; ?>
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
if ( count( $Current ) )
{
	?>
	<tr>
		<td colspan="10" class="text-left Georgian1">
			<?php echo Text::_( 'BL_CURRENT' ); ?>
		</td>
	</tr>
	<?php
	foreach ( $Current as $CItem )
	{
		?>
		<tr>
			<td><?php echo C::_( 'START_DATE', $CItem ); ?></td>
			<td><?php echo C::_( 'END_DATE', $CItem ); ?></td>
			<td><?php echo C::_( 'DAY_COUNT', $CItem ); ?></td>
			<td><?php echo XTranslate::_( C::_( 'APPROVER', $CItem ) ); ?></td>
			<td><?php echo C::_( 'APPROVE_DATE', $CItem ); ?></td>
			<td><?php echo XTranslate::_( C::_( 'UCOMMENT', $CItem ) ); ?></td>
		</tr>
		<?php
	}
}

if ( count( $Close ) )
{
	?>
	<tr>
		<td colspan="10" class="text-left Georgian1">
			<?php echo Text::_( 'BL_CLOSE' ); ?>
		</td>
	</tr>
	<?php
	foreach ( $Close as $CItem )
	{
		?>
		<tr>
			<td><?php echo C::_( 'START_DATE', $CItem ); ?></td>
			<td><?php echo C::_( 'END_DATE', $CItem ); ?></td>
			<td><?php echo C::_( 'DAY_COUNT', $CItem ); ?></td>
			<td><?php echo XTranslate::_( C::_( 'APPROVER', $CItem ) ); ?></td>
			<td><?php echo C::_( 'APPROVE_DATE', $CItem ); ?></td>
			<td><?php echo XTranslate::_( C::_( 'UCOMMENT', $CItem ) ); ?></td>
		</tr>
		<?php
	}
}
if ( count( $Confirmed ) )
{
	?>
	<tr>
		<td colspan="10" class="text-left Georgian1">
			<?php echo Text::_( 'BL_CONFIRMED' ); ?>
		</td>
	</tr>
	<?php
	foreach ( $Confirmed as $CItem )
	{
		?>
		<tr>
			<td><?php echo C::_( 'START_DATE', $CItem ); ?></td>
			<td><?php echo C::_( 'END_DATE', $CItem ); ?></td>
			<td><?php echo C::_( 'DAY_COUNT', $CItem ); ?></td>
			<td><?php echo XTranslate::_( C::_( 'APPROVER', $CItem ) ); ?></td>
			<td><?php echo C::_( 'APPROVE_DATE', $CItem ); ?></td>
			<td><?php echo XTranslate::_( C::_( 'UCOMMENT', $CItem ) ); ?></td>
		</tr>
		<?php
	}
}
?>
<tr>
	<td class="text-right Georgian2" colspan="2">
		<?php echo Text::_( 'B_DAY_COUNT' ); ?> : 
	</td>
	<td class="text-center Georgian2">
		<?php echo $DayCount; ?>
	</td>
	<td class="text-center Georgian2"  colspan="2" >
		<?php echo Text::_( 'B_COUNT' ); ?> : <?php echo $BCount; ?>
	</td>
</tr>
<tr>
	<td colspan="10">
		<br />
	</td>
</tr>
