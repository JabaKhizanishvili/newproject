<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

require_once 'table.php';

class overtimealertModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new OvertimealertTable();
		parent::__construct( $params );

	}

	public function getItem()
	{
		$id = Request::getVar( 'nid', array() );
		if ( isset( $id[0] ) && !empty( $id[0] ) )
		{
			$this->Table->load( $id[0] );
			echo '<pre><pre>';
			print_r( $this->Table );
			echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
			die;
		}
		return $this->Table;

	}

	public function SaveData( $data )
	{
		$id = C::_( 'ID', $data );
		if ( $id )
		{
			$this->Table->load( $id );
		}
		$PDate = trim( C::_( 'START_DATE', $data ) );
		$EDate = trim( C::_( 'END_DATE', $data ) );
		$TYPE = intval( C::_( 'TYPE', $data ) );
		$INFO = trim( C::_( 'INFO', $data ) );
		$DAY_COUNT = Helper::CleanNumber( C::_( 'DAY_COUNT', $data ) );
		if ( empty( $PDate ) )
		{
			XError::setError( 'Date Incorrect!' );
			return false;
		}
		if ( empty( $EDate ) )
		{
			XError::setError( 'Date Incorrect!' );
			return false;
		}
		if ( empty( $TYPE ) )
		{
			XError::setError( 'TYPE Incorrect!' );
			return false;
		}
		if ( empty( $INFO ) )
		{
			XError::setError( 'INFO Incorrect!' );
			return false;
		}

		$StartDate = PDate::Get( $PDate )->toFormat( '%Y-%m-%d' );
		$EndDate = PDate::Get( $EDate )->toFormat( '%Y-%m-%d' );
		$data['WORKER'] = Users::GetUserID();
		$data['TYPE'] = (int) $data['TYPE'];
		$data['START_DATE'] = $StartDate;
		$data['END_DATE'] = $EndDate;
		$data['DAY_COUNT'] = $DAY_COUNT;
		$data['INFO'] = $INFO;
		$data['USER_APPROVE'] = Users::GetUserID();
		$data['USER_APPROVE_DATE'] = PDate::Get()->toFormat();
		$data['STATUS'] = 1;
		$data['RESOLUTION'] = 0;
		$data['CHIEF_APPROVE'] = 0;
		$data['CREATE_USER'] = Users::GetUserID();
		$data['CREATE_DATE'] = PDate::Get()->toFormat();
		$data['DEL_USER'] = 0;

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
		$IDx = $this->Table->insertid();
		$Alert = XAlerts::GetInstance();
		$Keys = $this->Table->getProperties();
		$Keys['NAME'] = Users::GetUserFullName();
		$Keys['TYPENAME'] = ($this->Table->TYPE == 1) ? Text::_( 'Salary Alternative' ) : Text::_( 'RestTime Alternative' );
		$Alert->SendAlert( 'userovertimealert', $Keys, Users::GetUserData( 'CHIEFS' ) );
		return $IDx;

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				$Date = new PDate();
				$this->Table->load( $id );
				if ( C::_( 'RESOLUTION', $this->Table, 0 ) != 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'Overtime Restriction!' );
					Users::Redirect( $link );
				}
				$this->Table->STATUS = -2;
				$this->Table->DEL_USER = Users::GetUserID();
				$this->Table->DEL_DATE = $Date->toFormat( '%Y-%m-%d %H:%M:%S' );
				$this->Table->store();
			}
		}
		return true;

	}

}
