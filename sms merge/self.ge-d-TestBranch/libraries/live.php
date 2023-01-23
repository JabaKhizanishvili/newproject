<?php

/**
 * Description of live
 *
 * @author teimuraz.kevlishvili
 */
class LiveHelper
{
//put your code here

	public static function CalculateStatus( $User )
	{
		$StatusID = C::_( 'STATUS_ID', $User );
		switch ( $StatusID )
		{
			case 1:
			case 11:
				return 'st_staff_in';
			case 10:
				return 'cl_orange';
			default:
				return self::CalculateSubStatus( $User );
		}

	}

//	public function CalculateStatus( $User )
//	{
//		$RealTypeID = C::_( 'REAL_TYPE_ID', $User );
//		$StatusID = C::_( 'STATUS_ID', $User );
//		switch ( $RealTypeID )
//		{
//			case 1500:
//				return 'st_day_off';
//			case 2000:
//				if ( $StatusID == 1 )
//				{
//					return 'st_staff_in';
//				}
//				else
//				{
//					return $this->CalculateSubStatus( $User );
//				}
//			case 2500:
//				return 'st_day_off';
//			case 3000:
//				return 'st_day_off';
//			case 3500:
//				return 'st_day_off';
//		}
//
//		return 'st_day_off';
//
//	}

	public static function CalculateSubStatus( $User )
	{
		$Type = C::_( 'TYPE', $User );

		$IDx = array_flip( explode( ',', HolidayLimitsTable::GetHolidayIDx() ) );
		if ( isset( $IDx[$Type] ) )
		{
			return 'cl_blue';
		}

		switch ( $Type )
		{
			case null:
				break;
			case 0:
			case 1:
			case 3:
			case 4:
				return 'cl_blue';
			case 3:
			case 4:
			case 9:
				return 'cl_9400D3';
			case 5:
				return 'cl_grey';
			case 6:
				return 'cl_black';
			case 2:
				return 'cl_yellow';
			case 7:
				return 'cl_coffe';
			case 10:
				return 'st_day_off';
		}
		$RealTypeID = C::_( 'REAL_TYPE_ID', $User );

		$LIVELIST = C::_( 'LIVELIST', $User, 1 );
		if ( $LIVELIST == 0 )
		{
			return 'st_day_none';
		}
		else
		{
			switch ( $RealTypeID )
			{
				case 1500:
					return 'st_day_off';
				case 2000:
					return 'st_not_in';
				case 2500:
					return 'st_day_off';
				case 3000:
					return 'st_not_in';
				case 3500:
					return 'st_day_off';
			}
			return 'st_not_in';
		}

	}

	public static function getToolBar( $params )
	{
		ob_start();
		?>
		<div class="row">
			<div class="toolbar_block col-sm-10">
				<?php
				$Count = 0;
				foreach ( $params as $param )
				{
					?>
					<a href="javascript:void(0);" class="toolbat_item key_<?php echo $param['class']; ?>" rel="<?php echo $param['class']; ?>" >
						<span class="toolbat_item_in">
							<span class="toolbar_item_count">
								<?php echo $param['count']; ?>
							</span>
							<span class="toolbar_item_lab">
								<?php echo $param['name']; ?>
							</span>			
						</span>
					</a>
					<?php
					$Count += $param['count'];
				}
				?>
				<div class="cls"></div>
			</div>
			<div class="toolbar_block_none col-sm-2">
				<a href="javascript:ToolBarFilter('none');void(0);" class="toolbat_item_none key_none" >
					<span class="toolbat_item_in_none">
						<span class="toolbar_item_lab">
							<?php echo Text::_( 'All Items' ); ?>
						</span>			
						<span class="toolbar_item_count">
							( <?php echo $Count; ?> )
						</span>
					</span>
				</a>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public static function GetParams()
	{
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
		$none = 0;
		$params = array(
				'st_not_in' => array(
						'name' => 'გაცდენა',
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
				'cl_yellow' => array(
						'name' => 'პირადი გასვლა',
						'class' => 'cl_yellow',
						'count' => $grey
				),
				'cl_grey' => array(
						'name' => 'ბიულეტენი',
						'class' => 'cl_grey',
						'count' => $grey2
				),
				'cl_coffe' => array(
						'name' => 'ვადიანი გასვლა',
						'class' => 'cl_coffe',
						'count' => $coffe
				),
				'cl_orange' => array(
						'name' => 'სპორტ დარბაზი',
						'class' => 'cl_orange',
						'count' => $lime
				),
				'cl_9400D3' => array(
						'name' => 'საპატიო გასვლა',
						'class' => 'cl_9400D3',
						'count' => $orange
				),
				'st_day_off' => array(
						'name' => 'არ უნდა იყოს სამსახურში',
						'class' => 'st_day_off',
						'count' => $dayoff
				),
				'st_day_none' => array(
						'name' => 'კონტროლის გარეშე',
						'class' => 'st_day_none',
						'count' => $none
				),
		);
		return $params;

	}

	public static function SendLatenesSMS( $Items )
	{
		$Worker = C::_( '0', $Items );
		$Mobile = C::_( 'MOBILE_PHONE_NUMBER', $Worker );
		$Minutes = C::_( 'MINUTES', $Worker );
		$Worker->MINUTES = 0;
		$ID = C::_( 'ID', $Worker );
		$FirstName = C::_( 'FIRSTNAME', $Worker );
		$LastName = C::_( 'LASTNAME', $Worker );

		$M1 = Helper::getConfig( 'hr_lateness_1' );
		$M2 = Helper::getConfig( 'hr_lateness_2' );

		if ( $Minutes > $M2 )
		{
			$Type = 'hr_card_lateness_2';
			if ( self::NotSendStatus( $ID, $Worker, $Type ) )
			{
				self::_SendLatenesSMS( $ID, $FirstName, $LastName, '2', $Mobile, $Items, 1 );
				self::WriteSendStatus( $ID, $Worker, $Type );
			}
		}
		else if ( $Minutes > $M1 )
		{
			$Type = 'hr_card_lateness_1';
			if ( self::NotSendStatus( $ID, $Worker, $Type ) )
			{
				self::_SendLatenesSMS( $ID, $FirstName, $LastName, '1', $Mobile, $Items );
				self::WriteSendStatus( $ID, $Worker, $Type );
			}
		}

	}

	public static function SendNoLogOutNotice( $Worker )
	{
		$Email = trim( C::_( 'EMAIL', $Worker ) );

		$Date = new PDate( C::_( 'EVENT_DATE', $Worker ) );
		$Chiefs = Helper::getChiefsMails( $Worker->ID );
		$Subject = 'Worker LogOut Issue : ' . $Worker->FIRSTNAME . ' ' . $Worker->LASTNAME;
		$message = 'თანამშრომელი ' . $Worker->FIRSTNAME . ' ' . $Worker->LASTNAME . ' არ გასულა სისტემიდან' . "\n\n<br><br>"
						. 'ამიტომ, სისტემამ ავტომატურად განახორციელა თანამშრომლის გასვლის რეგისტრაცია.' . "\n\n<br><br>"
						. 'თარიღი : ' . $Date->toFormat( '%d-%m-%Y' ) . "\n<br>"
		;

		foreach ( $Chiefs as $value )
		{
			$email = trim( $value );
			if ( empty( $email ) )
			{
				continue;
			}
			Cmail( $email, $Subject, $message );
		}
//		Cmail( 'teimuraz@kevlishvili.ge', $Subject, $message );
//	Cmail( 'teimuraz@kevlishvili.ge', $Subject . ' - ' . $Email, $message );
		$WorkerMessage = $Worker->FIRSTNAME . ' ' . $Worker->LASTNAME . "\n\n<br><br>"
						. 'სამუშაოს დასრულებისას, თქვენ არ გასულხართ სისტემიდან' . "\n\n<br><br>"
						. 'ამიტომ, სისტემამ ავტომატურად განახორციელა თქვენი გასვლის რეგისტრაცია.' . "\n\n<br><br>"
						. 'თარიღი : ' . $Date->toFormat( '%d-%m-%Y' ) . "\n<br>"
		;
		if ( !empty( $email ) )
		{
			return Cmail( $Email, $Subject, $WorkerMessage );
		}
		return true;

	}

	public static function NotSendStatus( $ID, $Worker, $Type )
	{
		static $Now = null;
		if ( is_null( $Now ) )
		{
			$Now = new PDate();
		}
		$Folder = PATH_LOGS . DS . 'LatenessSMS' . DS . $Now->toFormat( '%Y%m%d' );

		$FileName = $ID . ' - ' . md5( json_encode( $Worker ) . $Type );
		if ( !Folder::exists( $Folder ) )
		{
			Folder::create( $Folder, 0777 );
		}
		return !File::exists( $Folder . DS . $FileName );

	}

	public static function WriteSendStatus( $ID, $Worker, $Type )
	{
//		if ( $ID == 37949 )
//		{
//			return true;
//		}
		static $Now = null;
		if ( is_null( $Now ) )
		{
			$Now = new PDate();
		}
		$Folder = PATH_LOGS . DS . 'LatenessSMS' . DS . $Now->toFormat( '%Y%m%d' );
		$FileName = $ID . ' - ' . md5( json_encode( $Worker ) . $Type );
		if ( !Folder::exists( $Folder ) )
		{
			Folder::create( $Folder, 0777 );
		}
		return file_put_contents( $Folder . DS . $FileName, print_r( $Worker, 1 ) );

	}

	public static function _SendLatenesSMS( $ID, $FirstName, $LastName, $MSGID, $Mobile, $Items, $ChiefSMS = null )
	{
//		static $Ver = 1;
//		$V = Request::getVar( 'v', 0 );
//		$Mobile = '599520022';
		$SMS = new oneWaySMS( );
		$MSG = ucfirst( $SMS->TranslitToLat( $FirstName ) ) . ' ' . ucfirst( $SMS->TranslitToLat( $LastName ) ) . PHP_EOL;
		$MSG .= Helper::getConfig( 'hr_lateness_sms_' . $MSGID ) . PHP_EOL; //. $Ver . ' - ' . $V;
		$SMS->Send( $Mobile, $MSG );
		if ( $ChiefSMS )
		{
			$Chiefs = array();
			$MSGAlert = sprintf( Helper::getConfig( 'hr_lateness_sms_chief' ), ucfirst( $SMS->TranslitToLat( $FirstName ) ) . ' ' . ucfirst( $SMS->TranslitToLat( $LastName ) ) );
			foreach ( $Items as $Worker )
			{
				$ID = C::_( 'ID', $Worker );
				$ChiefData = Helper::getUserChiefs( $ID );
				foreach ( $ChiefData as $Chief )
				{
					$CheifID = C::_( 'ID', $Chief );
					$Chiefs[$CheifID] = $Chief;
				}
			}			
			foreach ( $Chiefs as $Chief )
			{
				$Send = C::_( 'SMS_WORKER_LATENESS', $Chief );
				if ( !$Send )
				{
					continue;
				}
				$MSG = ucfirst( $SMS->TranslitToLat( C::_( 'FIRSTNAME', $Chief ) ) . ' ' . ucfirst( $SMS->TranslitToLat( C::_( 'LASTNAME', $Chief ) ) ) ) . PHP_EOL;
				$MSG .= $MSGAlert . PHP_EOL; // . $Ver . ' - ' . count( $Chiefs ) . ' - ' . $V;
				$Mobile = C::_( 'MOBILE_PHONE_NUMBER', $Chief );
//				$Mobile = '599520022';
				$SMS->Send( $Mobile, $MSG );
			}
		}

		return true;

	}

	public static function GetDateUnix( $DateStr )
	{
		if ( empty( $DateStr ) )
		{
			return 0;
		}
		$Date = new PDate( $DateStr );
		return $Date->toUnix();

	}

	public static function LogoutUser( $Worker, $DateIn = null )
	{
		$DoorCode = self::GetOutDoorCode();
		$Date = new PDate( $DateIn );
		$UserID = $Worker->ID;
		/* @var $Worker WorkersTable */
		$Query = ' insert '
						. ' into HRS_TRANSPORTED_DATA '
						. ' ( '
						. ' ID, '
						. ' REC_DATE, '
						. ' ACCESS_POINT_CODE, '
						. ' USER_ID, '
						. ' CARD_ID, '
						. ' DOOR_TYPE, '
						. ' CARDNAME '
						. ' ) '
						. ' values '
						. ' ( '
						. DB::Quote( 'AUTO_' . substr( md5( microtime() . 'sdDSADaAscVS DB HGF3WQSA##%#%$^dfc' ), 0, 16 ) ) . ', '
						. 'to_date(' . DB::Quote( $Date->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\'), '
						. DB::Quote( $DoorCode ) . ','
						. DB::Quote( $UserID ) . ','
						. DB::Quote( null ) . ','
						. DB::Quote( 2 ) . ','
						. DB::Quote( 'Auto LogOut' )
						. ' )'
		;
		return DB::Insert( $Query );

	}

	public static function GetOutDoorCode()
	{
		static $DoorCode = null;
		if ( is_null( $DoorCode ) )
		{
			$Query = 'select '
							. ' d.code '
							. ' from lib_doors d '
							. ' where '
							. ' d.type = 2 '
							. ' and d.defdoor = 1';
			$DoorCode = DB::LoadResult( $Query );
		}
		return $DoorCode;

	}

	public static function SetLatenes( $EventID, $Minutes, $C_COMMENT )
	{
		$Query = 'update '
						. ' hrs_staff_events e '
						. ' set '
						. ' e.time_min = ' . DB::Quote( $Minutes ) . ','
						. ' e.c_comment = ' . DB::Quote( $C_COMMENT )
						. ' where id = ' . DB::Quote( $EventID )
		;
		return DB::Update( $Query );

	}

}
