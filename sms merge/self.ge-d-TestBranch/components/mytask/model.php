<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class MyTaskModel extends Model
{
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new TaskTable( );
		parent::__construct( $params );

	}

	public function getTask( $WID )
	{

		$Check = $this->CheckUserAccess( $WID );
		if ( $Check < 1 )
		{
			$MSG = Text::_( 'YOU HAVE NOT ACCCESS' );
			Users::Redirect( '?', $MSG );
		}
		$query = 'select '
						. ' t.*, '
						. ' wf.ID, '
						. ' wf.TITLE, '
						. ' wf.DESCRIPTION, '
						. ' wf.FLOW,wf.OWNER, '
						. ' wf.END_DATE, '
						. ' wf.EDIT_DATE, '
						. ' wf.CREATE_DATE, '
						. ' wf.START_DATE, '
						. ' wf.COMPLETE_DATE, '
						. ' wf.STATE wf_state,'
						. ' wf.STATUS,'
						//		. ' w.FIRSTNAME || \' \' || w.LASTNAME task_creatorname, '
						. ' wc.FIRSTNAME || \' \' || wc.LASTNAME creatorname, '
						. ' to_char(END_DATE, \'dd-mm-yyyy\') END_DATE, '
						. ' to_char(CREATE_DATE, \'dd-mm-yyyy hh24:mi:ss\') CREATE_DATE, '
						. ' to_char(TASK_CREATE_DATE, \'dd-mm-yyyy hh24:mi:ss\') TASK_CREATE_DATE, '
						. ' to_char(TASK_DUE_DATE, \'dd-mm-yyyy hh24:mi:ss\') TASK_DUE_DATE, '
						. ' wf.XMODE '
						. ' from hrs_tasks t '
						. ' left join hrs_workflows wf on wf.id = t.workflow_id '
						. ' left join CWS_WORKERS wc on wc.ID = wf.OWNER '
						. 'where t.task_id =  ' . (int) $WID
						. ' and t.state > -1 '
						. ' and wf.state > -1 '
		;
		$data = DB::LoadObject( $query );
		return $data;

	}

	public function DeclineTask( $TASK_ID )
	{
		if ( !$this->Table->load( $TASK_ID ) )
		{
			return false;
		}
		$FLOW = $this->Table->WORKFLOW_ID;
		if ( !$FLOW )
		{
			return false;
		}
		$CurrentTaskData = TaskHelper::getTask( $TASK_ID );
		$State = C::_( 'STATE', $CurrentTaskData );
		if ( $State == 1 )
		{
			return true;
		}
		$Result = (int) TaskHelper::DeclineTask( $FLOW, $TASK_ID );
		if ( $Result < 0 )
		{
			$Tab = AppHelper::getTable();
			$Tab->load( $this->Table->WORKFLOW_ID );
			$Tab->STATUS = 1;
			$Tab->APPROVE = -1;
			$Tab->APPROVE_DATE = PDate::Get()->toFormat();
			$Tab->store();
		}
		TaskHelper::log( $FLOW, $TASK_ID, 'complete', Collection::get( 'TASK_TITLE', $CurrentTaskData ) );
		$Date = new PDate();
		$this->Table->STATE = 1;
		$this->Table->TASK_COMPLETE_RESULT = Text::_( 'Approve' );
		$this->Table->TASK_COMPLETE_DATE = $Date->toFormat();
		$this->Table->TASK_ACTOR = Users::GetUserID();
		$result = $this->Table->store();
		return $result;

	}

	public function ProcessNextTask( $TASK_ID )
	{
		if ( !$this->Table->load( $TASK_ID ) )
		{
			return false;
		}
		$FLOW = $this->Table->WORKFLOW_ID;

		if ( !$FLOW )
		{
			return false;
		}
		$CurrentTaskData = TaskHelper::getTask( $TASK_ID );
		$State = C::_( 'STATE', $CurrentTaskData );
		if ( $State == 1 )
		{
			return true;
		}
		$Result = (int) TaskHelper::ProcessNextTask( $FLOW, $TASK_ID );
		if ( $Result < 0 )
		{
			$Tab = AppHelper::getTable();
			$Tab->load( $this->Table->WORKFLOW_ID );
			$Tab->STATUS = 1;
			$Tab->APPROVE = -1;
			$Tab->APPROVE_DATE = PDate::Get()->toFormat();
			$Tab->store();
			$date = new PDate();
			$StartDate = C::_( 'START_DATE', $Tab );
			if ( $date->toUnix() > PDate::Get( $StartDate )->toUnix() )
			{
				$Worker = C::_( 'WORKER', $Tab );
				$EndDate = C::_( 'END_DATE', $Tab );
				$WIDx = XGraph::GetOrgUserIDByOrgID( $Worker );
				foreach ( $WIDx as $WID )
				{
					$Params = array(
							':p_date_start' => $StartDate,
							':p_date_end' => PDate::Get( PDate::Get( $EndDate )->toUnix() + 10 )->toFormat(),
							':p_worker' => $WID
					);
					DB::callProcedure( 'ReCalc', $Params );
				}
			}
		}
		TaskHelper::log( $FLOW, $TASK_ID, 'complete', Collection::get( 'TASK_TITLE', $CurrentTaskData ) );
		$Date = new PDate();
		$this->Table->STATE = 1;
		$this->Table->TASK_COMPLETE_RESULT = Text::_( 'Approve' );
		$this->Table->TASK_COMPLETE_DATE = $Date->toFormat();
		$this->Table->TASK_ACTOR = Users::GetUserID();
		$result = $this->Table->store();
		return $result;

	}

	public function TakeTask( $ID )
	{
		$this->Table->load( $ID );
		$this->Table->TASK_ACTOR = Users::GetUserID();
		return $this->Table->store();

	}

	public function Approve()
	{
		$idx = Request::getVar( 'nid', array() );
		if ( is_array( $idx ) )
		{
			foreach ( $idx as $id )
			{
				$this->ProcessNextTask( $id );
			}
			return true;
		}

	}

	public function Decline()
	{
		$idx = Request::getVar( 'nid', array() );
		if ( is_array( $idx ) )
		{
			foreach ( $idx as $id )
			{
				$this->DeclineTask( $id );
			}
			return true;
		}

	}

	public function CheckUserAccess( $WID )
	{
		$UserID = Users::GetUserID();
		$Query = ' select count(1) '
						. ' from hrs_tasks t '
						. ' WHERE '
						. ' (t.task_actor =  ' . $UserID
						. ' or t.task_actor_group in (select t.group_id from rel_workers_groups t where worker = ' . $UserID . ' )) '
						. ' and t.TASK_ID =  ' . $WID
		;
		$Result = DB::LoadResult( $Query );
		return $Result;

	}

	public function CheckData()
	{
		$FLOW = Request::getVar( 'FLOW', null );
		$TASK_ID = Request::getInt( 'TASK_ID', 0 );
		if ( empty( $TASK_ID ) )
		{
			return false;
		}
		$CurrentTaskData = TaskHelper::getTask( $TASK_ID );
		$LIB_TASK_ID = (int) C::_( 'LIB_TASK_ID', $CurrentTaskData );
		if ( empty( $LIB_TASK_ID ) )
		{
			return false;
		}
		$LibTaskData = TaskHelper::getLibTask( $LIB_TASK_ID );
		if ( C::_( 'MUSTCOMMENT', $LibTaskData ) )
		{
			$Comment = Collection::get( 'comment', Request::getVar( 'params', array() ), '' );
			if ( empty( $Comment ) )
			{
				XError::setError( 'Please, fill Comment Field!' );
				return false;
			}
		}
		$attributes = Request::getVar( 'attributes', array() );
		if ( !TaskHelper::ValidateNewAttributes( $attributes, $LIB_TASK_ID, $FLOW ) )
		{
			return false;
		}
		return true;

	}

	public function DeclineApp( $id )
	{
		
	}

}
