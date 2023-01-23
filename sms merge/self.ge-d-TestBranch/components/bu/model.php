<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class buModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new PersonTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = C::_( '0', Request::getVar( 'nid', array() ) );

		if ( !empty( $id ) )
		{
			$this->Table->load( $id );
		}

		return $this->Table;

	}

	public function SaveData( $data )
	{
		$count = 0;
		foreach ( $data['DATA'] as $Item )
		{
			$collect = (array) explode( ', ', C::_( 'PERMIT_ID', $Item, '' ) );

			$this->Table->resetAll();
			$PrivateNumber = C::_( 'PRIVATE_NUMBER', $Item );
			$this->Table->load( $PrivateNumber, 'PRIVATE_NUMBER' );

			if ( empty( $this->Table->ID ) && !$this->check_unique_permit( $collect ) )
			{
				$error = Text::_( 'permit id already exists' ) . '<br><br>';
				$error .= Text::_( 'worker' ) . ': ' . XTranslate::_( C::_( 'FIRSTNAME', $Item ), 'person' ) . ' ' . XTranslate::_( C::_( 'LASTNAME', $Item, 'person' ) . ' - ' . C::_( 'PRIVATE_NUMBER', $Item ) ) . '<br>';
				$error .= Text::_( 'permit_id' ) . ': ' . C::_( 'PERMIT_ID', $Item );
				XError::setError( $error );
				continue;
			}

			$this->Table->bind( $Item );
//			$this->Table->CALCULUS_DATE = PDate::Get()->toFormat( '%Y-%m-%d' );
			$this->Table->CHANGE_DATE = PDate::Get()->toFormat();
			$this->Table->PERMIT_ID = implode( '|', $collect );
			if ( $this->Table->store() )
			{
				$ID = $this->Table->insertid();
				$this->SavePermitRel( $collect, $ID );
				$count++;
			}
		}

		if ( $count > 0 )
		{
			return true;
		}

		return false;

	}

	public function check_unique_permit( &$collect = [] )
	{
		$collect = Helper::CleanArray( $collect );
		$query = 'select '
						. ' count(1) '
						. ' from rel_person_permit t '
						. ' where '
						. ' t.permit_id in (\'' . implode( '\', \'', $collect ) . '\') '
		;
		$result = DB::LoadResult( $query );

		if ( $result > 0 )
		{
			return false;
		}

		return true;

	}

	public function SavePermitRel( $data, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_person_permit cp '
						. ' where '
						. ' cp.person = ' . DB::Quote( $id );

		DB::Delete( $DelQuery );

		if ( !count( $data ) )
		{
			return;
		}
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $data as $DD )
		{
			$query .= ' into rel_person_permit '
							. ' (person, permit_id) '
							. 'values '
							. '('
							. DB::Quote( $id )
							. ','
							. DB::Quote( $DD )
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public function PrepareData( $data )
	{
		$Data = $this->Clean( C::_( 'DATA', $data ) );
		if ( empty( $Data ) )
		{
			return false;
		}
		$DataRows = helper::CleanArray( explode( PHP_EOL, $Data ), 'Str' );
		$Result = array();
		foreach ( $DataRows as $Row )
		{
			$RowData = explode( "\t", $Row );
			$Number = Helper::CleanNumber( C::_( '0', $RowData ) );
			if ( empty( $Number ) )
			{
				continue;
			}
			$Name = helper::CleanArray( explode( ' ', C::_( 1, $RowData ) ), 'Str' );
			$RowN = array();
			$RowN ['FIRSTNAME'] = trim( C::_( '1', $RowData ) );
			$RowN ['LASTNAME'] = trim( C::_( '2', $RowData ) );
			$RowN ['FATHER_NAME'] = trim( C::_( '3', $RowData ) );
			$RowN ['PRIVATE_NUMBER'] = C::_( '4', $RowData );
			$RowN ['BIRTHDATE'] = PDate::Get( strtotime( trim( preg_replace( '/[\.\/]/i', '-', C::_( '5', $RowData ) ) ) ) )->toFormat( '%Y-%m-%d' );
			$RowN ['GENDER'] = $this->GetGender( C::_( '6', $RowData ) );
			$RowN ['NATIONALITY'] = $this->GetCountry( C::_( '7', $RowData ) );
			$RowN ['COUNTRY_CODE'] = $this->otherCountry( C::_( '8', $RowData ) );
			$RowN ['MOBILE_PHONE_NUMBER'] = Helper::CleanNumber( C::_( '9', $RowData ) );
			$RowN ['EMAIL'] = trim( C::_( '10', $RowData ) );
			$RowN ['LEGAL_ADDRESS'] = trim( C::_( '11', $RowData ) );
			$RowN ['ACTUAL_ADDRESS'] = trim( C::_( '12', $RowData ) );
			$RowN ['ACTIVE'] = $this->GetStatus( C::_( '13', $RowData ) );
			$RowN ['PAY_PENSION'] = $this->GetYesNo( C::_( '14', $RowData ) );
			$RowN ['PERMIT_ID'] = trim( C::_( '15', $RowData ) );
			$RowN ['LDAP_USERNAME'] = trim( C::_( '16', $RowData ) );
			$RowN ['IBAN'] = trim( C::_( '17', $RowData ) );
			$RowN ['USER_ROLE'] = $this->GetUserRole( C::_( '18', $RowData ) );
			$RowN ['COUNTING_TYPE'] = $this->getCounting_type( C::_( '19', $RowData ) );
			$RowN ['TIMECONTROL'] = $this->GetYesNo( C::_( '20', $RowData ) );
			$RowN ['LIVELIST'] = $this->GetYesNo( C::_( '21', $RowData ) );
			$RowN ['SMS_REMINDER'] = $this->GetYesNo( C::_( '22', $RowData ) );
			$RowN ['SMS_WORKER_LATENESS'] = $this->GetYesNo( C::_( '23', $RowData ) );
			$Result[] = $RowN;
		}
		return $Result;

	}

	public function GetUserRole( $Role )
	{
		$Query = 'select t.id from LIB_ROLES t where '
						. ' t.lib_title = ' . DB::Quote( trim( $Role ) )
						. ' and t.active >  -1 '
		;
		return DB::LoadResult( $Query );

	}

	public function otherCountry( $Country )
	{
		$Query = 'select t.lib_code from LIB_COUNTRY t where t.lib_title = ' . DB::Quote( trim( $Country ) );
		return DB::LoadResult( $Query );

	}

	public function getCounting_type( $Type )
	{
		$ID = 1;
		switch ( trim( $Type ) )
		{
			case 'ბარათებით':
				$ID = 0;
				break;
			case 'ღილაკებით (IP)':
				$ID = 1;
				break;
			case 'ღილაკებით + GPS':
				$ID = 2;
				break;
			default:
				$ID = 1;
				break;
		}
		return $ID;

	}

	public function GetCalculusType( $Type )
	{
		$ID = 1;
		switch ( $Type )
		{
			case 'ელექტრონული':
				$ID = 1;
				break;
			case 'მატერიალური':
				$ID = 2;
				break;
			case 'გამონაკლისი':
				$ID = 3;
				break;
			default:
				$ID = 1;
				break;
		}
		return $ID;

	}

	public function GetGender( $Gender )
	{
		$ID = 1;
		switch ( $Gender )
		{
			case 'მამრობითი':
				$ID = 1;
				break;
			case 'მდედრობითი':
				$ID = 2;
				break;
			default:
				$ID = 1;
				break;
		}
		return $ID;

	}

	public function GetCountry( $Country )
	{
		if ( $Country == 'საქართველო' )
		{
			$ID = 1;
		}
		elseif ( empty( $Country ) )
		{
			$ID = 1;
		}
		else
		{
			$ID = 0;
		}
		return $ID;

	}

	public function GetYesNo( $P )
	{
		if ( trim( $P ) == 'დიახ' )
		{
			$ID = 1;
		}
		else
		{
			$ID = 0;
		}
		return $ID;

	}

	public function GetWorkType( $P )
	{
		if ( $P == 'ნახევარი' )
		{
			$ID = 2;
		}
		else
		{
			$ID = 1;
		}
		return $ID;

	}

	public function GetStatus( $P )
	{
		switch ( $P )
		{
			case 'აქტიური':
				$ID = 1;
				break;
            default:
				$ID = 0;
				break;
		}
		return $ID;

	}

	public function GetUserType( $P )
	{
		if ( $P == 'ხელმძღვანელი' )
		{
			$ID = 2;
		}
		else
		{
			$ID = 1;
		}
		return $ID;

	}

	public function Clean( $Text )
	{
		$String = trim( $Text );
		return preg_replace( '/[\'\"]/', '', $String );

	}

}
