<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );

TKCalendar::$step = 10;
$CopyParams = $params = '';
if ( $this->data )
{
	$params = HTML::convertParams( $this->data );
}
if ( $this->data->items )
{
	$CopyParams = HTML::convertParams( $this->data->items );
}
$group = '_default';
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="cls"></div>
</div>
<div class="page_content">  
	<form action="?option=<?php echo $this->_option; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<?php
		$startDate = new PDate( $this->data->start_date );
		echo HTML::renderFilters( $params, dirname( __FILE__ ) . DS . 'default.xml', $config );
		?>
		<?php
		$param = new Graph( $this->data->workers, dirname( __FILE__ ) . DS . 'default.xml', $config );

		echo $param->render( $group );
		Helper::SetJS( '$(".fixed-table").freezeTable({'
						. '"columnNum":4,'
						. '"shadow":true,'
//						. '"freezeColumnHead":true,'
//						. '"fixedNavbar":"page_content",'
						. '"scrollBar":true'
//						. '"scrollable": true'
						. '});' );
		?>
		<!--</div>-->
		<div class="cls"></div>
		<br />
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
		<input type="hidden" value="" name="task" /> 
	</form>
</div>
