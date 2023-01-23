<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
Helper::SetJS( '$(document).ready(function(){
HIDEshow();
$("#paramsPERIODICITY").change(()=>{
HIDEshow();});

function HIDEshow(){
if($("#paramsPERIODICITY").val() == "3")
{
$("#form-item-HOLIDAY_RESTART_DAY").show();
  $("#form-item-HOLIDAY_RESTART_MONTH").show();
  $("#form-item-HOLIDAY_START_LIMIT").show();
  $("#form-item-").show();
}
else
{
  $("#form-item-HOLIDAY_RESTART_DAY").hide();
  $("#form-item-HOLIDAY_RESTART_MONTH").hide();
  $("#form-item-HOLIDAY_START_LIMIT").hide();
  $("#form-item-").hide();
}
}
});'
);
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getToolbar( 'Save', $this->_option_edit, 'save' );
        Helper::getToolbar( 'Apply', $this->_option_edit, 'apply' );
        Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="row">
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml' );
				?>
			</div>
			<div class="col-md-6">
				<?php
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default.xml', 'params', 'msg_sending' );
				?>
			</div>
		</div>

		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

