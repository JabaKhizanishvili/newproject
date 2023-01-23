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
		<div class="ContractItem_name">
			<?php
			echo $index . '&nbsp;&nbsp;&nbsp;';
			echo $Worker->WORKERNAME;
			?>
		</div>
		<div class="Contracttools">
			<?php
			if ( $mode != 'view' )
			{
				?>
				<span class="Contracttool" onclick="delWorker('<?php echo $Worker->ID; ?>');">
					<img src="<?php echo X_TEMPLATE; ?>/images/delete.gif" alt="Delete" />
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
