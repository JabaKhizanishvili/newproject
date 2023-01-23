<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$Now = new PDate();
?>
<div class="page_title_add">
	<?php echo Helper::getPageTitle(); ?> 
	<div class="toolbar">
	</div>
	<div class="cls"></div>
</div>

<div class="page_content">
	<form action="" method="get" name="fform" id="fform" class="form-horizontal">
		<?php
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		if ( count( $this->data->items ) )
		{
			$UserItem = array();
			$UserItem['Current'] = array();
			$UserItem['Close'] = array();
			$UserItem['Confirmed'] = array();
			$DayCount = 0;
			?>
			<div class="report_page_result">
				<div class="page_title">
					<?php echo Helper::getPageTitle(); ?>
					<div class="toolbar">
						<?php
						Helper::getJSToolbar( 'Print', 'window.print', array() );
						?>
					</div>
					<div class="cls"></div>
				</div>
				<div class="bulletin_report_item">
					<table class="table table-bordered text-center">
						<tbody>
							<tr class="bulletin_report_item_head">
								<td class="Georgian1"><?php echo Text::_( 'BULLETINS START G' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'BULLETINS END G' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'day' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'Author' ); ?></td>
								<td class="Georgian1"><?php echo Text::_( 'BULLETIN_REC_DATE' ); ?></td>
								<td class="Georgian1" width='15%'><?php echo Text::_( 'UCOMMENT' ); ?></td>
							</tr>
							<?php
							foreach ( $this->data->items as $Key => $Items )
							{
								$UserItem['Department'] = C::_( '0.DEPARTMENT', $Items );
								$UserItem['Section'] = C::_( '0.SECTION', $Items );
								$UserItem['Worker'] = XTranslate::_( C::_( '0.WORKER', $Items ) );
								$BCount = count( $Items );
								foreach ( $Items as $Item )
								{
									$this->SetBData( $Item, $DayCount, $UserItem );
								}
								extract( $UserItem );
								require 'default_item.php';
								$UserItem = array();
								$UserItem['Current'] = array();
								$UserItem['Close'] = array();
								$UserItem['Confirmed'] = array();
								$DayCount = 0;
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="text-right Georgian2">
					<?php echo Text::_( 'Report Date' ); ?> - <?php echo $Now->toFormat( '%H:%M %d-%m-%Y' ); ?>
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
