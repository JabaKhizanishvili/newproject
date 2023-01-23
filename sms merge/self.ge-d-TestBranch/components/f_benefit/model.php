<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class f_benefitModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new f_benefitTable( );
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
		$id = (int) C::_( 'ID', $data, 0 );
		if ( $id )
		{
			$e = clone $this->Table;
			$e->load( $id );
			if ( C::_( 'ACTIVE', $data ) == 0 )
			{
				$exist = $this->check_actives( $id );
				if ( count( $exist ) )
				{
					XError::setError( 'workers detected in this id!' );
					return false;
				}
			}
		}

		return parent::SaveData( $data );

	}

	public function check_actives( $ids = null )
	{
		if ( is_array( $ids ) )
		{
			$ids = implode( ', ', $ids );
		}

		$query = 'select '
						. ' b.benefit '
						. ' from lib_benefit_binding r '
						. ' left join lib_f_benefit_types b on b.id = r.benefit '
						. ' left join slf_worker w on w.id = r.worker '
						. ' where '
						. ' b.benefit in (' . $ids . ') '
						. ' and w.active > 0 '
						. ' and b.active > 0 '
						. ' group by b.benefit '
		;
		return (array) DB::LoadList( $query );

	}

}
