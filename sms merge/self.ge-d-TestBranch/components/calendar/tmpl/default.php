<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
$CD = C::_( 'date', $this->data );
$Date = PDate::Get( $CD );
$DateBill = $Date->toFormat( '%y%m' );
$Dates = Helper::GetMarginDatesFromBillID( $DateBill );
$Start = PDate::Get( C::_( 'START', $Dates ) );
$End = PDate::Get( C::_( 'END', $Dates ) );
$WStart = $this->model->GetStartEnd( $Start->toFormat( '%Y' ), $Start->toFormat( '%W' ) );
$WEnd = $this->model->GetStartEnd( $End->toFormat( '%Y' ), $End->toFormat( '%W' ) );
$StartDate = C::_( 'START', $WStart );
$EndDate = C::_( 'END', $WEnd );
$Holidays = $this->model->getWorkerHolidays( PDate::Get( $StartDate ), PDate::Get( $EndDate ) );
$Days = Helper::GetDays( $StartDate, $EndDate );
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
    <div>
        <form action="" method="get" name="fform" id="fform">
            <?php
                echo HTML::renderFilters('', dirname(__FILE__) . DS . 'default.xml', $config);
            ?>
            <input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
            <input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
            <input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
            <input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
            <input type="hidden" value="" name="task" />
        </form>
    </div>
	<form action="" method="get" name="fform" id="fform">
		<div class="calendar-header">
			<div class="row">
				<div class="col-xs-6">
					<h1 class="title text-left">
						<?php echo $Date->toFormat( '%B %Y' ); ?>
					</h1>
				</div>
				<div class="col-xs-6 text-right">
					<h1 class="title text-right">
						<a href="?option=<?php echo $this->_option; ?>&date=<?php echo PDate::Get( $Start->toFormat() . ' - 10 day' )->toFormat( '%Y-%m' ); ?>" class=" btn btn-default btn-sm">
							<i class="bi bi-chevron-left"></i>
						</a>
						<a href="?option=<?php echo $this->_option; ?>&date=<?php echo PDate::Get( $End->toFormat() . ' + 10 day' )->toFormat( '%Y-%m' ); ?>"  class="btn btn-default btn-sm">
							<i class=" 	bi bi-chevron-right"></i>
						</a>
					</h1>
				</div>
			</div>
		</div>
		<div class="calendar" data-toggle="calendar">
			<div class="row">
				<?php
				foreach ( array_slice( $Days, 0, 7 ) as $Day )
				{
					$Day = PDate::Get( $Day );
					?>
					<div class="col-xs-12 calendar-day-header">
						<day><?php echo $Day->toFormat( '%A' ); ?></day>
					</div>
					<?php
				}
				?>
			</div>
			<div class="row">
				<?php
				$K = 1;
				foreach ( $Days as $Day )
				{
					$Day = PDate::Get( $Day );
					$DBill = $Day->toFormat( '%y%m' );
					$UDay = $Day->toFormat( '%Y-%m-%d' );
					$Add = '';
					if ( $DateBill != $DBill )
					{
						$Add = 'calendar-no-current-month';
					}
					?>
					<div class="col-xs-12 calendar-day <?php echo $Add; ?>">
						<?php
						$Events = C::_( $UDay, $Holidays );
						if ( $Events )
						{
							?>
							<div class="events">
								<?php
								foreach ( $Events as $Event )
								{
									?>
									<div class="event">
										<?php
										ob_start();
										?>
										<div class="event-label">
											<div class="key-value">
												<?php echo XTranslate::_( C::_( 'WORKER', $Event ) ) . ' - ' . XTranslate::_(C::_( 'ORG_NAME', $Event )) ?>
											</div>
											<div class="key-subvalue">
												<?php echo XTranslate::_( C::_( 'LIB_TITLE', $Event ) ); ?>
											</div>
										</div>
										<?php
										$Ttext = ob_get_clean();
										ob_start();
										?>
										<div class="desc">
											<div >
												<?php echo C::_( 'WORKER', $Event ) . ' - ' . XTranslate::_(C::_( 'ORG_NAME', $Event )); ?>
											</div>
											<div >
												<?php echo C::_( 'LIB_TITLE', $Event ); ?>
											</div>
											<div >
												<?php echo Text::_( 'Start_date' ); ?>: <?php echo Pdate::Get( C::_( 'START_DATE', $Event ) )->toFormat( '%d-%m-%Y' ); ?>
											</div>
											<div >
												<?php echo Text::_( 'End_date' ); ?>: <?php echo Pdate::Get( C::_( 'END_DATE', $Event ) )->toFormat( '%d-%m-%Y' ); ?>
											</div>
										</div>
										<?php
										$text = ob_get_clean();
										echo Helper::MakeDoubleToolTip( $Ttext, $text )
										?>
									</div>
									<?php
								}
								?>
							</div>
							<?php
						}
						?>
						<time datetime="<?php echo $UDay; ?>"><?php echo $Day->toFormat( '%d' ); ?></time>
					</div>
					<?php
					$K++;
					if ( $K > 7 )
					{
						?>
					</div>
					<div class="row">
						<?php
						$K = 1;
					}
				}
				?>
			</div>
		</div>
		<input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
		<input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
		<input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
		<input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
		<input type="hidden" value="" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();
