<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class App_typeModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new App_typeTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && $id[0] != '' )
		{
			$this->Table->load( $id[0] );
			$this->Table->ORG = $this->getOrgsRel( $this->Table->ID );
		}

		return $this->Table;

	}

	public function SaveData( $data )
	{
		if ( C::_( 'ID', $data ) == '-0' )
		{
			$data['ID'] = '00';
		}

		$id = parent::SaveData( $data );
		if ( !$id )
		{
			return false;
		}
		if ( !$this->SaveRel( C::_( 'ORG', $data ), $id ) )
		{
			return false;
		}
		return $id;

	}

	public function SaveRel( $ORGS, $id )
	{
		$DelQuery = 'delete '
						. ' from  rel_limit_app_types cp '
						. ' where '
						. ' cp.limit_app_type = ' . (int) $id;
		DB::Delete( $DelQuery );
		$query = 'Begin '
						. ' INSERT ALL ';
		foreach ( $ORGS as $DD )
		{
			$query .= ' into rel_limit_app_types '
							. ' (org, limit_app_type) '
							. 'values '
							. '('
							. DB::Quote( (int) $DD )
							. ','
							. DB::Quote( (int) $id )
							. ')';
		}
		$query .= ' SELECT * FROM dual;'
						. 'end;';
		$Result = DB::InsertAll( $query );
		return $Result;

	}

	public function getOrgsRel( $id )
	{
		if ( $id == '' )
		{
			return '';
		}
		$Query = ' select * from rel_limit_app_types re where  re.limit_app_type = ' . DB::Quote( $id );
		$result = DB::LoadList( $Query );
		return implode( '|', $result );

	}

	public function D_Delete( $data )
	{
		if ( empty( $data ) )
		{
			return false;
		}
		$query = 'update lib_limit_app_types t set t.active=-2 where t.id in (' . implode( ',', $data ) . ')';
		return DB::Update( $query );

	}

}
