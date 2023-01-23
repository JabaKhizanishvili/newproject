<?php
require_once PATH_BASE . DS . 'defines/ModelReturn.php';

/**
 * Base class for a WSCMS Model
 *
 * Class holding methods for displaying presentation data.
 *
 * @abstract
 * @package		WSCMS.Framework
 * @subpackage	Application
 * @since		1.5
 */
class Model
{
	protected $_option = '';
	protected $_option_edit = '';
	protected $_order = '';
	protected $_dir = '';
	protected $_space = '';
	protected $_ToNumberIN = array();

	function __construct( $params )
	{
		if ( is_array( $params ) || is_object( $params ) )
		{
			foreach ( $params as $k => $p )
			{
				if ( isset( $this->$k ) )
				{
					$this->$k = $p;
				}
			}
		}

	}

	public function getReturn()
	{
		/* @var $Return ModelReturn */
		$Return = new ModelReturn();
		$Return->start = Request::getState( $this->_space, 'start', 0 );
		$Return->limit = $Return->start + Request::getState( 'items.limit.per.Page', 'pagination_limit', PAGE_ITEMS_LIMIT );
		$Return->order = Request::getState( $this->_space, 'order', $this->_order );
		$Return->dir = Request::getState( $this->_space, 'dir', $this->_dir );
		$Return->total = 0;
		$Return->items = array();
		return $Return;

	}

	public function SaveData( $data )
	{
		if ( !$this->Table->bind( $data ) )
		{
			return false;
		}
		if ( !$this->Table->check() )
		{
			return false;
		}
		if ( !$this->Table->store() )
		{
			return false;
		}
		return $this->Table->insertid();

	}

	public function Delete( $data, $mode = 'archive' )
	{
		if ( is_array( $data ) )
		{
			foreach ( $data as $id )
			{
				if ( 'archive' == mb_strtolower( $mode ) )
				{
					$this->Table->load( $id );
					$this->Table->ACTIVE = -2;
					if ( property_exists( $this->Table, 'DELETE_USER' ) && property_exists( $this->Table, 'DELETE_DATE' ) )
					{
						$Date = new PDate();
						$this->Table->setDATE_FIELDS( 'DELETE_DATE', 'yyyy-mm-dd HH24:mi:ss' );
						$this->Table->DELETE_USER = Users::GetUserID();
						$this->Table->DELETE_DATE = $Date->toFormat();
					}

					$this->Table->store();
				}
				else
				{
					$this->Table->Delete( $id );
				}
			}
		}
		return true;

	}

	public function ChangeState()
	{
		$idx = Request::getVar( 'nid', array() );
		if ( is_array( $idx ) )
		{
			foreach ( $idx as $id )
			{
				$this->Table->load( $id );
				$this->Table->ACTIVE = 1 - $this->Table->ACTIVE;
				$this->Table->store();
			}
		}
		return true;

	}

	public function Export( $tmpl = 'export' )
	{
		global $DisableSF;
		$DisableSF = 1;
		$Data = $this->getList( true );
		$Rows = HTML::renderExport( $Data->items, PATH_BASE . DS . 'components' . DS . $this->_option . DS . 'tmpl' . DS . 'export.xml' );
		$name = C::_( 'FILENAME', $Rows ) . '-' . date( 'Y-m-d' ) . '-' . time() . '.xlsx';
		$EData = array();
		$Cols = count( $Rows['HEADER'] );
		if ( empty( $Cols ) )
		{
			return false;
		}
		$EData[] = $Rows['HEADER'];
		foreach ( $Rows['ROWS'] as $d )
		{
			$add = array();
			if ( is_array( $d ) || is_object( $d ) )
			{
				foreach ( $d as $v )
				{
					if ( is_null( $v ) )
					{
						$v = Text::_( '' );
					}
					$add[] = $v;
				}
				$EData[] = $add;
			}
		}
		$XLSX = SimpleXLSXGen::fromArray( $EData );
		$XLSX->downloadAs( $name );
		die;

	}

//	public function Export( $tmpl = 'export' )
//	{
//		global $DisableSF;
//		$DisableSF = 1;
//		$Data = $this->getList( true );
//		$Rows = HTML::renderExport( $Data->items, PATH_BASE . DS . 'components' . DS . $this->_option . DS . 'tmpl' . DS . $tmpl . '.xml' );
//		$name = C::_( 'FILENAME', $Rows ) . '-' . date( 'Y-m-d' ) . '-' . time() . '.xls';
//		$filename = X_EXPORT_DIR . DS . $name;
//		$total = $Data->total ? $Data->total + 10 : 5;
//		require_once PATH_BASE . DS . 'libraries' . DS . 'excelxml.php';
//		$Cols = count( $Rows['HEADER'] );
//		if ( empty( $Cols ) )
//		{
//			return false;
//		}
//		$excel = new ExcelXml( $filename, $Cols, $total );
//		$excel->start();
//		$excel->addRow( $Rows['HEADER'] );
//		$ToNumber = array_flip( $this->_ToNumberIN );
//		foreach ( $Rows['ROWS'] as $d )
//		{
//			$add = array();
//			if ( is_array( $d ) || is_object( $d ) )
//			{
//				foreach ( $d as $k => $v )
//				{
//					if ( is_null( $v ) )
//					{
//						$v = Text::_( '' );
//					}
//					else if ( isset( $ToNumber[$k] ) )
//					{
//						$v = Helper::FormatBalance( $v, 2 );
//					}
//					$add[] = $v;
//				}
//				$excel->addRow( $add );
//			}
//		}
//
//		$excel->storeRow();
//		return $excel->finish( 1 );
//
//	}

	public function _search( $text = '', $column = null, $table = '' )
	{
		if ( empty( $text ) || empty( $column ) )
		{
			return 0;
		}

		if ( is_array( $column ) )
		{
			$column = trim( implode( '\', \'', $column ) );
		}

		$query = 'select '
						. ' t.item_id '
						. ' from slf_translate_index t '
						. ' where '
						. ' t.item_value like ' . DB::Quote( '%' . mb_strtolower( $text ) . '%' )
						. ' and t.item_column in (\'' . strtoupper( $column ) . '\') '
						. ' and t.item_table = ' . DB::Quote( $table )
		;
		$result = DB::LoadList( $query );
		if ( !count( $result ) )
		{
			return 0;
		}

		return implode( ', ', $result );

	}

}
