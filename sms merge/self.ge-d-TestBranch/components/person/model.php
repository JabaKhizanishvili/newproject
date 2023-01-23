<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class PersonModel extends Model
{
	protected $Table = null;
	protected $AttributesTable = null;

	public function __construct( $params )
	{
		$this->Table = new PersonTable( );
		$this->AttributesTable = new TableRel_attributesInterface( 'rel_attributes', 'ID', 'sqs_rel_attributes.nextval' );

		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$ID = C::_( 'ID', $data, 0 );
		if ( $ID )
		{
			if ( C::_( 'ACTIVE', $data ) == 0 )
			{
				$exist = Xhelp::CheckWorkersInOrg( $ID, 'PERSON' );
				if ( count( $exist ) )
				{
					XError::setError( 'persons detected in this org!' );
					return false;
				}
			}
		}
		if ( !Xhelp::checkDate( C::_( 'BIRTHDATE', $data ) ) )
		{
			return false;
		}
		$imageSource = C::_( 'PHOTO', $data );
		$Photo = Helper::Base64ToImage( $imageSource, md5( microtime() ) . '-' . time() );
		$Date = new PDate();
		$data['PHOTO'] = $Photo;
		$data['MOBILE_PHONE_NUMBER'] = str_replace( '-', '', C::_( 'MOBILE_PHONE_NUMBER', $data ) );
		$data['CHANGE_DATE'] = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
		$data['ATTRIBUTES'] = array_filter( $data['ATTRIBUTES'], function ( $i )
		{
			return !empty( $i );
		} );

		$data['ATTRIBUTES'] = implode( ',', $data['ATTRIBUTES'] );
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
		$RelID = $this->Table->insertid();
		$this->SavePermitRel( explode( '|', $this->Table->PERMIT_ID ), $RelID );
		$this->savePersonAttributesRel( $RelID, explode( ',', $data['ATTRIBUTES'] ) );

//		Save relations
//		$this->SaveAccountingOfficesRel( $ACCOUNTING_OFFICE, $RelID );
		return $RelID;

	}

	public function savePersonAttributesRel( $itemId, $attributeIds )
	{
		$query = "DELETE FROM rel_attributes WHERE ITEM_TYPE = 1 AND ITEM_ID = " . $itemId;

		DB::Query( $query );

		if ( !empty( $attributeIds ) )
		{
			foreach ( $attributeIds as $attributeId )
			{

				$data = [
						'ATTRIBUTE_ID' => $attributeId,
						'ITEM_TYPE' => 1,
						'ITEM_ID' => $itemId
				];

				$this->AttributesTable->bind( $data );
				$this->AttributesTable->store();

				$this->AttributesTable->resetAll();
			}
		}

	}

	public function PasswordReset( $data )
	{
		$Alerts = XAlerts::GetInstance();
		$AllResult = true;
		$Table = clone $this->Table;
		foreach ( $data as $ID )
		{
			$Password = mb_strtolower( Helper::GenerateTocken( 8 ) );
			$Table->reset();
			$Table->load( $ID );
			$Table->U_PASSWORD = md5( $Password );
			$Table->store();
			$Table->U_PASSWORD = $Password;
			$Result = $Alerts->SendAlert( 'password', $Table->getProperties(), $ID );
			if ( !$Result )
			{
				XError::setError( Text::_( 'Auth Data Not Sent To' ) . ' ' . $Table->FIRSTNAME . ' ' . $Table->LASTNAME );
			}
			$AllResult = $Result && $AllResult;
		}
		return $AllResult;

	}

	public function UnsetUser( $data )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$this->Table->resetAll();
				$this->Table->load( $id );
				$this->Table->ACTIVE = 0;
				$this->Table->store();
			}
		}
		return true;

	}

	public function getAccountingOffices()
	{
		$ID = $this->Table->ID;
		if ( empty( $ID ) )
		{
			return '';
		}
		$query = 'select office from rel_accounting_offices where worker = ' . DB::Quote( $ID );
		return DB::LoadList( $query );

	}

	public function SaveAccountingOfficesRel( $data, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_accounting_offices cp '
						. ' where '
						. ' cp.worker = ' . (int) $id;

		DB::Delete( $DelQuery );

		if ( !count( $data ) )
		{
			return;
		}
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $data as $DD )
		{
			$query .= ' into rel_accounting_offices '
							. ' (worker, office) '
							. 'values '
							. '('
							. (int) $id
							. ','
							. (int) $DD
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public function SavePermitRel( $dataIN, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_person_permit cp '
						. ' where '
						. ' cp.person = ' . DB::Quote( $id );

		DB::Delete( $DelQuery );
		$data = Helper::CleanArray( $dataIN, 'Str' );
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

}
