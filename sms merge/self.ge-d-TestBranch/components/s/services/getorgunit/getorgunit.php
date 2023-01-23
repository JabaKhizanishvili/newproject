<?php

class getorgunit
{
	public function GetService()
	{
		$data = trim( Request::getVar( 'data' ) );
		$ORG = trim( Request::getVar( 'org' ) );
		Request::setVar( 'format', 'json' );
		$Data = (int) trim( preg_replace( '/[^0-9,]/', '', $data ) );
		$content = '';
		$Response = new stdClass();
		$Response->idx = '';
		$Response->html = '';
		if ( !empty( $Data ) )
		{
			$OrgUnit = $this->_getOrgUnit( $Data, $ORG );
			$Response = new stdClass();
			$IDx = 0;
			ob_start();
			require 'tmpl.php';
			$content = ob_get_contents();
			$Response->idx = $IDx;
			$Response->html = $content;
			ob_clean();
		}
		return json_encode( $Response );

	}

	protected function _getOrgUnit( $Data, $ORG )
	{
		$query = 'select o.*,'
						. ' ( select '
						. ' LISTAGG(t.lib_title, \' / \') WITHIN GROUP (ORDER BY t.lft) '
						. ' from LIB_UNITS t '
						. ' left join lib_unittypes ut on ut.id = t.type '
						. ' left join lib_units u on u.lft > t.lft and u.rgt < t.rgt '
						. ' where '
						. ' t.active > 0 '
						. ' and u.id is not null '
						. ' and u.id = o.id '
						. ' and t.org = ' . (int) $ORG
						. ' ) orgpath '
						. ' from lib_units o '
						. ' where o.id =' . (int) $Data
		;		
		return DB::LoadObject( $query );

	}

}
