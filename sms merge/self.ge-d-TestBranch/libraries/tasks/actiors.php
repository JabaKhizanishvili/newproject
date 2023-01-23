<?php

class Actiors
{
	public static function GetActiorWorkers( $Task, $Flow )
	{
		$TaskID = Collection::get( 'ID', $Task );
		$name = 'workers';
		$control_name = 'actiors';
		$id = substr( md5( rand( 0, 999999999 ) . microtime( 1 ) ), rand( 1, 20 ), 7 );
		$valueS = Collection::get( $control_name . '.' . $TaskID . '.' . $name, 'post', array() );
		If ( ($Task->LIB_GROUP == -2 || $Task->LIB_GROUP == -8) && !empty( $Task->LIB_GROUP_ATTR ) )
		{
			$Marks = TaskHelper::GetFlowAttributesMarkers( $Flow, 0 );
			$Attributes = Request::getVar( 'attributes', array() );
			$AtributeMarker = C::_( 'MARKER', TaskHelper::getAttribute( $Task->LIB_GROUP_ATTR ) );

			$Workers = explode( ',', C::_( 'a' . $Task->LIB_GROUP_ATTR, $Attributes, C::_( $AtributeMarker, $Marks ) ) );
			$valueS = array_flip( array_flip( array_merge( $valueS, $Workers ) ) );
		}

		if ( is_array( $valueS ) )
		{
			$value = implode( ',', $valueS );
		}
		else
		{
			$value = $valueS;
		}


		$ID = $control_name . $TaskID . $name . $id;
		$link = '?';
		$width = '95%';
		$height = '95%';
		$uri = URI::getInstance( $link );
		$uri->setVar( 'option', 'workersmodal' );
		$uri->setVar( 'tmpl', 'modal' );
		$uri->setVar( 'js', 'getWorkersData' );
		$uri->setVar( 'param', $ID );
		$uri->setVar( 'width', $width );
		$uri->setVar( 'iframe', 'true' );
		$uri->setVar( 'height', $height );
		ob_start();
		?>
		<div class="form_field">
			<div class="form_item">
				<div class="form_label">
					<label class="form_param_lbl" <?php echo $ID; ?>>
						<?php echo Text::_( 'workers' ); ?>
					</label>
				</div>
				<div class="form_input_area">
					<div class="WorkersBlock">
						<div class="WorkersContainer" id="WorkersContainer<?php echo $ID; ?>"></div>
						<div class="cls"></div>
						<div class="WorkersButtons">
							<a class="butn" rel="iframe-<?php echo $ID; ?>" href="<?php echo $uri->toString(); ?>">
								<?php echo Text::_( 'Add' ); ?> 
							</a>

							<button type="button" class="btn btn-danger btn-sm" onclick="javascript:if (confirm('<?php echo Text::_( 'Are you Sure?' ); ?>')) {
		                $('#WorkersContainer<?php echo $ID; ?>').html('');
		                $('#<?php echo $ID; ?>').val('');
		              }
		              ;">
												<?php echo Text::_( 'Clear Data' ); ?> 
							</button>
							<div class="cls"></div>
							<input type="hidden" name="<?php echo $control_name . '[' . $TaskID . '][' . $name . ']'; ?>" id="<?php echo $ID; ?>" value="<?php echo $value; ?>" class="WorkersData" />
						</div>
					</div>
				</div>
				<div class="cls"></div>
			</div>
		</div>
		<?php
		$return = ob_get_clean();
		$JS = '$("a[rel^=\'iframe-' . $ID . '\']") . prettyPhoto();';
		$JS .= 'var WorkersData' . $ID . '="' . $value . '";'
						. 'if ( WorkersData' . $ID . ' != "" )'
						. '{'
						. 'getWorkersData( WorkersData' . $ID . ', "' . $ID . '");'
						. '}';

		Helper::SetJS( $JS );

		return $return;

	}

	public static function GetChiefsWorkers( $Task )
	{
		$User = Users::GetUserID();
//		$User = 41787;
		$Workers = Helper::getChiefWorkers( $User );
		$TaskID = Collection::get( 'ID', $Task );
		$name = 'workers';
		$control_name = 'actiors';
		$id = substr( md5( rand( 0, 999999999 ) . microtime( 1 ) ), rand( 1, 20 ), 7 );
		$ID = $control_name . $TaskID . $name . $id;
		$Values = Collection::get( $control_name . '.' . $TaskID . '.' . $name, 'post', array() );
		ob_start();
		?>
		<div class="form_field">
			<div class="form_item">
				<div class="form_label">
					<label class="form_param_lbl" <?php echo $ID; ?>>
						<?php echo Text::_( 'Workers' ); ?>
					</label>
				</div>
				<div class="form_input_area">
					<div class="">
						<?php
						foreach ( $Workers as $key => $Item )
						{
							$SubWorkers = $Workers = Helper::getChiefWorkers( $key );
							$Checked = is_null( C::_( $key, $Values, null ) ) ? '' : ' checked="checked" ';
							?>
							<div class="WorkerItem">
								<div class="checkbox">
									<label for="<?php echo $ID ?>_<?php echo $key; ?>">
										<input type="checkbox" value="<?php echo $key; ?>" <?php echo $Checked; ?> name="<?php echo $control_name . '[' . $TaskID . '][' . $name . '][]'; ?>" id="<?php echo $ID ?>_<?php echo $key; ?>" />
										<?php echo C::_( 'WORKER', $Item ) ?>
									</label>
									<?php
									if ( count( $SubWorkers ) )
									{
										?>
										<span class="expand_workers" id="<?php echo $ID ?>_<?php echo $key; ?>_worker">
											<i class="glyphicon glyphicon-chevron-up"></i>
											<i class="glyphicon glyphicon-chevron-down"></i>
										</span>
										<?php
									}
									?>	
									<div class="cls"></div>
								</div>
								<?php
								if ( count( $SubWorkers ) )
								{
									?>
									<div class="SubWorkerItem" id="<?php echo $ID ?>_<?php echo $key; ?>_worker_data">
										<?php
										foreach ( $SubWorkers as $Skey => $SItem )
										{
											$Checked = is_null( C::_( $Skey, $Values, null ) ) ? '' : ' checked="checked" ';
											?>
											<div class="checkbox">
												<label for="<?php echo $ID ?>_<?php echo $Skey; ?>">
													<input type="checkbox" value="<?php echo $Skey; ?>" <?php echo $Checked; ?> name="<?php echo $control_name . '[' . $TaskID . '][' . $name . '][]'; ?>" id="<?php echo $ID ?>_<?php echo $Skey; ?>" />
													<?php echo C::_( 'WORKER', $SItem ) ?>
												</label>
											</div>
											<div class="cls"></div>
											<?php
										}
										?>
									</div>
									<div class="cls"></div>
									<?php
								}
								?>
								<div class="cls"></div>
							</div>
							<?php
						}
						?>
						<div class="cls"></div>
					</div>
					<?php // echo $Select;      ?>
				</div>
				<div class="cls"></div>
			</div>
		</div>
		<?php
		$return = ob_get_clean();
		return $return;

	}

	public static function GetActiorWorkersGroup( $Task )
	{
		$TaskID = Collection::get( 'ID', $Task );
		$name = 'groups';
		$control_name = 'actiors';
		$id = substr( md5( rand( 0, 999999999 ) . microtime( 1 ) ), rand( 1, 20 ), 7 );
		$ID = $control_name . $TaskID . $name . $id;
		$UserRole = Users::GetUserData( 'USER_ROLE' );
		$Values = Collection::get( $control_name . '.' . $TaskID . '.' . $name, 'post', array() );
		$Query = 'select '
						. ' wg.id, '
						. ' wg.lib_title '
						. ' from rel_roles_groups t '
						. ' left join lib_workers_groups wg on wg.id = t.group_id'
						. ' where'
						. ' t.role =' . $UserRole
						. ' and wg.active =1 '
						. ' and wg.dinamic =1 '
						. ' order by wg.ordering asc, wg.lib_title asc ';

		$data = (array) DB::LoadObjectList( $Query, 'ID' );
//		$options = array();
//		foreach ( $data as $dat )
//		{
//			$val = $dat->ID;
//			$text = $dat->LIB_TITLE;
//			$options[] = HTML::_( 'select.option', $val, $text );
//		}
//		$Select = HTML::_( 'select.genericlist', $options, $control_name . '[' . $TaskID . '][' . $name . '][]', ' size="5" class="filter_droplist" multiple="multiple" ', 'value', 'text', $Values, $ID );
		ob_start();
		?>
		<div class="form_field">
			<div class="form_item">
				<div class="form_label">
					<label class="form_param_lbl" <?php echo $ID; ?>>
						<?php echo Text::_( 'groups' ); ?>
					</label>
				</div>
				<div class="form_input_area">
					<div class="">
						<?php
						foreach ( $data as $key => $Item )
						{
							$Checked = is_null( C::_( $key, $Values, null ) ) ? '' : ' checked="checked" ';
							?>
							<div class="checkbox">
								<label for="<?php echo $ID ?>_<?php echo $key; ?>">
									<input type="checkbox" value="<?php echo $key; ?>" <?php echo $Checked; ?> name="<?php echo $control_name . '[' . $TaskID . '][' . $name . '][]'; ?>" id="<?php echo $ID ?>_<?php echo $key; ?>" />
									<?php echo C::_( 'LIB_TITLE', $Item ) ?>
								</label>
							</div>
							<div class="cls"></div>
							<?php
						}
						?>
						<div class="cls"></div>
					</div>
					<?php // echo $Select;       ?>
				</div>
				<div class="cls"></div>
			</div>
		</div>
		<?php
		$return = ob_get_clean();
		return $return;

	}

	public static function GetGroupWorkers( $WORKERS, $TaskID )
	{
		if ( !is_array( $WORKERS ) )
		{
			$WORKERS = explode( ',', $WORKERS );
		}

		$IDX = Helper::CleanArray( $WORKERS );
		if ( empty( $IDX ) )
		{
			return '';
		}

		$Query = 'select '
						. ' t.id, '
						. ' t.firstname, '
						. ' t.Lastname '
						. ' from CWS_WORKERS t '
						. ' where '
						. ' t.id in (' . implode( ',', $IDX ) . ') '
						. ' order by t.lastname asc';

		$data = DB::LoadObjectList( $Query );
		$options = array();
		$options[] = HTML::_( 'select.option', 0, Text::_( 'WORKERS FILTER' ) );
		foreach ( $data as $dat )
		{
			$val = $dat->ID;
			$text = $dat->FIRSTNAME . ' ' . $dat->LASTNAME;
			$options[] = HTML::_( 'select.option', $val, $text );
		}
		$name = 'workers';
		$control_name = 'actiors';
		$ID = substr( md5( rand( 0, 999999999 ) . microtime( 1 ) ), rand( 1, 20 ), 7 );
		$value = Collection::get( $control_name . '.' . $TaskID . '.' . $name, 'post', array() );
		$Select = HTML::_( 'select.genericlist', $options, $control_name . '[' . $TaskID . '][' . $name . '][]', ' class="filter_droplist" ', 'value', 'text', $value, $ID );
		ob_start();
		?>
		<div class="form_field">
			<div class="form_item">
				<div class="form_label">
					<label class="form_param_lbl Georgian">
						<?php echo Text::_( 'Task executive' ); ?>
					</label>
				</div>
				<div class="form_input_area">
					<?php echo $Select; ?>
					<span class="form_must_fill">*</span>
				</div>
				<div class="cls"></div>
			</div>
		</div>
		<?php
		$return = ob_get_clean();
		return $return;

	}

	public static function getTaskHeader( $Task, $Num, $must = 0 )
	{
		ob_start();
		?>
		<h2 class="text-center">
			<?php echo Text::_( 'task' ); ?> # <?php echo $Num; ?> : <?php echo $Task->LIB_TITLE; ?> 
			<?php
			if ( $must )
			{
				?>
				<span class="form_must_fill">*</span>
				<?php
			}
			?>
		</h2>
		<?php
		$Content = ob_get_clean();
		return $Content;

	}

	public static function getTaskDesc( $Text, $Delimiter = 1 )
	{
		ob_start();
		if ( !empty( $Text ) )
		{
			?>
			<h3 class="text-center">
				<?php echo Text::_( 'Task executive' ); ?> : <?php echo $Text; ?>
			</h3>
			<?php
		}
		if ( $Delimiter )
		{
			?>
			<div class="padding10"></div>
			<?php
		}
		?>
		<?php
		$Content = ob_get_clean();
		return $Content;

	}

	public static function getTaskActior( $Task, $Flow = null )
	{
		$ALTER_TYPE = Collection::get( 'ALTER_TYPE', $Task );
		switch ( $ALTER_TYPE )
		{
			case 1:
				return self::GetActiorWorkers( $Task, $Flow );
			case 2:
				return self::GetActiorWorkersGroup( $Task );
			case 3:
				$Content = self::GetActiorWorkers( $Task, $Flow );
				$Content .= self::GetActiorsDelimiter();
				$Content .= self::GetActiorWorkersGroup( $Task );
				return $Content;
			default:
				return '';
		}

	}

	public static function GetActiorsDelimiter()
	{
		ob_start();
		?>
		<h2 class="text-center">
			<?php echo Text::_( 'OR/and' ); ?>
		</h2>
		<?php
		$Content = ob_get_clean();
		return $Content;

	}

	public static function RenderActiors( $ActionID, $Flow = 0 )
	{
		$NextTasks = TaskHelper::getNextTaskByAction( $ActionID );
		$Num = 1;
		foreach ( $NextTasks as $Task )
		{
			$TaskGroup = Collection::get( 'LIB_GROUP', $Task );
			$TaskID = Collection::get( 'ID', $Task );
			if ( !TaskHelper::CheckIf( $Task, $Flow ) )
			{
				continue;
			}
			if ( !TaskHelper::CheckActionIf( $Task, $Flow ) )
			{
				continue;
			}

			switch ( $TaskGroup )
			{
				case -7:
					echo Actiors::getTaskHeader( $Task, $Num );
					$GroupSelect = C::_( 'LIB_GROUP_SELECT', $Task, '0' );
					$Group = TaskHelper::getGroup( $GroupSelect );
					$WORKERS = explode( ',', C::_( 'WORKERS', $Group ) );
					echo Actiors::GetGroupWorkers( $WORKERS, $TaskID );
//					echo self::getTaskDesc( Collection::get( 'LIB_TITLE', $Group ) );
					break;
				case -8:
					echo Actiors::getTaskHeader( $Task, $Num, 1 );
					echo Actiors::GetActiorWorkers( $Task, $Flow );

					break;
				case -9:
					$AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
					$Values = TaskHelper::GetFlowAttributesByID( $Flow );
					$User = C::_( $AttrID, $Values );
					if ( !$User )
					{
						continue;
					}
					echo Actiors::getTaskHeader( $Task, $Num );
					$UserData = Users::getUser( $User );
					echo self::getTaskDesc( C::_( 'FIRSTNAME', $UserData ) . '  ' . C::_( 'LASTNAME', $UserData ) );
					break;
				case -12:
					$AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
					$Values = TaskHelper::GetFlowAttributesByID( $Flow );
					$Users = Helper::CleanArray( explode( ',', C::_( $AttrID, $Values ) ) );
					if ( !count( $Users ) )
					{
						continue;
					}
					echo Actiors::getTaskHeader( $Task, $Num );
					foreach ( $Users as $User )
					{
						$UserData = Users::getUser( $User );
						echo self::getTaskDesc( C::_( 'FIRSTNAME', $UserData ) . '  ' . C::_( 'LASTNAME', $UserData ), 0 );
					}
					echo self::getTaskDesc( '' );
					break;
				case -17:
					echo Actiors::getTaskHeader( $Task, $Num );
					echo self::GetChiefsWorkers( $Task );
					break;
				case -19:
					$GroupID = TaskHelper::ExecCondition( $Task, $Flow );
					$Group = TaskHelper::getGroup( $GroupID );
					echo Actiors::getTaskHeader( $Task, $Num );
					echo self::getTaskDesc( Collection::get( 'LIB_TITLE', $Group ) );
					echo Actiors::getTaskActior( $Task, $Flow );
					break;
				case -13:
					echo Actiors::getTaskHeader( $Task, $Num );
					$User = Users::GetUserData( 'DIRECTCHIEF' );
					$Chief = Users::getUser( $User );
					echo self::getTaskDesc( C::_( 'FIRSTNAME', $Chief ) . '  ' . C::_( 'LASTNAME', $Chief ) );
					break;
				case -15:
					echo Actiors::getTaskHeader( $Task, $Num );
					$AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
					$Values = TaskHelper::GetFlowAttributesByID( $Flow );
					$Group = (int) C::_( $AttrID, $Values );
					$Limiter = ' + ';
					if ( $Group < 0 )
					{
						$Limiter = '';
					}
					$Data = TaskHelper::getGroup( $Group );
					echo self::getTaskDesc( Text::_( 'initiator' ) . $Limiter . C::_( 'LIB_TITLE', $Data, '' ) );
//					echo self::getTaskDesc( C::_( 'LIB_TITLE', $Data ) );
					break;
				case -11:
					echo Actiors::getTaskHeader( $Task, $Num );
					$AttrID = C::_( 'LIB_GROUP_ATTR', $Task );
					$Values = TaskHelper::GetFlowAttributesByID( $Flow );
					$Groups = Helper::CleanArray( C::_( $AttrID, $Values ) );
					if ( Count( $Groups ) == 0 )
					{
						continue;
					}
					$Gr = '';
					$K = 1;
					foreach ( $Groups as $Group )
					{
						$Data = TaskHelper::getGroup( $Group );
						$Gr .= ' * ' . C::_( 'LIB_TITLE', $Data ) . '; ';
						$K++;
					}
					echo self::getTaskDesc( $Gr );
					break;
				case -5:
					echo Actiors::getTaskHeader( $Task, $Num );
					$User = Users::GetUserFullName();
					echo self::getTaskDesc( $User );
					echo Actiors::getTaskActior( $Task, $Flow );
//					echo Actiors::getTaskHeader( $Task, $Num, $must );
					break;
				case -3:
					echo Actiors::getTaskHeader( $Task, $Num );
					$AltTaskID = C::_( 'LIB_GROUP_ALT', $Task, '0' );
					$WorkFlowID = Request::getVar( 'FLOW', 0 );
					$Tasks = TaskHelper::getAltTasks( $AltTaskID, $WorkFlowID );
					foreach ( $Tasks as $AltTask )
					{
						$AltWorker = C::_( 'TASK_ACTOR', $AltTask );
						$AltGroup = C::_( 'TASK_ACTOR_GROUP', $AltTask );
						if ( $AltWorker )
						{
							$UserName = Users::GetUserFullName( $AltWorker );
							echo self::getTaskDesc( $UserName );
							break;
						}
						if ( $AltGroup )
						{
							$Group = TaskHelper::getGroup( $AltGroup );
							echo self::getTaskDesc( Collection::get( 'LIB_TITLE', $Group ) );
						}
					}
					break;
				case -2:
				case -1:
					echo Actiors::getTaskHeader( $Task, $Num, 1 );
					echo Actiors::getTaskActior( $Task, $Flow );
					break;
				case 0:
					echo Actiors::getTaskHeader( $Task, $Num );
					echo self::getTaskDesc( Text::_( 'initiator' ) );
					echo Actiors::getTaskActior( $Task, $Flow );
					break;
				default:
					$Group = TaskHelper::getGroup( $Task->LIB_GROUP );
					echo Actiors::getTaskHeader( $Task, $Num );
					echo self::getTaskDesc( Collection::get( 'LIB_TITLE', $Group ) );
					echo Actiors::getTaskActior( $Task, $Flow );
					break;
			}
			++$Num;
			//self::getTaskDueDate( $Task );
			//self::getTaskPriority( $Task );
		}

	}

	public static function getTaskDueDate( $Task )
	{
		ob_start();
		$TaskID = C::_( 'ID', $Task );
		$DURATION = C::_( 'DURATION', $Task );
		$name = 'due_date';
		$Space = 'params';
		$id = $Space . $TaskID . $name;
		$value = TaskHelper::CalculateDueDate( $DURATION, $TaskID );
		$format = '%Y-%m-%d';
		$class = 'inputbox';
		?>
		<div class="form_item">
			<div class="form_label">
				<label for="<?php echo $id; ?>" class="form_param_lbl">
					<?php echo Text::_( 'DUE_DATE' ); ?>
				</label>
			</div>
			<div class="form_input_area">
				<?php
				echo HTML::_( 'calendar', $value, $Space . '[' . $TaskID . '][' . $name . ']', $id, $format, array( 'class' => $class ) );
				?>
				<div class="cls"></div>
			</div>
		</div>
		<div class="cls"></div>
		<div class="padding10"></div>
		<?php
		$Content = ob_get_clean();
		echo $Content;

	}

	public static function getTaskPriority( $Task )
	{
		global $Priority;
		ob_start();
		$TaskID = C::_( 'ID', $Task );
		$name = 'priority';
		$Space = 'params';
		$id = $Space . $TaskID . $name;
		$value = C::_( $Space . '.' . $TaskID . '.' . $name, 'post', 200 );
		$options = array();
		foreach ( $Priority as $Key => $P )
		{
			$options[] = HTML::_( 'select.option', $Key, Text::_( $P ) );
		}
		?>
		<div class="form_item">
			<div class="form_label">
				<label for="<?php echo $id; ?>" class="form_param_lbl">
					<?php echo Text::_( 'PRIORITY' ); ?>
				</label>
			</div>
			<div class="form_input_area">
				<?php
				echo HTML::_( 'select.genericlist', $options, $Space . '[' . $TaskID . '][' . $name . ']', '', 'value', 'text', $value, $id );
				?>
				<div class="cls"></div>
			</div>
		</div>
		<div class="cls"></div>
		<div class="padding10"></div>
		<?php
		$Content = ob_get_clean();
		echo $Content;

	}

}
