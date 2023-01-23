<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getJSToolbar( 'Print', 'window.print', array() );
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform" class="form-horizontal scrollBox">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		if ( count( $this->data->items ) )
		{
			?>
			<!--<div class="report_page_result">-->
			<div class="bulletin_report_item">
				<table class="table table-bordered text-center">
					<tbody>
						<tr class="bulletin_report_item_head">
							<td class="Georgian1"><?php echo Text::_( 'Action' ); ?></td>
							<td class="Georgian1"><?php echo Text::_( 'Time' ); ?></td>
							<td class="Georgian1"><?php echo Text::_( 'DELAY_MINUTES' ); ?></td>
							<!--<td class="Georgian1"><?php // echo Text::_( 'Lateness Reason' );          ?></td>-->
							<td class="Georgian1"><?php echo Text::_( 'User Comment/Reason' ); ?></td>
							<td class="Georgian1"><?php echo Text::_( 'Chief Resolution' ); ?></td>
						</tr>
						<?php
						foreach ( $this->data->items as $Items )
						{
							$ORG = XTranslate::_( C::_( '0.ORG_NAME', $Items ) );
							$Worker = XTranslate::_( C::_( '0.WFIRSTNAME', $Items ), 'person' );
							$Worker .= ' ' . XTranslate::_( C::_( '0.WLASTNAME', $Items ), 'person' );
							$staff_schedule = XTranslate::_( C::_( '0.STAFF_SCHEDULE', $Items ) );
							require 'default_item.php';
						}
						?>
					</tbody>
				</table>
			</div>
	</div>
	<?php
}
?>

<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
<input type="hidden" value="" name="task" /> 
</form>
</div>
<?php
$this->setHelp();
