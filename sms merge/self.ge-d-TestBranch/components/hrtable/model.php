<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';
include_once PATH_BASE . DS . 'libraries' . DS . 'Table.php';
include_once PATH_BASE . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';

class hrtableModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new TableHRS_Table( );
		parent::__construct( $params );

	}

	public function getItem( $BillID, $ORG )
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->loads( array(
					'ID' => C::_( '0', $id ),
					'BILL_ID' => $BillID,
//					'ORG' => $ORG,
			) );
//			$this->Table->loads( $id[0] );
		}
		$this->Table->BILL_ID = $BillID;
//		$this->Table->WORKER = C::_( '0', $id );		
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$id = C::_( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
//		$User =  Users::getUser( C::_( 'WORKER', $data ) );
//		if ( $id )
//		{
//			Mail::sendLeaveEditMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ), Users::getUser( C::_( 'WORKER', $data ) ) );
//		}
//		else
//		{
//			Mail::sendLeaveMail( C::_( 'START_DATE', $data ), C::_( 'END_DATE', $data ), C::_( 'TYPE', $data ) );
//		}
		return $this->Table->insertid();

	}

	public function Approve()
	{
		$idx = Request::getVar( 'nid', array() );
		if ( is_array( $idx ) )
		{
			$date = new PDate();
			foreach ( $idx as $id )
			{
				$this->Table->load( $id );
				if ( C::_( 'ID', $this->Table ) )
				{
					$Worker = C::_( 'WORKER', $this->Table );
					$StartDate = C::_( 'START_DATE', $this->Table );
					$EndDate = C::_( 'END_DATE', $this->Table );
					$WorkerData = Users::getUser( $Worker );
					$SalaryID = C::_( 'SALARY_EMPLOYEE_ID', $WorkerData );
					$Type = (int) C::_( 'TYPE', $this->Table );
					$Days = Helper::getHolidayDays( $Worker, $StartDate, $EndDate );

					if ( !count( $Days ) )
					{
						return false;
					}
					foreach ( $Days as $Day )
					{
						$Date = C::_( 'REAL_DATE', $Day );
						$status = Helper::RegisterHoliday( $SalaryID, $Type, $Date );
						if ( $status != 0 && $status != -4 )
						{
							return false;
						}
					}
					if ( C::_( 'STATUS', $this->Table, 0 ) != 0 )
					{
						$link = '?option=' . $this->_option;
						XError::setMessage( 'Holiday Deleted!' );
						Users::Redirect( $link );
					}
					$this->Table->STATUS = 1;
					$this->Table->APPROVE = Users::GetUserID();
					$this->Table->APPROVE_DATE = $date->toFormat( '%Y-%m-%d %H:%M:%S' );
					$this->Table->store();
					include_once BASE_PATH . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';
					oneWaySMS::SendLeaveSMSWraper( $this->Table->WORKER, $this->Table->START_DATE, $this->Table->END_DATE );
				}
				else
				{
					return false;
				}
			}
			return true;
		}

	}

	public function Generate( $data )
	{
		$BILLID = (int) C::_( 'BILL_ID', $data, 0 );
		$ORG = (int) C::_( 'ORG', $data, 0 );
		if ( $BILLID < 1 )
		{
			return false;
		}
		if ( $ORG < 1 )
		{
			return false;
		}
		$XTable = new XHRSTable();
		return $XTable->Generate( $data );

	}

	public function GetAPPS( $UserID, $StartDate, $EndDate )
	{
		$Query = ' select '
						. ' to_char(t.start_date, \'yyyy-mm-dd\')  start_date, '
						. ' to_char(t.end_date, \'yyyy-mm-dd\')  end_date, '
						. ' a.lib_title title, '
						. ' a.type '
						. ' from HRS_APPLICATIONS t '
						. ' left join v_lib_applications_types a on a.type = t.type '
						. ' where '
						. ' t.worker = (SELECT ORGPID FROM SLF_WORKER WHERE ID = ' . DB::Quote( $UserID ) . ')'
						. ' and t.status > 0 '
						. ' and ( '
						. ' t.start_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') '
						. ' or t.end_date between to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') and to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') '
						. ' or to_date(' . DB::Quote( $StartDate ) . ', \'yyyy-mm-dd\') between t.start_date and t.end_date '
						. ' or to_date(' . DB::Quote( $EndDate ) . ', \'yyyy-mm-dd\') between t.start_date and t.end_date '
						. ' ) '
						. ' and t.type in (' . HolidayLimitsTable::GetHolidayIDx() . ', 3, 4, 5, 17) '
		;

		$Dates = DB::LoadObjectList( $Query );
		$Result = array();
		foreach ( $Dates as $Date )
		{
			$Day_dates = $this->GetDays( C::_( 'START_DATE', $Date ), C::_( 'END_DATE', $Date ), $Date );
			$Result = array_merge( $Result, $Day_dates );
		}
		return $Result;

	}

	public function GetDays( $Start, $End, $Value )
	{
		$StartDate = new PDate( $Start );
		$ENDTMP = new PDate( $End );
		$EndDate = new PDate( $ENDTMP->toformat( '%Y-%m-%d 23:59:59' ) );
		$Days = array();
		while ( $StartDate->toUnix() < $EndDate->toUnix() )
		{
			$Days[$StartDate->toformat( '%Y-%m-%d' )] = $Value;
			$StartDate = new PDate( $StartDate->toUnix() + 86400 );
		}
		return $Days;

	}

	public function multiSend( $data, $Tabels = array() )
	{
		$bill_id = C::_( 'BILL_ID', $data, 0 );
		$query = ' select '
						. ' distinct t.org '
						. ' from hrs_table t '
						. ' where '
						. ' t.bill_id = ' . (int) $bill_id
		;
		$orgs = DB::LoadList( $query );

		if ( empty( $orgs ) )
		{
			return false;
		}

		$O = 0;
		foreach ( $orgs as $org )
		{
			$data['ORG'] = $org;
			if ( $this->Send( $data, $Tabels ) )
			{
				$O++;
			}
		}

		return $O > 0 ? true : false;

	}

	public function Send( $data, $Tabels = array() )
	{
		$BillID = C::_( 'BILL_ID', $data );
		$ORG = C::_( 'ORG', $data );
		$Users = $this->GetUsers( $Tabels, $ORG );
		if ( !$this->CheckBillID( $BillID, $Users, $ORG ) )
		{
			return false;
		}
		$this->UpdateBill( $BillID, $Users, $ORG );
		$Add = '';
		if ( count( $Users ) )
		{
			$Add = ' and d.id in (' . implode( ',', $Users ) . ' )';
		}
		$Query = 'select '
						. ' t.id,'
						. ' t.firstname, '
						. ' t.lastname, '
						. ' t.email, '
						. ' t.mobile_phone_number '
						. ' from hrs_workers_sch t '
						. ' left join rel_worker_chief wc on wc.chief = t.id '
						. ' where t.active = 1 '
						. ' and wc.worker in (select d.id '
						. ' from slf_worker d '
						. ' where d.active = 1 '
						. ' and d.calculus_type = 1 '
						. $Add
						. ' and d.org = ' . $ORG
						. ' ) '
		;

		$ORgData = XGraph::GetOrgData( $ORG );
		$Company = C::_( 'LIB_TITLE', $ORgData );
		$items = DB::LoadObjectList( $Query, 'ID' );
		if ( !empty( $items ) )
		{
			$EmailMessage = 'მოგესალმებით, ' . PHP_EOL . PHP_EOL
							. 'მოგახსენებთ, რომ საქართველოს ოკუპირებული ტერიტორიებიდან დევნილთა, შრომის, ჯანმრთელობისა და სოციალური დაცვის მინისტრის 2021 წლის 12 თებერვლის N01-15/ნ ბრძანების შესაბამისად, კომპანიის თითოეულ სტრუქტურული ქვედანაყოფის ხელმძღვანელს (უშუალო ხელმძღვანელი) ეკისრება პასუხისმგებლობა მის დაქვემდებარებაში მყოფი პირების (დასაქმებულების) სამუშაო დროის აღრიცხვის ფორმის წარმოებაზე/შევსებაზე.' . PHP_EOL . PHP_EOL
							. ' მოგეხსენებათ, ' . $Company . ' მინისტრის მიერ დადგენილი ფორმის მიხედვით ელექტრონულად ახორციელებს თანამშრომელთა ნამუშევარი საათების აღრიცხვას, შესაბამისად თანამშრომელთა სამუშაო დროის აღრიცხვის რეპორტის დადასტურება სისტემაში ხორციელდება ელექტრონულად.' . PHP_EOL . PHP_EOL
							. ' ხელმძღვანელი ვალდებულია მის დაქვემდებარებაში მყოფი თანამშრომლების სამუშაო დროის აღრიცხვის ფორმა დაადასტუროს არაუგვიანეს მიმდინარე თვის 10 რიცხვამდე.' . PHP_EOL . PHP_EOL
							. ' თუ პასუხისმგებელი პირის მიერ აღნიშნულ ვადაში არ იქნება დადასტურებული მის დაქვემდებარებაში მყოფი პირების ფორმები, ან არ იქნება წარმოდგენილი რაიმე სახის ცვლილებები, კალენდარული თვის 10 რიცხვის შემდეგ ასეთი მონაცემები ჩაითვლება პასუხისმგებელ პირთა მიერ გაცნობილად და დადასტურებულად და გაიგზავნება თანამშრომლებთან.' . PHP_EOL . PHP_EOL
							. 'გთხოვთ, თქვენ დაქვემდებაში მყოფი თანამშრომლების ნამუშევარი დროის რეპორტის გასაცნობად/დასადასტურებლად ეწვიოთ შემდეგ ბმულს: ' . Uri::root() . 'confirm' . PHP_EOL . PHP_EOL
			;
			$sms = new oneWaySMS();
			foreach ( $items as $Worker )
			{
				$ID = C::_( 'ID', $Worker );
				$Firstname = C::_( 'FIRSTNAME', $Worker );
				$Lastname = C::_( 'LASTNAME', $Worker );
				$Email = C::_( 'EMAIL', $Worker );
//				$Email = 'teimuraz@kevlishvili.ge'; //C::_( 'EMAIL', $Worker );

				if ( $Email )
				{
					$Subject = 'ხელმძღვანელთა საყურადღებოდ!';
					$Result = Cmail( $Email, $Subject, nl2br( $EmailMessage ) );
					if ( $Result )
					{
						file_put_contents( PATH_BASE . DS . 'logs' . DS . 'c-alert-send.log', $ID . ' - ' . $Firstname . ' - ' . $Lastname . ' - ' . $Email . ' - R:' . $Result . PHP_EOL, FILE_APPEND );
					}
					else
					{
						file_put_contents( PATH_BASE . DS . 'logs' . DS . 'c-alert-failed.log', $ID . ' - ' . $Firstname . ' - ' . $Lastname . ' - ' . $Email . ' - R:' . $Result . PHP_EOL, FILE_APPEND );
					}
				}
				$SMSMessage = 'მოგესალმებით! თქვენი თანამშრომლების ნამუშევარი დროის რეპორტი დაგენერირდა. გთხოვთ, დაადასტუროთ არაუგვიანეს მიმდინარე თვის 10 რიცხვისა, თუ თქვენ მიერ აღნიშნულ ვადაში არ იქნება დადასტურებული, მონაცემები ჩაითვლება თქვენ მიერ დადასტურებულად და გაიგზავნება თანამშრომლებთან. დეტალური ინფორმაციისთვის გთხოვთ გაეცნოთ ელ.ფოსტას.';
				$Mobile = C::_( 'MOBILE_PHONE_NUMBER', $Worker );
//				$Mobile = '599520022';
				if ( $Mobile )
				{
					$Result = $sms->Send( $Mobile, $sms->TranslitToLat( $SMSMessage ) );
					if ( $Result )
					{
						file_put_contents( PATH_BASE . DS . 'logs' . DS . 'SMS-alert-send.log', $ID . ' - ' . $Firstname . ' - ' . $Lastname . ' - ' . $Email . ' - R:' . $Result . PHP_EOL, FILE_APPEND );
					}
					else
					{
						file_put_contents( PATH_BASE . DS . 'logs' . DS . 'SMS-alert-failed.log', $ID . ' - ' . $Firstname . ' - ' . $Lastname . ' - ' . $Email . ' - R:' . $Result . PHP_EOL, FILE_APPEND );
					}
				}
			}
			return true;
		}

	}

	public function CheckBillID( $BillID, $Users, $ORG )
	{
		$Add = '';
		if ( count( $Users ) )
		{
			$Add = ' and t.worker in (' . implode( ',', $Users ) . ' )';
		}
		$Query = 'select count(1) from hrs_table t where t.status = 0 and t.bill_id =   ' . (int) $BillID . $Add . ' and t.org = ' . (int) $ORG;
		return DB::LoadResult( $Query );

	}

	public function UpdateBill( $BillID, $Users, $ORG )
	{
		$Add = '';
		if ( count( $Users ) )
		{
			$Add = ' and t.worker in (' . implode( ',', $Users ) . ' )';
		}
		$Query = 'update hrs_table t set t.status = 1 where t.status = 0 and t.bill_id =   ' . (int) $BillID . $Add . ' and t.org = ' . (int) $ORG;
		return DB::Update( $Query );

	}

	public function GetUsers( $Tabels, $ORG )
	{
		$Query = 'select t.worker from hrs_table t where t.id in (   ' . implode( ',', $Tabels ) . ') and t.org = ' . $ORG;
		return DB::LoadList( $Query );

	}

}
