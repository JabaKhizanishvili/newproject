<?php
$index = 1;
$hash = substr( md5( microtime() ), rand( 1, 25 ), 5 );
$IDx = C::_( 'ID', $OrgUnit );
?>
<div class="ContractItem">
	<div class="x-small">
		<?php echo C::_( 'ORGPATH', $OrgUnit ); ?> / 
	</div>
	<div class="ContractItem_name">
		<?php echo C::_( 'LIB_TITLE', $OrgUnit ); ?> 
	</div>
	<div class="cls"></div>
</div>
<?php
