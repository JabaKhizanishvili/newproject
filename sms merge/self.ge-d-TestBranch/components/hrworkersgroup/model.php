<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class hrworkersgroupModel extends Model
{
	protected $Table = null;
	private $ID;

	function getID()
	{
		return $this->ID;

	}

	function setID( $ID )
	{
		$this->ID = $ID;

	}

	public function __construct( $params )
	{
		$this->Table = new hrworkersgroupTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );

			$this->Table->WORKERS = implode( ',', $this->getGroupWorkers());
		}
		if ( empty( $this->Table->ORG ) )
		{
			$this->Table->ORG = (int) trim( Request::getState( 'hrworkersgroups.display', 'ORG', '' ) );
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$data['WORKERS'] = $this->CheckWorkers( $data['WORKERS'], $data );
		if ( empty( $data['WORKERS'] ) )
		{
			$data['WORKERS'] = 0;
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
		$id = $this->Table->insertid();
		if ( !$id )
		{
			return false;
		}
		$this->setID( $id );
		$RELResult = $this->SaveRel( $data, $id, $this->Table->ORG );
		if ( !$RELResult )
		{
			return false;
		}
		return $id;

	}

	public function CheckWorkers( $Workers, $data )
	{
		$DataList = $this->cleanData( explode( ',', preg_replace( '/[^0-9,]/', '', $Workers ) ) );
		if ( empty( $DataList ) )
		{
			return null;
		}
		$Data = array_flip( array_flip( $DataList ) );
		$ID = (int) C::_( 'ID', $data );
		$where = '';
		if ( $ID )
		{
			$where = ' and group_id != ' . $ID;
		}
		$query = 'select * from rel_workers_groups '
						. 'where worker in(' . implode( ',', $Data ) . ') ' . $where;
		$result = DB::LoadResult( $query );
		if ( $result )
		{
			return '0';
		}
		return $Data;

	}

	protected function cleanData( $DataList )
	{
		$Return = array();
		foreach ( $DataList as $d )
		{
			$d = trim( $d );
			if ( !empty( $d ) )
			{
				$Return[] = $d;
			}
		}
		return $Return;

	}

	public function SaveRel( $data, $id, $ORG )
	{
		$DelQuery = 'delete '
						. ' from rel_workers_groups wg '
						. ' where '
						. ' wg.group_id = ' . $id;
		DB::Delete( $DelQuery );
		$Workers = C::_( 'WORKERS', $data, 0 );
		if ( empty( $Workers ) )
		{
			return 1;
		}

		$query = 'Begin '
						. ' INSERT ALL ';
		$Orders = C::_( 'worker_order', $data );
		foreach ( $Workers as $Wk )
		{
			$Wk = (int) str_replace( ',', '', $Wk );
			$query .= ' into ' . DB_SCHEMA . '.rel_workers_groups '
							. ' (group_id, worker, ordering, org) '
							. 'values '
							. '('
							. $id
							. ','
							. $Wk
							. ','
							. (int) C::_( $Wk, $Orders, 999 )
							. ','
							. DB::Quote( $ORG )
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$DelQuery = 'delete '
								. ' from rel_workers_groups wg '
								. ' where '
								. ' wg.group_id = ' . $id;
				DB::Delete( $DelQuery );
				if ( 'archive' == mb_strtolower( $mode ) )
				{
					$this->Table->load( $id );
					$this->Table->ACTIVE = -2;
					$this->Table->store();
				}
			}
		}
		return true;

	}

	public function getGroupWorkers()
	{
		$ID = $this->Table->ID;
		$ORG = $this->Table->ORG;
		if(empty($ID))
		{
			return '';
		}
		if(empty($ORG))
		{
			return '';
		}
		$query = 'select worker from rel_workers_groups where group_id = ' . DB::Quote($ID) . ' and org = ' . DB::Quote($ORG);
		return DB::LoadList($query);
	}

}
