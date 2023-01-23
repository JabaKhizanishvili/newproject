<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
<div class="page_title_add">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="" method="post" class="form-horizontal" name="fform" id="fform">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		if ( count( $this->data->items ) )
		{
			?>
			<div class="report_page_result">
				<div class="page_title">
					<div class="toolbar">
						<?php
						Helper::getToolbar( 'Apply', '', 'save', 0, 1 );
						?>
					</div>
					<div class="cls"></div>
				</div>
				<div class="bulletin_report_item">
					<table class="table table-bordered text-center">
						<tbody>
							<tr class="bulletin_report_item_head">
								<td class="Georgian1"><?php echo Text::_( 'Action' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Time' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Date' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'DELAY_MINUTES' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Lateness Reason' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'User Comment' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'User Comment Date' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Are You Agree?' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Why?' ); ?></td>
							</tr>
							<?php
							foreach ( $this->data->items as $Items )
							{
								$Worker = C::_( '0.WORKER', $Items );
								$ORG = C::_( '0.ORG_NAME', $Items );
								$staff_schedule = C::_( '0.STAFF_SCHEDULE', $Items );
								require 'default_item.php';
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="page_title_add">
				<div class="toolbar">
					<?php
					Helper::getToolbar( 'Apply', '', 'save', 0, 1 );
					?>
				</div>
				<div class="cls"></div>
			</div>
			<?php
		}
		else
		{
			?> 
			<div class="report_page_result">
				<div class="page_title">
				</div>
				<div class="text-danger text-center Georgian3">
					<?php echo Text::_( 'Comments not detected!' ) ?>
				</div>
				<div class="page_title">
				</div>
			</div>
		<?php }
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
