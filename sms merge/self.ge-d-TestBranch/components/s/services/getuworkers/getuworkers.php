<?php

class getUWorkers
{
	public function GetService()
	{
		$data = trim( Request::getVar( 'data' ) );
		$type = trim( Request::getVar( 'type' ) );
		$name = trim( Request::getVar( 'name' ) );
		Request::setVar( 'format', 'json' );
		$DataList = Helper::CleanArray( explode( ',', preg_replace( '/[^0-9,]/', '', $data ) ) );
		$content = '';
		$delete = Request::getVar( 'delete', false );
		if ( $delete )
		{
			$DataListD = array_flip( $DataList );
			if ( isset( $DataListD[$delete] ) )
			{
				unset( $DataListD[$delete] );
			}
			$DataList = array_flip( $DataListD );
		}
		$Response = new stdClass();
		$Response->idx = '';
		$Response->html = '';
		if ( count( $DataList ) )
		{
			$IDmx = implode( ',', $DataList );
			$Workers = $this->_getWorkers( $IDmx, $type );
			$Response = new stdClass();
			$IDx = array();
			ob_start();
			require 'tmpl.php';
			$content = ob_get_contents();
			$Response->idx = $IDx;
			$Response->html = $content;
			ob_clean();
		}
		return json_encode( $Response );

	}

	protected function _getWorkers( $Data, $type = '' )
	{
		$query = 'select u.id, '
						. ' u.firstname || \' \' || u.lastname as workername,'
						. ' u.email, '
						. ' u.private_number '
						. ' from slf_persons u '
						. ' where u.id in(' . $Data . ')'
		;
		if ( $type == 'assigned' )
		{
			$query = 'select k.id, '
							. ' u.firstname || \' \' || u.lastname || \' - \' || lu.lib_title || \' - \' ||  lss.lib_title as workername '
							. ' from  slf_worker k '
							. ' left join slf_persons u on u.id = k.person '
							. ' left join lib_unitorgs lu on lu.id = k.org '
							. ' left join lib_staff_schedules lss on lss.id = k.staff_schedule '
							. ' where k.id in(' . $Data . ')'
			;
		}
		return DB::LoadObjectList( $query );

	}

}
