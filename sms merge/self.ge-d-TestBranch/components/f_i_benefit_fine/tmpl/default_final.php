<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Prev', $this->_option_edit, 'final_prev' );
		Helper::getToolbar( 'Save', $this->_option_edit, 'save' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<?php
		$dinamics = [
				'WORKER' => 'schworker',
				'ORG' => 'org',
				'BENEFIT_ID' => 'benefit',
				'PERIOD_ID' => 'genperiod',
				'COST' => 'print'
		];

		$collect = new stdClass();
		foreach ( $dinamics as $key => $value )
		{
			$v = C::_( $key, $this->data, '' );
			$collect->$key = $v;
		}

		Xhelp::DataBox( $collect, $dinamics, 'col-md-6', [], 'WORKER' );
		$calculated = C::_( 'CALC', $this->data, [] );
		Xhelp::DataBox( $calculated, $dinamics, 'col-md-6', [], '' );
		echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_final.xml' );
		?>
		<input type="hidden" value="" name="task" />
	</form>
</div>
<?php
$this->setHelp();

