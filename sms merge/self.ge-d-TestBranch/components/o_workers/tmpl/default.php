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
						<?php echo Helper::getPageTitle(); ?>
					<div class="toolbar">
						<?php
						Helper::getToolbar( 'Save', '', 'save' );
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
								<td class="Georgian1"><?php echo Text::_( 'Minutes' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Lateness Reason' ); ?></td>
							</tr>
							<?php
							foreach ( $this->data->items as $Items )
							{
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
					Helper::getToolbar( 'Save', '', 'save' );
					?>
				</div>
				<div class="cls"></div>
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
