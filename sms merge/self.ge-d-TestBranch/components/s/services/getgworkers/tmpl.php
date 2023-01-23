<?php
$index = 1;
$GROUP = null;
$mode = Request::getVar( 'mode', false );
$hash = substr( md5( microtime() ), rand( 1, 25 ), 5 );
foreach ( $Workers as $Worker )
{
	$IDx[] = $Worker->ID;
	?>
	<div class="ContractItem">
		<div class="ContractItem_name"  title="<?php echo $Worker->WORKERNAME; ?> - <?php echo $Worker->MOBILE_PHONE_NUMBER; ?>  - <?php echo $Worker->PRIVATE_NUMBER; ?>">
			<?php
			echo $index . '&nbsp;&nbsp;&nbsp;';
			echo $Worker->WORKERNAME;
			?>
			<small> - 
				<?php echo $Worker->MOBILE_PHONE_NUMBER; ?>  - <?php echo $Worker->PRIVATE_NUMBER; ?>
			</small>
		</div>
		<div class="Contracttools">
			<input name="params[worker_order][<?php echo $Worker->ID; ?>]" size="2" class="worker_item_order" value="<?php echo $index * 10; ?>" />
			<?php
			if ( $mode != 'view' )
			{
				?>
				<span class="Contracttool" onclick="delGWorker('<?php echo $Worker->ID; ?>', <?php echo $Group; ?>);">
					<img src="templates/images/delete.gif" alt="Delete" />
				</span>
				<?php
			}
			?>
		</div>
		<div class="cls"></div>
	</div>
	<?php
	++$index;
}
echo '</div>';
