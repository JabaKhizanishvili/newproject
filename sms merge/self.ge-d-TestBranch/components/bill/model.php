<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class BillModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	public function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->id = trim( Request::getState( $this->_space, 'id', '0' ) );
		$Return->user = trim( Request::getVar( 'user', '0' ) );

		$UserID = Users::GetUserID();
		if ( $Return->user && $this->CheckUser( $Return->user, $UserID ) )
		{
			$UserID = $Return->user;
		}
		$XTable = new XHRSTable();
		$Table = $XTable->getTable();
		$Table->loads( array(
				'WORKER' => $UserID,
				'BILL_ID' => $Return->id
		) );

		$Return->items = $Table;
		$Return->User = XGraph::getWorkerDataSch( $UserID );
		return $Return;

	}

	public function CheckUser( $User, $Chief )
	{
		return 1;
		$Query = 'select '
						. ' t.worker '
						. ' from REL_WORKER_CHIEF t '
						. ' where '
						. ' t.worker = ' . $User
						. ' and t.chief = ' . $Chief;
		return DB::LoadResult( $Query );

	}

}
