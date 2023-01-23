<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?> - <?php echo Text::_( 'Confirmation' ); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Confirmation', $this->_option_edit, 'save', 0, 1 );
		Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel', 0, 1 );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content" style="overflow: scroll;">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" name="fform" id="fform" class="form-horizontal" role="form">
		<table class="table table-bordered">
			<?php
			$Headers = array_keys( C::_( '0', $this->DATA ) );
			$GName = 'params[DATA]';
			echo $this->_GetHTMLHeader( $Headers );
			$K = 0;
			foreach ( $this->DATA as $Row )
			{
				echo $this->_GetHTML( $K, $GName, $Row );
				$K++;
			}
			?>
		</table>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

