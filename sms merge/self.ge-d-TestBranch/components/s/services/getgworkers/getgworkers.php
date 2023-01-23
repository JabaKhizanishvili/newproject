<?php

class getgworkers
{
	public function GetService()
	{
		$data = trim( Request::getVar( 'data' ) );
		Request::setVar( 'format', 'json' );
		$DataList = $this->cleanData( explode( ',', preg_replace( '/[^0-9,]/', '', $data ) ) );
		$delete = Request::getVar( 'delete', false );
		$Group = Request::getVar( 'group', 0 );
		if ( $delete )
		{
			$DataListD = array_flip( $DataList );
			if ( isset( $DataListD[$delete] ) )
			{
				unset( $DataListD[$delete] );
			}
			$DataList = array_flip( $DataListD );
		}
		$content = '';
		$Response = new stdClass();
		$Response->idx = '';
		$Response->html = '';
		if ( !empty( $DataList ) )
		{
			$Workers = $this->_getWorkers( $DataList, $Group );

			$IDx = array();
			$Response = new stdClass();
			ob_start();
			require 'tmpl.php';
			$content = ob_get_contents();
			$Response->idx = $IDx;
			$Response->html = $content;
			ob_clean();
		}
		return json_encode( $Response );

	}

	protected function _getWorkers( $DataList, $Group )
	{
		if ( $Group > 0 )
		{
			$query = 'select u.id, '
							. ' u.firstname || \' \' || u.lastname  as workername, '
							. '  u.mobile_phone_number,   '
							. '  u.private_number   '
							. ' from ' . 'slf_persons u '
							. ' left join REL_WGROUPS wg on u.id = wg.worker '
							. ' and wg.group_id = ' . (int) $Group
							. ' where u.id in(' . implode( ',', $DataList ) . ')'
							. ' order by wg.ordering asc, u.firstname '
			;
		}
		else
		{
			$query = 'select u.id, '
							. ' u.firstname || \' \' || u.lastname  as workername, '
							. '  u.mobile_phone_number,   '
							. '  u.private_number   '
							. ' from ' . 'slf_persons u '
							. ' where u.id in(' . implode( ',', $DataList ) . ')'
							. ' order by u.firstname  asc'
			;
		}
		
		return DB::LoadObjectList( $query );

	}

	protected function cleanData( $DataList )
	{
		$Return = array();
		foreach ( $DataList as $d )
		{
			$d = trim( $d );
			if ( !empty( $d ) )
			{
				$Return[] = $d;
			}
		}
		return $Return;

	}

}
