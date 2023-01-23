<?php
$index = 1;
$GROUP = null;
$mode = Request::getVar( 'mode', false );
$hash = substr( md5( microtime() ), rand( 1, 25 ), 5 );
foreach ( $Chiefs as $Chief )
{
	$IDx[] = $Chief->ID;
	?>
	<div class="ContractItem">
		<div class="ContractItem_name" title="<?php echo $Chief->WORKERNAME; ?> - <?php echo $Chief->POSITION; ?>">
			<?php
			echo $index . '.&nbsp;&nbsp;&nbsp;';
			echo $Chief->WORKERNAME;
			?>
			<small> - 
				<?php echo $Chief->POSITION; ?>
			</small>
		</div>
		<div class="Contracttools">
			<?php
			if ( $mode != 'view' )
			{
				?>
				<span class="Contracttool" onclick="delChief('<?php echo $Chief->ID; ?>', <?php echo $Org;?>);">
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