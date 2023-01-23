<?php
/**
 * @version		$Id: parameter.php 1 2011-07-13 05:09:23Z $
 * @package	WSCMS.Framework
 * @copyright	Copyright (C) 2009 - 2010 WebSolutions. All rights reserved.
 * @license		GNU General Public License version 2 or later
 */
// Check to ensure this file is within the rest of the framework
defined( 'PATH_BASE' ) or die();
require_once 'elements' . DS . 'grid.php';

/**
 * Parameter handler
 *
 * @package 	WSCMS.Framework
 * @subpackage		Parameter
 * @since		1.5
 */
require_once dirname( __FILE__ ) . DS . 'registry.php';

class Grid extends Registry
{
	/**
	 * The raw params string
	 *
	 * @access	private
	 * @var		string
	 * @since	1.5
	 */
	protected $_raw = null;

	/**
	 * The Data Array
	 *
	 * @access	private
	 * @var		string
	 * @since	1.5
	 */
	protected $_data = null;

	/**
	 * The xml params element
	 *
	 * @access	private
	 * @var		object
	 * @since	1.5
	 */
	protected $_xml = null;

	/**
	 * loaded elements
	 *
	 * @access	private
	 * @var		array
	 * @since	1.5
	 */
	protected $_elements = array();

	/**
	 * directories, where element types can be stored
	 *
	 * @access	private
	 * @var		array
	 * @since	1.5
	 */
	protected $_elementPath = array();

	/**
	 * current Grid Order By
	 *
	 * @access	private
	 * @var		array
	 * @since	1.5
	 */
	protected $_config = array();

	/**
	 * Constructor
	 *
	 * @access	protected
	 * @param	string The raw parms text
	 * @param	string Path to the xml setup file
	 * @since	1.5
	 */
	public function __construct( $data, $path = '', $config = array() )
	{

		$this->_defaultNameSpace = '_default';
		$this->makeNameSpace( $this->_defaultNameSpace );
		// Set base path
		$this->_elementPath[] = dirname( __FILE__ ) . DS . 'elements' . DS . 'grid';
		if ( is_array( $data ) )
		{
			$this->_data = $data;
		}

		if ( $path )
		{
			$this->loadSetupFile( $path );
		}
		$this->_config = $config;
		parent::__construct();

	}

	/**
	 * Create a namespace
	 *
	 * @access	public
	 * @param	string	$namespace	Name of the namespace to create
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function makeNameSpace( $namespace )
	{
		$this->_registry[$namespace] = array( 'data' => new stdClass() );
		return true;

	}

	/**
	 * Set a value
	 *
	 * @access	public
	 * @param	string The name of the param
	 * @param	string The value of the parameter
	 * @return	string The set value
	 * @since	1.5
	 */
	public function set( $key, $value = '', $group = '_default' )
	{
		return $this->setValue( $group . '.' . $key, (string) $value );

	}

	/**
	 * Get a value
	 *
	 * @access	public
	 * @param	string The name of the param
	 * @param	mixed The default value if not found
	 * @return	string
	 * @since	1.5
	 */
	public function get( $key, $default = '', $group = '_default' )
	{
		$value = $this->getValue( $group . '.' . $key );
		$result = (empty( $value ) && ($value !== 0) && ($value !== '0')) ? $default : $value;
		return $result;

	}

	/**
	 * Sets a default value if not alreay assigned
	 *
	 * @access	public
	 * @param	string	The name of the param
	 * @param	string	The value of the parameter
	 * @param	string	The parameter group to modify
	 * @return	string	The set value
	 * @since	1.5
	 */
	public function def( $key, $default = '', $group = '_default' )
	{
		$value = $this->get( $key, (string) $default, $group );
		return $this->set( $key, $value );

	}

	/**
	 * Sets the XML object from custom xml files
	 *
	 * @access	public
	 * @param	object	An XML object
	 * @since	1.5
	 */
	public function setXML( &$xml )
	{
		if ( is_object( $xml ) )
		{
			$group = $xml->attributes( 'group' );
			if ( $group )
			{
				$this->_xml[$group] = $xml;
			}
			else
			{
				$this->_xml['_default'] = $xml;
			}
		}

	}

	/**
	 * Bind data to the parameter
	 *
	 * @param	mixed	$data Array or Object
	 * @return	boolean	True if the data was successfully bound
	 * @access	public
	 * @since	1.5
	 */
	public function bind( $data, $group = '_default' )
	{
		if ( is_array( $data ) )
		{
			return $this->loadArray( $data, $group );
		}
		elseif ( is_object( $data ) )
		{
			return $this->loadObject( $data, $group );
		}
		else
		{
			return $this->loadINI( $data, $group );
		}

	}

	/**
	 * Render
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	string	HTML
	 * @since	1.5
	 */
	public function render( $group = '_default' )
	{
		if ( !isset( $this->_xml[$group] ) )
		{
			return false;
		}

		ob_start();
		$this->StartContainer();
		$this->StartTable( $group );
		$this->GenerateHeader( $group );
		$this->getTableFooter( $group );
		$this->getTableBody( $group );
		$this->EndTable( $group );
		$this->EndContainer();
		$this->getPagination( $group );
		$content = ob_get_clean();
		return $content;

	}

	/**
	 * Return number of params to render
	 *
	 * @access	public
	 * @return	mixed	Boolean falst if no params exist or integer number of params that exist
	 * @since	1.5
	 */
	public function getNumParams( $group = '_default' )
	{
		if ( !isset( $this->_xml[$group] ) || !count( $this->_xml[$group]->children() ) )
		{
			return false;
		}
		else
		{
			return count( $this->_xml[$group]->children() );
		}

	}

	/**
	 * Get the number of params in each group
	 *
	 * @access	public
	 * @return	array	Array of all group names as key and param count as value
	 * @since	1.5
	 */
	public function getGroups()
	{
		if ( !is_array( $this->_xml ) )
		{
			return false;
		}
		$results = array();
		foreach ( $this->_xml as $name => $group )
		{
			$results[$name] = $this->getNumParams( $name );
		}
		return $results;

	}

	/**
	 * Render all parameters
	 *
	 * @access	public
	 * @param	string	The name of the control, or the default text area if a setup file is not found
	 * @return	array	Aarray of all parameters, each as array Any array of the label, the form element and the tooltip
	 * @since	1.5
	 */
	public function getTableBody( $group = '_default' )
	{
		if ( !isset( $this->_xml[$group] ) )
		{
			return false;
		}
		$children = $this->_xml[$group]->children();
		?>
		<tbody>
			<?php
			$RowType = 'even';
			foreach ( $this->_data as $data )
			{
				$data = (object) $data;
				$ClassIf = $this->_xml[$group]->attributes( 'classif' );
				$C = $this->ExecClassIF( $ClassIf, $data );
				?>
				<tr class="<?php echo $RowType; ?> <?php echo $C; ?>">
					<?php
					$loadAddData = false;
					$loadAddItem = null;
					foreach ( $children as $param )
					{
						$id = '';
						if ( $param->attributes( 'adddata' ) == 1 )
						{
							$loadAddData = true;
							$loadAddItem = $param;
							$key = $param->attributes( 'key', 'ID' );
							$id = ' id="tab' . $data->{$key} . '"';
						}
						$class = $param->attributes( 'rclass' ) ? ' class="' . $param->attributes( 'rclass' ) . '" ' : '';
						?>
						<td<?php echo $class . $id; ?>>
							<?php $this->getParam( $param, (object) $data ); ?>
						</td>
						<?php
					}
					?>
				</tr>
				<?php
				if ( $RowType == 'even' )
				{
					$RowType = 'odd';
				}
				else
				{
					$RowType = 'even';
				}
				if ( $loadAddData && $loadAddItem )
				{
					$key = $loadAddItem->attributes( 'key', 'ID' );
					?>
					<tr class="dresrow" id="dresrow<?php echo $data->{$key}; ?>">
						<td id="dres<?php echo $data->{$key}; ?>" colspan="50" class="dres_cell">
							<div class="cell_loader">
								<img src="templates/images/loading-div.gif" alt="" />
							</div>
						</td>
					</tr>
					<?php
				}
			}
			?>
		</tbody>
		<?php

	}

	/**
	 * Render a parameter type
	 *
	 * @param	object	A param tag node
	 * @param	string	The control name
	 * @return	array	Any array of the label, the form element and the tooltip
	 * @since	1.5
	 */
	public function getParam( $node, $row, $group = '_default' )
	{
		//get the type of the parameter
		$type = trim( $node->attributes( 'type' ) );
		/* @var $element JGridElement */
		$element = $this->loadElement( $type );
		// error happened
		if ( $element === false )
		{
			echo Text::_( 'Element not defined for type' ) . ' = ' . $type;
		}
		else
		{
			$element->render( $row, $node, $this->_config );
		}

	}

	/**
	 * Loads an xml setup file and parses it
	 *
	 * @access	public
	 * @param	string	path to xml setup file
	 * @return	object
	 * @since	1.5
	 */
	public function loadSetupFile( $path )
	{
		$result = false;
		if ( $path )
		{
			require_once 'simplexml.php';
			$xml = new SimpleXML();
			if ( $xml->loadFile( $path ) )
			{
				if ( isset( $xml->document->params ) && $params = $xml->document->params )
				{
					foreach ( $params as $param )
					{
						$this->setXML( $param );
						$result = true;
					}
				}
			}
		}
		else
		{
			$result = true;
		}

		return $result;

	}

	/**
	 * Loads a element type
	 *
	 * @access	public
	 * @param	string	elementType
	 * @return	object
	 * @since	1.5
	 */
	public function loadElement( $type, $new = false )
	{
		$false = false;
		$signature = md5( $type );

		if ( (isset( $this->_elements[$signature] ) && !($this->_elements[$signature] instanceof __PHP_Incomplete_Class)) && $new === false )
		{
			return $this->_elements[$signature];
		}

		$elementClass = 'JGridElement' . $type;
		if ( !class_exists( $elementClass ) )
		{
			if ( isset( $this->_elementPath ) )
			{
				$dirs = $this->_elementPath;
			}
			else
			{
				$dirs = array();
			}

			$file = dirname( __FILE__ ) . DS . 'elements' . DS . 'grid' . DS . FilterInput::getInstance()->clean( str_replace( '_', DS, $type ) . '.php', 'path' );
			if ( is_file( $file ) )
			{
				include_once $file;
			}
		}

		if ( !class_exists( $elementClass ) )
		{
			return $false;
		}

		$this->_elements[$signature] = new $elementClass( $this );

		return $this->_elements[$signature];

	}

	/**
	 * Add a directory where Parameter should search for element types
	 *
	 * You may either pass a string or an array of directories.
	 *
	 * Parameter will be searching for a element type in the same
	 * order you added them. If the parameter type cannot be found in
	 * the custom folders, it will look in
	 * Parameter/types.
	 *
	 * @access	public
	 * @param	string|array	directory or directories to search.
	 * @since	1.5
	 */
	public function addElementPath( $path )
	{
		// just force path to array
		settype( $path, 'array' );

		// loop through the path directories
		foreach ( $path as $dir )
		{
			// no surrounding spaces allowed!
			$dir = trim( $dir );

			// add trailing separators as needed
			if ( substr( $dir, -1 ) != DIRECTORY_SEPARATOR )
			{
				// directory
				$dir .= DIRECTORY_SEPARATOR;
			}

			// add to the top of the search dirs
			array_unshift( $this->_elementPath, $dir );
		}

	}

	/**
	 * Get the list of namespaces
	 *
	 * @access	public
	 * @return	array	List of namespaces
	 * @since	1.5
	 */
	public function getNameSpaces()
	{
		return array_keys( $this->_registry );

	}

	/**
	 * Get a registry value
	 *
	 * @access	public
	 * @param	string	$regpath	Registry path (e.g. WSCMS.content.showauthor)
	 * @param	mixed	$default	Optional default value
	 * @return	mixed	Value of entry or null
	 * @since	1.5
	 */
	public function getValue( $regpath, $default = null )
	{
		$result = $default;
		// Explode the registry path into an array
		$nodes = explode( '.', $regpath );
		if ( $nodes )
		{
			// Get the namespace
			//$namespace = array_shift($nodes);
			$count = count( $nodes );
			if ( $count < 2 )
			{
				$namespace = $this->_defaultNameSpace;
				$nodes[1] = $nodes[0];
			}
			else
			{
				$namespace = $nodes[0];
			}
			if ( isset( $this->_registry[$namespace] ) )
			{
				$ns = $this->_registry[$namespace]['data'];
				$pathNodes = $count - 1;
				//for ($i = 0; $i < $pathNodes; $i ++) {
				for ( $i = 1; $i < $pathNodes; $i++ )
				{
					if ( (isset( $ns->{$nodes[$i]} ) ) )
					{
						$ns = $ns->{$nodes[$i]};
					}
				}

				if ( isset( $ns->{$nodes[$i]} ) )
				{
					$result = $ns->{$nodes[$i]};
				}
			}
		}
		return $result;

	}

	/**
	 * Set a registry value
	 *
	 * @access	public
	 * @param	string	$regpath 	Registry Path (e.g. WSCMS.content.showauthor)
	 * @param 	mixed	$value		Value of entry
	 * @return 	mixed	Value of old value or boolean false if operation failed
	 * @since	1.5
	 */
	public function setValue( $regpath, $value )
	{
		// Explode the registry path into an array
		$nodes = explode( '.', $regpath );

		// Get the namespace
		$count = count( $nodes );

		if ( $count < 2 )
		{
			$namespace = $this->_defaultNameSpace;
		}
		else
		{
			$namespace = array_shift( $nodes );
			$count--;
		}

		if ( !isset( $this->_registry[$namespace] ) )
		{
			$this->makeNameSpace( $namespace );
		}

		$ns = $this->_registry[$namespace]['data'];

		$pathNodes = $count - 1;

		if ( $pathNodes < 0 )
		{
			$pathNodes = 0;
		}

		for ( $i = 0; $i < $pathNodes; $i++ )
		{
			// If any node along the registry path does not exist, create it
			if ( !isset( $ns->{$nodes[$i]} ) )
			{
				$ns->{$nodes[$i]} = new stdClass();
			}
			$ns = $ns->{$nodes[$i]};
		}

		// Get the old value if exists so we can return it
		$ns->{$nodes[$i]} = $value;

		return $ns->{$nodes[$i]};

	}

	/**
	 * Load a associative array of values into the default namespace
	 *
	 * @access	public
	 * @param	array	$array		Associative array of value to load
	 * @param	string	$namepsace 	The name of the namespace
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function loadArray( $array, $namespace = null )
	{
		// If namespace is not set, get the default namespace
		if ( $namespace == null )
		{
			$namespace = $this->_defaultNameSpace;
		}

		if ( !isset( $this->_registry[$namespace] ) )
		{
			// If namespace does not exist, make it and load the data
			$this->makeNameSpace( $namespace );
		}

		// Load the variables into the registry's default namespace.
		foreach ( $array as $k => $v )
		{
			$this->_registry[$namespace]['data']->{$k} = $v;
		}

		return true;

	}

	/**
	 * Load the public variables of the object into the default namespace.
	 *
	 * @access	public
	 * @param	object	$object		The object holding the public vars to load
	 * @param	string	$namespace 	Namespace to load the INI string into [optional]
	 * @return	boolean	True on success
	 * @since	1.5
	 */
	public function loadObject( $object, $namespace = null )
	{
		// If namespace is not set, get the default namespace
		if ( $namespace == null )
		{
			$namespace = $this->_defaultNameSpace;
		}

		if ( !isset( $this->_registry[$namespace] ) )
		{
			// If namespace does not exist, make it and load the data
			$this->makeNameSpace( $namespace );
		}

		/*
		 * We want to leave groups that are already in the namespace and add the
		 * groups loaded into the namespace.  This overwrites any existing group
		 * with the same name
		 */
		if ( is_object( $object ) )
		{
			foreach ( get_object_vars( $object ) as $k => $v )
			{
				if ( substr( $k, 0, 1 ) != '_' || $k == '_name' )
				{
					$this->_registry[$namespace]['data']->{$k} = $v;
				}
			}
		}

		return true;

	}

	public function StartContainer()
	{
		echo '<div class="page-block FloatingScrollbar">';

	}

	public function StartTable( $group )
	{
		$IDx = '';
		$ID = $this->_xml[$group]->attributes( 'id' );
		if ( $ID )
		{
			$IDx = ' id="' . $ID . '"';
		}
		echo '<table class="' . $this->_xml[$group]->attributes( 'class' ) . '" ' . $IDx . '>';

	}

	public function EndTable()
	{
		echo '</table>';

	}

	public function EndContainer()
	{
		echo '</div>';

	}

	public function GenerateHeader( $group )
	{
		?>
		<thead>
			<tr>
				<?php
				foreach ( $this->_xml[$group]->children() as $param )
				{
					$class = $param->attributes( 'hclass' ) ? ' class="list_header ' . $param->attributes( 'hclass' ) . '" ' : ' class="list_header' . '" ';
					$order = $param->attributes( 'order' ) ? $param->attributes( 'order' ) : '';
					$label = $param->attributes( 'label' ) ? $param->attributes( 'label' ) : '';
					if ( $param->attributes( 'type' ) == 'checkall' )
					{
						$class = $param->attributes( 'hclass' ) ? ' class="list_header_x radio ' . $param->attributes( 'hclass' ) . '" ' : ' class="list_header_x radio' . '" ';
						?>
						<th>
							<div <?php echo $class; ?>>
								<input type="checkbox" id="checknid" name="nids" />
								<label for="checknid"></label>
							</div>
						</th>
						<?php
					}
					else
					{
						$_order = isset( $this->_config['data']->order ) ? $this->_config['data']->order : '';
						$_dir = isset( $this->_config['data']->dir ) ? $this->_config['data']->dir : '';
						if ( $param->attributes( 'hclass' ) == 'text-center' )
						{
							$class = ' class= "list_header_x" ';
						}
						?>
						<th>
							<div <?php echo $class; ?>>
								<span><?php echo Helper::getTableHeader( $order, $label, $_order, $_dir ); ?></span>
							</div>
						</th>
						<?php
					}
				}
				?>
		</thead>
		<?php

	}

	public function getTableFooter()
	{
		?>
		<tfoot>
			<tr>
				<td colspan="300">
				</td>
			</tr>
		</tfoot>
		<?php

	}

	public function getPagination( $group )
	{
		$total = isset( $this->_config['data']->total ) ? $this->_config['data']->total : '';
		$start = isset( $this->_config['data']->start ) ? $this->_config['data']->start : '';
		$paging = Pagination::Generate( $total, $start );
		if ( empty( $total ) )
		{
			return;
		}
		?>
		<div class="footer_block">
			<?php
			if ( !empty( $paging ) )
			{
				echo $paging;
			}
			?>	
			<div class="cls"></div>
		</div>
		<?php

	}

	public function ExecClassIF( $ClassIf, $data )
	{
		if ( empty( $ClassIf ) )
		{
			return '';
		}
		extract( (array) $data );
		return eval( 'return ' . $ClassIf . ';' );

	}

}
