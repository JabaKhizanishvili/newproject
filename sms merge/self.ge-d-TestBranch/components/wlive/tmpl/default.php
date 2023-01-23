<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
/* @var $this LiveView */
$config = get_object_vars( $this );
?>
<div class="page_title">
	<?php echo Helper::getPageTitle(); ?>
	<div class="toolbar">
		<?php
//		Helper::getToolbar( 'New', $this->_option_edit );
//		Helper::getToolbar( 'Edit', $this->_option_edit, '', 1 );
//		Helper::getToolbar( 'Delete', $this->_option_edit, 'delete', 1, 1 );
		?>
	</div>
	<div class="cls"></div>
</div>
<form action="" method="get" name="fform" id="fform">
	<?php
	$Refresh = C::_( 'data.refresh', $this );
	$Alerts = C::_( 'data.alerts', $this );
	$RefreshTime = (int) C::_( 'data.refreshtime', $this );
	if ( $Refresh )
	{
		Helper::SetJS( 'setInterval("setFilter()",(' . $RefreshTime . ' * 60 * 1000));' );
	}
	echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
	$Items = C::_( 'data.items', $this );
	$Data = array();
	$red = 0;
	$black = 0;
	$green = 0;
	$blue = 0;
	$grey = 0;
	$grey2 = 0;
	$coffe = 0;
	$lime = 0;
	$orange = 0;
	$dayoff = 0;
	$params = array(
			'st_not_in' => array(
					'name' => 'გასვლა',
					'class' => 'st_not_in',
					'count' => $red
			),
			'cl_black' => array(
					'name' => 'სამსახურ. გასვლა',
					'class' => 'cl_black',
					'count' => $black
			),
			'st_staff_in' => array(
					'name' => 'არის',
					'class' => 'st_staff_in',
					'count' => $green
			),
			'cl_blue' => array(
					'name' => 'შვებულება',
					'class' => 'cl_blue',
					'count' => $blue
			),
//			'cl_yellow' => array(
//					'name' => 'პირადი გასვლა',
//					'class' => 'cl_yellow',
//					'count' => $grey
//			),
			'cl_grey' => array(
					'name' => 'ბიულეტენი',
					'class' => 'cl_grey',
					'count' => $grey2
			),
			'cl_coffe' => array(
					'name' => 'მივლინება / გრაფიკი',
					'class' => 'cl_coffe',
					'count' => $coffe
			),
//			'cl_orange' => array(
//					'name' => 'სპორტ დარბაზი',
//					'class' => 'cl_orange',
//					'count' => $lime
//			),
//			'cl_9400D3' => array(
//					'name' => 'საპატიო გასვლა',
//					'class' => 'cl_9400D3',
//					'count' => $orange
//			),
			'st_day_off' => array(
					'name' => 'არ უნდა იყოს სამსახურში',
					'class' => 'st_day_off',
					'count' => $dayoff
			),
	);
	foreach ( $Items as $Item )
	{
		$Status = $this->CalculateStatus( $Item );
		$Name = C::_( 'FIRSTNAME', $Item ) . ' ' . C::_( 'LASTNAME', $Item ) . ' - ' . C::_( 'ORG_NAME', $Item );
		$Date = C::getVarIf( 'STATUS_DATE', $Item, '?', 'EVENT_DATE' );
		++$params[$Status]['count'];
		ob_start();
		?>
		<div class="col-lg-3 col-sm-6 col-md-5 <?php echo $Status; ?> btn-xs" >
			<textarea class="hidden"><?php echo print_r( $Item, 1 ); ?></textarea>
			<div class="list_block_item live_item">
				<div class="live_item_inrrrr">
					<?php echo $Name; ?> - <?php echo $Date; ?>
				</div>
				<div class="list_user_menu" >
					<a href="?option=official&worker=<?php echo $Item->ID; ?>"  target="_blank" >
						<?php echo Text::_( 'Official Register' ); ?>
					</a>
					<a href="?option=missionreghr&worker=<?php echo $Item->ID; ?>"  target="_blank" >
						<?php echo Text::_( 'Mission Register' ); ?>
					</a>
					<a href="?option=ptimesashr&worker=<?php echo $Item->ID; ?>"  target="_blank" >
						<?php echo Text::_( 'Private Time Register' ); ?>
					</a>
					<a href="?option=o_sms&worker=<?php echo $Item->ID; ?>" >
						<?php echo Text::_( 'Send Lateness SMS' ); ?>
					</a>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		$Data[] = $content;
	}

	$toolbar = $this->getToolBar( $params );
	?>
	<div class="page_content">
		<div id="list_block_groups" class="list_block row" >

		</div>
		<div class="list_block" id="list_block_abc"  style="display: none;">
			<?php echo implode( '', $Data ); ?>
		</div>
	</div>

	<hr style=" color: blue;background-color: blue;height: 1px;" />
	<?php
	echo $toolbar;
	?>
	<input type = "hidden" value = "<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name = "option" />
	<input type = "hidden" value = "<?php echo $this->data->order; ?>" name = "order" id = "order" />
	<input type = "hidden" value = "<?php echo $this->data->dir; ?>" name = "dir" id = "dir"/>
	<input type = "hidden" value = "<?php echo $this->data->start; ?>" name = "start" id = "start"/>
	<input type = "hidden" value = "" name = "task" />
</form>
</div>
<?php
$this->setHelp();
$js = ' Grouping(); ';
Helper::SetJS( $js );
//  $('.toolbat_item').click(function () {
//    ToolBarFilter($(this).attr('rel'));
//  });
//  $('form').submit(function () {
////    $.cookie.set('refreshNow', '1', {path: '/'});
//  });
////  var groups = $.cookie.get('groupsBy');
//  if (groups == 'groups')
//  {
//    groupsByGroups();
//    $('#groups').addClass('groups_active');
//  } else
//  {
//    $('#abc').addClass('groups_active');
//  }
//
////  var cookiesData = $.cookie.filter(/^toolbarItem_/);
//  $.each(cookieData, function (i, a) {
//    ToolBarFilter(a);
//  });
//  $('.live_item').click(function () {
//    if ($(this).hasClass('live_item_active'))
//    {
//      $('.live_item_active').removeClass('live_item_active');
//    } else
//    {
//      $('.live_item_active').removeClass('live_item_active');
//      $(this).addClass('live_item_active');
//    }
//  });'