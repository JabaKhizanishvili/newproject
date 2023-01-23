<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
/* @var $model GraphHistoryModel */

$worker = Request::getInt( "worker" );
$day = Request::getInt( "day" );
$year = Request::getInt( "year" );


$model = $this->getModel();
$data = $model->getWorkerNameAndDay( $worker, $day, $year );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
		Helper::getJSToolbar( 'Close', 'IframeClose' )
		?>
	</div>
	<div class="cls"></div>
</div>

<div class="profile_item_block">
	<div class="profile_private_row">
		<div class="profile_private_key">
			<?php echo Text::_( 'Name, Surname' ); ?> : 
		</div>
		<div class="profile_private_value">
			<?php echo $data->workerName; ?>
		</div>
		<div class="cls"></div>
		<div class="profile_private_key">
			<?php echo Text::_( 'Week day' ); ?> : 
		</div>
		<div class="profile_private_value">
			<?php echo $data->weekday; ?>
		</div>
		<div class="cls"></div>
		<div class="profile_private_key">
			<?php echo Text::_( 'Month and number' ); ?> : 
		</div>
		<div class="profile_private_value">
			<?php echo $data->dayAndMonth; ?>
		</div>
		<div class="cls"></div>
	</div>
</div>
<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		echo HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default.xml', $config );
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
