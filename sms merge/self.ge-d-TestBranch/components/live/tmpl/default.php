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
<div class="page_content">
	<form action="" method="get" name="fform" id="fform">
		<?php
		$Refresh = C::_( 'data.refresh', $this, 0 );
		$Alerts = C::_( 'data.alerts', $this, 0 );
		$ShowPopup = true;

		$RefreshTime = (int) C::_( 'data.refreshtime', $this );
		if ( $Refresh )
		{
			Helper::SetJS( 'setInterval("setFilter()",(' . $RefreshTime . ' * 60 * 1000));' );
		}
		echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
		$Now = new PDate();
		?>
		<div class="text-right">
			<span class="text-info Georgian3">
				<?php echo Text::_( 'Server Time' ); ?>: 
				<?php echo $Now->toFormat( '%H:%M:%S %d-%m-%Y' ); ?>
			</span>
		</div>
		<?php
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
						'name' => XTranslate::_( 'გაცდენა' ),
						'class' => 'st_not_in',
						'count' => $red
				),
				'cl_black' => array(
						'name' => XTranslate::_( 'სამსახურ. გასვლა' ),
						'class' => 'cl_black',
						'count' => $black
				),
				'st_staff_in' => array(
						'name' => XTranslate::_( 'არის' ),
						'class' => 'st_staff_in',
						'count' => $green
				),
				'cl_blue' => array(
						'name' => XTranslate::_( 'შვებულება' ),
						'class' => 'cl_blue',
						'count' => $blue
				),
				'cl_yellow' => array(
						'name' => XTranslate::_( 'პირადი გასვლა' ),
						'class' => 'cl_yellow',
						'count' => $grey
				),
				'cl_grey' => array(
						'name' => XTranslate::_( 'ბიულეტენი' ),
						'class' => 'cl_grey',
						'count' => $grey2
				),
				'cl_coffe' => array(
						'name' => XTranslate::_( 'მივლინება / გრაფიკი' ),
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
						'name' => XTranslate::_( 'არ უნდა იყოს სამსახურში' ),
						'class' => 'st_day_off',
						'count' => $dayoff
				),
		);
		$Workers = array();
		foreach ( $Items as $Item )
		{
			$WorkerID = C::_( 'ID', $Item );
			if ( isset( $Workers[$WorkerID] ) )
			{
				continue;
			}
			$Workers[$WorkerID] = $WorkerID;
			$EventDate = new PDate( C::_( 'EVENT_DATE', $Item, 'now' ) );
			$StatusDateStr = C::_( 'STATUS_DATE', $Item, null );
			if ( empty( $StatusDateStr ) )
			{
				$StatusDate = new PDate( '- 5 hours' );
			}
			else
			{
				$StatusDate = new PDate( $StatusDateStr );
			}

			$LastDate = new PDate();
			if ( $EventDate->toUnix() > $StatusDate->toUnix() )
			{
				$LastDate = $EventDate;
			}
			else
			{
				$LastDate = $StatusDate;
			}
			$Minutes = intval( ($Now->toUnix() - $LastDate->toUnix()) / 60 );
			$Status = $this->CalculateStatus( $Item );
			$Name = XTranslate::_( C::_( 'FIRSTNAME', $Item ), 'person' ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $Item ), 'person' ) . ' - ' . XTranslate::_( C::_( 'ORG_NAME', $Item ) ) . ' - ' . XTranslate::_( C::_( 'STAFF_SCHEDULE', $Item ) );
			$Date = C::getVarIf( 'STATUS_DATE', $Item, '?', 'EVENT_DATE' );
			++$params[$Status]['count'];
			$AddClass = '';
			if ( $Minutes > 20 && $Status == 'st_not_in' )
			{
				$AddClass = ' cl_popup';
			}
			ob_start();
			?>
			<div class="col-lg-3 col-sm-6 col-md-5 <?php echo $Status; ?> btn-xs" >
				<div class="list_block_item live_item">
					<div class="live_item_inrrrr <?php echo $AddClass; ?>">
						<?php echo $Name; ?> - <?php
						echo $LastDate->toFormat( '%H:%M:%S' );
						if ( $Status == 'st_not_in' )
						{
							?> ( <span class="lateness_minutes"><?php echo $Minutes; ?></span> )<?php
						}
						?>
					</div>
					<div class="list_user_menu" >
						<a href="?option=official&worker=<?php echo $Item->ID; ?>"  target="_blank" >
							<?php echo Text::_( 'Official Register' ); ?>
						</a>
						<a href="?option=missionreghr&worker=<?php echo $Item->ID; ?>"  target="_blank" >
							<?php echo Text::_( 'Mission Register' ); ?>
						</a>
		<!--					<a href="?option=ptimesashr&worker=<?php echo $Item->ID; ?>"  target="_blank" >
						<?php //echo Text::_( 'Private Time Register' ); ?>
						</a>-->
						<a href="?option=o_sms&worker=<?php echo $Item->PARENT_ID; ?>" >
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
			<div id="list_block_groups" class="list_block row boards-block" >
				<?php
				echo $toolbar;
				?>
			</div>
			<div class="list_block" id="list_block_abc"  style="display: none;">
				<?php echo implode( '', $Data ); ?>
			</div>
		</div>

		<?php
//	echo $toolbar;
		?>
		<input type = "hidden" value = "<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name = "option" />
		<input type = "hidden" value = "<?php echo $this->data->order; ?>" name = "order" id = "order" />
		<input type = "hidden" value = "<?php echo $this->data->dir; ?>" name = "dir" id = "dir"/>
		<input type = "hidden" value = "<?php echo $this->data->start; ?>" name = "start" id = "start"/>
		<input type = "hidden" value = "<?php echo ( Request::getState( $this->_space, 'refresh_count', '0' ) + 1) ?>" name = "refresh_count" id = "start"/>
		<input type = "hidden" value = "" name = "task" />
		<div id="popupContainer" class="hidden"></div>
	</form>
</div>
</div>
<?php
$this->setHelp();
$js = ' Grouping(); ';
if ( $Alerts && $ShowPopup )
{
	$js .= 'var SHOW_POPUP = 0;
				    $(document).ready(function () {
				      $(\'.cl_popup\').each(function (i, e) {
				        SHOW_POPUP = 1;
				        $(\'#popupContainer\').append(\'<div class="list_block_item_popup" style="float:left; width:320px;margin:5px;position:relative;font-weight:bold;color:red; border: 1px solid #CCC;padding:4px 7px;">\' + $(e).html() + \'<div class="cls"></div></div>\');
				      });
				      if (SHOW_POPUP)
				      {
				        var kk = 1;
				        $(\'.list_block_item_popup\',\'#popupContainer\').each(function (i, e) {
				          $(this).attr(\'onclick\', \'toggle(\' + kk + \', this)\');
				          $(\'.list_user_menu\', this).attr(\'id\', \'ppmenu_\' + kk);
				          kk++;
				        });
				        var w = window.open(\'\', \'UserMonitoringWindow\', \'status=yes,height=300,scrollbars=yes,width=740\', true);
				        w.document.write($(\'#popupContainer\').html());
				        w.focus();
				        w.document.close();
				      }
				    })';
}

Helper::SetJS( $js );
