<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class wgroupModel extends Model
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
		$this->Table = new wgroupTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
		}
//		$this->Table->ROLE_REL = explode( ',', $this->Table->ROLE_REL );		
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$data['WORKERS'] = $this->CheckWorkers( $data['WORKERS'] );
//	
//		$ROLE_REL = $this->CheckRoles( C::_( 'ROLE_REL', $data, array() ) );
//		$Chapters = $data['CHAPTERS'] = Helper::FilterArray( C::_( 'CHAPTERS', $data ) );
//		$Positions = $data['POSITIONS'] = Helper::FilterArray( C::_( 'POSITIONS', $data ) );
//		$data['ROLE_REL'] = implode( ',', $ROLE_REL );
		if ( !($data['WORKERS']) ) // || $data['CHAPTERS'] || $data['POSITIONS']) )
		{
			return false;
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
//		$this->SaveRoleRel( $ROLE_REL, $id );
//		$this->SaveChaptersRel( $data['CHAPTERS'], $id );
//		$this->SavePositionsRel( $data['POSITIONS'], $id );
		$this->CleanRel( $id );
		$RELResult = $this->SaveRel( $data, $id );
//		$this->SaveStructRel( $data, $Chapters, $Positions, $id );
		return $id;

	}

	public function CheckWorkers( $Workers )
	{
		$DataList = $this->cleanData( explode( ',', preg_replace( '/[^0-9,]/', '', $Workers ) ) );
		if ( empty( $DataList ) )
		{
			return false;
		}
		$Data = array_flip( array_flip( $DataList ) );
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

	protected function CleanRel( $ID )
	{
		$DelQuery = 'delete '
						. ' from ' . 'rel_wgroups wg '
						. ' where '
						. ' wg.group_id = ' . $ID;
		return DB::Delete( $DelQuery );

	}

	public function SaveRel( $data, $id, $WorkersIN = null, $Hidden = 0 )
	{
		if ( $WorkersIN )
		{
			$Workers = $WorkersIN;
		}
		else
		{
			$Workers = Collection::getVar( 'WORKERS', $data, 0 );
		}
		$query = 'Begin '
						. ' INSERT ALL ';
		$Orders = Collection::get( 'worker_order', $data );
		if ( count( $Workers ) )
		{
			foreach ( $Workers as $Wk )
			{
				$Wk = (int) str_replace( ',', '', $Wk );
				$query .= ' into ' . 'rel_wgroups '
								. ' (group_id, worker, ordering, w_hidden) '
								. 'values '
								. '('
								. $id
								. ','
								. $Wk
								. ','
								. (int) Collection::get( $Wk, $Orders, 999 )
								. ','
								. (int) $Hidden
								. ')';
			}
			$query .= ' SELECT * FROM dual;'
							. 'end;';
			$Result = DB::InsertAll( $query );
			return $Result;
		}
		return true;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$DelQuery = 'delete '
								. ' from ' . 'rel_wgroups wg '
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

	public function CheckRoles( $Roles )
	{
		$DataList = $this->cleanData( $Roles );
		if ( empty( $DataList ) )
		{
			return array();
		}
		$Data = array_flip( array_flip( $DataList ) );
		return $Data;

	}

	public function SaveRoleRel( $ROLES, $id )
	{
		$DelQuery = 'delete '
						. ' from ' . 'rel_roles_groups wg '
						. ' where '
						. ' wg.group_id = ' . $id;
		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $ROLES as $Wk )
		{
			$query .= ' into rel_roles_groups  '
							. ' (role, group_id) '
							. 'values '
							. '('
							. $Wk
							. ','
							. $id
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public function SaveChaptersRel( $CHAPTERS, $ID )
	{
		$DelQuery = 'delete '
						. ' from  rel_group_chapter tc '
						. ' where '
						. ' tc.id = ' . (int) $ID;
		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		if ( count( $CHAPTERS ) )
		{

			foreach ( $CHAPTERS as $DD )
			{
				$DD = trim( $DD );
				if ( empty( $DD ) )
				{
					continue;
				}
				$query .= ' into rel_group_chapter '
								. ' (id, chapter) '
								. 'values '
								. '('
								. DB::Quote( $ID )
								. ','
								. DB::Quote( $DD )
								. ')';
			}
			$query .= ' SELECT * FROM dual;'
							. 'end;';
			$Result = DB::InsertAll( $query );
			return $Result;
		}
		return true;

	}

	public function SavePositionsRel( $POSITIONS, $ID )
	{
		$DelQuery = 'delete '
						. ' from  rel_group_position tc '
						. ' where '
						. ' tc.id = ' . (int) $ID;
		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		if ( count( $POSITIONS ) )
		{
			foreach ( $POSITIONS as $DD )
			{
				$DD = trim( $DD );
				if ( empty( $DD ) )
				{
					continue;
				}
				$query .= ' into rel_group_position '
								. ' (id, position) '
								. 'values '
								. '('
								. DB::Quote( $ID )
								. ','
								. DB::Quote( $DD )
								. ')';
			}
			$query .= ' SELECT * FROM dual;'
							. 'end;';
			$Result = DB::InsertAll( $query );
			return $Result;
		}
		return true;

	}

	public function SaveStructRel( $Data, $Chapters, $Positions, $id )
	{
		if ( count( $Chapters ) || count( $Positions ) )
		{

			$Where = array();
			$ChapterType = (C::_( 'CHAPTERS_TYPE', $Data ) == 1) ? '' : 'not';
			$PositionType = (C::_( 'POSITIONS_TYPE', $Data ) == 1) ? '' : 'not';
			if ( !empty( $Chapters ) )
			{
				$Where[] = ' w.chapter ' . $ChapterType . ' in ( ' . implode( ', ', $Chapters ) . ' )';
			}
			if ( !empty( $Positions ) )
			{
				$Where[] = ' w.position_id ' . $PositionType . ' in (' . implode( ', ', $Positions ) . ' )';
			}
			$Where[] = ' w.active =1 ';
			$Where[] = ' w.id not in '
							. ' ( '
							. ' select '
							. ' wg.worker '
							. ' from rel_wgroups wg '
							. ' where '
							. ' wg.group_id = ' . DB::Quote( $id )
							. ' and wg.w_hidden = 0 '
							. ' ) ';
			$whereQ = count( $Where ) ? ' WHERE (' . implode( ') AND (', $Where ) . ')' : '';
			$Query = ' select '
							. ' w.id '
							. ' from  cws_workers w '
							. $whereQ
			;
			$Items = DB::LoadList( $Query );

			return $this->SaveRel( $Data, $id, $Items, 1 );
		}

	}

}
