<?php
/**
 * @version		$Id: table.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */

/**
 * Abstract Table class
 *
 * Parent classes to all tables.
 *
 * @abstract
 * @package 	WSCMS.Framework
 * @subpackage	Table
 * @since		1.0
 * @tutorial	WSCMS.Framework/Table.cls
 */
class Table
{
	/**
	 * Name of the table in the db schema relating to child class
	 *
	 * @var 	string
	 * @access	protected
	 */
	protected $_tbl = '';

	/**
	 * Name of the primary key field in the table
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_tbl_key = '';

	/**
	 * sequence of the primary key field in the table
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_sequence = '';

	/**
	 * sequence of the primary key field in the table
	 *
	 * @var		string
	 * @access	protected
	 */
	protected $_insertid = '';

	/**
	 * Database connector
	 *
	 * @var  JDatabase object
	 * @access	protected
	 */
	protected $_db = null;
	protected $_DATE_FIELDS = array();
	protected $_BLOB_FIELDS = array();

	/**
	 * Object constructor to set table and key field
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access protected
	 * @param string $table name of the table in the db schema relating to child class
	 * @param string $key name of the primary key field in the table
	 */
	public function __construct( $table, $key, $sequence = false )
	{
		$this->_tbl = $table;
		$this->_tbl_key = $key;
		$this->_sequence = $sequence;

	}

	/**
	 * Gets the internal table name for the object
	 *
	 * @return string
	 * @since 1.5
	 */
	public function getTableName()
	{
		return $this->_tbl;

	}

	/**
	 * Gets the internal primary key name
	 *
	 * @return string
	 * @since 1.5
	 */
	public function getKeyName()
	{
		return $this->_tbl_key;

	}

	/**
	 * Resets the default properties
	 * @return	void
	 */
	public function reset()
	{
		$k = $this->_tbl_key;
		$props = $this->getDefaultProperties();
		foreach ( $props as $name => $value )
		{
			if ( $name != $k )
			{
				$this->{$name} = $value;
			}
		}

	}

	public function resetAll()
	{
		$props = $this->getDefaultProperties();
		foreach ( $props as $name => $value )
		{
			$this->{$name} = $value;
		}

	}

	/**
	 * Binds a named array/hash to this object
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access	public
	 * @param	$from	mixed	An associative array or object
	 * @param	$ignore	mixed	An array or space separated list of fields not to bind
	 * @return	boolean
	 */
	public function bind( $from, $ignore = array() )
	{
		$fromArray = is_array( $from );
		$fromObject = is_object( $from );

		if ( !$fromArray && !$fromObject )
		{
			$this->setError( get_class( $this ) . '::bind failed. Invalid from argument' );
			return false;
		}
		if ( !is_array( $ignore ) )
		{
			$ignore = explode( ' ', $ignore );
		}
		foreach ( $this->getProperties() as $k => $v )
		{
			// internal attributes of an object are ignored
			if ( !in_array( $k, $ignore ) )
			{
				if ( $fromArray && isset( $from[$k] ) )
				{
					$this->{$k} = $from[$k];
				}
				else if ( $fromObject && isset( $from->{$k} ) )
				{
					$this->{$k} = $from->{$k};
				}
			}
		}
		return true;

	}

	/**
	 * Loads a row from the database and binds the fields to the object properties
	 *
	 * @access	public
	 * @param	mixed	Optional primary key.  If not specifed, the value of current key is used
	 * @return	boolean	True if successful
	 */
	public function load( $oid = null, $key = null )
	{
		if ( $key )
		{
			property_exists( $this, $key ) ? $tbl_key = $key : '';
		}
		if ( empty( $tbl_key ) )
		{
			$tbl_key = $this->_tbl_key;
		}
//        if($oid !== null)
//        {
//            $this->{$tbl_key} = $oid;
//        }
//        $oid = $this->{$tbl_key};

		if ( $oid === null )
		{
			return false;
		}
		$this->reset();
		$select = '';
		if ( count( $this->_DATE_FIELDS ) )
		{
			foreach ( $this->_DATE_FIELDS as $k => $v )
			{
				$select .= ',  to_char(' . $k . ', \'' . $v . '\') ' . $k;
			}
		}
		$query = 'SELECT t.* ' . $select
						. ' FROM ' . $this->_tbl . ' t '
						. ' WHERE t.' . $tbl_key . ' = ' . $this->Quote( $oid )
		;
		$result = DB::LoadObject( $query );
		if ( !empty( $this->_BLOB_FIELDS ) )
		{
			foreach ( $this->_BLOB_FIELDS as $Blob )
			{
				if ( isset( $result->{$Blob} ) )
				{
					if ( is_object( $result->{$Blob} ) )
					{
						$result->{$Blob} = $result->{$Blob}->load();
					}
				}
				else
				{
					continue;
				}
			}
		}
		if ( $result )
		{
			return $this->bind( $result );
		}
		else
		{
			return false;
		}

	}

	public function loads( array $Data = array(), $Reset = true )
	{ // Implement JObservableInterface: Pre-processing by observers
//		$this->_observers->update( 'onBeforeLoad', array( $Keys, $Reset ) );
		if ( empty( $Data ) )
		{
			return false;
		}
		if ( !is_array( $Data ) )
		{
			return false;
		}

		// Load by primary key.
		$Keys = $this->getProperties();
		if ( $Reset )
		{
			$this->reset();
		}
		$select = '';
		if ( count( $this->_DATE_FIELDS ) )
		{
			foreach ( $this->_DATE_FIELDS as $k => $v )
			{
				$select .= ',  to_char(' . $k . ', \'' . $v . '\') ' . $k;
			}
		}

		$where = array();
		foreach ( $Data as $Key => $Value )
		{
			$where[] = ' t.' . $Key . ' = ' . DB::Quote( $Value );
		}
		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';

		$query = 'SELECT t.* ' . $select
						. ' FROM ' . $this->_tbl . ' t '
						. $whereQ
		;
		$result = DB::LoadObject( $query );
		if ( $result )
		{
			return $this->bind( $result );
		}
		else
		{
			return false;
		}

	}

	public function loadForUpdate( $oid = null, $key = null )
	{
		if ( $key )
		{
			property_exists( $this, $key ) ? $tbl_key = $key : '';
		}
		if ( empty( $tbl_key ) )
		{
			$tbl_key = $this->_tbl_key;
		}
//        if($oid !== null)
//        {
//            $this->{$tbl_key} = $oid;
//        }
//        $oid = $this->{$tbl_key};

		if ( $oid === null )
		{
			return false;
		}
		$this->reset();
		$select = '';
		if ( count( $this->_DATE_FIELDS ) )
		{
			foreach ( $this->_DATE_FIELDS as $k => $v )
			{
				$select .= ',  to_char(' . $k . ', \'' . $v . '\') ' . $k;
			}
		}
		$query = 'SELECT t.* ' . $select
						. ' FROM ' . $this->_tbl . ' t '
						. ' WHERE t.' . $tbl_key . ' = ' . $this->Quote( $oid )
						. ' for update '
		;
		$result = DB::LoadObject( $query );
		if ( $result )
		{
			return $this->bind( $result );
		}
		else
		{
			return false;
		}

	}

	/**
	 * Generic check method
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @return boolean True if the object is ok
	 */
	public function check()
	{
		return true;

	}

	/**
	 * Inserts a new row if id is zero or updates an existing row in the database table
	 *
	 * Can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param boolean If false, null object variables are not updated
	 * @return null|string null if successful otherwise returns and error message
	 */
	public function store( $updateNulls = false )
	{
		$k = $this->_tbl_key;

		if ( $this->{$k} )
		{
			$ret = $this->updateObject( $this->_tbl, $this, $this->_tbl_key, $updateNulls );
		}
		else
		{
			$ret = $this->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		if ( !$ret )
		{
			return false;
		}

		XRedis::CleanDBCache( $this->getTableName() );
		return true;

	}

	/**
	 * Description
	 *
	 * @access public
	 * @param $dirn
	 * @param $where
	 */
	public function move( $dirn, $where = '' )
	{
		if ( !in_array( 'ordering', array_keys( $this->getProperties() ) ) )
		{
			$this->setError( get_class( $this ) . ' does not support ordering' );
			return false;
		}
		$k = $this->_tbl_key;
		$sql = 'SELECT `' . $this->_tbl_key . '`, `ordering` FROM `' . $this->_tbl . '` ';
		if ( $dirn < 0 )
		{
			$sql .= ' WHERE `ordering` < ' . (int) $this->ordering;
			$sql .= ($where ? ' AND ' . $where : '');
			$sql .= ' ORDER BY `ordering` DESC';
		}
		else if ( $dirn > 0 )
		{
			$sql .= ' WHERE `ordering` > ' . (int) $this->ordering;
			$sql .= ($where ? ' AND ' . $where : '');
			$sql .= ' ORDER BY `ordering` ';
		}
		else
		{
			$sql .= ' WHERE `ordering` = ' . (int) $this->ordering;
			$sql .= ($where ? ' AND ' . $where : '');
			$sql .= ' ORDER BY `ordering` ';
		}
		$this->_db->setQuery( $sql, 0, 1 );
		$row = $this->_db->loadObject();
		if ( isset( $row ) )
		{
			$query = 'UPDATE `' . $this->_tbl . '` '
							. ' SET `ordering` = ' . (int) $row->ordering
							. ' WHERE `' . $this->_tbl_key . '` = ' . $this->_db->Quote( $this->{$k} )
			;
			$this->_db->setQuery( $query );

			if ( !$this->_db->query() )
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError( 500, $err );
			}
			$query = ' UPDATE `' . $this->_tbl . '` '
							. ' SET `ordering` = ' . (int) $this->ordering
							. ' WHERE `' . $this->_tbl_key . '` = ' . $this->_db->Quote( $row->{$k} )
			;
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError( 500, $err );
			}
			$this->ordering = $row->ordering;
		}
		else
		{
			$query = ' UPDATE `' . $this->_tbl . '` '
							. ' SET `ordering` = ' . (int) $this->ordering
							. ' WHERE `' . $this->_tbl_key . '` = ' . $this->_db->Quote( $this->{$k} )
			;
			$this->_db->setQuery( $query );
			if ( !$this->_db->query() )
			{
				$err = $this->_db->getErrorMsg();
				JError::raiseError( 500, $err );
			}
		}
		return true;

	}

	/**
	 * Returns the ordering value to place a new item last in its group
	 *
	 * @access public
	 * @param string query WHERE clause for selecting MAX(ordering).
	 */
	public function getNextOrder( $where = '' )
	{
		if ( !in_array( 'ordering', array_keys( $this->getProperties() ) ) )
		{
			$this->setError( get_class( $this ) . ' does not support ordering' );
			return false;
		}

		$query = ' SELECT MAX(`ordering`)'
						. ' FROM `' . $this->_tbl . '` '
						. ($where ? ' WHERE ' . $where : '');

		$this->_db->setQuery( $query );
		$maxord = $this->_db->loadResult();

		if ( $this->_db->getErrorNum() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return $maxord + 1;

	}

	/**
	 * Compacts the ordering sequence of the selected records
	 *
	 * @access public
	 * @param string Additional where query to limit ordering to a particular subset of records
	 */
	public function reorder( $where = '' )
	{
		$k = $this->_tbl_key;

		if ( !in_array( 'ordering', array_keys( $this->getProperties() ) ) )
		{
			$this->setError( get_class( $this ) . ' does not support ordering' );
			return false;
		}

		if ( $this->_tbl == '#__content_frontpage' )
		{
			$order2 = ", `content_id` DESC";
		}
		else
		{
			$order2 = "";
		}

		$query = ' SELECT `' . $this->_tbl_key . '`, `ordering` '
						. ' FROM `' . $this->_tbl . '` '
						. ' WHERE `ordering` >= 0' . ( $where ? ' AND ' . $where : '' )
						. ' ORDER BY `ordering`' . $order2
		;
		$this->_db->setQuery( $query );
		if ( !($orders = $this->_db->loadObjectList()) )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		// compact the ordering numbers
		for ( $i = 0, $n = count( $orders ); $i < $n; $i++ )
		{
			if ( $orders[$i]->ordering >= 0 )
			{
				if ( $orders[$i]->ordering != $i + 1 )
				{
					$orders[$i]->ordering = $i + 1;
					$query = ' UPDATE `' . $this->_tbl . '` '
									. ' SET `ordering` = ' . (int) $orders[$i]->ordering
									. ' WHERE `' . $k . '` = ' . $this->_db->Quote( $orders[$i]->{$k} )
					;
					$this->_db->setQuery( $query );
					$this->_db->query();
				}
			}
		}

		return true;

	}

	/**
	 * Generic check for whether dependancies exist for this object in the db schema
	 *
	 * can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @param string $msg Error message returned
	 * @param int Optional key index
	 * @param array Optional array to compiles standard joins: format [label=>'Label',name=>'table name',idfield=>'field',joinfield=>'field']
	 * @return true|false
	 */
	public function canDelete( $oid = null, $joins = null )
	{
		$k = $this->_tbl_key;
		if ( $oid )
		{
			$this->{$k} = intval( $oid );
		}

		if ( is_array( $joins ) )
		{
			$select = "$k";
			$join = "";
			foreach ( $joins as $table )
			{
				$select .= ', COUNT(DISTINCT `' . $table['idfield'] . '`) AS `' . $table['idfield'] . '`';
				$join .= ' LEFT JOIN `' . $table['name'] . '` ON `' . $table['joinfield'] . '` = ' . $k;
			}

			$query = ' SELECT `' . $select . '` '
							. ' FROM `' . $this->_tbl . '` '
							. $join
							. ' WHERE `' . $k . '` = ' . $this->_db->Quote( $this->{$k} )
							. ' GROUP BY `' . $k . '` '
			;
			$this->_db->setQuery( $query );

			if ( !$obj = $this->_db->loadObject() )
			{
				$this->setError( $this->_db->getErrorMsg() );
				return false;
			}
			$msg = array();
			$i = 0;
			foreach ( $joins as $table )
			{
				$k = $table['idfield'] . $i;
				if ( $obj->{$k} )
				{
					$msg[] = JText::_( $table['label'] );
				}
				$i++;
			}

			if ( count( $msg ) )
			{
				$this->setError( "noDeleteRecord" . ": " . implode( ', ', $msg ) );
				return false;
			}
			else
			{
				return true;
			}
		}

		return true;

	}

	/**
	 * Default delete method
	 *
	 * can be overloaded/supplemented by the child class
	 *
	 * @access public
	 * @return true if successful otherwise returns and error message
	 */
	public function delete( $oid = null )
	{
		$k = $this->_tbl_key;
		if ( $oid )
		{
			$this->{$k} = intval( $oid );
		}

		$query = 'DELETE FROM ' . $this->_tbl
						. ' WHERE ' . $this->_tbl_key . ' = ' . $this->Quote( $this->{$k} )
		;

		if ( DB::Delete( $query ) )
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	/**
	 * Checks out a row
	 *
	 * @access public
	 * @param	integer	The id of the user
	 * @param 	mixed	The primary key value for the row
	 * @return	boolean	True if successful, or if checkout is not supported
	 */
	public function checkout( $who, $oid = null )
	{
		if ( !in_array( 'checked_out', array_keys( $this->getProperties() ) ) )
		{
			return true;
		}

		$k = $this->_tbl_key;
		if ( $oid !== null )
		{
			$this->{$k} = $oid;
		}

		$date = JFactory::getDate();
		$time = $date->toMysql();

		$query = ' UPDATE ' . $this->_tbl .
						' SET `checked_out` = ' . (int) $who . ', `checked_out_time` = ' . $this->_db->Quote( $time ) .
						' WHERE `' . $this->_tbl_key . '` = ' . $this->_db->Quote( $this->{$k} );
		$this->_db->setQuery( $query );

		$this->checked_out = $who;
		$this->checked_out_time = $time;

		return $this->_db->query();

	}

	/**
	 * Checks in a row
	 *
	 * @access	public
	 * @param	mixed	The primary key value for the row
	 * @return	boolean	True if successful, or if checkout is not supported
	 */
	public function checkin( $oid = null )
	{
		if ( !(
						in_array( 'checked_out', array_keys( $this->getProperties() ) ) ||
						in_array( 'checked_out_time', array_keys( $this->getProperties() ) )
						) )
		{
			return true;
		}

		$k = $this->_tbl_key;

		if ( $oid !== null )
		{
			$this->{$k} = $oid;
		}

		if ( $this->{$k} == NULL )
		{
			return false;
		}

		$query = ' UPDATE ' . $this->_tbl .
						' SET `checked_out` = 0, `checked_out_time` = ' . $this->_db->Quote( $this->_db->getNullDate() ) .
						' WHERE `' . $this->_tbl_key . '` = ' . $this->_db->Quote( $this->{$k} );
		$this->_db->setQuery( $query );

		$this->checked_out = 0;
		$this->checked_out_time = '';

		return $this->_db->query();

	}

	/**
	 * Description
	 *
	 * @access public
	 * @param $oid
	 * @param $log
	 */
	public function hit( $oid = null )
	{
		if ( !in_array( 'hits', array_keys( $this->getProperties() ) ) )
		{
			return;
		}

		$k = $this->_tbl_key;

		if ( $oid !== null )
		{
			$this->{$k} = intval( $oid );
		}

		$query = ' UPDATE `' . $this->_tbl . '` '
						. ' SET `hits` = ( `hits` + 1 )'
						. ' WHERE `' . $this->_tbl_key . '`=' . $this->_db->Quote( $this->{$k} );
		$this->_db->setQuery( $query );
		$this->_db->query();
		$this->hits++;

	}

	/**
	 * Generic save function
	 *
	 * @access	public
	 * @param	array	Source array for binding to class vars
	 * @param	string	Filter for the order updating
	 * @param	mixed	An array or space separated list of fields not to bind
	 * @returns TRUE if completely successful, FALSE if partially or not succesful.
	 */
	function save( $source, $order_filter = '', $ignore = '' )
	{
		if ( !$this->bind( $source, $ignore ) )
		{
			return false;
		}
		if ( !$this->check() )
		{
			return false;
		}
		if ( !$this->store() )
		{
			return false;
		}
		if ( !$this->checkin() )
		{
			return false;
		}
		if ( $order_filter )
		{
			$filter_value = $this->{$order_filter};
			$this->reorder( $order_filter ? $order_filter . ' = ' . $this->_db->Quote( $filter_value ) : ''  );
		}
		$this->setError( '' );
		return true;

	}

	/**
	 * Generic Publish/Unpublish function
	 *
	 * @access public
	 * @param array An array of id numbers
	 * @param integer 0 if unpublishing, 1 if publishing
	 * @param integer The id of the user performnig the operation
	 * @since 1.0.4
	 */
	public function publish( $cid = null, $publishIN = 1, $user_idIN = 0 )
	{
		JArrayHelper::toInteger( $cid );
		$user_id = (int) $user_idIN;
		$publish = (int) $publishIN;
		$k = $this->_tbl_key;

		if ( count( $cid ) < 1 )
		{
			if ( $this->{$k} )
			{
				$cid = array( $this->{$k} );
			}
			else
			{
				$this->setError( "No items selected." );
				return false;
			}
		}

		$cids = '`' . $k . '`=' . implode( ' OR `' . $k . '`=', $cid );

		$query = ' UPDATE `' . $this->_tbl . '` '
						. ' SET `published` = ' . (int) $publish
						. ' WHERE (' . $cids . ')'
		;

		$checkin = in_array( 'checked_out', array_keys( $this->getProperties() ) );
		if ( $checkin )
		{
			$query .= ' AND (`checked_out` = 0 OR `checked_out` = ' . (int) $user_id . ')';
		}

		$this->_db->setQuery( $query );
		if ( !$this->_db->query() )
		{
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}

		if ( count( $cid ) == 1 && $checkin )
		{
			if ( $this->_db->getAffectedRows() == 1 )
			{
				$this->checkin( $cid[0] );
				if ( $this->{$k} == $cid[0] )
				{
					$this->published = $publish;
				}
			}
		}
		$this->setError( '' );
		return true;

	}

	/**
	 * Export item list to xml
	 *
	 * @access public
	 * @param boolean Map foreign keys to text values
	 */
	public function toXML( $mapKeysToText = false )
	{
		$xml = '<record table="' . $this->_tbl . '"';

		if ( $mapKeysToText )
		{
			$xml .= ' mapkeystotext="true"';
		}
		$xml .= '>';
		foreach ( get_object_vars( $this ) as $k => $v )
		{
			if ( is_array( $v ) or is_object( $v ) or $v === NULL )
			{
				continue;
			}
			if ( $k[0] == '_' )
			{ // internal field
				continue;
			}
			$xml .= '<' . $k . '><![CDATA[' . $v . ']]></' . $k . '>';
		}
		$xml .= '</record>';

		return $xml;

	}

	/**
	 * Add a directory where Table should search for table types. You may
	 * either pass a string or an array of directories.
	 *
	 * @access	public
	 * @param	string	A path to search.
	 * @return	array	An array with directory elements
	 * @since 1.5
	 */
	public static function addIncludePath( $path = null )
	{
		static $paths = null;

		if ( !isset( $paths ) )
		{
			$paths = array( dirname( __FILE__ ) . DS . 'table' );
		}

		// just force path to array
		settype( $path, 'array' );

		if ( !empty( $path ) && !in_array( $path, $paths ) )
		{
			// loop through the path directories
			foreach ( $path as $dir )
			{
				// no surrounding spaces allowed!
				$dir = trim( $dir );

				// add to the top of the search dirs
				// so that custom paths are searched before core paths
				array_unshift( $paths, $dir );
			}
		}
		return $paths;

	}

	/**
	 * Returns an associative array of object properties
	 *
	 * @access	public
	 * @param	boolean $public If true, returns only the public properties
	 * @return	array
	 * @see		get()
	 * @since	1.5
	 */
	public function getProperties( $public = true )
	{
		$vars = get_object_vars( $this );
		if ( $public )
		{
			foreach ( $vars as $key => $value )
			{
				if ( '_' == substr( $key, 0, 1 ) )
				{
					unset( $vars[$key] );
				}
			}
		}

		return $vars;

	}

	/**
	 * Returns an associative array of object properties
	 *
	 * @access	public
	 * @param	boolean $public If true, returns only the public properties
	 * @return	array
	 * @see		get()
	 * @since	1.5
	 */
	public function getDefaultProperties( $public = true )
	{
		static $newTable = null;
		if ( is_null( $newTable ) )
		{
			$class = get_class( $this );
			$newTable = new $class( $this->_tbl, $this->_tbl_key, $this->_sequence );
		}
		$vars = get_object_vars( $newTable );
		if ( $public )
		{
			foreach ( $vars as $key => $value )
			{
				if ( '_' == substr( $key, 0, 1 ) )
				{
					unset( $vars[$key] );
				}
			}
		}

		return $vars;

	}

	/**
	 * Description
	 *
	 * @access public
	 * @param [type] $updateNulls
	 */
	public function updateObject( $table, &$object, $keyName, $updateNulls = true )
	{
		if ( property_exists( $object, 'MODIFY_USER' ) )
		{
			$this->MODIFY_USER = Users::GetUserID();
		}

		if ( property_exists( $object, 'MODIFY_DATE' ) )
		{
			$this->MODIFY_DATE = PDate::Get()->toFormat();
		}

		$fmtsql = 'UPDATE ' . $table . ' SET %s WHERE %s';
		$tmp = array();
		$Blob = false;
		$BlobData = array();
		foreach ( get_object_vars( $object ) as $k => $v )
		{
			if ( is_array( $v ) or is_object( $v ) or $k[0] == '_' )
			{ // internal or NA field
				continue;
			}
			if ( $k == $keyName )
			{ // PK not to be updated
				$where = $keyName . '=' . $this->Quote( $v );
				continue;
			}
			if ( $v === null )
			{
				if ( $updateNulls )
				{
					$val = 'NULL';
				}
				else
				{
					continue;
				}
			}
			else
			{
				if ( isset( $this->_DATE_FIELDS[$k] ) )
				{
					$t = trim( $v );
					if ( !empty( $t ) )
					{
						$val = 'to_date(\'' . $v . '\', \'' . $this->_DATE_FIELDS[$k] . '\')';
					}
					else
					{
						$val = $this->Quote( $v );
					}
				}
				else if ( in_array( $k, $this->_BLOB_FIELDS ) )
				{
					$BlobData[$k] = $v;
					$Blob = true;
					continue;
				}
				else
				{
					$val = $this->Quote( $v );
				}
			}
			$tmp[] = $k . '=' . $val;
		}
		$query = sprintf( $fmtsql, implode( ",", $tmp ), $where );
		$this->_insertid = $this->{$keyName};
		$Update = DB::Update( $query );
		if ( $Blob )
		{
			$this->SetBlob( $this->_insertid, $BlobData );
		}

		if ( $Update && property_exists( $object, '_TRANSLATE_FIELDS' ) )
		{
			TranslateIndex::SaveData( $this->_insertid, $table, $object );
		}

		return $Update;

	}

	/**
	 * Inserts a row into a table based on an objects properties
	 *
	 * @access	public
	 * @param	string	The name of the table
	 * @param	object	An object whose properties match table fields
	 * @param	string	The name of the primary key. If provided the object property is updated.
	 */
	public function insertObject( $table, &$object, $keyName = NULL )
	{
		if ( property_exists( $object, 'MODIFY_USER' ) )
		{
			$this->MODIFY_USER = Users::GetUserID();
		}

		if ( property_exists( $object, 'MODIFY_DATE' ) )
		{
			$this->MODIFY_DATE = PDate::Get()->toFormat();
		}

		$fmtsql = 'INSERT INTO ' . $table . ' ( %s ) VALUES ( %s ) ';
		$fields = array();
		$Blob = false;
		$BlobData = array();
		foreach ( get_object_vars( $object ) as $k => $v )
		{
			if ( is_array( $v ) or is_object( $v ) or ( $v === NULL && $k != $this->_tbl_key) )
			{
				continue;
			}
			if ( $k[0] == '_' )
			{ // internal field
				continue;
			}
			if ( $this->_tbl_key == $k && empty( $v ) && $this->_sequence )
			{
				$values[] = $this->_sequence;
			}
			else
			{
				if ( isset( $this->_DATE_FIELDS[$k] ) )
				{
					$t = trim( $v );
					if ( !empty( $t ) )
					{
						$values[] = 'to_date(\'' . $v . '\', \'' . $this->_DATE_FIELDS[$k] . '\')';
					}
					else
					{
						$values[] = $this->Quote( $v );
					}
				}
				else if ( in_array( $k, $this->_BLOB_FIELDS ) )
				{
					$values[] = 'EMPTY_CLOB()';
					$BlobData[$k] = $this->{$k};
					$Blob = true;
				}
				else
				{
					$values[] = $this->Quote( $v );
				}
			}
			$fields[] = $k;
		}
		$Query = sprintf( $fmtsql, implode( ",", $fields ), implode( ",", $values ) );
		$this->_insertid = DB::Insert( $Query, $this->_tbl_key );
		if ( $keyName && $this->_insertid )
		{
			$object->{$keyName} = $this->_insertid;
		}
		if ( $Blob )
		{
			$this->SetBlob( $this->_insertid, $BlobData );
		}

		if ( !empty( $this->_insertid ) && property_exists( $object, '_TRANSLATE_FIELDS' ) )
		{
			TranslateIndex::SaveData( $this->_insertid, $table, $object );
		}

		return $this->_insertid;

	}

	/**
	 * @param $text
	 * @param bool $escaped
	 * @return string
	 */
	public static function Quote( $data )
	{
		return '\'' . str_replace( "'", "''", $data ) . '\'';

	}

//	public function Quote( $text, $escaped = true )
//	{
//		return '\'' . ($escaped ? $this->getEscaped( $text ) : $text) . '\'';
//
//	}

	public function getEscaped( $text )
	{
		$result = DB::Quote( stripcslashes( $text ) );
		return $result;

	}

	public function insertid()
	{
		return $this->_insertid;

	}

	public function SetBlob( $ID, $BlobData )
	{
		/* @var $Table TemplateTable */
		$Table = clone($this);
		$Table->reset();
		$Table->loadForUpdate( $ID );
		$conn = DB::getInstance();

		foreach ( $this->_BLOB_FIELDS as $BField )
		{
			if ( !isset( $BlobData[$BField] ) )
			{
				continue;
			}
			if ( is_object( $Table->{$BField} ) )
			{
				$Table->{$BField}->truncate();
				$Table->{$BField}->save( $BlobData[$BField] );
			}
			else
			{
				$query = 'update ' . $this->_tbl . ' t '
								. ' set t.' . $BField . ' = EMPTY_CLOB() '
								. ' WHERE t.' . $this->_tbl_key . ' = ' . $this->Quote( $ID )
				;
				DB::Update( $query );
				$Table->reset();
				$Table->loadForUpdate( $ID );
				$Table->{$BField}->truncate();
				$Table->{$BField}->save( $BlobData[$BField] );
			}
		}
		oci_commit( $conn );
		return true;

	}

	public function SetDateField( $Field, $Format )
	{
		$this->_DATE_FIELDS[$Field] = $Format;

	}

	public function setDATE_FIELDS( $Field, $Format )
	{
		$this->_DATE_FIELDS[$Field] = $Format;

	}

	/**
	 * 
	 * @staticvar type $finder
	 * @param type $id
	 * @param type $value
	 * @param type $column
	 * @param type $table
	 * @return boolean
	 */
	public function checkUnique( $column, $value, $pValue = null, $pKey = null, $table = null, $TKey = null )
	{
		$Key = md5( implode( '|', func_get_args() ) );
		static $finder = [];
		if ( !isset( $finder[$Key] ) )
		{
			if ( !$TKey )
			{
				$TKey = $this->_tbl_key;
			}
			if ( !$table )
			{
				$table = $this->_tbl;
			}
			$query = 'select '
							. $TKey
							. ' from ' . $table
							. ' where ' . $column . ' = ' . DB::Quote( $value );
			if ( $pValue )
			{
				if ( !$pKey )
				{
					$pKey = $this->_tbl_key;
				}
				$query .= ' and ' . $pKey . ' != ' . DB::Quote( $pValue );
			}
			if ( DB::LoadResult( $query ) )
			{
				$finder[$Key] = true;
			}
			else
			{
				$finder[$Key] = false;
			}
		}
		return $finder[$Key];

	}

	public function checkPermitUnique( $value, $ID )
	{
		$Key = md5( implode( '|', func_get_args() ) );
		static $finder = [];
		if ( !isset( $finder[$Key] ) )
		{
			$query = 'select '
							. ' pp.person '
							. ' from rel_person_permit pp '
							. ' left join slf_persons p on p.id = pp.person '
							. ' where pp.permit_id = ' . DB::Quote( $value );
			$query .= ' and pp.person != ' . DB::Quote( $ID )
							. '  and p.active > 0 '
			;
			if ( DB::LoadResult( $query ) )
			{
				$finder[$Key] = true;
			}
			else
			{
				$finder[$Key] = false;
			}
		}
		return $finder[$Key];

	}

}
