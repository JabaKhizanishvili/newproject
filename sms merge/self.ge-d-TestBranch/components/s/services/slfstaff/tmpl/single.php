<?php
$index = 1;
$GROUP = null;
$mode = Request::getVar( 'mode', false );
$hash = substr( md5( microtime() ), rand( 1, 25 ), 5 );
$rr = [];

foreach ( $Workers as $Worker )
{
	$ex = (array) explode( '-', $Worker->WORKERNAME );
	foreach ( $ex as $val )
	{
		$value = trim( $val );
		if ( empty( $value ) )
		{
			continue;
		}

		$rr[] = XTranslate::_( $value );
	}
	$IDx[] = $Worker->ID;
	$position = !empty( C::_( 'POSITION', $Worker ) ) ? ' - ' . C::_( 'POSITION', $Worker ) : '';
	?>
	<div class="ContractItem">
		<div class="ContractItem_name"  title="<?php echo $Worker->WORKERNAME . $position; ?>">
			<?php
			echo $index . '&nbsp;&nbsp;&nbsp;';
			echo implode( ' - ', $rr );
			?>
		</div>
	</div>
	<?php
	++$index;
}
echo '</div>';
