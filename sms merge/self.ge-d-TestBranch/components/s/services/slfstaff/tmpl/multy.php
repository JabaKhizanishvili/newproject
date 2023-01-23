<?php
$index = 1;
foreach ( $Workers as $Worker )
{
	$ID = C::_( 'ID', $Worker );
	$IDx[] = $ID;

	$rr = [];
	$ex = explode( '-', C::_( 'WORKERNAME', $Worker ) );
	foreach ( $ex as $val )
	{
		$value = trim( $val );
		if ( empty( $value ) )
		{
			continue;
		}

		$rr[] = XTranslate::_( $value );
	}
	?>
	<div class="ContractItem">
		<div class="ContractItem_name">
			<?php
			echo $index . '&nbsp;&nbsp;&nbsp;';
			echo implode( ' - ', $rr );
			?>
		</div>
		<div class="Contracttools">
			<span class="Contracttool" onclick="Delete_SlfStaff('<?php echo $ID; ?>', '<?php echo $name; ?>', '<?php echo $Case; ?>', '<?php echo $tmpl; ?>');">
				<a class="bi bi-x-lg" data-option-array-index="0"></a>
			</span>
		</div>
		<div class="cls"></div>
	</div>
	<?php
	$index++;
}