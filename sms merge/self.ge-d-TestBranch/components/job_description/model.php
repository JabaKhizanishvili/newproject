<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class Job_descriptionModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new Job_descriptionTable( );
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->V_FUNCTIONS = '';
			$this->Table->V_TASKS = '';
			$this->Table->RESPONSIBILITIES = '';
			$this->Table->REQUIREMENTS = '';
			$this->Table->load( $id[0] );
				for ( $K = 0; $K <= 9; $K++ )
				{
					$this->Table->V_FUNCTIONS .= $this->Table->{'V_FUNCTIONS' . $K};
					$this->Table->V_TASKS .= $this->Table->{'V_TASKS' . $K};
					$this->Table->RESPONSIBILITIES .= $this->Table->{'RESPONSIBILITIES' . $K};
					$this->Table->REQUIREMENTS .= $this->Table->{'REQUIREMENTS' . $K};
				}
			if ( !empty( $this->Table->UNPUBLISH_DATE ) )
			{
				if ( PDate::Get( $this->Table->UNPUBLISH_DATE )->toFormat( '%Y' ) == '2050' )
				{
					$this->Table->UNPUBLISH_DATE = null;
				}
			}
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		if ( !$this->Table->bind($data) )
		{
			return false;
		}
		
		$Functions = $this->Table->SplitText( C::_( 'V_FUNCTIONS', $data ), 'V_FUNCTIONS' );
		$this->Table->bind( $Functions );
		$Functions = $this->Table->SplitText( C::_( 'V_TASKS', $data ), 'V_TASKS' );
		$this->Table->bind( $Functions );
		$Functions = $this->Table->SplitText( C::_( 'RESPONSIBILITIES', $data ), 'RESPONSIBILITIES' );
		$this->Table->bind( $Functions );
		$Functions = $this->Table->SplitText( C::_( 'REQUIREMENTS', $data ), 'REQUIREMENTS' );
		$this->Table->bind( $Functions );
		
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
		if ( empty( $data['V_FILE'] ) )
		{
			$data['V_FILE'] = ' ';
		}
		return $this->Table->insertid();

	}

}
