<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
require_once 'table.php';

class LanguagesModel extends Model
{
	/** @var LanguageTable $this->Table */
	protected $Table = null;

	public function __construct( $params )
	{
		$this->Table = new LanguageTable();
		parent::__construct( $params );

	}

	/**
	 *
	 * @return type
	 */
	function getList()
	{
		/* @var $Return ModelReturn */
		$Return = $this->getReturn();
		$Return->text = mb_strtolower( trim( Request::getState( $this->_space, 'text', '' ) ) );
		$Return->lib_code = trim( Request::getState( $this->_space, 'lib_code', '' ) );
		$Return->active = (int) trim( Request::getState( $this->_space, 'active', '-1' ) );

		$where = array();
		if ( $Return->text )
		{
			$where[] = ' lower(t.lib_title) like ' . DB::Quote( '%' . $Return->text . '%' ) . ' or lower(t.lib_desc) like ' . DB::Quote( '%' . $Return->text . '%' );
		}
		if ( $Return->lib_code )
		{
			$where[] = ' t.lib_code like ' . DB::Quote( '%' . $Return->lib_code . '%' );
		}
		if ( $Return->active != '-1' )
		{
			$where[] = ' t.active= ' . $Return->active;
		}
		else
		{
			$where[] = 't.active >-1 ';
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$dir = ($Return->dir == 1) ? 'desc' : 'asc';
		$order = $Return->order;
		$order_by = ' order by ' . $order . ' ' . $dir;
		$countQuery = 'select count(*) from lib_languages t '
						. $whereQ
		;
		$Return->total = DB::LoadResult( $countQuery );
		$Query = 'select '
						. ' t.* '
						. ' from lib_languages t '
						. $whereQ
						. $order_by
		;
		$Limit_query = 'select * from ( '
						. ' select a.*, rownum rn from (' .
						$Query
						. ') a) where rn > '
						. $Return->start
						. ' and rn <= ' . $Return->limit;
		$Return->items = DB::LoadObjectList( $Limit_query );
		return $Return;

	}

	public function ChangeLangState( $data, $State = 0 )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				/** @var LanguageTable|  $this->Table */
				$this->Table->Load( $id );
				$this->Table->ACTIVE = $State;
				$this->Table->CHANGE_DATE = PDate::Get()->toFormat();
				$this->Table->CHANGE_USER = Users::GetUserID();
				if ( $this->Table->DEF_LANG == 1 && $State == 0 )
				{
					return false;
				}
				return $this->Table->store();
			}
		}
		return true;

	}

	public function SetDefault( $data )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				/** @var LanguageTable|  $this->Table */
				$this->Table->Load( $id );
				$this->Table->DEF_LANG = 1;
				$this->Table->CHANGE_DATE = PDate::Get()->toFormat();
				$this->Table->CHANGE_USER = Users::GetUserID();
				if ( $this->Table->ACTIVE == 0 )
				{
					return false;
				}
				if ( $this->Table->store() )
				{
					return $this->ResetDef( $this->Table->ID );
				}
				return false;
			}
		}

	}

	public function ResetDef( $ID )
	{
		$Q = 'update lib_languages '
						. ' set '
						. ' def_lang = 0, '
						. ' change_user=' . Users::GetUserID() . ', '
						. ' change_date=to_date( ' . DB::Quote( PDate::Get()->toFormat() ) . ', \'yyyy-mm-dd hh24:mi:ss\') '
						. ' where id <> ' . DB::Quote( $ID )
		;
		return DB::Update( $Q );

	}

}
