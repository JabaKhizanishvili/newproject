<?PHP

class TableHRS_Table extends TableHrs_tableInterface
{
	public $_DATE_FIELDS = array(
			'APPROVE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
			'CHANGE_DATE' => 'yyyy-mm-dd HH24:mi:ss',
	);

	public function __construct()
	{
		parent::__construct( 'hrs_table', 'ID', 'library.nextval' );

	}

	public function check()
	{
		$this->BILL_ID = (int) trim( $this->BILL_ID );
		$this->WORKER = trim( $this->WORKER );
		if ( empty( $this->BILL_ID ) )
		{
			return false;
		}
		if ( empty( $this->WORKER ) )
		{
			return false;
		}
		$Sum1 = 0;
		$Sum2 = 0;
		$Holidays = 0;
		$WorkDays = 0;
		$Table = New XHRSTable();
		$Dates = $Table->GetMarginDatesFromBillID( $this->BILL_ID );
		$Days = $Table->LoadDays( C::_( 'START', $Dates ), C::_( 'END', $Dates ) );
		$Graph = $Table->LoadGraph( $this->WORKER, C::_( 'START', $Dates ), C::_( 'END', $Dates ) );
		foreach ( $Days as $Day )
		{
			$K = PDate::Get( $Day )->toFormat( '%d' );
			$TimeID = C::_( $Day, $Graph, 0 );
			$Key = str_pad( $K, 2, '0', STR_PAD_LEFT );
			$DayH = (float) $this->{'DAY' . $Key};
			if ( $DayH == 0 )
			{
				$Holidays++;
			}
			else if ( $DayH < 0 && $TimeID == 0 && $DayH > -79 )
			{
				$this->{'DAY' . $Key} = 0;
			}
			else if ( $DayH < 0 )
			{
				continue;
			}
			else
			{
				if ( $K <= 15 )
				{
					$Sum1 += $DayH;
				}
				else
				{
					$Sum2 += $DayH;
				}
				$WorkDays++;
			}
		}
		$this->DAYSUM01 = $Sum1;
		$this->DAYSUM02 = $Sum2;
		$this->SUMHOUR = $Sum1 + $Sum2;
		$this->DAYSUM = $WorkDays;
		$this->HOLIDAYS = $Holidays;
		return true;

	}

}
