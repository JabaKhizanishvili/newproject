<?php

/**
 * Joomla! Cache base object
 *
 * @abstract
 * @package		Joomla.Framework
 * @subpackage	Cache
 * @since		1.5
 */
class XCache
{
	/**
	 * Storage Handler
	 * @access	private
	 * @var		object
	 */
	public $_handler;

	/**
	 * Cache Options
	 * @access	private
	 * @var		array
	 */
	public $_options;
	static $_instance = NULL;

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	array	$options	options
	 */
	public function __construct( $group, $options )
	{
		$this->_options = & $options;
		if ( isset( $options['cachebase'] ) )
		{
			$this->_options['cachebase'] = $options['cachebase'];
		}
		else
		{
			$this->_options['cachebase'] = PATH_BASE . DS . 'cache';
		}

		if ( isset( $group ) )
		{
			$this->_options['defaultgroup'] = $group;
		}
		else
		{
			$this->_options['defaultgroup'] = 'Default';
		}
		if ( empty( $this->_options['lifetime'] ) )
		{
			$this->_options['lifetime'] = 15 * 60;
		}

		if ( isset( $options['caching'] ) )
		{
			$this->_options['caching'] = $options['caching'];
		}
		else
		{
			$this->_options['caching'] = true;
		}

	}

	/**
	 * Returns a reference to a cache adapter object, always creating it
	 *
	 * @static
	 * @param	string	$type	The cache object type to instantiate
	 * @return	object	A JCache object
	 * @since	1.5
	 */
	public static function getInstance( $group = 'Default', $options = array() )
	{
		require_once(dirname( __FILE__ ) . DS . 'output.php');
		if ( self::$_instance == NULL )
		{
			$instance = new XCacheOutput( $group, $options );
		}
		else
		{
			$instance->_options['defaultgroup'] = $group;
		}
		return $instance;

	}

	public function GetCache( $name, $data )
	{
		$c = $this->get( $name );
		if ( $c )
		{
			return $c;
		}
		else
		{
			$this->store( $data, $name );
		}
		return false;

	}

	public function GetTimeStamp( $now = false )
	{
		$parts = 60 / TIMECACHE;
		$p = 0;
		if ( $now )
		{
			$time = $now;
		}
		else
		{
			$time = time();
		}

		$t = getdate( $time );
		$tc = 0;
		while ( $p <= $t['minutes'] )
		{
			$tc = $p;
			$p += $parts;
		}
		$end = $time - $t['seconds'] - $t['minutes'] * 60 + $tc * 60;
		return $end;

	}

	/**
	 * Get the storage handlers
	 *
	 * @access public
	 * @return array An array of available storage handlers
	 */
	public function getStores()
	{
		jimport( 'joomla.filesystem.folder' );
		$handlers = JFolder::files( dirname( __FILE__ ) . DS . 'cache' . DS . 'storage', '.php' );

		$names = array();
		foreach ( $handlers as $handler )
		{
			$name = substr( $handler, 0, strrpos( $handler, '.' ) );
			$class = 'JCacheStorage' . $name;

			if ( !class_exists( $class ) )
			{
				require_once(dirname( __FILE__ ) . DS . 'cache' . DS . 'storage' . DS . $name . '.php');
			}

			if ( call_user_func_array( array( trim( $class ), 'test' ), array() ) )
			{
				$names[] = $name;
			}
		}

		return $names;

	}

	/**
	 * Set caching enabled state
	 *
	 * @access	public
	 * @param	boolean	$enabled	True to enable caching
	 * @return	void
	 * @since	1.5
	 */
	public function setCaching( $enabled )
	{
		$this->_options['caching'] = $enabled;

	}

	/**
	 * Set cache lifetime
	 *
	 * @access	public
	 * @param	int	$lt	Cache lifetime
	 * @return	void
	 * @since	1.5
	 */
	public function setLifeTime( $lt )
	{
		$this->_options['lifetime'] = $lt;

	}

	/**
	 * Set cache validation
	 *
	 * @access	public
	 * @return	void
	 * @since	1.5
	 */
	public function setCacheValidation()
	{
		// Deprecated

	}

	/**
	 * Get cached data by id and group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	mixed	Boolean false on failure or a cached data string
	 * @since	1.5
	 */
	public function get( $id, $group = null )
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler
		$handler = $this->_getStorage();
		if ( $handler && $this->_options['caching'] )
		{
			return $handler->get( $id, $group, (isset( $this->_options['checkTime'] )) ? $this->_options['checkTime'] : true );
		}
		return false;

	}

	/**
	 * Store the cached data by id and group
	 *
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @param	mixed	$data	The data to store
	 * @return	boolean	True if cache stored
	 * @since	1.5
	 */
	public function Store( $data, $id, $group = null )
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler and store the cached data
		$handler = $this->_getStorage();
		if ( $handler && $this->_options['caching'] )
		{
			return $handler->store( $id, $group, $data );
		}
		return false;

	}

	/**
	 * Remove a cached data entry by id and group
	 *
	 * @abstract
	 * @access	public
	 * @param	string	$id		The cache data id
	 * @param	string	$group	The cache data group
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	public function remove( $id, $group = null )
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler
		$handler = & $this->_getStorage();
		if ( $handler )
		{
			return $handler->remove( $id, $group );
		}
		return false;

	}

	/**
	 * Clean cache for a group given a mode.
	 *
	 * group mode		: cleans all cache in the group
	 * notgroup mode	: cleans all cache not in the group
	 *
	 * @access	public
	 * @param	string	$group	The cache data group
	 * @param	string	$mode	The mode for cleaning cache [group|notgroup]
	 * @return	boolean	True on success, false otherwise
	 * @since	1.5
	 */
	public function clean( $group = null, $mode = 'group' )
	{
		// Get the default group
		$group = ($group) ? $group : $this->_options['defaultgroup'];

		// Get the storage handler
		$handler = & $this->_getStorage();
		if ( $handler )
		{
			return $handler->clean( $group, $mode );
		}
		return false;

	}

	/**
	 * Garbage collect expired cache data
	 *
	 * @access public
	 * @return boolean  True on success, false otherwise.
	 * @since	1.5
	 */
	public function gc()
	{
		// Get the storage handler
		$handler = & $this->_getStorage();
		if ( !JError::isError( $handler ) )
		{
			return $handler->gc();
		}
		return false;

	}

	/**
	 * Get the cache storage handler
	 *
	 * @access protected
	 * @return object A JCacheStorage object
	 * @since	1.5
	 */
	public function _getStorage()
	{
		require_once(dirname( __FILE__ ) . DS . 'file.php');
		$this->_handler = new XCacheStorageFile( $this->_options );
		return $this->_handler;

	}

}
