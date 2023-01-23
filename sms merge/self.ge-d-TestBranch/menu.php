<?php

class MenuConfig
{
	protected $_menu = '';
	protected $_options = array();
	protected $_option = '';
	protected $active = array();
	protected $path = array();
	private $_parent = 0;
	private $_userRole = 0;
	private static $_UserMenu = null;
	private static $_UserTreeMenu = null;

	/**
	 * Object Constructor
	 */
	function __construct()
	{
		$this->_option = Request::getCmd( 'option', DEFAULT_COMPONENT );
		$this->_userRole = Users::GetUserRole();
		$this->getUserMenu();
		$this->GenOption();
		$this->SetActiveItemPath();

	}

	public static function getInstance()
	{
		static $instance = null;
		if ( !$instance )
		{
			$instance = new self();
		}
		return $instance;

	}

	public function CheckOption( $optionIN, $type )
	{
		$option = mb_strtolower( $optionIN );
		$ss = array_flip( $this->_options );

		if ( DEFAULT_COMPONENT == $option )
		{
			return true;
		}
		if ( isset( $ss[$option] ) )
		{
			return true;
		}
		return false;

	}

	/**
	 * Generate Option Array For different User Types
	 * @param int $type
	 */
	public function GenOption()
	{
		foreach ( self::$_UserMenu as $i )
		{
			if ( empty( $i->LIB_OPTION ) )
			{
				continue;
			}
			array_push( $this->_options, $i->LIB_OPTION );
			if ( $i->LIB_OPTION == $this->_option )
			{
				$this->active = $i;
			}
		}

	}

	public function IsActiveMenuItem( $option )
	{
		if ( isset( $this->path[$option] ) )
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	public function SetActiveItemPath()
	{
		$item = $this->active;
		$path = array();
		if ( !empty( $item ) )
		{
			do
			{
				$path[] = C::getVarIf( 'LIB_OPTION', $item, '', 'ID' );
				if ( $item->LIB_PARENT )
				{
					$item = C::_( $item->LIB_PARENT, self::$_UserMenu );
				}
				else
				{
					$this->_parent = $item->ID;
					$item = false;
				}
			}
			while ( $item );
		}
		$this->path = array_flip( $path );

	}

	/**
	 * 
	 * @return int
	 */
	public function getParent()
	{
		return $this->_parent;

	}

	/**
	 * Get Action
	 * 
	 * @return String
	 */
	public function getActive()
	{
		return $this->active;

	}

	public function getAllMenuItems( $tree = true, $where = '' )
	{
		static $Childrens = [];
		if ( empty( $Childrens ) )
		{
			$W = [ 'm.active > -1' ];
			if ( !empty( $where ) )
			{
				$W[] = $where;
			}
//			$whereQ = count( $W ) ? ' WHERE (' . implode( ') AND (', $W ) . ')' : '';
			$Query = ' select * from lib_menus m '
							. $where
							. ' order by m.ordering ';
			$Menus = XRedis::getDBCache( 'lib_menus', $Query, 'LoadObjectList' );
//			$Menus = DB::LoadObjectList( $query );
			$Childrens = array();
			foreach ( $Menus as $Menu )
			{
				if ( C::_( $Menu->LIB_PARENT, $Childrens ) )
				{
					$Childrens[$Menu->LIB_PARENT][] = $Menu;
				}
				else
				{
					$Childrens[$Menu->LIB_PARENT] = array();
					$Childrens[$Menu->LIB_PARENT][] = $Menu;
				}
			}
		}
		if ( $tree )
		{
			return $Childrens;
		}
		else
		{
			static $PlainData = null;
			if ( is_null( $PlainData ) )
			{
				$PlainData = $this->Tree2Plain( $Childrens );
			}
			if ( !count( $PlainData ) && count( $Childrens ) )
			{
				$collect = [];
				foreach ( $Childrens as $k => $ch )
				{
					$collect[$k] = $ch[0];
				}
				return $collect;
			}
			return $PlainData;
		}

	}

	public function Tree2Plain( $Childrens, $idx = 0, $key = 'ID' )
	{
		static $Plain = array();
		if ( isset( $Childrens[$idx] ) )
		{
			foreach ( $Childrens[$idx] as $Child )
			{
				if ( isset( $Child->{$key} ) )
				{
					$Plain[$Child->{$key}] = $Child;
				}
				$C = C::_( $Child->ID, $Childrens );
				if ( $C )
				{
					$this->Tree2Plain( $Childrens, $Child->ID, $key );
				}
			}
		}
		return $Plain;

	}

	public function getUserMenu( $tree = true )
	{
		if ( is_null( self::$_UserMenu ) )
		{
			if ( $this->_userRole == -500 )
			{
				$Query = 'select m.* from lib_menus m where m.active > 0 order by m.ordering asc';
			}
			else
			{
				$Query = 'select m.* '
								. ' from rel_roles_menus rm '
								. ' left join lib_menus m on m.id = rm.menu '
								. ' where rm.role = ' . $this->_userRole
								. ' and m.active > 0 '
								. ' order by m.ordering asc';
			}

			self::$_UserMenu = XRedis::getDBCache( 'rel_roles_menus', $Query, 'LoadObjectList', 'ID' );
//			self::$_UserMenu = DB::LoadObjectList( $query, 'ID' );
		}


		if ( is_null( self::$_UserTreeMenu ) )
		{
			foreach ( self::$_UserMenu as $Menu )
			{
				if ( C::_( $Menu->LIB_PARENT, self::$_UserTreeMenu ) )
				{
					self::$_UserTreeMenu[$Menu->LIB_PARENT][] = $Menu;
				}
				else
				{
					self::$_UserTreeMenu[$Menu->LIB_PARENT] = array();
					self::$_UserTreeMenu[$Menu->LIB_PARENT][] = $Menu;
				}
			}
		}
		if ( $tree )
		{
			return self::$_UserTreeMenu;
		}
		else
		{
			static $PlainData = null;
			if ( is_null( $PlainData ) )
			{
				$PlainData = $this->Tree2Plain( self::$_UserTreeMenu );
			}
			return $PlainData;
		}

	}

	public function getItem( $key, $value )
	{
		$menu = $this->getAllMenuItems();
		$MenuData = $this->Tree2Plain( $menu, 0, $key );
		return C::_( $value, $MenuData, false );

	}

}
