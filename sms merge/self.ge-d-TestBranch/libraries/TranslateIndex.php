<?php
require_once PATH_BASE . DS . 'tables/helper.php';

class TranslateIndex
{
	public static $Table = null;
	public static $def_lng = null;

	public static function insert_xml_collection( $limited = 1 )
	{
		$all = self::collect_T();

		$collect = $all;
		if ( $limited == 1 )
		{
			$collect = array_slice( $all, 0, 1 );
		}

		if ( !count( $collect ) )
		{
			return true;
		}
		foreach ( $collect as $table => $columns )
		{
			if ( !count( $columns ) )
			{
				continue;
			}

			$query = 'select id, ' . mb_strtolower( implode( ', ', $columns ) ) . ' from ' . mb_strtolower( $table );
			$result = DB::LoadObjectList( $query );

			foreach ( $result as $data )
			{
				self::process_T( $table, $data, true );
			}
		}

		$count = count( $all ) - 1;
		if ( $count > 0 )
		{
			echo 'Rest: ' . ($count < 0 ? 0 : $count);
			return false;
		}

		return true;

	}

	public static function SaveData( $insert_id = 0, $table = '', $object = null )
	{
		$fields = $object->_TRANSLATE_FIELDS;
		$delete = C::_( 'ACTIVE', $object, 1 ) == -2 ? true : false;
		if ( $delete && self::delete_index( $table, '', $insert_id ) )
		{
			return true;
		}

		$data = [];
		foreach ( $fields as $column )
		{
			$data[$column] = $object->$column;
		}

		$data['ID'] = $insert_id;
		if ( !self::process_T( $table, $data, false, true ) )
		{
			return false;
		}

		return true;

	}

	public static function process_T( $table = '', $data = [], $lng_check = false, $update = false )
	{
		if ( empty( $table ) || empty( $data ) )
		{
			return false;
		}

		$indexes = self::collect_index_data();
		$collect = self::translate_T( $table, $data, $lng_check );

		foreach ( $collect as $column => $value )
		{
			if ( $column == 'ID' )
			{
				continue;
			}

			if ( empty( $value ) && !$lng_check && self::delete_index( $table, $column ) )
			{
				continue;
			}

			foreach ( (array) $value as $lng => $val )
			{
				if ( $lng_check && C::_( $table . '.' . $lng, $indexes, false ) )
				{
					continue;
				}

				if ( !self::insert_T( $table, C::_( 'ID', $data ), $column, $val, $lng, $update ) )
				{
					continue;
				}
			}
		}

		return true;

	}

	public static function translate_T( $table = '', $data = [], $lng_check = false )
	{
		if ( empty( $data ) )
		{
			return [];
		}

		$all_lng = self::collect_index_lngs( $table, $lng_check );

		$DATA = (object) $data;
		foreach ( $DATA as $column => $value )
		{
			if ( $column == 'ID' )
			{
				continue;
			}

			if ( empty( $value ) )
			{
				continue;
			}

			$Tscope = '';
			if ( in_array( $column, [ 'FIRSTNAME', 'LASTNAME' ] ) )
			{
				$Tscope = 'person';
			}

			$set = [];
			$set[self::$def_lng] = mb_strtolower( $value );
			foreach ( (array) $all_lng as $lng )
			{
				$set[$lng->LIB_CODE] = mb_strtolower( XTranslate::Translate( $value, $lng->LIB_CODE, $Tscope ) );
			}

			$DATA->$column = $set;
		}

		return $DATA;

	}

	public static function insert_T( $item_table = 0, $item_id = 0, $item_column = 0, $item_value = 0, $item_lng = '', $update = false )
	{
		if ( is_null( self::$Table ) )
		{
			self::$Table = new TableSlf_translate_indexInterface( 'slf_translate_index', 'ID', 'sqs_translate_index.nextval' );
		}

		$loads = [
				'ITEM_ID' => $item_id,
				'ITEM_TABLE' => $item_table,
				'ITEM_COLUMN' => $item_column,
				'ITEM_LNG' => $item_lng
		];

		self::$Table->resetAll();

		if ( $update )
		{
			self::$Table->loads( $loads );
		}

		self::$Table->ITEM_ID = $item_id;
		self::$Table->ITEM_TABLE = $item_table;
		self::$Table->ITEM_COLUMN = $item_column;
		self::$Table->ITEM_VALUE = $item_value;
		self::$Table->ITEM_LNG = $item_lng;
		if ( !self::$Table->store() )
		{
			return false;
		}

		return true;

	}

	public static function collect_index_lngs( $table = '', $lng_check = false )
	{
		static $collect_index_lngs = null;
		if ( is_null( self::$def_lng ) || is_null( $collect_index_lngs ) )
		{
			self::$def_lng = XTranslate::GetDefaultLang()->LIB_CODE;
			$all_lng = XTranslate::GetLangs();
			$index = self::collect_index_data();
			foreach ( $all_lng as $key => $value )
			{
				if ( $key == self::$def_lng )
				{
					continue;
				}
				if ( $lng_check && C::_( $table . '.' . $key, $index, false ) )
				{
					continue;
				}

				$collect_index_lngs[$key] = $value;
			}
		}

		return $collect_index_lngs;

	}

	public static function collect_index_data()
	{
		static $collect_index_data = null;
		if ( is_null( $collect_index_data ) )
		{
			$query = 'select k.* from slf_translate_index k';
			$result = DB::LoadObjectList( $query );
			$collect = [];
			foreach ( $result as $id => $value )
			{
				$collect[$value->ITEM_TABLE][$value->ITEM_LNG][$value->ITEM_COLUMN] = $value->ITEM_VALUE;
			}
			$collect_index_data = $collect;
		}

		return $collect_index_data;

	}

	public static function delete_index( $table = '', $column = '', $insert_id = 0 )
	{
		$query = 'delete from slf_translate_index x where '
						. ' x.item_table =  ' . DB::Quote( $table )
						. ($insert_id > 0 ? ' and x.item_id =  ' . (int) $insert_id : ' and x.item_column =  ' . DB::Quote( $column ))
		;

		if ( !DB::Delete( $query ) )
		{
			return false;
		}

		return true;

	}

	public static function collect_T()
	{
		$all_lng = array_keys( XTranslate::GetLangs() );
		$indexes = self::collect_index_data();
		$XMLTables = TableHelper::getXMLTablesList();

		$collect = [];
		foreach ( $XMLTables as $Table => $Item )
		{
			$kk = array_keys( C::_( trim( $Table ), $indexes, [] ) );
			$mm = array_diff( $all_lng, $kk );
			if ( array_key_exists( trim( $Table ), $indexes ) && !count( $mm ) )
			{
				continue;
			}

			$C = $Item->getElementByPath( 'columns' );

			$Columns = array();
			if ( !empty( $C->_children ) )
			{
				$Columns = $C->_children;
			}

			if ( !count( $Columns ) )
			{
				continue;
			}

			$count = null;
			foreach ( (object) $Columns as $Column )
			{
				$T = $Column->attributes( 'ti' );
				if ( $T == 1 )
				{
					if ( is_null( $count ) )
					{
						$query = 'select count(1) from ' . strtolower( $Table );
						$count = (int) DB::LoadResult( $query );
					}

					if ( empty( $count ) )
					{
						continue;
					}

					$collect[strtolower( $Table )][] = strtoupper( $Column->attributes( 'name' ) );
				}
			}
		}
		return $collect;

	}

}
