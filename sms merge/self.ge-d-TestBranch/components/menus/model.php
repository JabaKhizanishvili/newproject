<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );

class MenusModel extends Model
{
	/**
	 * 
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$order_by = ' order by t.ordering';
		$Return->lib_title = trim( Request::getState( $this->_space, 'lib_title', '' ) );
		$Return->lib_option = trim( Request::getState( $this->_space, 'lib_option', '' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );
		$where = array();

		if ( $Return->lib_title )
		{
			$where[] = ' m.id in (' . $this->_search( $Return->lib_title, [ 'lib_title' ], 'lib_menus' ) . ')';
		}
		if ( $Return->lib_option )
		{
			$where[] = ' m.lib_option like ' . DB::Quote( '%' . $Return->lib_option . '%' );
		}

		$where[] = 'm.active > -1';

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$Return->total = 0;
		$Menu = MenuConfig::getInstance();
		$Menus = $Menu->getAllMenuItems( false, $whereQ );
		foreach ( $Menus as $key => $m )
		{
			$m->LIB_TITLE = XTranslate::_( $m->LIB_TITLE );
			$preT = str_repeat( '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $m->LIB_LEVEL );
			$pre = (!empty( $preT )) ? $preT . '<sup>|_</sup>&nbsp;' : '';
			$Desc = Collection::get( 'LIB_DESC', $m, '' );
			if ( $Desc )
			{
				$Desc = ' ( ' . $Desc . ' ) ';
			}
			$m->LIB_TITLE = $pre . $m->LIB_TITLE . $Desc;
			$Menus[$key] = $m;
		}
		$Return->items = $Menus;
		return $Return;

	}

}
