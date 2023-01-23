<?php
$index = 1;
foreach ( $Workers as $Worker )
{
	$ID = C::_( 'ID', $Worker );
	$IDx[] = $ID;
	?>
	<div class="ContractItem">
		<div class="ContractItem_name">
			<?php
			echo $index . '&nbsp;&nbsp;&nbsp;';
			echo C::_( 'WORKERNAME', $Worker );
			?>
			<small>
				- <?php echo C::_( 'EMAIL', $Worker ); ?> - <?php echo C::_( 'PRIVATE_NUMBER', $Worker ); ?>
			</small>
		</div>
		<div class="Contracttools">
			<span class="Contracttool" onclick="delUWorker('<?php echo $ID; ?>', '<?php echo $name; ?>');">
				<img src="templates/images/delete.gif" alt="Delete" />
			</span>
		</div>
		<div class="cls"></div>
	</div>
	<?php
	$index++;
}