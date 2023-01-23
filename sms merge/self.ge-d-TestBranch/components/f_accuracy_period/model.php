<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class f_accuracy_periodModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new f_accuracy_periodTable( );
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
			if ( isset( $data['PERIOD_TYPE'] ) )
			{
				$data['PERIOD_TYPE'] = (int) C::_( 'PERIOD_TYPE', $e );
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
						. ' ac.id '
						. ' from slf_worker w '
						. ' left join lib_f_salary_types st on st.id = w.salarytype '
						. ' left join lib_f_accuracy_periods ac on ac.id = st.accuracy_period '
						. ' where '
						. ' ac.id in (' . $ids . ')'
						. ' and w.active > 0 '
						. ' and st.active > 0 '
						. ' group by ac.id '
		;
		return (array) DB::LoadList( $query );

	}

}
