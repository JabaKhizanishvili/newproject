<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class TaskHelper
{
	public static function StartWorkFlow( $WorkFlowID )
	{
		if ( self::FlowExists( $WorkFlowID ) )
		{
			return true;
		}
		$LibFlow = self::getWorkFlow( $WorkFlowID );
		$FlowType = C::_( 'TYPE', $LibFlow );
		$TaskData = self::getTasks( $FlowType );
		foreach ( $TaskData as $Task )
		{
			$ActiorGroup = array();
			$ActiorWorkers = array();
			$ID = C::_( 'ID', $Task );
			$TaskGroup = C::_( 'LIB_GROUP', $Task );
			$Actiors = self::GetActors( $LibFlow, $TaskGroup, $Task );
			$WorkersData = array_filter( array_unique( array_merge( C::_( 'workers', $Actiors, array() ), $ActiorWorkers ) ) );
			$GroupsData = array_filter( array_unique( array_merge( C::_( 'group', $Actiors, array() ), $ActiorGroup ) ) );
			$Count = 0;
			$NextTasks = [];
			if ( empty( $WorkersData ) && empty( $GroupsData ) )
			{
				$NextTasks[] = self::CreateNoActorTask( $WorkFlowID, $ID, true );
			}
			else
			{
				$Count++;
				foreach ( $WorkersData as $Worker )
				{
					self::CreateTask( $WorkFlowID, $ID, $Worker, true );
				}
				foreach ( $GroupsData as $Group )
				{
					self::CreateGroupTask( $WorkFlowID, $ID, $Group );
				}
			}
		}
		if ( $Count == 0 )
		{
			foreach ( $NextTasks as $TaskID )
			{
				self::ProcessNextTask( $WorkFlowID, $TaskID );
			}
		}
		return true;

	}

	public static function ProcessNextTask( $WorkFlowID, $TaskID )
	{
		if ( self::GetParalelTasksCount( $WorkFlowID ) > 1 )
		{
			return false;
		}
		$CurrentTask = self::getTask( $TaskID );
		$CurrentLibTask = self::getLibTask( C::_( 'LIB_TASK_ID', $CurrentTask ) );
		$Flow = C::_( 'FLOW', $CurrentLibTask );
		$Level = C::_( 'LIB_LEVEL', $CurrentLibTask );
		$WorkFlow = self::getWorkFlow( $WorkFlowID );
		$TaskData = self::getTasks( $Flow, $Level );
		if ( empty( $TaskData ) )
		{
			return -1;
		}
		$Count = 0;
		$NextTasks = [];
		foreach ( $TaskData as $Task )
		{
			$ActiorGroup = array();
			$ActiorWorkers = array();
			$ID = C::_( 'ID', $Task );
			$TaskGroup = C::_( 'LIB_GROUP', $Task );
			$Actiors = self::GetActors( $WorkFlow, $TaskGroup, $Task );
			$WorkersData = array_filter( array_unique( array_merge( C::_( 'workers', $Actiors, array() ), $ActiorWorkers ) ) );
			$GroupsData = array_filter( array_unique( array_merge( C::_( 'group', $Actiors, array() ), $ActiorGroup ) ) );
			if ( empty( $WorkersData ) && empty( $GroupsData ) )
			{
				$NextTasks[] = self::CreateNoActorTask( $WorkFlowID, $ID, true );
			}
			else
			{
				$Count++;
				foreach ( $WorkersData as $Worker )
				{
					self::CreateTask( $WorkFlowID, $ID, $Worker, true );
				}
				foreach ( $GroupsData as $Group )
				{
					self::CreateGroupTask( $WorkFlowID, $ID, $Group );
				}
			}
		}
		if ( $Count == 0 )
		{
			foreach ( $NextTasks as $TaskID )
			{
				$Result = (int) self::ProcessNextTask( $WorkFlowID, $TaskID );
				if ( $Result < 0 )
				{
					return $Result;
				}
			}
		}
		return true;

	}

	public static function DeclineTask( $WorkFlowID, $TaskID )
	{
		$Tasks = TaskHelper::GetActiveTasks( $WorkFlowID );
		echo '<pre><pre>';
		print_r( $Tasks );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";

		echo '<pre><pre>';
		print_r( $WorkFlowID );
		echo '</pre><b>FILE:</b> ' . __FILE__ . '     <b>Line:</b> ' . __LINE__ . '</pre>' . "\n";
		die;
//		$Q = 
		return true;

	}

	public static function PrepareNextTask( $Action, $WorkFlowID )
	{
		$NextTasks = TaskHelper::getNextTaskByAction( $Action );
		$Result = true;
		$Actiors = Request::getVar( 'actiors', array() );
		foreach ( $NextTasks as $Task )
		{
			$TaskData = self::getLibTask( $Task->ID );
			if ( !self::CheckIf( $TaskData, $WorkFlowID ) )
			{
				continue;
			}
			if ( !TaskHelper::CheckActionIf( $Task, $WorkFlowID ) )
			{
				continue;
			}

			switch ( $TaskData->LIB_GROUP )
			{
				case -1:
					$CurActiors = C::_( $Task->ID, $Actiors );
					$Workers = C::_( 'workers', $CurActiors, '' );
					$Groups = C::_( 'groups', $CurActiors, array() );
					$Check = (!empty( $Workers ) || !empty( $Groups ) );
					$Result = $Result && $Check;
					break;
				case -7:
					$CurActiors = C::_( $Task->ID, $Actiors );
					$Workers = C::_( 'workers', $CurActiors, array() );
					$CleanWorkers = array();
					foreach ( $Workers as $Worker )
					{
						$Worker = (int) $Worker;
						if ( empty( $Worker ) )
						{
							continue;
						}
						$CleanWorkers[] = $Worker;
					}
					$Check = !empty( $CleanWorkers );
					$Result = $Result && $Check;
					break;
				default :
					$Result = $Result && true;
					continue;
			}
		}
		return $Result;

	}

	public static function CreateNoActorTask( $WFID, $Task )
	{
		$TaskData = self::getLibTask( $Task );
		$DUE_Date = self::CalculateDueDate( 1, $Task );
		$PRIORITY = C::_( 'params' . '.' . $Task . '.' . 'priority', 'post', 200 );
		$Table = new TasksTable();
		$data = array();
		$data['LIB_TASK_ID'] = C::_( 'ID', $TaskData, 0 );
		$data['TASK_TITLE'] = C::_( 'LIB_TITLE', $TaskData, 'Unknown' );
		$data['TASK_DESCRIPTION'] = C::_( 'LIB_DESC', $TaskData, '' );
		$data['WORKFLOW_ID'] = $WFID;
		$data['TASK_DUE_DATE'] = $DUE_Date;
		$data['STATE'] = 1;
		$data['TASK_ACTOR'] = 0;
		$data['TASK_ACTOR_GROUP'] = 0;
		$data['PRIORITY'] = $PRIORITY;
		if ( !$Table->bind( $data ) )
		{
			return false;
		}
		if ( !$Table->check() )
		{
			return false;
		}
		if ( !$Table->store() )
		{
			return false;
		}
		$NewID = $Table->insertid();
		return $NewID;

	}

	public static function CreateTask( $WFID, $Task, $ActiorIN, $email = true, $GROUP = 0 )
	{
		$Actior = trim( $ActiorIN );
		if ( empty( $Actior ) )
		{
			return false;
		}
		$TaskData = self::getLibTask( $Task );
		$WorkFlowData = self::getWorkFlow( $WFID );
		$DUE_Date = self::CalculateDueDate( 1, $Task );
		$PRIORITY = C::_( 'params' . '.' . $Task . '.' . 'priority', 'post', 200 );
		$Table = new TasksTable();
		$data = array();
		$data['LIB_TASK_ID'] = C::_( 'ID', $TaskData, 0 );
		$data['TASK_TITLE'] = C::_( 'LIB_TITLE', $TaskData, 'Unknown' );
		$data['TASK_DESCRIPTION'] = C::_( 'LIB_DESC', $TaskData, '' );
		$data['WORKFLOW_ID'] = $WFID;
		$data['TASK_DUE_DATE'] = $DUE_Date;
		$data['STATE'] = 0;
		$data['TASK_ACTOR'] = $Actior;
		$data['TASK_ACTOR_GROUP'] = $GROUP;
		$data['PRIORITY'] = $PRIORITY;
		if ( !$Table->bind( $data ) )
		{
			return false;
		}
		if ( !$Table->check() )
		{
			return false;
		}
		if ( !$Table->store() )
		{
			return false;
		}
		$NewID = $Table->insertid();
		if ( $email )
		{
			self::SendEmailToUser( $NewID, $Actior, $TaskData, $WorkFlowData );
		}
		return $NewID;

	}

	protected static function _CreateGroupTask( $WFID, $Task, $GroupActior )
	{
		if ( !$GroupActior )
		{
			return false;
		}
		$TaskData = self::getLibTask( $Task );
		$DUE_Date = self::CalculateDueDate( 1, $Task );
		$Table = new TasksTable();
		$data = array();
		$PRIORITY = C::_( 'params' . '.' . $Task . '.' . 'priority', 'post', 200 );
		$data['PRIORITY'] = $PRIORITY;
		$data['LIB_TASK_ID'] = C::_( 'ID', $TaskData, 0 );
		$data['TASK_TITLE'] = C::_( 'LIB_TITLE', $TaskData, 'Unknown' );
//		$data['TASK_TOKEN'] = $Token;
		$data['TASK_DESCRIPTION'] = C::_( 'LIB_DESC', $TaskData, '' );
		$data['WORKFLOW_ID'] = $WFID;
		$data['TASK_DUE_DATE'] = $DUE_Date;
		$data['STATE'] = 0;
		$data['TASK_ACTOR'] = 0;
		$data['TASK_ACTOR_GROUP'] = $GroupActior;
		if ( !$Table->bind( $data ) )
		{
			return false;
		}
		if ( !$Table->check() )
		{
			return false;
		}
		if ( !$Table->store() )
		{
			return false;
		}
		$NewID = $Table->insertid();
		return $NewID;

	}

	public static function CreateGroupTask( $WFID, $Task, $GroupActior )
	{
		if ( !$GroupActior )
		{
			return false;
		}
		$GroupData = self:: getGroup( $GroupActior );
		$EmailTo = C::_( 'EMAILTO', $GroupData );
		switch ( $EmailTo )
		{
			case 8:
			case 9:
				$GroupUsers = self::getGroupUsers( $GroupActior );
				$Email = ($EmailTo == 9);
				foreach ( $GroupUsers as $User )
				{
					$NewID = self::CreateTask( $WFID, $Task, $User, $Email );
				}
				break;
			case 10:
				$GroupUsers = self::GroupUsersLeave( $GroupActior );
				$Default = 0;
				foreach ( $GroupUsers as $User )
				{
					if ( empty( $Default ) )
					{
						$Default = C::_( 'ID', $User );
					}
					if ( C::_( 'IS_HOLIDAY', $User ) < 0 )
					{
						$Default = C::_( 'ID', $User );
						break;
					}
				}
				$NewID = self::CreateTask( $WFID, $Task, $Default, 1 );
				break;
			case 5:
				$NewID = self::_CreateGroupTask( $WFID, $Task, $GroupActior );
				break;
			default:
			case 6:
			case 7:
				$NewID = self::_CreateGroupTask( $WFID, $Task, $GroupActior );
//				self:: SendEmailToGroup( $NewID, $GroupActior, $TaskData, $WorkFlowData );
				break;
		}
		return true;

	}

	public static function GetMyTasksCount()
	{
		$where = array();
		$where[] = 't.state = 0 ';
		$where[] = ' w.state = 0 ';
//		$where[] = 't.task_actor = 0 ';
		$SubQuery = 'select /*+ index(t REL_WORKERS_GROUPS_IDX1)*/  * from rel_workers_groups g where g.group_id = t.task_actor_group and g.worker = ' . Users::GetUserID();
		$where[] = ' exists( ' . $SubQuery . ') ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$whereUser = array();
		$whereUser[] = 'w.state = 0 ';
		$whereUser[] = 'tt.state = 0 ';
		$whereUser[] = ' tt.task_actor = ' . Users::GetUserID();
		$whereQU = count( $whereUser ) ? ' WHERE (' . implode( ') AND (', $whereUser ) . ')' : '';

		$countQuery = ' select count(1) from ( '
						. ' select '
						. ' 1 from hrs_tasks t '
						. ' left join hrs_workflows w on w.id = t.workflow_id'
						. $whereQ
						. ' union all '
						. ' select 1 from hrs_tasks tt '
						. ' left join hrs_workflows w on w.id = tt.workflow_id'
						. $whereQU
						. ' ) k '
		;
		return $Return = (int) DB::LoadResult( $countQuery );

	}

	public static function SendEmailToGroup( $NewID, $Group, $TaskData, $WorkFlowData )
	{
		$Emails = Helper::getUsersMailsByGroup( $Group );
		if ( count( $Emails ) == 0 )
		{
			return true;
		}
		$Subject = C::_( 'TITLE', $WorkFlowData );
		$URI = URI::getInstance();
		$URI->setPath( '' );
		$URI->setVar( 'option', 'mytask' );
		$URI->setVar( 'task', 'edit' );
		$URI->setVar( 'nid[]', $NewID );
		$data['LINK'] = $URI->toString();
		$data['EMAILTITLE'] = $Subject;
		$data['MESSAGE'] = Text::_( 'EMAIL_MESSAGE' );
		$data['TASKDETALES'] = Text::_( 'EMAIL_TASKDETALES' );
		$data['TASKTITLE'] = Text::_( 'EMAIL_TASKTITLE' ) . ' : ' . C::_( 'LIB_TITLE', $TaskData );
		$data['DUEDATE'] = Text::_( 'EMAIL_DUEDATE' ) . ' : ' . C::_( 'DURATION', $TaskData ) . ' ' . Text::_( 'Day' );
		$data['WORKFLOW'] = Text::_( 'EMAIL_WORKFLOW' ) . ' : ' . C::_( 'TITLE', $WorkFlowData );
		$data['LINKTITLE'] = Text::_( 'EMAIL_LINKTITLE' );
		$EmailSend = Email::getInstance();
		foreach ( $Emails as $UserID => $Email )
		{
			$UserData = Users::getUser( $UserID );
			$data['GREETING'] = Text::_( 'EMAIL_GREETING' ) . ' ' . $UserData->FIRSTNAME . ' ' . $UserData->LASTNAME;
			$EmailSend->setEmailData( $data );
			$UEmails = explode( ',', $Email );
			foreach ( $UEmails as $E )
			{
				$E = trim( $E );
				if ( empty( $E ) )
				{
					continue;
				}
				$EmailSend->send( $E, $Subject, 'newtask' );
			}
		}

	}

	public static function SendEmailToUser( $NewID, $User, $TaskData, $WorkFlowData )
	{
		return true;
		$data = array();
		$UserData = Users::getUser( $User );
		$Email = C::_( 'EMAIL', $UserData );
		$Email = 'teimuraz@kevlishvili.ge';
		if ( empty( $Email ) )
		{
			return true;
		}

		$MyUser = Users::GetUserID();
		if ( $MyUser == $User )
		{
			return true;
		}
		$URI = URI::getInstance();
		$URI->setVar( 'option', 'mytask' );
		$URI->setVar( 'task', 'edit' );
		$URI->setVar( 'nid[]', $NewID );
		$Subject = C::_( 'TITLE', $WorkFlowData );
		$data['LINK'] = $URI->toString();
		$data['GREETING'] = Text::_( 'EMAIL_GREETING' ) . ' ' . $UserData->FIRSTNAME . ' ' . $UserData->LASTNAME;
		$data['EMAILTITLE'] = $Subject;
		$data['MESSAGE'] = Text::_( 'EMAIL_MESSAGE' );
		$data['TASKDETALES'] = Text::_( 'EMAIL_TASKDETALES' );
		$data['TASKTITLE'] = Text::_( 'EMAIL_TASKTITLE' ) . ' : ' . C::_( 'LIB_TITLE', $TaskData );
		$data['DUEDATE'] = Text::_( 'EMAIL_DUEDATE' ) . ' : ' . C::_( 'DURATION', $TaskData ) . ' ' . Text::_( 'Day' );
		$data['WORKFLOW'] = Text::_( 'EMAIL_WORKFLOW' ) . ' : ' . C::_( 'TITLE', $WorkFlowData );
		$data['LINKTITLE'] = Text::_( 'EMAIL_LINKTITLE' );
		$EmailSend = Email::getInstance();
		$EmailSend->setEmailData( $data );
		return $EmailSend->send( $Email, $Subject, 'newtask' );

	}

	public static function LoadWorkFlow( $WorkFlowID )
	{
		require_once PATH_BASE . DS . 'components' . DS . 'workflow' . DS . 'table.php';
		$Table = new WorkflowTable();
		$Table->load( $WorkFlowID );
		return $Table;

	}

	public static function log( $FlowID, $TaskID, $Type, $LogTitle, $UserID = null, $add = 0 )
	{
		if ( empty( $UserID ) )
		{
			$UserID = Users::GetUserID();
		}
		else
		{
			$UserID = (int) $UserID;
		}
		$Query = ' insert into hrs_workflow_log '
						. ' (log_flow, log_task, log_type, log_title, log_user, log_date) '
						. ' values '
						. ' ( '
						. $FlowID . ', '
						. $TaskID . ', '
						. DB::Quote( $Type ) . ', '
						. DB::Quote( $LogTitle ) . ', '
						. $UserID . ', '
						. ' sysdate + ' . $add
						. ')'
		;
		return DB::Insert( $Query );

	}

	public static function getStartTask( $WorkFlowID )
	{
		$Query = 'select * from LIB_FLOW_ELEMENTS e '
						. ' where e.FLOW = ' . (int) $WorkFlowID
						. ' and e.LIB_TASK in( 1000 , 999, 998)'
						. ' and e.active > 0'
		;
		return DB::LoadObject( $Query );

	}

	public static function getStartTaskAction( $CurrentTaskID )
	{
		$Query = 'select * from LIB_FLOW_ELEMENTS_ACTIONS a '
						. ' where a.LIB_ELEMENT = ' . (int) $CurrentTaskID
						. ' and a.active > 0'
						. ' order by a.ordering asc '
		;
		return DB::LoadObject( $Query );

	}

	public static function getStartTaskActions( $CurrentTaskID )
	{
		$whereVal = ' AND a.ACTIVE = 1 ';
		$xmod = Request::getInt( 'xmode' );
		if ( $xmod == 1 )
		{
			$whereVal = ' AND a.ACTIVE >0 ';
		}

		$Query = 'select * from LIB_FLOW_ELEMENTS_ACTIONS a '
						. ' where a.LIB_ELEMENT = ' . (int) $CurrentTaskID
						. $whereVal
						. ' order by a.ordering asc '
		;
		return DB::LoadObjectList( $Query );

	}

	public static function getTaskActions( $TaskID )
	{

		$Query = 'select * from LIB_FLOW_ELEMENTS_ACTIONS a '
						. ' where a.LIB_ELEMENT = ' . (int) $TaskID
		;
		return DB::LoadObject( $Query );

	}

	public static function getTaskActionsFromMyTaskID( $TaskID )
	{
		$Task = TaskHelper::getTask( $TaskID );
		$Xmode = TaskHelper::GetMode( $Task->WORKFLOW_ID );
		$Active = ' a.active = 1 ';
		if ( $Xmode == 1 )
		{
			$Active = ' a.active in(1, 2) ';
		}
		$Query = 'select *'
						. ' from LIB_FLOW_ELEMENTS_ACTIONS a '
						. ' where a.LIB_ELEMENT in '
						. ' ('
						. ' select t.lib_task_id '
						. ' from hrs_tasks t '
						. ' where '
						. ' t.task_id = ' . (int) $TaskID
						. ' )'
						. ' and ' . $Active
						. ' order by a.ORDERING asc '
		;
		return DB::LoadObjectList( $Query );

	}

	public static function getAction( $action )
	{

		$Query = 'select * from LIB_FLOW_ELEMENTS_ACTIONS a '
						. ' where a.ID = ' . (int) $action
		;

		return DB::LoadObject( $Query );

	}

	public static function getLibTask( $TaskID )
	{
		static $Tasks = array();
		if ( !isset( $Tasks[$TaskID] ) )
		{
			$Query = 'select * from LIB_FLOW_ELEMENTS e '
							. ' where e.ID = ' . (int) $TaskID
							. ' and e.ACTIVE = 1 '
			;
			$Tasks[$TaskID] = DB::LoadObject( $Query );
		}
		return $Tasks[$TaskID];

	}

	public static function getTask( $TaskID )
	{
		$Query = 'select * from hrs_tasks t '
						. ' where t.TASK_ID = ' . (int) $TaskID
		;
		return DB::LoadObject( $Query );

	}

	public static function getTasks( $FlowType, $Level = -1 )
	{
		$Query = 'select '
						. ' * '
						. ' from LIB_FLOW_ELEMENTS t '
						. ' where '
						. ' t.lib_level in '
						. ' ( '
						. ' select '
						. ' min(e.lib_level) '
						. ' from LIB_FLOW_ELEMENTS e '
						. ' where '
						. ' e.active = 1 '
						. ' and e.flow = ' . (int) $FlowType
						. ' and e.lib_level > ' . (int) $Level
						. ' ) '
						. ' and t.active = 1 '
						. ' and t.flow =' . (int) $FlowType
						. ' and t.lib_level > ' . (int) $Level
		;
		return DB::LoadObjectList( $Query );

	}

	public static function SaveAttributes( $FlowID, $TaskID, $Attributes )
	{
		$AttributesItems = self::getElementAttribs( $TaskID, $FlowID );
		$AttributesValues = C::_( 'attributes', 'post', $Attributes );
		//Prepate data
		foreach ( $AttributesItems as $key => $AttributeData )
		{
			$AttributeID = Helper::CleanNumber( $key );
			$AttributeName = 'a' . $AttributeID;
			if ( !Attributes::IsShownAttribute( $AttributeData, $FlowID ) )
			{
				unset( $AttributesItems[$key] );
				unset( $AttributesValues[$AttributeName] );
				continue;
			}
			$HTMLAttributeValue = C::_( $AttributeName, $AttributesValues );
			$AttributesValues[$AttributeName] = Attributes::PrepareAttribute( $AttributeData, $HTMLAttributeValue, $FlowID );
		}
		//Assemble Final Data
		foreach ( $AttributesItems as $key => $AttributeItem )
		{
			$AttributesValues = Attributes::AssembleAttribute( $AttributesValues, $AttributeItem, $FlowID );
		}
		foreach ( $AttributesValues as $key => $AttributeValue )
		{
			$AttributeID = Helper::CleanNumber( $key );
			self::_saveAttribute( $FlowID, $TaskID, $AttributeID, self::_Split( $AttributeValue ) );
		}

	}

	public static function getAttribute( $AttributeID )
	{
		$Query = 'select * from LIB_FLOW_ELEMENTS_ATTRIBUTES t '
						. ' where t.ID = ' . (int) $AttributeID

		;
		return DB::LoadObject( $Query );

	}

	public static function _saveAttribute( $FlowID, $TaskID, $AttributeID, $AttributeValue )
	{
		$Exists = self::getExistsRow( $FlowID, $TaskID, $AttributeID );
		static $Table = null;
		if ( is_null( $Table ) )
		{
			require_once 'AttributeTable.php';
			$Table = new ParamsTable();
		}
		$Table->resetAll();
		if ( $Exists )
		{
			$Table->load( $Exists );
		}
		$result = false;
		if ( $Exists && $Table->TDATA == $AttributeValue && !is_array( $AttributeValue ) )
		{
			return false;
		}
		else
		{
			$Data = array(
					'FLOW' => $FlowID,
					'TASK_ID' => $TaskID,
					'ATTRIBUTE' => $AttributeID,
					'ACTOR' => Users::GetUserID(),
					'TDATA' => null
			);
			$AttributeValue = (array) $AttributeValue;
			$Key = '';
			foreach ( $AttributeValue as $Key => $Datax )
			{
				$SKey = 'TDATA' . (empty( $Key ) ? '' : $Key);
				$Data[$SKey] = $Datax;
			}
			for ( $K = ($Key + 1); $K <= 4; $K++ )
			{
				$SKey = 'TDATA' . $K;
				$Data[$SKey] = '';
			}
			$Table->bind( $Data );
			$result = $Table->store();
		}
		if ( $result )
		{
			$ID = $Table->insertid();
			$params = array( ':id' => $ID );
			DB::callProcedure( 'workflow.workflow_hist', $params );
			return $result;
		}
		else
		{
			return false;
		}

	}

	/**
	 * 
	 * @staticvar type $Table
	 * @param int $FlowID
	 * @param string $Comment
	 * @param int $type
	 * @param int $ref
	 * @return boolean
	 */
	public static function SaveComment( $FlowID, $Comment, $ref = 0 )
	{
		$CommentText = trim( $Comment );
		if ( empty( $CommentText ) )
		{
			return false;
		}
		static $Table = null;
		if ( is_null( $Table ) )
		{
			require_once 'CommentTable.php';
			$Table = new CommentTable();
		}
		$Table->reset();
		$Date = new PDate();
		$Data = array(
				'COMMENT_FLOW' => $FlowID,
				'COMMENT_DATE' => $Date->toFormat(),
				'COMMENT_USER' => Users::GetUserID(),
				'COMMENT_TEXT' => trim( $Comment ),
				'COMMENT_REF' => $ref
		);
		$Table->bind( $Data );
		$IDx = $Table->store();
		if ( $IDx )
		{
			self::SendAlert( $Data, $FlowID );
			return $IDx;
		}
		return false;

	}

	public static function RenderAtributes( $FlowTaskID, $Data = array(), $FLOW = null )
	{
		$attributes = self::getElementAttribs( $FlowTaskID );
		$CurrentData = array();
		if ( $FLOW )
		{
			$CurrentData = TaskHelper::getAttributesValues( $FLOW, 'ATTRIBUTE' );
		}
		foreach ( $attributes as $Attrib )
		{
			if ( !self::CheckAttributeIf( $Attrib, $FLOW ) )
			{
				continue;
			}
			$Value = C::_( 'a' . $Attrib->ID, $Data, C::_( $Attrib->ID . '.TDATA', $CurrentData, '' ) );
			echo Attributes::renderAttribute( $Attrib, $Value, $FLOW );
		}

	}

	public static function getElementAttribs( $FlowTaskID, $FlowID = 0 )
	{
		$whereVal = ' f.ACTIVE = 1 ';
		$xmod = Request::getInt( 'xmode', self::GetMode( $FlowID ) );
		if ( $xmod == 1 )
		{
			$whereVal = ' f.ACTIVE > 0 ';
		}

		$Query = 'select '
						. ' f.* '
						. ' from LIB_FLOW_ELEMENTS_ATTRIBUTES f '
						. ' where  ' . $whereVal
						. ' and f.LIB_ELEMENT = ' . $FlowTaskID
						. ' order by f.ORDERING '
		;

		return DB::LoadObjectList( $Query, 'ID' );

	}

	public static function getFlowAttributes( $FLOW )
	{
		$Query = 'select f.*,'
						. ' a.lib_title, '
						. ' a.lib_attribute, '
						. ' a.params,'
						. ' a.edit,'
						. ' a.xcopy,'
						. ' to_char(h.start_date, \'hh24:mi YYYY-mm-dd\') edit_date, '
						. ' to_char(f.CHECKED_OUT_DATE, \'YYYY-mm-dd hh24:mi\') CHECKED_OUT_DATE '
						. ' from CWS_FLOW_PARAMS f '
						. ' left join CWS_FLOW_PARAMS_HIST h on h.id = f.id '
						. ' left join LIB_FLOW_ELEMENTS_ATTRIBUTES a on a.id = f.attribute '
						. ' where f.FLOW = ' . $FLOW
						. '  and h.end_date is null '
						. ' order by a.ordering '
		;
		return $return = DB::LoadObjectList( $Query );

	}

	public static function getAttributesValues( $FLOW, $Key = false )
	{
		$Query = 'select f.* '
						. ' from CWS_FLOW_PARAMS f '
						. ' where f.FLOW = ' . $FLOW
		;
		return $return = DB::LoadObjectList( $Query, $Key );

	}

	public static function getFlowLog( $FLOW )
	{
		$Query = 'select t.*, '
						. ' m.task_complete_result complete_result, '
						. ' to_char(t.LOG_DATE, \'dd-mm-yyyy hh24:mi:ss\') FLOG_DATE, '
						. ' w.FIRSTNAME || \' \' || w.LASTNAME actiorname '
						. ' from hrs_workflow_log t '
						. ' left join CWS_WORKERS w on w.ID = t.LOG_USER '
						. ' left join hrs_tasks m on m.task_id = t.log_task '
						. ' where t.LOG_FLOW = ' . $FLOW
						. ' order by LOG_DATE desc, t.log_task asc '
		;
		return DB::LoadObjectList( $Query );

	}

	public static function getFlowComments( $FLOW )
	{
		$Query = 'select t.*, '
						. ' to_char(t.COMMENT_DATE, \'dd-mm-yyyy hh24:mi:ss\') FCOMMENT_DATE, '
						. ' w.FIRSTNAME || \' \' || w.LASTNAME actiorname '
						. ' from CWS_WORKFLOW_COMMENTS t '
						. ' left join CWS_WORKERS w on w.ID = t.COMMENT_USER '
						. ' where t.COMMENT_FLOW = ' . $FLOW
						. ' order by COMMENT_DATE desc '
		;
		return DB::LoadObjectList( $Query );

	}

//	public static function getBlobAttributes( $FLOW )
//	{
//		$Query = 'select f.*,'
//						. ' a.lib_title, '
//						. ' a.lib_attribute, '
//						. ' a.edit '
//						. ' from CWS_FLOW_PARAMS_BLOB f '
//						. ' left join LIB_FLOW_ELEMENTS_ATTRIBUTES a on a.id = f.attribute '
//						. ' where f.FLOW =' . $FLOW
//						. ' order by a.ordering '
//		;
//		return DB::LoadObjectList( $Query );
//
//	}

	public static function SaveAttribute( $ID, $TDATA, $CREF = 0 )
	{
		static $Table = null;
		if ( is_null( $Table ) )
		{
			require_once 'AttributeTable.php';
			$Table = new ParamsTable();
		}
		$Table->resetAll();
		$Table->load( $ID );
//		$AttributeID = C::_( 'ATTRIBUTE', $Table );
//		$AttributeData = self::getAttribute( $AttributeID );
		$SDATA = self::_Split( $TDATA );
		$result = false;
		if ( $Table->TDATA != $SDATA || is_array( $SDATA ) )
		{
			$SDATA = (array) $SDATA;
			foreach ( $SDATA as $Key => $Data )
			{
				$SKey = 'TDATA' . (empty( $Key ) ? '' : $Key);
				$Table->{$SKey} = $Data;
			}
			for ( $K = ($Key + 1); $K <= 4; $K++ )
			{
				$SKey = 'TDATA' . $K;
				$Table->{$SKey} = '';
			}
//			$Table->TDATA = $SDATA;
			$Table->ACTOR = Users::GetUserID();
			$Table->CREF = $CREF;
			$result = $Table->store();
		}
		if ( $result )
		{
			$params = array( ':id' => $ID );
			DB ::callProcedure( 'workflow.workflow_hist', $params );
			return $result;
		}
		else
		{
			return false;
		}

	}

	public static function getStartElement( $FlowID )
	{
		$Query = 'select * from LIB_FLOW_ELEMENTS f '
						. ' where f.ACTIVE = 1 '
						. ' and f.FLOW = ' . $FlowID
						. ' and f.ORDERING = 0 '
		;
		return DB::LoadObject( $Query );

	}

	public static function getFlows( $status = null, $order = 'ordering', $Operator = null )
	{
		if ( is_null( $Operator ) )
		{
			$Operator = '=';
		}
		if ( $status )
		{
			$Query = ' select * from lib_limit_app_types f '
							. ' where f.active  ' . $Operator . ' ' . (int) $status
							. ' order by f.' . $order
			;
		}
		else
		{
			$Query = ' select * from lib_limit_app_types f '
							. ' order by f.' . $order
			;
		}
		return DB::LoadObjectList( $Query );

	}

	public static function getFlow( $FlowID )
	{
		$Query = 'select * from lib_limit_app_types f '
						. ' where f.id = ' . (int) $FlowID
		;
		return DB::LoadObject( $Query );

	}

	public static function GenTaskToken()
	{
		$Query = 'select TASK_TOKEN_SEQ.nextval from dual';
		return DB::LoadResult( $Query );

	}

	public static function getWorkFlow( $WFID )
	{
		static $WData = array();
		if ( !isset( $WData[$WFID] ) )
		{
			$Query = 'select * from hrs_applications wf '
							. ' where wf.id = ' . DB::Quote( $WFID )
			;
			$WData[$WFID] = DB::LoadObject( $Query );
		}
		return $WData[$WFID];

	}

	/**
	 * 
	 * @param int $DURATION
	 * @return String
	 */
	/*	 * *	
	  public static function CalculateDueDate( $DURATIONiN, $TaskID, $startDate = 'now' )
	  {
	  $name = 'due_date';
	  $Space = 'params';
	  $value = C::_( $Space . '.' . $TaskID . '.' . $name, 'post', null );
	  if ( !empty( $value ) )
	  {
	  $Date = new PDate( $value );
	  return $Date->toFormat( '%Y-%m-%d 23:59:59' );
	  }
	  $DURATION = (int) $DURATIONiN;
	  $DaySec = 0;
	  $DateStart = new PDate( $startDate );
	  $day = 0;
	  while ( $DURATION >= 0 )
	  {
	  $DaySec = $day * 60 * 60 * 24;
	  $Date = new PDate( $DateStart->toUnix() + $DaySec );
	  $DayNum = $Date->toFormat( '%w' );
	  $day++;
	  if ( $DayNum == 0 or $DayNum == 6 )
	  {
	  continue;
	  }
	  --$DURATION;
	  }
	  return $Date->toFormat();

	  }
	 */
	public static function getNextTaskByAction( $ActionID )
	{
//		$Action = self::getAction( $ActionID );
		$Query = 'select '
						. ' e.*, '
						. ' fat.if aif, '
						. ' fat.stop '
						. ' from lib_flow_elements e '
						. ' left join rel_flow_action_task fat on e.id = fat.task '
						. ' where '
						. ' e.active = 1 '
						. ' and fat.action = ' . DB::Quote( $ActionID )
						. ' order by fat.ordering asc'
		;
		$Data = DB::LoadObjectList( $Query );
		return $Data;

	}

	public static function getCurrentTasks()
	{
		$where = array();
		$where[] = 't.state = 0 ';
//		$where[] = 't.task_actor = 0 ';
		$SubQuery = 'select t.group_id from rel_workers_groups t where worker = ' . Users::GetUserID();
		$where[] = 't.task_actor_group in( ' . $SubQuery . ') ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$whereUser = array();
		$whereUser[] = 'tt.state = 0 ';
		$whereUser[] = ' tt.task_actor = ' . Users::GetUserID();
		$whereQU = count( $whereUser ) ? ' WHERE (' . implode( ') AND (', $whereUser ) . ')' : '';

		$Query = ' select * from ( '
						. ' select '
						. ' t.*, '
						. ' to_char(t.TASK_DUE_DATE, \'dd-mm-yyyy hh24:mi:ss\') DUE_DATE,'
						. ' case when sysdate > t.TASK_DUE_DATE then 0 when trunc(sysdate) = trunc( t.TASK_DUE_DATE) then 1 else 2 end as task_state '
						. ' from hrs_tasks t '
						. $whereQ
						. ' union all '
						. ' select '
						. ' tt.*, '
						. ' to_char(tt.TASK_DUE_DATE, \'dd-mm-yyyy hh24:mi:ss\') DUE_DATE, '
						. ' case when sysdate > tt.TASK_DUE_DATE then 0 when trunc(sysdate) = trunc( tt.TASK_DUE_DATE) then 1 else 2 end as task_state '
						. ' from hrs_tasks tt '
						. $whereQU
						. ' ) k '
						. ' order by k.TASK_DUE_DATE asc '
		;
		return $Return = DB::LoadObjectList( $Query );

	}

	public static function getCurrentTaskCount()
	{
		$where = array();
		$where[] = 't.state = 0 ';
		$where[] = 'w.state = 0 ';
//		$where[] = 't.task_actor = 0 ';
		$SubQuery = 'select t.group_id from rel_workers_groups t where worker = ' . Users::GetUserID();
		$where[] = 't.task_actor_group in( ' . $SubQuery . ') ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$whereUser = array();
		$whereUser[] = 'tt.state = 0 ';
		$whereUser[] = 'w.state =0 ';
		$whereUser[] = ' tt.task_actor = ' . Users::GetUserID();
		$whereQU = count( $whereUser ) ? ' WHERE (' . implode( ') AND (', $whereUser ) . ')' : '';

		$Query = ' select count(1) from ( '
						. ' select '
						. ' t.task_id'
						. ' from hrs_tasks t '
						. ' left join hrs_workflows w on w.ID = t.WORKFLOW_ID '
						. $whereQ
						. ' union all '
						. ' select '
						. ' tt.task_id '
						. ' from hrs_tasks tt '
						. ' left join hrs_workflows w on w.ID = tt.WORKFLOW_ID '
						. $whereQU
						. ' ) k '
		;
		return (int) $Return = DB::LoadResult( $Query );

	}

	public static function getCompleteTaskCount()
	{
		$where = array();
		$where[] = 't.state = 1 ';
		$where[] = 'w.state = 0 ';
//		$where[] = 't.task_actor = 0 ';
		$SubQuery = 'select /*+ index(t REL_WORKERS_GROUPS_IDX1)*/ t.group_id from rel_workers_groups t where worker = ' . Users::GetUserID();
		$where[] = 't.task_actor_group in( ' . $SubQuery . ') ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$whereUser = array();
		$whereUser[] = 'tt.state = 1 ';
		$whereUser[] = 'w.state = 0 ';
		$whereUser[] = ' tt.task_actor = ' . Users::GetUserID();
		$whereQU = count( $whereUser ) ? ' WHERE (' . implode( ') AND (', $whereUser ) . ')' : '';

		$Query = ' select count(1) from ( '
						. ' select '
						. ' /*+ index(t hrs_tasks_IDX2)*/ '
						. ' t.task_id'
						. ' from hrs_tasks t '
						. ' left join hrs_workflows w on w.ID = t.WORKFLOW_ID '
						. $whereQ
						. ' union all '
						. ' select '
						. ' tt.task_id '
						. ' from hrs_tasks tt '
						. ' left join hrs_workflows w on w.ID = tt.WORKFLOW_ID '
						. $whereQU
						. ' ) k '
		;
		return (int) $Return = DB::LoadResult( $Query );

	}

	public static function ShowSidebar()
	{
		$Result = (bool) self::_getMenuAttrib( 'SIDEBAR' );
		$Result2 = $Result || (bool) self::_getMenuAttrib( 'LINKS' );
		return $Result2;

	}

	public static function ShowBlock( $Key )
	{
		return (bool) self::_getMenuAttrib( $Key );

	}

	protected static function _getMenuAttrib( $Key )
	{
		static $Menu = null;
		if ( is_null( $Menu ) )
		{
			$MenuItems = MenuConfig::getInstance();
			$Menu = $MenuItems->getActive();
		}
		return C::_( $Key, $Menu );

	}

	public static function GetParalelTasksCount( $WorkFlowID )
	{
		$Query = 'select '
						. ' count(1) num_task '
						. ' from hrs_tasks t '
						. ' where '
						. ' t.workflow_id = ' . $WorkFlowID
						. ' and t.state = 0'
		;
		return DB::LoadResult( $Query );

	}

	public static function RenderAtributeDisplay( $Attriobute, $Attrib, $FLOW = null, $PrintNull = 1, $Archive = 1 )
	{
		$Value = C::_( 'TDATA', $Attrib ) . C::_( 'TDATA1', $Attrib ) . C::_( 'TDATA2', $Attrib ) . C::_( 'TDATA3', $Attrib ) . C::_( 'TDATA4', $Attrib );
		if ( !$PrintNull && $Value == '' )
		{
			return '';
		}
		echo Attributes::renderAttributeDisplay( $Attrib, $Value, $FLOW, $Archive );

	}

	public static function RenderAtributePrint( $Attriobute, $Attrib, $FLOW = null, $PrintNull = 1 )
	{
		$Value = C::_( 'TDATA', $Attrib ) . C::_( 'TDATA1', $Attrib ) . C::_( 'TDATA2', $Attrib ) . C::_( 'TDATA3', $Attrib ) . C::_( 'TDATA4', $Attrib );
		if ( !$PrintNull && $Value == '' )
		{
			return '';
		}
		echo Attributes::renderAttributePrint( $Attrib, $Value, $FLOW, $PrintNull );

	}

	public static function DefineActior( $Task )
	{
		$Actior = '';
		$Group = C::_( 'LIB_GROUP', $Task );
		switch ( $Group )
		{
			case '0':
				$Actior = Text::_( 'Initiator' );
				break;
			case '-1':
				$Actior = Text::_( 'Variable' );
				break;
			case '-3':
				$ALTTask = C::_( 'LIB_GROUP_ALT', $Task );
				$FLOW = C::_( 'FLOW', $Task );
				$Query = 'select'
								. ' wg.lib_title group_name, '
								. ' fe.*, '
								. ' t.lib_title as task_title '
								. ' from lib_flow_elements fe '
								. ' left join lib_workers_groups wg on fe.lib_group = wg.id '
								. ' left join lib_tasks t on t.id = fe.lib_task '
								. ' where fe.flow = ' . $FLOW
								. ' and fe.id = ' . $ALTTask
				;
				$TaskData = DB::LoadObject( $Query );
				$ALTTaskTitle = C::_( 'LIB_TITLE', $TaskData );
				$Actior = TaskHelper::DefineActior( $TaskData ) . ' &ndash; ' . Text::_( 'From Task' ) . ' : ' . $ALTTaskTitle;
				break;
			case '-5':
				$Actior = Text::_( 'Current_User' );
				break;
			case '-17':
				$Actior = Text::_( 'CHIEF WORKERS' );
				break;
			case '-9':
				$AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
				$Values = TaskHelper::GetFlowAttributesByID( 0 );
				$User = C::_( $AttrID, $Values );
				if ( !$User )
				{
					break;
				}
				$UserData = Users::getUser( $User );
				$Actior = self::getTaskDesc( C::_( 'FIRSTNAME', $UserData ) . '  ' . C::_( 'LASTNAME', $UserData ) );
				break;
			default:
				$Actior = C::_( 'GROUP_NAME', $Task );
				break;
		}
		return $Actior;

	}

	public static function RenderAtributeView( $Attrib )
	{
		$Value = C::_( 'TDATA', $Attrib ) . C::_( 'TDATA1', $Attrib ) . C::_( 'TDATA2', $Attrib ) . C::_( 'TDATA3', $Attrib ) . C::_( 'TDATA4', $Attrib );
		echo Attributes::renderAttributeView( $Attrib, $Value );

	}

	public static function RenderAtributeDiff( $Attrib )
	{
		$Value = C::_( 'TDATA', $Attrib ) . C::_( 'TDATA1', $Attrib ) . C::_( 'TDATA2', $Attrib ) . C::_( 'TDATA3', $Attrib ) . C::_( 'TDATA4', $Attrib );
		echo Attributes::renderAttributeDiff( $Attrib, $Value );

	}

	public static function getGroup( $Group )
	{
		$GroupID = (int) $Group;
		if ( $GroupID )
		{
			$Query = 'select * from LIB_WGROUPS t '
							. 'where '
							. ' t.ID = ' . $GroupID
							. ' and t.ACTIVE = 1'
			;
			$Data = DB::LoadObject( $Query );
			return $Data;
		}
		return false;

	}

	public static function getAltTasks( $AltTask, $WorkFlowID )
	{
		$Query = 'select '
						. ' max(t.TASK_ID) TASK_ID, '
						. ' max(t.LIB_TASK_ID) LIB_TASK_ID, '
						. ' max(t.TASK_ACTOR) TASK_ACTOR, '
						. ' max(t.TASK_ACTOR_GROUP) TASK_ACTOR_GROUP '
						. ' from hrs_tasks t '
						. 'where '
						. ' t.workflow_id = ' . $WorkFlowID
						. ' and t.lib_task_id = ' . $AltTask
						. ' group by t.LIB_TASK_ID '
		;
		$Data = DB::LoadObjectList( $Query );
		return $Data;

	}

	public static function CompleteFlow( $WorkFlowID )
	{
		$sql = 'update '
						. ' hrs_workflows w '
						. ' set '
						. ' w."STATE" = 1, '
						. ' w.COMPLETE_DATE = sysdate '
						. ' where w.ID = ' . DB::Quote( $WorkFlowID );
		DB::Update( $sql );

	}

	public static function RenderAtributeFilter( $Attrib, $ProcessID )
	{
		echo Attributes::renderAttributeFilter( $Attrib, $ProcessID );

	}

	public static function getTaskResult( $LIB_TASK_ID, $TASK_ID, $FLOW )
	{
		require_once 'task.php';
		$TaskLibData = self::getLibTask( $LIB_TASK_ID );
		$TaskLibID = C::_( 'LIB_TASK', $TaskLibData );
		$TaskLib = self::getTaskLib( $TaskLibID );
		$TaskFile = C::_( 'LIB_FILE', $TaskLib );
		if ( $TaskFile )
		{
			$Path = PATH_BASE . DS . 'tasks' . DS . $TaskFile . DS . 'task.php';
			if ( is_file( $Path ) )
			{
				require_once $Path;
				$ClassName = ucfirst( $TaskFile . 'Task' );
				$Class = new $ClassName();
				return $Class->getTask( $TASK_ID, $FLOW );
			}
			else
			{
				return '';
			}
		}
		return '';

	}

	public static function getTaskLib( $TaskLibID )
	{
		static $Tasks = array();
		if ( !isset( $Tasks[$TaskLibID] ) )
		{
			$Query = 'select * from LIB_TASKS t '
							. ' where t.ID = ' . (int) $TaskLibID
							. ' and t.ACTIVE = 1 '
			;
			$Tasks[$TaskLibID] = DB::LoadObject( $Query );
		}
		return $Tasks[$TaskLibID];

	}

	public static function ValidateNewAttributes( $attributes, $TaskID, $FlowID = null )
	{
		$AttributesItems = self::getElementAttribs( $TaskID, $FlowID );
		//Prepate data
		$Check = true;
		foreach ( $AttributesItems as $key => $AttribObject )
		{
			if ( !self::CheckAttributeIf( $AttribObject, $FlowID ) )
			{
				continue;
			}
			$AttributeData = Attributes::PrepareAttribute( $AttribObject, C::_( 'a' . $key, $attributes ), $FlowID );
			$ItemValid = Attributes::ValidateData( $AttributeData, $AttribObject, $attributes, $FlowID );
			if ( !$ItemValid )
			{
				$Attribute = TaskHelper::getAttribute( $key );
				XError::setError( Text::_( 'Invalid Attribute' ) . ' : ' . C::_( 'LIB_TITLE', $Attribute ) ) . ' ';
			}
			$Check = $Check && $ItemValid;
		}
		if ( $Check == false )
		{
			XError::setError( Text::_( 'Error! Please fill all required fields!' ) );
		}
		return $Check;

	}

	public static function PreviewNewAttributes( $key, $AttributeData, $Flow = null )
	{
		$AttributeID = Helper::CleanNumber( $key );
		$AttribObject = self::getAttribute( $AttributeID );
		if ( $AttribObject )
		{
			$Result = Attributes::PreviewAttributes( $AttribObject, $AttributeData, $Flow );
		}
		else
		{
			$Result = '';
		}
		return $Result;

	}

	public static function getGroupUsersNames( $Group )
	{
		$GroupID = (int) $Group;
		if ( $GroupID )
		{
			$Query = 'select w.firstname||\' \'|| w.lastname worker from REL_WORKERS_GROUPS t '
							. ' left join CWS_WORKERS w on w.ID = t.WORKER '
							. 'where '
							. ' t.GROUP_ID = ' . $GroupID
							. ' and w.ACTIVE = 1 '
							. ' order by t.ORDERING asc '
			;
			$Data = DB::LoadList( $Query );
			return $Data;
		}
		return false;

	}

	public static function getGroupUsers( $Group )
	{
		$GroupID = (int) $Group;
		if ( $GroupID )
		{
			$Query = 'select w.id from REL_WORKERS_GROUPS t '
							. ' left join CWS_WORKERS w on w.ID = t.WORKER '
							. 'where '
							. ' t.GROUP_ID = ' . $GroupID
							. ' and w.ACTIVE = 1 '
							. ' order by t.ORDERING asc '
			;
			$Data = DB::LoadList( $Query );
			return $Data;
		}
		return false;

	}

	public static function GroupUsersLeave( $Group )
	{
		$GroupID = (int) $Group;
		if ( $GroupID )
		{
			$Query = 'select w.id, '
							. ' gettodayholiday(w.salary_employee_id, 1) is_holiday '
							. 'from REL_WORKERS_GROUPS t '
							. ' left join CWS_WORKERS w on w.ID = t.WORKER '
							. 'where '
							. ' t.GROUP_ID = ' . $GroupID
							. ' and w.ACTIVE = 1 '
							. ' order by t.ORDERING asc '
			;
			$Data = DB::LoadObjectList( $Query );
			return $Data;
		}
		return false;

	}

	public static function LogTaskView( $WData )
	{
		if ( C::_( 'STATE', $WData ) == 1 )
		{
			return true;
		}

		$TaskID = C::_( 'TASK_ID', $WData, 0 );
		$FlowID = C::_( 'ID', $WData, 0 );
		$LogTitle = C::_( 'TASK_TITLE', $WData, 0 );
		if ( empty( $TaskID ) )
		{
			return false;
		}
		$Type = 'view';
		$UserID = Users::GetUserID();
		$Check = self::CheckLog( $FlowID, $TaskID, $UserID, $Type );
		if ( empty( $Check ) )
		{
			self::log( $FlowID, $TaskID, $Type, $LogTitle );
		}

	}

	public static function CheckLog( $FlowID, $TaskID, $UserID, $Type )
	{
		$Query = 'select wl.LOG_FLOW from hrs_workflow_log wl '
						. ' where '
						. ' wl.LOG_FLOW = ' . DB::Quote( $FlowID )
						. ' and wl.LOG_USER = ' . (int) $UserID
						. ' and wl.LOG_TASK = ' . (int) $TaskID
						. ' and wl.LOG_TYPE = ' . DB::Quote( $Type )
		;
		return DB::LoadResult( $Query );

	}

	public static function RenderLog( $FLOW, $Print = false )
	{
		ob_start();
		$Logs = TaskHelper::getFlowLog( $FLOW );
		?>
		<table class="table table-striped table-condensed table-bordered">
			<thead>
				<tr>
					<td colspan="2" class="center">
						<div class="items_spacer">
							<?php echo Text::_( 'Task Log' ); ?>
						</div>
					</td>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="2" class="center filter_log">
						<?php
						if ( !$Print )
						{
							?>
							<a href="javascript:setLogFilter('all');" class="type_all active">
								<?php echo Text::_( 'All' ); ?></a> 
							<a href="javascript:setLogFilter('view');" class="type_view">
								<?php echo Text::_( 'View' ); ?></a> 
							<a href="javascript:setLogFilter('complete');" class="type_complete">
								<?php echo Text::_( 'Action' ); ?></a> 
							<a href="javascript:setLogFilter('assign');" class="type_assign">
								<?php echo Text::_( 'assign' ); ?></a> 
							<a href="javascript:setLogFilter('postpone');" class="type_postpone">
								<?php echo Text::_( 'Postpone' ); ?></a> 
							<a href="javascript:setLogFilter('connect');" class="type_connect">
								<?php echo Text::_( 'connect' ); ?></a> 
							<a href="javascript:setLogFilter('common');" class="type_connect">
								<?php echo Text::_( 'common' ); ?></a> 
							<?php
						}
						?>
					</td>
				</tr>
			</tfoot>
			<tbody class="log_items">
				<?php
				foreach ( $Logs as $Log )
				{
					$ResultText = '';
					$Result = Collection::get( 'COMPLETE_RESULT', $Log );
					$Type = Collection::get( 'LOG_TYPE', $Log, '' );
					if ( $Result && $Type == 'complete' )
					{
						$ResultText = Text::_( 'WITH RESULT' ) . ' : <strong>' . $Result . '</strong>';
					}
					?>	
					<tr class="<?php echo $Type; ?>">
						<td class="col-sm-4">
							<div class="text-right">
								<?php echo Collection::get( 'ACTIORNAME', $Log ); ?><br />
								<?php echo Collection::get( 'FLOG_DATE', $Log ); ?>
							</div>
						</td>
						<td>
							<?php echo Text::_( 'Task_Term_' . Collection::get( 'LOG_TYPE', $Log ) ); ?> - 
							<strong><?php echo Collection::get( 'LOG_TITLE', $Log ); ?></strong>
							<?php echo $ResultText; ?> 
						</td>
					</tr>	
					<?php
				}
				?>
			</tbody>
		</table>
		<?php
		$Content = ob_get_clean();
		//ob_clean();
		return $Content;

	}

	public static function RenderComments( $FLOW )
	{
		ob_start();
		$Comments = TaskHelper::getFlowComments( $FLOW );
		?>
		<table class="table table-striped table-bordered table-responsive" style="width: 100%;">
			<tr>
				<td colspan="2" class="center">
					<div class="items_spacer">
						<?php echo Text::_( 'Task Comments' ); ?>
					</div>
				</td>
			</tr>
			<?php
			if ( count( $Comments ) )
			{
				foreach ( $Comments as $Comment )
				{
					?>	
					<tr>
						<td>
							<div class="page_key">
								<?php echo Collection::get( 'ACTIORNAME', $Comment ); ?><br />
								<?php echo Collection::get( 'FCOMMENT_DATE', $Comment ); ?>
							</div>
						</td>
						<td>
							<?php
							$C = stripslashes( Collection::get( 'COMMENT_TEXT', $Comment ) );
							if ( preg_match( '/<br/i', $C ) )
							{
								echo $C;
							}
							else
							{
								echo nl2br( $C );
							}
							?>
						</td>
					</tr>	
					<?php
				}
			}
			else
			{
				?>
				<tr>
					<td colspan="2" class="center">
						<?php echo Text::_( 'No Comments yet' ); ?>
					</td>
				</tr>	
				<?php
			}
			?>
		</table>
		<?php
		$Content = ob_get_clean();
		//ob_clean();
		return $Content;

	}

	public static function getExistsRow( $FlowID, $TaskID, $AttributeID )
	{
		$Query = 'select t.id from cws_flow_params t where t.flow = ' . $FlowID
						. ' and t.task_id = ' . $TaskID
						. ' and t.attribute = ' . $AttributeID
		;
		return (int) DB::LoadResult( $Query );

	}

	public static function getRelatedFlows( $WF_ID )
	{
		$Query = ' select '
						. ' w.id, '
						. ' w.title, '
						. ' w.description '
						. ' from hrs_workflows w '
						. ' where '
						. ' w.id in ('
						. ' select '
						. ' t.workflow_idx '
						. ' from REL_WORKFLOW_WORKFLOW t '
						. ' where t.workflow_id = ' . $WF_ID
						. ' union all '
						. ' select '
						. ' t.workflow_id '
						. ' from REL_WORKFLOW_WORKFLOW t '
						. ' where '
						. ' t.workflow_idx = ' . $WF_ID
						. ' ) '
						. ' and w.state > -1'
		;
		return DB::LoadObjectList( $Query );

	}

	public static function CheckWorkFlowDeny( $IDx )
	{
		$FullAccess = array_flip( explode( ',', Helper::getConfig( 'full_access_right' ) ) );
		$ID = Helper::CleanNumber( $IDx );
		if ( empty( $ID ) )
		{
			return true;
		}
		$UserID = (int) Users::GetUserData( 'ID' );

		if ( isset( $FullAccess[$UserID] ) )
		{
			return false;
		}
		$Query = ' select '
						. ' m.id '
						. ' from '
						. ' ( '
						. ' select '
						. ' w.id '
						. ' from hrs_workflows w '
						. ' left join lib_limit_app_types f on f.id = w.flow '
						. ' left join cws_search_categories sc on sc.id = f.search_category '
						. ' where '
						. ' w.state > -1 '
						. ' and f.search_category in ('
						. ' select a.id '
						. ' from acl a '
						. ' where '
						. ' a.space = \'cws_search_categories\' '
						. ' and a.role = ' . (int) Users::GetUserData( 'USER_ROLE' )
						. ' ) '
						. ' union all '
						. ' select tk.workflow_id '
						. ' from hrs_tasks tk '
						. 'where tk.task_actor in '
						. ' (select wc.worker from rel_worker_chief wc where wc.chief = ' . $UserID . ' ) '
						. 'union all '
						. 'select wf.id '
						. ' from hrs_workflows wf '
						. ' where wf.owner in '
						. ' (select wc.worker from rel_worker_chief wc where wc.chief =   ' . $UserID . ') '
						. ' union all '
						. ' select tk.workflow_id '
						. ' from hrs_tasks tk '
						. ' where tk.task_actor_group in '
						. ' (select wg.group_id '
						. ' from rel_workers_groups wg '
						. ' where wg.worker in (select wc.worker '
						. ' from rel_worker_chief wc '
						. ' where wc.chief =   ' . $UserID . ')) '
						. ' union all '
						. ' select t.id '
						. ' from hrs_workflows t '
						. ' left join cws_workers w on w.id = t.owner '
						. ' left join lib_limit_app_types f on f.id = t.flow '
						. ' where '
						. ' t.state > -1 '
						. ' and t.id in ( '
						. ' select distinct (ts.workflow_id) '
						. ' from hrs_tasks ts '
						. ' where ts.task_actor = ' . $UserID
						. ' or ts.task_actor_group in '
						. ' ( '
						. ' select t.group_id from rel_workers_groups t where worker = ' . $UserID
						. ' ) '
						. ' ) '
						. ' ) m '
						. ' where m.id in ('
						. ' select ' . $ID
						. ' from dual '
						. ' union all '
						. ' select w1.workflow_idx '
						. ' from rel_workflow_workflow w1 '
						. ' where w1.workflow_id = ' . $ID
						. ' union all '
						. ' select w1.workflow_id '
						. ' from rel_workflow_workflow w1 '
						. ' where w1.workflow_idx = ' . $ID
						. ' ) '
		;

		$Result = (int) DB::LoadResult( $Query );
		if ( $Result )
		{
			return false;
		}
		else
		{
			return true;
		}

	}

	public static function SaveWFRelData( $WID, $WIDx )
	{
		$Query = ' insert '
						. ' into REL_WORKFLOW_WORKFLOW '
						. ' ( '
						. ' WORKFLOW_ID, '
						. ' WORKFLOW_IDX'
						. ' ) '
						. ' values '
						. ' ( '
						. $WID . ' , '
						. $WIDx
						. ') '
		;
		$Result = DB::Insert( $Query );
		if ( $Result )
		{
			self::log( $WID, 0, TASK_LOG_CONNECT, $WIDx );
			self::log( $WIDx, 0, TASK_LOG_CONNECT, $WID );
		}
		return $Result;

	}

	public static function SaveBindedData( $WID, $WIDx )
	{
		$Query = ' insert '
						. ' into rel_workflow_letters_answers '
						. ' ( '
						. ' WORKFLOW_ID, '
						. ' ANSWER_IDX'
						. ' ) '
						. ' values '
						. ' ( '
						. $WID . ' , '
						. $WIDx
						. ') '
		;
		$Result = DB::Insert( $Query );
		return $Result;

	}

	/**
	 * 
	 * @param int $DURATION
	 * @return String
	 */
	public static function CalculateDueDate( $DURATIONiN, $TaskID, $startDate = 'now' )
	{
		$name = 'due_date';
		$Space = 'params';
		$value = C::_( $Space . '.' . $TaskID . '.' . $name, 'post', null );
		if ( !empty( $value ) )
		{
			$Date = new PDate( $value );
			return $Date->toFormat( '%Y-%m-%d 23:59:59' );
		}
		$AllHoldays = Helper::GetAllHoldays();
		$DURATION = (int) $DURATIONiN;
		$DaySec = 0;
		$DateStart = new PDate( $startDate );
		$day = 0;
		while ( $DURATION >= 0 )
		{
			$DaySec = $day * 60 * 60 * 24;
			$Date = new PDate( $DateStart->toUnix() + $DaySec );
			$FDate = $Date->toFormat( '%Y-%m-%d' );
			$DayNum = $Date->toFormat( '%w' );
			$day++;
			if ( isset( $AllHoldays[$FDate] ) )
			{
				continue;
			}
			if ( $DayNum == 0 or $DayNum == 6 )
			{
				continue;
			}
			--$DURATION;
		}
		return $Date->toFormat( '%Y-%m-%d 23:59:59' );

	}

	public static function GetAllHoldaysJS()
	{
		$Query = 'select '
						. ' t.lib_day || \'-\' || t.lib_month holiday '
						. ' from hrs.LIB_HOLIDAYS t ';
		return DB::LoadList( $Query, 'HOLIDAY' );

	}

	public static function CalculateDay( $startDate, $endDate )
	{
		$StartDate = substr( $startDate, 0, 10 );
		$EndDate = substr( $endDate, 0, 10 );
		$date1 = strtotime( $StartDate . " 0:00:00" );
		$date2 = strtotime( $EndDate . " 23:59:59" );
		$Rem = $date2 - $date1;
		$DaySec = 86400;
		$res = (int) ($Rem / $DaySec);
		return $res + 1;

	}

	public static function getEditButton( $TASK_ID, $FLOW )
	{
		ob_start();
		?>
		<a href="?option=editattributes&TASK_ID=<?php echo $TASK_ID; ?>&FLOW=<?php echo $FLOW; ?>" class="btn btn-danger">
			<?php echo Text::_( 'Edit Data' ); ?>
		</a>
		<?php
		$return = ob_get_clean();
		return $return;

	}

	public static function IsOwner( $FLOW )
	{
		$Query = ' select id from hrs_workflows w where id = ' . $FLOW . ' and w.owner = ' . Users::GetUserID();
		return (bool) DB::LoadResult( $Query );

	}

	public static function CanEditAttribs( $FLOW )
	{
		$Query = ' select id from hrs_workflows w where id = ' . $FLOW . ' and w.state = 0 and w.owner = ' . Users::GetUserID();
		return (bool) DB::LoadResult( $Query );

	}

	public static function RenderSubcribe( $WF_ID )
	{
		$UserID = Users::GetUserID();
		$DBStatus = Alerts::GetSubscribeStatus( $WF_ID, $UserID );
		$checked = '';
		if ( $DBStatus )
		{
			$checked = 'checked';
		}
		ob_start();
		?>
		<?php echo Text::_( 'Process Subscribe' ); ?> : 
		<input <?php echo $checked; ?> data-toggle="toggle" data-onstyle="success" data-offstyle="danger" data-id="<?php echo $WF_ID; ?>" type="checkbox" id="subscribe_process" class="skip_this" data-on="<?php echo Text::_( 'On' ); ?>" data-off="<?php echo Text::_( 'Off' ); ?>" />
		<?php
		$Content = ob_get_clean();
		$JS = '$("#subscribe_process").change(function(){'
						. 'var $WFID = $(this).attr("data-id");'
						. 'if($(this).prop("checked"))'
						. '{'
						. ' var $State = 1 ;'
						. '}'
						. 'else'
						. '{'
						. ' var $State = 0 ;'
						. '}'
						. 'SetAlerts($WFID, $State);'
						. '});';
		Helper::SetJS( $JS );
		return $Content;

	}

	public static function GetFlowAttributesMarkers2( $WorkFlow, $Regex = 1 )
	{
		$Return = array();
		if ( $WorkFlow )
		{
			$query = 'select '
							. ' a.marker, '
							. ' p.* '
							. ' from CWS_FLOW_PARAMS p '
							. ' LEFT JOIN lib_flow_elements_attributes a on a.id = p.attribute '
							. ' where '
							. ' p.flow = ' . DB::Quote( $WorkFlow ) . ' '
			;
			$Data = DB::LoadObjectList( $query );
			foreach ( $Data as $Item )
			{
				$ID = C::_( 'ID', $Item );
				$Value = C::_( 'TDATA', $Item );
				if ( is_array( $Value ) )
				{
					continue;
				}
				$Key = trim( C::_( 'MARKER', $Item ) );
				$IDKey = 'a' . $ID;
				if ( $Key )
				{
					if ( $Regex )
					{
						$RKey = '/\$\{' . $Key . '\}/i';
					}
					else
					{
						$RKey = $Key;
					}
					$Return[$RKey] = $Value;
				}
				if ( $Regex )
				{
					$RKey = '/\$\{' . $IDKey . '\}/i';
				}
				else
				{
					$RKey = $IDKey;
				}
				$Return[$RKey] = $Value;
			}
		}
		$Attributes = Request::getVar( 'attributes', array() );
		$IDx = preg_replace( '/[^0-9,]/i', '', implode( ',', array_keys( $Attributes ) ) ) . "";
		if ( $IDx )
		{
			$Query = 'select '
							. ' a.marker,'
							. ' a.id '
							. ' from lib_flow_elements_attributes a '
							. ' where '
							. ' a.id in( ' . $IDx . ') '
			;
			$FData = DB::LoadObjectList( $Query, 'ID' );
			foreach ( $FData as $Item )
			{
				$ID = C::_( 'ID', $Item );
				$Value = C::_( 'a' . $ID, $Attributes );
				if ( is_array( $Value ) )
				{
					continue;
				}
				$Key = trim( C::_( 'MARKER', $Item ) );
				$IDKey = 'a' . $ID;
				if ( !empty( $Key ) )
				{
					if ( $Regex )
					{
						$RKey = '/\$\{' . $Key . '\}/i';
					}
					else
					{
						$RKey = $Key;
					}
					$Return[$RKey] = $Value;
				}
				if ( $Regex )
				{
					$RKey = '/\$\{' . $IDKey . '\}/i';
				}
				else
				{
					$RKey = $IDKey;
				}


				$Return[$RKey] = $Value;
			}
		}

		if ( empty( $Return ) )
		{
			$Return['/\$\{[A-Z0-9_\-]+\}/i'] = '-99999999999999';
		}
		return $Return;

	}

	public static function GetFlowAttributesMarkers( $WorkFlow, $Regex = 1 )
	{
		$Return = array();

		if ( $WorkFlow )
		{
			$query = 'select '
							. ' a.marker, '
							. ' p.*, '
							. ' nvl(p.tdata, -99999999999999) tdata '
							. ' from CWS_FLOW_PARAMS p '
							. ' LEFT JOIN lib_flow_elements_attributes a on a.id = p.attribute '
							. ' where '
							. ' p.flow = ' . DB::Quote( $WorkFlow ) . ' '
							. ' and a.marker is not null'
			;

			$Data = DB::LoadObjectList( $query, 'MARKER' );
			$Return = array();
			foreach ( $Data as $Key => $Item )
			{
				if ( $Regex )
				{
					$RKey = '/\$\{' . $Key . '\}/i';
				}
				else
				{
					$RKey = $Key;
				}
				$Return[$RKey] = C::_( 'TDATA', $Item );
			}
		}

		$Attributes = Request::getVar( 'attributes', array() );
		$IDx = preg_replace( '/[^0-9,]/i', '', implode( ',', array_keys( $Attributes ) ) ) . "";
		if ( $IDx )
		{
			$Query = 'select '
							. ' a.marker,'
							. ' a.id '
							. ' from lib_flow_elements_attributes a '
							. ' where '
							. ' a.id in( ' . $IDx . ') '
							. ' and a.marker is not null '
			;
			$FData = DB::LoadObjectList( $Query, 'ID' );
			foreach ( $FData as $Item )
			{
				$ID = C::_( 'ID', $Item );
				$Value = C::_( 'a' . $ID, $Attributes );
				if ( is_array( $Value ) )
				{
					continue;
				}
				$Key = C::_( 'MARKER', $Item );
				if ( $Regex )
				{
					$RKey = '/\$\{' . $Key . '\}/i';
				}
				else
				{
					$RKey = $Key;
				}
				$Return[$RKey] = $Value;
			}
		}

		if ( empty( $Return ) )
		{
			$Return['/\$\{[A-Z0-9_\-]+\}/i'] = '-99999999999';
		}
		return $Return;

	}

	public static function GetFlowAttributesByID( $WorkFlow = 0 )
	{
		$Return = array();
		if ( $WorkFlow )
		{
			$query = 'select '
							. ' a.id, '
							. ' a.marker, '
							. ' p.flow, '
							. ' p.task_id, '
							. ' p.attribute, '
							. ' p.actor, '
							. ' p.checked_out, '
							. ' p.checked_out_date, '
							. ' p.cref, '
							. ' p.tdata, '
							. ' p.tdata1, '
							. ' p.tdata2, '
							. ' p.tdata3, '
							. ' p.tdata4'
							. ' from CWS_FLOW_PARAMS p '
							. ' LEFT JOIN lib_flow_elements_attributes a on a.id = p.attribute '
							. ' where '
							. ' p.flow = ' . DB::Quote( $WorkFlow ) . ' '
//							. ' and a.marker is not null'
			;
			$Data = DB::LoadObjectList( $query, 'ID' );
			$Return = array();
			foreach ( $Data as $Key => $Item )
			{
				$Return[$Key] = C::_( 'TDATA', $Item );
			}
		}

		$Attributes = Request::getVar( 'attributes', array() );
		$IDx = preg_replace( '/[^0-9,]/i', '', implode( ',', array_keys( $Attributes ) ) ) . "";
		if ( $IDx )
		{
			$Query = 'select '
							. ' a.marker,'
							. ' a.id '
							. ' from lib_flow_elements_attributes a '
							. ' where '
							. ' a.id in( ' . $IDx . ') '
//							. ' and a.marker is not null '
			;
			$FData = DB::LoadObjectList( $Query, 'ID' );
			foreach ( $FData as $Item )
			{
				$Key = C::_( 'ID', $Item );
				$Return[$Key] = C::_( 'a' . $Key, $Attributes );
			}
		}
		if ( empty( $Return ) )
		{
			$Return['/\$\{[A-Z0-9]+\}/i'] = '-99999999999999';
		}
		return $Return;

	}

	public static function ParseBoolean( $String, $ID = null, $Task = null )
	{
		$IF = trim( $String );
		if ( empty( $IF ) )
		{
			return true;
		}
		return (bool) eval( 'return ' . $IF . ';' );

	}

	public static function CheckAttributeIf( $Attribute, $WorkFlowID = 0 )
	{
		$Markers = self::GetFlowAttributesMarkers( $WorkFlowID );
		$IF = trim( C::_( 'SHOWIF', $Attribute ) );
		if ( !empty( $IF ) )
		{
			$Patterns = array_keys( $Markers );
			$Replacements = array_values( $Markers );
			$IFx = preg_replace( '/\$\{[a-z0-9_-]+\}/i', '-99999999999999', preg_replace( $Patterns, $Replacements, $IF ) );
			return self::ParseBoolean( $IFx, $WorkFlowID, $Attribute );
		}
		return true;

	}

	public static function CheckIf( $Task, $WorkFlowID = 0 )
	{
		$Markers = self::GetFlowAttributesMarkers( $WorkFlowID );
		$IF = trim( C::_( 'IF', $Task ) );
		if ( !empty( $IF ) )
		{
			$Patterns = array_keys( $Markers );
			$Replacements = array_values( $Markers );
			$IFx = preg_replace( '/\$\{[a-z0-9_-]+\}/i', '-99999999999999', preg_replace( $Patterns, $Replacements, $IF ) );
			return self::ParseBoolean( $IFx, $WorkFlowID, $Task );
		}
		return true;

	}

	public static function CheckActionIf( $Task, $WorkFlowID = 0 )
	{
		$Markers = self::GetFlowAttributesMarkers( $WorkFlowID );
		$IF = trim( C::_( 'AIF', $Task ) );
		if ( !empty( $IF ) )
		{
			$Patterns = array_keys( $Markers );
			$Replacements = array_values( $Markers );
			$IFx = preg_replace( '/\$\{[a-z0-9_-]+\}/i', '-99999999999999', preg_replace( $Patterns, $Replacements, $IF ) );
			return self::ParseBoolean( $IFx, $WorkFlowID, $Task );
		}
		return true;

	}

	public static function ExecCondition( $Task, $WorkFlowID = 0 )
	{
		$Markers = self::GetFlowAttributesMarkers( $WorkFlowID );
		$Condition = trim( C::_( 'GROUP_CONDITION', $Task ) );
		$Result = 0;
		if ( !empty( $Condition ) )
		{
			$Patterns = array_keys( $Markers );
			$Replacements = array_values( $Markers );
			$ConditionX = ' $Result = (' . preg_replace( '/\$\{[a-z0-9_-]+\}/i', '-99999999999999', preg_replace( $Patterns, $Replacements, $Condition ) ) . ' ); ';
			eval( $ConditionX );
		}
		return $Result;

	}

	public static function ExecAttributeCondition( $Attribute, $WorkFlowID = 0 )
	{
		$Markers = self::GetFlowAttributesMarkers2( $WorkFlowID );
		$Condition = trim( C::_( 'IF', $Attribute ) );
		$Result = 0;
		if ( !empty( $Condition ) )
		{
			$Patterns = array_keys( $Markers );
			$Replacements = array_values( $Markers );
			$ConditionX = ' $Result = (' . preg_replace( '/\$\{[a-z0-9_-]+\}/i', '-99999999999999', preg_replace( $Patterns, $Replacements, $Condition ) ) . ' ); ';
			eval( $ConditionX );
		}
		return $Result;

	}

	public static function _Split( $SDATA )
	{
		$TDATAx = array();
		$Index = 0;
		$MaxLength = 3500;
		if ( strlen( $SDATA ) > $MaxLength )
		{
			$MData = explode( ' ', str_replace( '&nbsp;', ' ', trim( $SDATA ) ) );
			foreach ( $MData as $Ph )
			{
				$TDATAxT = C::_( $Index, $TDATAx ) . ' ' . $Ph;
				$TDATAx[$Index] = $TDATAxT;
				if ( strlen( $TDATAxT ) >= $MaxLength )
				{
					$Index++;
				}
			}
			$SDATA = $TDATAx;
		}
		return $SDATA;

	}

	public static function GetMode( $WorkFlowID )
	{
		$Xmode = Request::getInt( 'xmode', -1 );
		if ( $Xmode < 0 )
		{
			$WData = TaskHelper::getWorkFlow( $WorkFlowID );
			$Xmode = (int) C::_( 'XMODE', $WData, 0 );
		}
		return $Xmode;

	}

	public static function GetInitiator( $WorkFlowID )
	{
		$WData = TaskHelper::getWorkFlow( $WorkFlowID );
		return C::_( 'OWNER', $WData );

	}

	public static function GetPontTasks( $Flow, $WorkFlow )
	{
		$UserID = Users::GetUserID();
		$Query = ' select '
						. ' e.id LIB_TASK_ID, '
						. ' w.id WORKFLOW_ID, '
						. ' e.lib_title TASK_TITLE, '
						. ' e.lib_desc TASK_DESCRIPTION, '
						. ' e.duration, '
						. ' w.title, '
						. ' w.description, '
						. ' t.task_id old_id '
						. ' from lib_flow_elements e '
						. ' left join hrs_workflows w on w.flow = e.flow '
						. ' left join hrs_tasks t on t.workflow_id = w.id and t.lib_task_id = e.id and w.id = ' . $WorkFlow
						. ' where '
						. ' e.active = 1 '
						. ' and e.flow = ' . $Flow
						. ' and e.rollback_point = 1 '
						. ' and w.state = 1 '
						//	. ' and w.owner = ' . $UserID
						. ' and t.task_actor = ' . $UserID
						. ' order by e.ordering '
		;
		$FData = DB::LoadObjectList( $Query, 'OLD_ID' );
		return $FData;

	}

	public static function SendAlert( $Data, $ID )
	{
		$AlertsEnabled = self::GetCommentAlertState( $ID );

		if ( $AlertsEnabled == 0 )
		{
			return false;
		}
		$Workers = self::GetAlertWorkers( $ID );
		$WorkflowData = self::getWorkFlow( $ID );
		foreach ( $Workers as $Worker )
		{
			self::SendCommentToUser( $ID, $Worker, $Data, $WorkflowData );
		}
		return true;

	}

	public static function GetAlertWorkers( $ID )
	{
		$Query = 'select '
						. ' k.worker '
						. ' from ('
						. ' select '
						. ' wg.worker '
						. ' from hrs_tasks t '
						. ' left join rel_workers_groups wg on wg.group_id = t.task_actor_group '
						. ' where '
						. ' workflow_id =  ' . DB::Quote( $ID )
						. ' and t.task_actor_group > 0 '
						. ' union all '
						. ' select '
						. ' m.task_actor worker '
						. ' from hrs_tasks m '
						. ' where '
						. ' m.workflow_id = ' . DB::Quote( $ID )
						. ' and m.task_actor_group = 0'
						. ' ) K '
						. ' left join rel_workflow_unsubscribe wu on wu.worker = k.worker '
						. ' where '
						. ' wu.worker is null'
		;
		return DB::LoadList( $Query, 'WORKER' );

	}

	public static function GetCommentAlertState( $ID )
	{
		$Query = 'select '
						. ' f.comments_subscribe '
						. ' from hrs_workflows w '
						. ' left join lib_limit_app_types f on f.id = w.flow '
						. ' where '
						. ' w.id = ' . DB::Quote( $ID )
		;
		return DB::LoadResult( $Query );

	}

	public static function SendCommentToUser( $NewID, $User, $TaskData, $WorkFlowData )
	{
		$AuthorData = Users::getUser( C::_( 'COMMENT_USER', $TaskData ) );
		$data = array();
		$UserData = Users::getUser( $User );
		$Email = C::_( 'EMAIL', $UserData );
		if ( empty( $Email ) )
		{
			return true;
		}

		$MyUser = Users::GetUserID();
		if ( $MyUser == $User )
		{
			return true;
		}
		$URI = URI::getInstance();
		$URL = clone $URI;

		$URI->setVar( 'option', 'print' );
		$URI->setVar( 'wflow', $NewID );

		$Subject = Text::_( 'New Comment' ) . ' - ' . C::_( 'TITLE', $WorkFlowData );
		$data['LINK'] = $URI->toString();
		$data['GREETING'] = Text::_( 'EMAIL_GREETING' ) . ' ' . $UserData->FIRSTNAME . ' ' . $UserData->LASTNAME;
		$data['EMAILTITLE'] = $Subject;
		$data['MESSAGE'] = Text::_( 'COMMENT_EMAIL_MESSAGE' );
		$data['TASKDETALES'] = Text::_( 'COMMENT_EMAIL_TASKDETALES' );
		$data['TASKTITLE'] = Text::_( 'COMMENT_EMAIL_TASKTITLE' ) . ' : ' . nl2br( C::_( 'COMMENT_TEXT', $TaskData ) );
		$data['DUEDATE'] = Text::_( 'COMMENT_EMAIL_AUTHOR' ) . ' : ' . $AuthorData->FIRSTNAME . ' ' . $AuthorData->LASTNAME;
		$data['COMMENT_DATE'] = Text::_( 'COMMENT_DATE' ) . ' : ' . PDate::Get( C::_( 'COMMENT_DATE', $TaskData ) )->toFormat( '%H:%M:%S %d-%m-%Y' );
		$data['WORKFLOW'] = Text::_( 'COMMENT_EMAIL_WORKFLOW' ) . ' : ' . C::_( 'TITLE', $WorkFlowData );
		$data['LINKTITLE'] = Text::_( 'COMMENT_EMAIL_LINKTITLE' );
		$URL->setVar( 'option', 'comment' );
		$URL->setVar( 'task', 'unsubscribe' );
		$URL->setVar( 'wflow', $NewID );
		$URL->setVar( 'worker', $User );
		$data['UNSUBSCRIBE_LINK'] = $URL->toString();
		$data['UNSUBSCRIBE_TITLE'] = Text::_( 'UNSUBSCRIBE_TITLE' );
		$EmailSend = Email::getInstance();
		$EmailSend->setEmailData( $data );
//		$Email = 'teimuraz.kevlishvili@magticom.ge';
		return $EmailSend->send( $Email, $Subject, 'newcomment' );

	}

	public static function getAttributeParams( $AttributeID )
	{
		$Query = 'select a.*, t.id
  from CWS_FLOW_PARAMS t
  left join lib_flow_elements_attributes a
    on a.id = t.attribute
						where t.id =' . DB::Quote( $AttributeID );
		return DB::LoadObject( $Query );

	}

	public static function FlowExists( $WorkFlowID )
	{
		$Query = 'select t.task_id from hrs_tasks t where t.workflow_id = ' . DB::Quote( $WorkFlowID );
		return DB::LoadResult( $Query );

	}

	public static function GetActors( $WorkFlow, $TaskGroup, $Task )
	{
		$ActiorWorkers = array();
		$ActiorGroup = array();
		switch ( $TaskGroup )
		{
			/*
			  case -5:
			  $ActiorWorkers[] = Users::GetUserID();
			  break;
			  case -9:
			  $AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
			  $Values = Helper::CleanArray( TaskHelper::GetFlowAttributesByID( $WorkFlowID ), 'Str' );
			  if ( count( $Values ) )
			  {
			  $ActiorWorkers[] = C::_( $AttrID, $Values );
			  }
			  break;
			  case -12:
			  $AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
			  $Values = Helper::CleanArray( TaskHelper::GetFlowAttributesByID( $WorkFlowID ), 'Str' );
			  $Users = Helper::CleanArray( explode( ',', C::_( $AttrID, $Values ) ) );
			  if ( count( $Users ) )
			  {
			  $ActiorWorkers = array_merge( $ActiorWorkers, $Users );
			  }
			  break;
			  case -19:
			  $ActiorGroup[] = TaskHelper::ExecCondition( $Task, $WorkFlowID );
			  break;
			  case -11:
			  $AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
			  $Values = TaskHelper::GetFlowAttributesByID( $WorkFlowID );
			  $Groups = Helper::CleanArray( C::_( $AttrID, $Values ) );
			  if ( Count( $Groups ) == 0 )
			  {
			  continue;
			  }
			  foreach ( $Groups as $Group )
			  {
			  $ActiorGroup[] = $Group;
			  }
			  break;
			  case -13:
			  $User = Users::GetUserData( 'DIRECTCHIEF' );
			  $ActiorWorkers[] = $User;
			  break;
			  case -15:
			  $Flow = self::getWorkFlow( $WorkFlowID );
			  $ActiorWorkers[] = C::_( 'OWNER', $Flow );
			  $AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
			  $Values = TaskHelper::GetFlowAttributesByID( $WorkFlowID );
			  $Group = (int) C::_( $AttrID, $Values );
			  break;
			  case -3:
			  $AltTaskID = C::_( 'LIB_GROUP_ALT', $Task, '0' );
			  $WorkFlowID = Request::getVar( 'FLOW', 0 );
			  $Tasks = TaskHelper::getAltTasks( $AltTaskID, $WorkFlowID );
			  foreach ( $Tasks as $AltTask )
			  {
			  $AltWorker = C::_( 'TASK_ACTOR', $AltTask );
			  $AltGroup = C::_( 'TASK_ACTOR_GROUP', $AltTask );
			  if ( $AltWorker )
			  {
			  $ActiorWorkers[] = $AltWorker;
			  break;
			  }
			  if ( $AltGroup )
			  {
			  $ActiorGroup[] = $AltGroup;
			  }
			  }
			  break;
			 */
			case 0:
				$ActiorWorkers[] = C::_( 'WORKER', $WorkFlow );
				break;
			case -1:
				$Object = C::_( 'WORKER', $WorkFlow );
				$ActiorWorkers = (array) self::GetDirectChiefs( $Object );
				break;
			case -2:
				$Object = Users::GetUserID();
				$Org = C::_( 'ORG', $WorkFlow );
				$ActiorWorkers = (array) self::GetCurrentUserChiefs( $Object, $Org );
				break;
			case -3:
				$Workers = C::_( 'REPLACING_WORKERS', $WorkFlow );
				$ActiorWorkers = Helper::CleanArray( explode( ',', $Workers ) );
				break;
			case -4:
				$Object = C::_( 'WORKER', $WorkFlow );
				$ActiorWorkers = (array) self::GetAdditionalChiefs( $Object );
				break;
			case -5:
				$Object = Users::GetUserID();
				$Org = C::_( 'ORG', $WorkFlow );
				$ActiorWorkers = (array) self::GetCurrentUserChiefs( $Object, $Org, false );
				break;
			default:
				$ActiorGroup[] = $TaskGroup;
				break;
		}
		return array(
				'workers' => $ActiorWorkers,
				'group' => $ActiorGroup
		);

	}

	public static function GetDirectChiefs( $Object )
	{
		return DB::LoadList( 'select '
										. ' c.chief_pid '
										. ' from rel_worker_chief c '
										. ' where '
										. ' c.worker_opid = ' . (int) $Object
										. ' and c.clevel = 0'
		);

	}

	public static function GetCurrentUserChiefs( $Object, $Org, $Direct = true )
	{
		if ( $Direct )
		{
			$Type = 0;
		}
		else
		{
			$Type = 1;
		}
		return DB::LoadList( 'select '
										. ' c.chief_pid '
										. ' from rel_worker_chief c '
										. ' where '
										. ' c.worker_pid = ' . (int) $Object
										. ' and c.org = ' . (int) $Org
										. ' and c.clevel = ' . $Type
		);

	}

	public static function GetAdditionalChiefs( $Object )
	{
		return DB::LoadList( 'select '
										. ' c.chief_pid '
										. ' from rel_worker_chief c '
										. ' where '
										. ' c.worker_opid = ' . (int) $Object
										. ' and c.clevel = 1 '
		);

	}

	public static function GetActiveTasks( $WorkFlowID )
	{
		
	}

	public static function getLimitAppType( $id = null )
	{
		if ( $id == null )
		{
			return [];
		}
		$Query = 'select '
						. ' p.* '
						. ' from lib_limit_app_types p '
						. ' where '
						. ' p.id= ' . DB::Quote( $id )
						. ' and p.active = 1 '
		;
		return DB::LoadObject( $Query );

	}

}
