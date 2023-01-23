<div class="counting-system">
	<div class="row">
		<div class="col-xs-5">
			<div class="counting-system-clock">
				<?php echo HTML::image( X_TEMPLATE . '/images/clock.png', '', array( 'class' => 'img-responsive' ) ); ?>
			</div>
		</div>
		<div class="col-xs-7 nopadding">
			<div class="counting-system-msg">
				<?php echo Text::_( 'FIx it' ); ?>
			</div>
			<div class="counting-system-desc">
				<?php echo Text::_( 'check-in - check-out' ); ?>
			</div>
		</div>
		<div class="cls"></div>
	</div>

	<div class="buttons-container" id="ButtonControlSet"></div>
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
ob_start();
if ( false )
{
	?>
	<script type="text/javascript">
	<?php
}
?>
  GetWorkerStatus('#ButtonControlSet', '<?php echo Users::GetUserData( 'LDAP_USERNAME' ); ?>');
  var $GetWorkerStatus = function () {
    GetWorkerStatus('#ButtonControlSet', '<?php echo Users::GetUserData( 'LDAP_USERNAME' ); ?>');
  };
  window.setInterval($GetWorkerStatus, 8000);
<?php
if ( false )
{
	?>
	</script>
	<?php
}
$JSContent = ob_get_clean();
Helper::SetJS( $JSContent );
