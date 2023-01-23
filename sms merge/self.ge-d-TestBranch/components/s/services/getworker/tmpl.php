<?php
$index = 1;
$hash = substr( md5( microtime() ), rand( 1, 25 ), 5 );
$IDx = C::_( 'ID', $Worker );
?>
<div class="ContractItem">
	<div class="ContractItem_name">
		<?php echo C::_( 'WORKERNAME', $Worker ); ?>
		<small> - 
			<?php echo C::_( 'POSITION', $Worker ); ?>
			<?php echo C::_( 'ORG_NAME', $Worker ); ?>
		</small>
	</div>

	<div class="cls"></div>
</div>
<?php
