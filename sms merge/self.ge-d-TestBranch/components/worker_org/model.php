<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';
require_once 'orgtable.php';

class Worker_orgModel extends Model
{
	protected $OrgTable = null;
	protected $Table = null;
	protected $ldap = null;

	public function __construct( $params )
	{
		$this->Table = new WorkersTable( );
		$this->OrgTable = new WorkersORgTable( );
		parent::__construct( $params );

	}

	/**
	 * 
	 * @return WorkersTable
	 */
	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
			$this->Table->ACCOUNTING_OFFICE = implode( '|', $this->getAccountingOffices() );
			$this->Table->ACCESSS_TYPE = implode( '|', $this->getAccessTypes() );
		}
		return $this->Table;

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

	public function getAccessTypes()
	{
		$ID = $this->Table->ID;
		if ( empty( $ID ) )
		{
			return '';
		}
		$query = 'select access_id from rel_access_types where worker = ' . DB::Quote( $ID );
		return DB::LoadList( $query );

	}

	/**
	 * 
	 * @return WorkersORgTable
	 */
	public function getOrgData()
	{
		$id = Request::getVar( 'nid', array() );
		$Orgs = Units::getOrgList();
		$ORGData = array();
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			foreach ( $Orgs as $Org )
			{
				$OrgTable = clone $this->OrgTable;
				$OID = C::_( 'ID', $Org );
				$OrgTable->loads( array(
						'PARENT_ID' => $id[0],
						'ORG' => $OID
				) );
				$OrgTable->PARENT_ID = $id[0];
				$OrgTable->ORG = $OID;
				$ORGData[$OID] = $OrgTable;
			}
		}
		return $ORGData;

	}

	public function SaveData( $data )
	{
		$Orgs = Units::getOrgList();
		$OrgData = array();
		foreach ( $Orgs as $Item )
		{
			$Org = C::_( 'ID', $Item );
			$OrgData[$Org] = Request::getVar( 'ORG_' . $Org, array() );
			$OrgData[$Org]['ORG'] = $Org;
		}
		$imageSource = C::_( 'PHOTO', $data );
		$Photo = Helper::Base64ToImage( $imageSource, md5( microtime() ) . '-' . time() );
		$data['PHOTO'] = $Photo;
		$IDx = C::_( 'ID', $data );
		$data['MOBILE_PHONE_NUMBER'] = str_replace( '-', '', C::_( 'MOBILE_PHONE_NUMBER', $data ) );
		$data['WORK_PHONE_NUMBER'] = str_replace( '-', '', C::_( 'WORK_PHONE_NUMBER', $data ) );
		$ACCOUNTING_OFFICE = Helper::CleanArray( C::_( 'ACCOUNTING_OFFICE', $data, array() ), 'Int' );
		$ACCESSS_TYPE = Helper::CleanArray( C::_( 'ACCESSS_TYPE', $data, array() ), 'Int' );
		if ( $IDx )
		{
			$this->Table->load( $IDx );
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
		$ID = $this->Table->insertid();
		foreach ( $OrgData as $Item )
		{
			$T = clone $this->OrgTable;
			$T->loads( array(
					'PARENT_ID' => $ID,
					'ORG' => C::_( 'ORG', $Item )
			) );
			$ORGID = C::_( 'ORG', $Item );
			$CHIEFS = explode( ',', $Item['CHIEFS'] );
			$WORKERS = explode( ',', $Item['WORKERS'] );
			$USER_TYPE = C::_( 'USER_TYPE', $Item );
			$GRAPHTYPE = C::_( 'GRAPHTYPE', $Item );
//			$GRAPHGROUP = C::_( 'GRAPHGROUP', $Item );
			if ( $USER_TYPE <> 2 )
			{
				$Item['WORKERS'] = '';
			}
			if ( $GRAPHTYPE <> 0 )
			{
				$Item['GRAPHGROUP'] = '';
			}

			if ( empty( $Item['FILES'] ) )
			{
				$Item['FILES'] = ' ';
			}

			if ( !$T->bind( $Item ) )
			{
				return false;
			}
			$T->PARENT_ID = $ID;
			if ( !$T->check() )
			{
				return false;
			}
			if ( !$T->store() )
			{
				return false;
			}
			$RelID = $T->insertid();
			if ( $GRAPHTYPE > 0 )
			{
				$this->SaveGroupRel( $GRAPHTYPE, 0, $RelID, $ORGID );
			}
			$this->SaveChiefsRel( $CHIEFS, $RelID, $ORGID );
			$this->SaveWorkersRel( $WORKERS, $RelID, $USER_TYPE, $ORGID );
//			$this->Update();
		}
		$this->SaveAccountingOfficesRel( $ACCOUNTING_OFFICE, $this->Table->ID );
		$this->SaveAccessTypesRel( $ACCESSS_TYPE, $this->Table->ID );
		return true;

	}

	public function SaveAccessTypesRel( $data, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_access_types cp '
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
			$query .= ' into rel_access_types '
							. ' (worker, access_id) '
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

	public function SaveChiefsRel( $data, $id, $ORG )
	{
		$DelQuery = 'delete '
						. ' from  rel_worker_chief cp '
						. ' where '
						. ' cp.worker = ' . (int) $id
						. ' and cp.org= ' . (int) $ORG
		;

		DB::Delete( $DelQuery );
		if ( !count( $data ) )
		{
			return;
		}
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $data as $DD )
		{
			if ( empty( $DD ) )
			{
				continue;
			}
			$query .= ' into rel_worker_chief '
							. ' (worker, chief, org) '
							. 'values '
							. '('
							. (int) $id
							. ','
							. (int) $DD
							. ','
							. (int) $ORG
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public function SaveWorkersRel( $data, $id, $USER_TYPE, $ORGID )
	{
		$DelQuery = 'delete '
						. ' from  rel_worker_chief cp '
						. ' where '
						. ' cp.CHIEF = ' . (int) $id
						. ' and cp.org= ' . (int) $ORGID
		;

		DB::Delete( $DelQuery );
		if ( $USER_TYPE <> 2 )
		{
			return;
		}
		if ( !count( $data ) )
		{
			return;
		}
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $data as $DD )
		{
			if ( empty( $DD ) )
			{
				continue;
			}
			$query .= ' into rel_worker_chief '
							. ' (worker, chief, org) '
							. 'values '
							. '('
							. (int) $DD
							. ','
							. (int) $id
							. ','
							. (int) $ORGID
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';

		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public function SaveGroupRel( $GRAPHTYPE, $GRAPHGROUP, $id, $ORGID )
	{
		$DelQuery = 'delete '
						. ' from  rel_workers_groups wp '
						. ' where '
						. ' wp.worker = ' . $id
						. ' and wp.org = ' . $ORGID
		;
		DB::Delete( $DelQuery );
		if ( $GRAPHGROUP < 1 )
		{
			return;
		}
//		if ( $GRAPHTYPE == 0 )
//		{
//			$query = ' insert into rel_workers_groups '
//							. ' (group_id,worker,ordering, org) '
//							. 'values '
//							. '('
//							. (int) $GRAPHGROUP . ','
//							. (int) $id . ','
//							. ' 9999' . ','
//							. (int) $ORGID
//							. ')';
//			$Result = DB::Insert( $query );
//			DB::callProcedure( 'updategroups' );
//			return $Result;
//		}
		return;

	}

	public function Update()
	{
		DB::callProcedure( 'updateworkers' );
		DB::callProcedure( 'updatechiefs' );

	}

	public function SaveUserRole( $data )
	{
		$Workers = C::_( 'WORKERS', $data, array() );
		$USER_ROLE = trim( C::_( 'USER_ROLE', $data, null ) );
		if ( empty( $USER_ROLE ) )
		{
			return false;
		}
		foreach ( $Workers as $Worker )
		{
			$Worker = (int) $Worker;
			if ( empty( $Worker ) )
			{
				continue;
			}
			$UserTable = clone $this->Table;
			$UserTable->load( $Worker );
			if ( !$UserTable->ID )
			{
				continue;
			}
			$UserTable->USER_ROLE = $USER_ROLE;
			$UserTable->store();
		}
		return true;

	}

	public function SaveUserGraph( $data )
	{
		$Workers = C::_( 'WORKERS', $data, array() );
		$GRAPHTYPE = trim( C::_( 'GRAPHTYPE', $data, null ) );
		$GRAPHGROUP = trim( C::_( 'GRAPHGROUP', $data, null ) );
		if ( $GRAPHTYPE < 0 )
		{
			return false;
		}
		if ( $GRAPHTYPE == 0 && $GRAPHGROUP < 1 )
		{
			return false;
		}
		else if ( $GRAPHTYPE > 0 )
		{
			$GRAPHGROUP = 0;
		}
		foreach ( $Workers as $Worker )
		{
			$Worker = (int) $Worker;
			if ( empty( $Worker ) )
			{
				continue;
			}
			$UserTable = clone $this->Table;
			$UserTable->load( $Worker );
			if ( !$UserTable->ID )
			{
				continue;
			}
			$UserTable->GRAPHTYPE = $GRAPHTYPE;
			$UserTable->GRAPHGROUP = $GRAPHGROUP;
			$UserTable->store();
			$this->SaveGroupRel( $GRAPHTYPE, $GRAPHGROUP, $Worker );
		}
		return true;

	}

	public function UnsetUser( $data )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$this->Table->resetAll();
				$this->Table->load( $id );
				$this->Table->ACTIVE = -2;
				$this->Table->store();
			}
		}
		return true;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{

				if ( 'archive' == mb_strtolower( $mode ) )
				{
					$this->Table->load( $id );
					$this->Table->ACTIVE = -2;
					if ( property_exists( $this->Table, 'DELETE_USER' ) && property_exists( $this->Table, 'DELETE_DATE' ) )
					{
						$Date = new PDate();
						$this->Table->setDATE_FIELDS( 'DELETE_DATE', 'yyyy-mm-dd HH24:mi:ss' );
						$this->Table->DELETE_USER = Users::GetUserID();
						$this->Table->DELETE_DATE = $Date->toFormat();
					}
					$this->Table->store();
				}
				else
				{
					$this->Table->Delete( $id );
				}
			}
		}
		return true;

	}

	public function SaveCategory( $data )
	{
		$Workers = C::_( 'WORKERS', $data, array() );
		$CATEGORY_ID = trim( C::_( 'CATEGORY_ID', $data, null ) );
		if ( empty( $CATEGORY_ID ) )
		{
			return false;
		}
		foreach ( $Workers as $Worker )
		{
			$Worker = (int) $Worker;
			if ( empty( $Worker ) )
			{
				continue;
			}
			$UserTable = clone $this->Table;
			$UserTable->load( $Worker );
			if ( !$UserTable->ID )
			{
				continue;
			}
			$UserTable->CATEGORY_ID = $CATEGORY_ID;
			$UserTable->store();
		}
		return true;

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

}
