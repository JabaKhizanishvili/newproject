<?php

class Xhelp
{
	public static function hasHelp()
	{
		return true;

	}

	public static function getHelp( $name = '' )
	{
		$option = Request::getVar( 'option', false );
		if ( !$option )
		{
			return '';
		}
		if ( !empty( $name ) )
		{
			$option = $name;
		}
		$filename = X_PATH_BASE . DS . 'docs' . DS . $option . '/' . XTranslate::GetCurrentLang() . '.php';
		if ( !is_file( $filename ) )
		{
			return '';
		}
		ob_start();
		require $filename;
		$content = ob_get_clean();
		return $content;

	}

	public static function SameKeyData( $data1, $data2 )
	{
		$result = [];
		foreach ( $data1 as $key => $vall )
		{
			if ( is_object( $data2 ) && property_exists( $data2, $key ) )
			{
				$result[$key] = $vall;
			}
			if ( is_array( $data2 ) && array_key_exists( $key, $data2 ) )
			{
				$result[$key] = $vall;
			}
		}
		ksort( $result );
		return $result;

	}

	public static function data_compare( $data1 = null, $data2 = null, $label1 = '', $label2 = '', $dinamic = [], $cut = [] )
	{
		if ( !count( $data1 ) && !count( $data2 ) )
		{
			return false;
		}

		$diff_keys = [];
		foreach ( $data1 as $key => $val )
		{
			if ( in_array( $key, $cut ) || (count( $data2 ) && !array_key_exists( $key, $data2 )) )
			{
				if ( is_object( $data1 ) )
				{
					unset( $data1->$key );
				}

				if ( is_array( $data1 ) )
				{
					unset( $data1[$key] );
				}
				$diff_keys[] = $key;
				continue;
			}

			if ( is_array( $data2 ) && count( $data2 ) && $data2[$key] != $val )
			{
				$diff_keys[] = $key;
			}

			if ( is_object( $data2 ) && count( $data2 ) && $data2->$key != $val )
			{
				$diff_keys[] = $key;
			}
		}

		foreach ( $data2 as $key => $val )
		{
			if ( in_array( $key, $cut ) || (count( $data1 ) && !array_key_exists( $key, $data1 ) ) )
			{
				if ( is_object( $data2 ) )
				{
					unset( $data2->$key );
				}

				if ( is_array( $data2 ) )
				{
					unset( $data2[$key] );
				}
				$diff_keys[] = $key;
				continue;
			}
		}

		$html = '<div class = "row">';
		self::data_print( $data1, $label1, $dinamic, $diff_keys, 'from' );
		self::data_print( $data2, $label2, $dinamic, $diff_keys, 'to' );
		$html .= '</div>';

	}

	public static function data_print( $data = null, $label = '', $X_dinamic = [], $X_diff_keys = [], $X_diff_keys_type = 'from' )
	{
		$html = '<div class = "displ container col-md-6">';
		$html .= '<div class = "form-control noheight">';
		$html .= '<div class = "i_flex data_label  text-success">' . ($label ? $label : '...') . '</div>';

		if ( !count( $data ) )
		{
			$html .= '<div class = "text-danger">' . Text::_( 'Data not found!' ) . '</div>';
		}

		ksort( $data );
		foreach ( $data as $line => $val )
		{
			$class = '';
			if ( in_array( $line, $X_diff_keys ) )
			{
				if ( $X_diff_keys_type == 'from' )
				{
					$class = 'data_origin';
				}
				if ( $X_diff_keys_type == 'to' )
				{
					$class = 'data_changed';
				}
			}

			$html .= '<div class = "i_flex ' . $class . '">';
			$html .= '<strong>' . Text::_( $line ) . '</strong>: ';
			if ( !is_array( C::_( $line, $X_dinamic ) ) )
			{
				if ( array_key_exists( $line, $X_dinamic ) )
				{
					$name = $X_dinamic[$line];
					$html .= self::htmlElement( $name, 'grid', [ $line, $val ] );
				}
				else
				{
					$html .= $val;
				}
			}
			else
			{
				foreach ( C::_( $line, $X_dinamic ) as $k => $v )
				{
					if ( $k == $val )
					{
						$html .= Text::_( $v );
					}
				}
			}
			$html .= '</div>';
		}

		$html .= '</div></div>';
		echo $html;

	}

	public static function DataBox( $DATA = [], $dinamic = [], $class = '', $compareTo = [], $lblKey = '' )
	{
		$INPUT = [];
		$compare = count( (array) $compareTo );
		if ( $compare )
		{
			$T1 = self::SameKeyData( $DATA, $compareTo );
			$T2 = self::SameKeyData( $compareTo, $DATA );

			if ( !empty( $lblKey ) )
			{
				if ( C::_( $lblKey, $T1 ) )
				{
					$array = $T1[$lblKey];
					unset( $T1[$lblKey] );
					$T1 = array( $lblKey => $array ) + $T1;
				}
				if ( C::_( $lblKey, $T2 ) )
				{
					unset( $T2[$lblKey] );
					$T2 = array( $lblKey => $T1[$lblKey] ) + $T2;
				}
			}

			$INPUT[] = $T1;
			$INPUT[] = $T2;
		}
		else
		{
			$INPUT[] = $DATA;
			if ( C::_( 'CONFIRMATION', $INPUT[0] ) )
			{
				unset( $INPUT[0]['CONFIRMATION'] );
			}
		}

		foreach ( $INPUT as $KEY => $data )
		{
			$html = '<div class = "displ container ' . $class . '">';
			$html .= '<div class = "form-control noheight">';
			if ( empty( $data ) )
			{
				$html .= '<div class = "text-danger">' . Text::_( 'Data not found!' ) . '</div>';
			}
			if ( is_array( $data ) )
			{
				unset( $data['ID'] );
				unset( $data['CLIENT_ID'] );
			}
			else
			{
				unset( $data->ID );
				unset( $data->CLIENT_ID );
			}

			$html .= self::DataBoxData( $data, $dinamic, $compare, $lblKey, $INPUT, $KEY );
			$html .= '</div></div>';
			echo $html;
		}

	}

	public static function DataBoxData( $data = [], $dinamic = [], $compare = 0, $lblKey = '', $INPUT = [], $KEY = 0 )
	{
		$html = '';
		$counter = 0;
		foreach ( $data as $line => $val )
		{
			$linelbl = '<strong>' . Text::_( $line ) . '</strong>: ';
			$diffclass = '';
			if ( $compare )
			{
				if ( $INPUT[0][$line] != $INPUT[1][$line] )
				{
					if ( $KEY == 0 )
					{
						$diffclass = ' data_origin ';
					}
					if ( $KEY == 1 )
					{
						$diffclass = ' data_changed ';
					}
				}
			}
			if ( !empty( $lblKey ) && $counter == 0 )
			{
				$diffclass = ' data_label ';
				if ( $KEY == 0 )
				{
					$diffclass .= ' text-success ';
				}
				if ( $KEY == 1 )
				{
					$diffclass .= ' text-danger ';
				}
				$linelbl = '';
			}
			$html .= '<div class = "i_flex ' . $diffclass . '">';
			$html .= $linelbl;
			if ( !is_array( C::_( $line, $dinamic ) ) )
			{
				if ( array_key_exists( $line, $dinamic ) )
				{
					$name = $dinamic[$line];
					$html .= self::htmlElement( $name, 'grid', [ $line, $val ] );
				}
				else
				{
					$html .= $val;
				}
			}
			else
			{
				foreach ( C::_( $line, $dinamic ) as $k => $v )
				{
					if ( $k == $val )
					{
						$html .= Text::_( $v );
					}
				}
			}
			$html .= '</div>';
			$counter++;
		}
		return $html;

	}

	public static function htmlElement( $name, $type = '', $params = array() )
	{
		$html = '';
		if ( empty( $name ) || empty( $type ) || !count( $params ) )
		{
			return '';
		}
		$val = '';
		$file = X_PATH_BASE . DS . 'libraries' . DS . 'html' . DS . 'elements' . DS . $type . DS . FilterInput::getInstance()->clean( str_replace( '_', DS, $name ) . '.php', 'path' );
		if ( is_file( $file ) )
		{
			switch ( $type )
			{
				case 'grid':
					$line = $params[0];
					$val = $params[1];
					include_once $file;
					$className = 'JGridElement' . $name;
					$CLASS = new $className();
					$row = (object) array( $line => $val );
					$node = new SimpleXMLElements( '<param />' );
					$node->addAttribute( 'key', $line );
					$node->addAttribute( 't', 1 );
					$group = array();
					$html .= '<span>';
					$html .= $CLASS->fetchElement( $row, $node, $group );
					$html .= '</span>';
					break;
				case 'edit':
					$Pname = $params[0];
					$valueIN = $params[1];
					$node = $params[2];
					$control_name = $params[3];
					include_once $file;
					$className = 'JElement' . $name;
					$CLASS = new $className();
					$html .= '<span>';
					$html .= $CLASS->fetchElement( $Pname, $valueIN, $node, $control_name );
					$html .= '</span>';
					break;
			}
		}
		else
		{
			return $val . '( ' . $name . '.php ) ';
		}
		return $html;

	}

	public static function TransportParams( $data )
	{
		$hiddens = '';
		unset( $data->display_changed );
		foreach ( $data as $key => $val )
		{
			if ( is_array( $val ) )
			{
				foreach ( $val as $k => $v )
				{
					if ( empty( $v ) )
					{
						unset( $val[$k] );
					}
				}
				$val = implode( ', ', $val );
			}
			$hiddens .= '<input type = "hidden" name = "params[' . $key . ']" id = "params' . $key . '" value = "' . $val . '">';
		}
		echo $hiddens;

	}

	public static function Confirmation( $option, $page_title = '', $before = '' )
	{
		if ( empty( $before ) )
		{
			return '';
		}
		if ( empty( $page_title ) )
		{
			return '';
		}
		$addParam = '<input type = "hidden" name = "params[CONFIRMATION]" id = "paramsCONFIRMATION" value = "' . $before . '">';
		Helper::SetJS( '$( \'' . $addParam . '\').appendTo("#fform");' );
		$Label = '<input type="hidden" name="params[CONFIRMATIONLABEL]" id="paramsCONFIRMATIONLABEL" value="' . $page_title . '">';
		Helper::SetJS( '$(\'' . $Label . '\').appendTo("#fform");' );
		return Helper::getToolbar( 'Save', $option, 'display' );

	}

	public static function getWorker_sch( $id )
	{
		if ( empty( $id ) )
		{
			return false;
		}

		$query = 'select sw.* from slf_worker sw where '
						. ' sw.id = ' . $id
		;
		return DB::LoadObject( $query );

	}

	public static function getWorkerData( $id )
	{
		if ( empty( $id ) )
		{
			return [];
		}
		$query = 'select sw.* from slf_worker sw where '
						. ' sw.id in (' . $id . ') '
						. ' and sw.active = 1'
		;
		return DB::LoadObjectList( $query );

	}

	public static function getWorkerChiefs( $id = '', $org = '' )
	{
		static $persons = [];
		$query = ' select sw.id, wc.worker_pid, wc.org, sw.firstname || \' \' || sw.lastname as chief '
						. ' from	rel_worker_chief wc '
						. ' left join slf_persons sw on sw.id = wc.chief_pid '
						. ' left join slf_worker ww on ww.id = wc.chief '
						. ' where '
//						. '	wc.worker_pid = ' . (int) $id
//						. ' and wc.org = ' . (int) $org
//						. ' and '
						. ' wc.clevel in (0, 1) '
						. ' and sw.id is not null '
						. ' and ww.active = 1 '
						. ' and sw.active = 1 '
		;
		if ( !count( $persons ) )
		{
			$result = DB::LoadObjectList( $query );
			$collect = [];
			foreach ( $result as $v )
			{
				$collect[$v->WORKER_PID . '-' . $v->ORG][] = XTranslate::_( $v->CHIEF );
			}
			$persons = $collect;
		}
		$k = $id . '-' . $org;
		return C::_( $k, $persons, array() );

	}

	public static function collect( $data = [], $name1 = '', $name2 = '' )
	{
		$result = '';
		if ( !empty( $name1 ) )
		{
			$collect = [];
			foreach ( $data as $one )
			{
				$add = '';
				if ( !empty( $name2 ) )
				{
					$add = ',' . C::_( $name2, $one );
				}
				$collect[] = C::_( $name1, $one ) . $add;
			}
			$result = implode( '|', $collect );
		}
		return $result;

	}

	public static function bind( $data1 = [], $data2 = [], $cut = [] )
	{
		if ( count( $cut ) )
		{
			foreach ( $cut as $key )
			{
				$upper = strtoupper( $key );
				if ( array_key_exists( $upper, $data2 ) )
				{
					if ( is_array( $data2 ) )
					{
						unset( $data2[$upper] );
					}
					else
					{
						unset( $data2->$upper );
					}
				}
			}
		}

		foreach ( $data2 as $key => $value )
		{
			$upper = strtoupper( $key );
			if ( array_key_exists( $upper, $data1 ) )
			{
				$data1[$upper] = $value;
			}
		}

		return $data1;

	}

	public static function ChiefWorkers( $org )
	{
		$ORG = '';
		if ( !empty( $org ) )
		{
			$ORG = ' and t.org = ' . DB::Quote( $org );
		}

		$select = [];
		$select[] = ' t.id ';
		$select[] = ' t.firstname ';
		$select[] = ' t.lastname ';
		$select[] = ' t.org_name ';
		$config_based = Helper::getConfig( 'apps_worker_identificator' );
		$ex = (array) array_diff( explode( '|', $config_based ), [ '-1' ] );

		foreach ( $ex as $line )
		{
			if ( empty( $line ) )
			{
				continue;
			}

			$add = 't.' . $line;
			if ( $line == 'birthdate' )
			{
				$add = ' to_char(' . $add . ', \'yyyy-mm-dd\') birthdate ';
			}

			if ( $line == 'tablenum' )
			{
				continue;
			}

			$select[] = $add;
		}

		$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
		$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
		$DirectTreeUnion = '';
		$AdditionalTreeUnion = '';
		if ( $DirectTree )
		{
			$DirectTreeUnion = ' or t.parent_id in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
		}
		if ( $AdditionalTree )
		{
			$AdditionalTreeUnion = ' or t.parent_id in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
		}

		$query = 'select '
						. implode( ', ', $select )
						. ' from hrs_workers t '
						. ' where '
						. ' t.active = 1 '
						. ' and t.id in (select wc.worker_opid from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1)) ' . $DirectTreeUnion . $AdditionalTreeUnion
						. $ORG
						. ' order by t.firstname asc';
		$return = DB::LoadObjectList( $query );
		foreach ( $return as $data )
		{
			$rr = [];
			foreach ( $data as $x => $y )
			{
				if ( empty( $y ) )
				{
					continue;
				}

				if ( $x == 'ID' || $x == 'TITLE' )
				{
					continue;
				}

				$rr[] = XTranslate::_( $y );
			}

			$data->TITLE = implode( ' - ', $rr );
		}
		return $return;

	}

	public static function getAssignedWorkers( $person = null, $where = [] )
	{
		if ( empty( $person ) )
		{
			$person = (int) Users::GetUserID();
		}
		$where[] = ' sw.person in (' . $person . ')';
		$where[] = ' sw.active = 1 ';
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$collect = [];
		$Q = 'select '
						. ' sw.accounting_offices,'
						. ' sw.person, '
						. ' to_char(dd.change_date, \'yyyy-mm-dd\') assignment_date, '
						. ' sw.org, '
						. ' lu.lib_title org_name,'
						. ' sw.staff_schedule, '
						. ' sw.calculus_type, '
						. ' sw.salary_payment_type, '
						. ' sw.category_id, '
						. ' coalesce(sw.iban, (select ppp.iban from slf_persons ppp where ppp.id = sw.person)) iban, '
						. ' sw.graphtype, '
						. ' sw.chiefs, '
						. ' ls.position, '
						. ' sw.tablenum, '
						. ' sw.contract_type, '
//						. ' (SELECT '
//						. ' max(tt.lib_title) title '
//						. ' from LIB_UNITS tt '
//						. ' left join lib_unittypes ut on ut.id = tt.type '
//						. ' left join lib_units u on u.lft >= tt.lft and u.rgt <= tt.rgt'
//						. '  where '
//						. ' tt.active > 0 '
//						. ' and u.id is not null '
//						. ' and ut.def = 1'
//						. ' and u.id = ls.org_place '
//						. ' and tt.org = sw.org '
//						. ') MAINUNIT,'
						. '(select g.lib_title from rel_workers_groups r '
						. ' left join lib_workers_groups g on g.id = r.group_id '
						. ' where '
						. ' r.org = ls.org '
						. ' and r.worker = sw.id '
						. ') GRAPH_GROUP, '
						. ' ls.org_place, '
						. ' sw.contracts_date, '
						. ' sw.salary, '
						. ' sw.contract_end_date '
						. ' from slf_worker sw '
						. ' left join lib_staff_schedules ls on ls.id = sw.staff_schedule '
						. ' left join lib_unitorgs lu on lu.id = sw.org '
						. ' left join lib_standard_graphs  gr on gr.id = sw.graphtype '
						. ' left join (select * from slf_changes where change_type in (1, 5) ) dd on dd.org = sw.org and dd.worker_id = sw.id '
						. $whereQ
		;
//		$DATA = DB::LoadObjectList( $Q );
		$DATA = XRedis::getDBCache( 'slf_worker', $Q );
		$MainUnits = Units::GetMainUnits();
		foreach ( $DATA as $data )
		{
			$data->MAINUNIT = C::_( C::_( 'ORG_PLACE', $data ) . '.TITLE', $MainUnits );
			$collect[C::_( 'PERSON', $data )][] = $data;
		}
		return $collect;

	}

	public static function checkTime( $Time = '' )
	{
		$T = explode( ':', $Time );
		if ( empty( $T[0] ) || !is_numeric( $T[0] ) )
		{
			return false;
		}
		if ( empty( $T[1] ) || !is_numeric( $T[1] ) )
		{
			return false;
		}

		$h = (int) $T[0];
		$m = (int) $T[1];
		if ( $h < 0 || $h > 23 || $m < 0 || $m > 59 )
		{
			return false;
		}
		return true;

	}

	public static function checkDate( $Date = '', $recieve = 0 )
	{
		$D = explode( '-', $Date );
		$ss = explode( ' ', implode( '', $D ) )[0];
		if ( strlen( $ss ) != 8 )
		{
			return false;
		}
		if ( !is_numeric( (int) $ss ) )
		{
			return false;
		}

		if ( $recieve == 1 )
		{
			return true;
		}

		if ( empty( $D[0] ) || !is_numeric( $D[0] ) )
		{
			return false;
		}
		if ( empty( $D[1] ) || !is_numeric( $D[1] ) )
		{
			return false;
		}
		if ( empty( $D[2] ) || !is_numeric( $D[2] ) )
		{
			return false;
		}

		$d = (int) $D[0];
		$m = (int) $D[1];
		$y = (int) $D[2];

		if ( $d < 0 || $d > 31 )
		{
			return false;
		}
		if ( $m < 0 || $m > 12 )
		{
			return false;
		}
		if ( $y < 1700 )
		{
			return false;
		}

		return true;

	}

	public static function strNumber( $input = '', $decN = 0 )
	{
		if ( empty( $input ) )
		{
			return '';
		}

		$N = explode( '.', $input );
		if ( $N[0] == '' )
		{
			$N[0] = '0';
		}
		if ( $decN > 0 && !empty( $N[1] ) )
		{
			$n = strlen( $N[1] );
			$nn = $decN - $n;
			if ( $nn > 0 )
			{
				$N[1] .= str_repeat( '0', $nn );
			}
		}

		return implode( '.', $N );

	}

	public static function ChiefWorkersSchedule( $org )
	{
		$ORG = '';
		if ( !empty( $org ) )
		{
			$ORG = ' and t.org = ' . DB::Quote( $org );
		}

		$select = [];
		$select[] = ' t.id ';
		$select[] = ' sp.firstname ';
		$select[] = ' sp.lastname ';
		$select[] = ' lu.lib_title ';
		$select[] = ' sc.lib_title ';
		$config_based = Helper::getConfig( 'apps_worker_identificator' );
		$ex = (array) array_diff( explode( '|', $config_based ), [ '-1' ] );

		foreach ( $ex as $line )
		{
			if ( empty( $line ) )
			{
				continue;
			}

			$add = 'sp.' . $line;
			if ( $line == 'birthdate' )
			{
				$add = ' to_char(' . $add . ', \'yyyy-mm-dd\') birthdate ';
			}

			if ( $line == 'tablenum' )
			{
				continue;
			}

			$select[] = $add;
		}

		$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
		$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
		$DirectTreeUnion = '';
		$AdditionalTreeUnion = '';
		if ( $DirectTree )
		{
			$DirectTreeUnion = ' or t.person in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
		}
		if ( $AdditionalTree )
		{
			$AdditionalTreeUnion = ' or t.person in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
		}

		$query = 'select '
						. implode( ', ', $select )
						. ' from slf_worker t '
						. ' left join slf_persons sp on sp.id  = t.person'
						. ' left join lib_unitorgs lu on lu.id  = t.org'
						. ' left join lib_staff_schedules sc on sc.id  = t.staff_schedule'
						. ' where '
						. ' t.active = 1 '
						. ' and sp.active = 1 '
						. ' and t.id in (select wc.worker from rel_worker_chief wc where wc.chief_pid in ( ' . Users::GetUserID() . ' )  and wc.clevel in (0, 1) ) ' . $DirectTreeUnion . $AdditionalTreeUnion
						. $ORG
						. ' order by sp.firstname asc';
		$return = DB::LoadObjectList( $query );
		foreach ( $return as $data )
		{
			$rr = [];
			foreach ( $data as $x => $y )
			{
				if ( empty( $y ) )
				{
					continue;
				}

				if ( $x == 'ID' || $x == 'TITLE' )
				{
					continue;
				}

				$rr[] = XTranslate::_( $y );
			}

			$data->TITLE = implode( ' - ', $rr );
		}

		return $return;

	}

	public static function inNowDate( $date )
	{
		if ( empty( $date ) )
		{
			return false;
		}
//		$Now = new PDate( '2022-05-04 18:00' );
		$Now = new PDate( );
		$Date = new PDate( $date );
		if ( $Date->toUnix() < $Now->toUnix() )
		{
			return false;
		}
		return true;

	}

	public static function HelpBox( $name = '' )
	{
		?>
		<div class="msg0">
			<div class="msg01">
				<i class="bi bi-info-circle info-ico"></i>
				<?php echo Text::_( 'PLEASE_REMEMBER' ); ?>
				<i class="bi bi-eye show-ico show-ico"></i><i class="bi bi-eye-slash show-ico"></i>
			</div>
			<div class="msg02">
				<?php
				echo self::getHelp( $name );
				?>
			</div>
		</div>
		<?php

	}

	public static function ConfirmationData( $data, $collect, $header = '' )
	{
		if ( $data && $collect )
		{
			$task = C::_( 'CONFIRMATION', $data );
			$label = C::_( 'CONFIRMATIONLABEL', $data );
			$data = $collect;
			$data['TASK'] = $task;
			$data['LABEL'] = $label;
			$data['HEADER'] = $header;
			return $data;
		}

	}

	public static function CheckWorkersInOrg( $ID, $KEY = 'ORG', $ALIAS = 'hws' )
	{
		if ( is_array( $ID ) )
		{
			$ID = implode( ',', $ID );
		}

		if ( !empty( $ID ) )
		{
			$Q = ' SELECT ' . $ALIAS . '.' . $KEY . '  FROM slf_worker hws '
							. 'left join LIB_STAFF_SCHEDULES lss on lss.id = hws.STAFF_SCHEDULE '
							. 'WHERE hws.active in (0, 1) AND  ' . $ALIAS . '.' . $KEY . '  in (' . $ID . ') group by ' . $ALIAS . '.' . $KEY;
			return DB::LoadList( $Q );
		}

	}

	public static function CheckStandartTimeInGlobalGrs( $ID )
	{
		if ( is_array( $ID ) )
		{
			$ID = implode( ',', $ID );
		}
//

		if ( !empty( $ID ) )
		{

			$Q = ' SELECT * FROM LIB_STANDARD_GRAPHS hws '
							. ' left join LIB_GRAPH_TIMES tg on tg.id = hws.MONDAY '
							. ' left join LIB_GRAPH_TIMES tg on tg.id = hws.TUESDAY '
							. ' left join LIB_GRAPH_TIMES tg on tg.id = hws.WEDNESDAY '
							. ' left join LIB_GRAPH_TIMES tg on tg.id = hws.THURSDAY '
							. ' left join LIB_GRAPH_TIMES tg on tg.id = hws.FRIDAY '
							. ' left join LIB_GRAPH_TIMES tg on tg.id = hws.SATURDAY '
							. ' left join LIB_GRAPH_TIMES tg on tg.id = hws.SUNDAY '
							. ' WHERE (hws.MONDAY = ' . $ID
							. ' or hws.TUESDAY = ' . $ID
							. ' or hws.WEDNESDAY = ' . $ID
							. ' or hws.THURSDAY = ' . $ID
							. ' or hws.FRIDAY = ' . $ID
							. ' or hws.SATURDAY = ' . $ID
							. ' or hws.SUNDAY = ' . $ID
							. ' ) '
							. ' and hws.ACTIVE =1 '
							. ' and tg.ACTIVE =1 '
			;
			return DB::LoadList( $Q );
		}
		return false;

	}

	public static function CheckStandartTimeInWorkers( $ID )
	{
		if ( is_array( $ID ) )
		{
			$ID = implode( ',', $ID );
		}

		if ( !empty( $ID ) )
		{

			$Q = ' SELECT * FROM slf_worker sl '
							. ' left join LIB_STANDARD_GRAPHS g on g.id = sl.GRAPHTYPE '
							. ' where sl.GRAPHTYPE = ' . $ID
							. ' and sl.ACTIVE =1 '
			;
			return DB::LoadList( $Q );
		}

	}

	public static function getLimitType( $id )
	{
		if ( empty( $id ) )
		{
			return [];
		}
		$query = 'select p.* '
						. ' from lib_limit_app_types p where '
						. ' p.id =' . (int) $id;
		return DB::LoadObject( $query );

	}

	public static function CheckRolesInPersons( $ID )
	{
		if ( is_array( $ID ) )
		{
			$ID = implode( ',', $ID );
		}

		if ( !empty( $ID ) )
		{

			$Q = ' select sl.USER_ROLE from slf_persons sl '
							. ' left join LIB_ROLES g on g.ID = sl.USER_ROLE '
							. ' where sl.USER_ROLE in (' . $ID . ')'
							. ' and sl.ACTIVE =1 '
							. ' and g.ACTIVE =1 '
			;
			return DB::LoadList( $Q );
		}

	}

	public static function CheckPositionsInStructures( $ID )
	{
		if ( is_array( $ID ) )
		{
			$ID = implode( ',', $ID );
		}

		if ( !empty( $ID ) )
		{

			$Q = ' select g.POSITION from lib_positions sl '
							. ' left join LIB_STAFF_SCHEDULES g on g.POSITION = sl.ID '
							. ' where g.POSITION in (' . $ID . ')'
							. ' and sl.ACTIVE =1 '
							. ' and g.ACTIVE =1 '
			;
			return DB::LoadList( $Q );
		}

	}

	public static function CheckWorkingRatesInStructures( $ID )
	{
		if ( is_array( $ID ) )
		{
			$ID = implode( ',', $ID );
		}

		if ( !empty( $ID ) )
		{

			$Q = ' select g.WORKING_RATE from LIB_WORKING_RATES sl '
							. ' left join LIB_STAFF_SCHEDULES g on g.WORKING_RATE = sl.ID '
							. ' where g.WORKING_RATE in (' . $ID . ')'
							. ' and sl.ACTIVE =1 '
							. ' and g.ACTIVE =1 '
			;
			return DB::LoadList( $Q );
		}

	}

	public static function workedHours( $ss, $sb, $eb, $ee )
	{
		if ( $ss == '' || $ee == '' )
		{
			return false;
		}
		$s = PDate::Get( $ss )->toUnix();
		$e = PDate::Get( $ee )->toUnix();
		$se = $e - $s;
		$chs = $s > $e ? true : false;

		$br = 0;
		if ( $sb != '' || $eb != '' )
		{

			$sb = PDate::Get( $sb )->toUnix();
			$eb = PDate::Get( $eb )->toUnix();
			$br = $eb - $sb;
			$che = $sb > $eb ? true : false;
			if ( $sb > $eb && !$che )
			{
				return false;
			}
			if ( $eb > $e && $ee != '00:00' && $chs && $che )
			{
				return false;
			}
			if ( !$chs && $che && $ss != $ee )
			{
				return false;
			}
			if ( !$chs && !$che && $e < $eb && $ss != $ee )
			{
				return false;
			}
			if ( !$chs && !$che && $s > $sb && $ss != $ee )
			{
				return false;
			}
			if ( $s > $sb && $che )
			{
				return false;
			}
		}

		$result = $se - $br;
		$H = gmdate( 'H', $result );
		$i = gmdate( 'i', $result ) / 60;

		return (float) $H + $i;

	}

	public function readPeriodTypeCode( $type, $value, $start1 )
	{
		$Date = PDate::Get();
		$y = $Date->toFormat( '%Y' );
		switch ( $type )
		{
			case 1:
				$k = (int) substr( $value, -2 );
				$Y = substr( $y, 0, 2 ) . substr( $value, 0, 2 );
				$D = explode( ' ', $Date->setISODate( $Y, $k, $start1 ) )[0];
				$m = PDate::Get( $D )->toFormat( '%d-%m ' ) . $Y . ' ' . Text::_( 'year' );
				return $m;
				break;
			case 2:

				break;
			case 3:
				$m = substr( $value, -2 );
				$d = PDate::Get( substr( $y, 0, 2 ) . substr( $value, 0, 2 ) . '-' . $m )->toFormat( '%B %Y' );
				return $d;
				break;
			case 4:
				$ex = explode( '|', $value );
				$a = substr( $y, 0, 2 ) . substr( $ex[0], 0, 2 ) . '-' . substr( $ex[0], -4, -1 ) . '15';
				$b = date( 'Y-m-t', strtotime( $a ) );
				$m = PDate::Get( $a )->toFormat( '%d %B' ) . ' - ' . PDate::Get( $b )->toFormat( '%d %B %Y' );
				return $m;
				break;
			case 5:
				$i = substr( $value, -1 );
				$m = substr( $y, 0, 2 ) . substr( $value, 0, 2 ) . ' ' . Text::_( 'year' ) . ' ' . $i . ' ' . Text::_( 'quarter' );
				return $m;
				break;
			case 6:
				return substr( $y, 0, 2 ) . substr( $value, 0, 2 ) . ' ' . Text::_( 'year' );
				break;

			default:
				return $value;
				break;
		}

	}

	public static function GetChiefsContacts( $Worker = 0 )
	{
		$Query = 'select w.email, w.mobile_phone_number '
						. ' from rel_worker_chief wc '
						. ' left join slf_persons w on w.id = wc.chief_pid '
						. ' where wc.worker_opid = ' . (int) $Worker
						. ' and w.active=1'
		;
		return DB::LoadObjectList( $Query );

	}

	public static function readXML( $file = '' )
	{
		if ( !is_file( $file ) )
		{
			return [];
		}
		$xml = simplexml_load_file( $file );
		$table = File::stripExt( File::getName( $file ) );
		$collect[$table] = [];
		$collect[$table]['title'] = C::_( '@attributes.title', $xml->attributes(), $table );
		foreach ( $xml as $val )
		{
			$collect[$table]['columns'][] = (string) $val->attributes()->name;
		}

		return $collect;

	}

	public static function multiImplode( $keys = [], $data = [], $delimiter = ',' )
	{
		$collect = [];
		foreach ( $keys as $key )
		{
			$collect[] = C::_( $key, $data );
		}

		return implode( $delimiter, $collect );

	}

	public static function addDay( $start = '', &$end = '' )
	{
		if ( empty( $start ) || empty( $end ) )
		{
			return false;
		}

		$s = PDate::Get( $start )->toUnix();
		$e = PDate::Get( $end )->toUnix();
		if ( $s > $e )
		{
			$end = PDate::Get( $end . ' +1 day' );
			return true;
		}
		return false;

	}

	public static function checkPersonsActive( $ids = '' )
	{
		$Q = 'select '
						. ' count(*) '
						. ' from slf_persons p '
						. ' where '
						. ' p.id in (' . $ids . ') '
						. ' and p.active > 0 '
		;

		if ( DB::LoadResult( $Q ) > 0 )
		{
			return true;
		}
		return false;

	}

	public function lng_chars( $text, $from = '', $to = '' )
	{
		$lngs = [];
		$lngs['ge'] = 'ა, ბ, გ, დ, ე, ვ, ზ, თ, ი, კ, ლ, მ, ნ, ო, პ, ჟ, რ, ს, ტ, უ, ფ, ქ, ღ, ყ, შ, ჩ, ც, ძ, წ, ჭ, ხ, ჯ, ჰ';
		$lngs['en'] = 'a, b, g, d, e, v, z, t, i, k, l, m, n, o, p, zh, r, s, t, u, f, q, gh, k, sh, ch, c, dz, ts, tc, kh, j, h';

		$lng_from = !empty( $lngs[$from] ) ? $lngs[$from] : false;
		$lng_to = !empty( $lngs[$to] ) ? $lngs[$to] : false;

		if ( !$from || !$to )
		{
			return $text;
		}

		if ( !empty( $text ) )
		{
			$from = explode( ', ', $lng_from );
			$to = explode( ', ', $lng_to );
			$trans = str_replace( $from, $to, trim( $text ) );
			return $trans;
		}
		return $text;

	}

	public static function caseText( $case = null, $options = [], $print_text = true )
	{
		if ( is_null( $case ) )
		{
			return '';
		}

		if ( !$print_text )
		{
			return $case;
		}

		$result = C::_( $case, $options, false );
		if ( $result === false )
		{
			return '';
		}

		return Text::_( $result );

	}

}
