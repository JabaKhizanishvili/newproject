<?php

/**
 * Base class for a WSCMS View
 *
 * Class holding methods for displaying presentation data.
 *
 * @abstract
 * @package		WSCMS.Framework
 * @subpackage	Application
 * @since		1.5
 */
class View extends XObject
{
	/**
	 * The name of the view
	 *
	 * @var		array
	 * @access protected
	 */
	protected $_name = null;
	private $disable_config = false;

	/**
	 * show_help 
	 *
	 * @var boolean
	 * @access protected
	 */
	protected $_show_help = true;

	/**
	 * Registered models
	 *
	 * @var		array
	 * @access protected
	 */
	protected $_models = array();

	/**
	 * The base path of the view
	 *
	 * @var		string
	 * @access 	protected
	 */
	protected $_basePath = null;

	/**
	 * The default model
	 *
	 * @var	string
	 * @access protected
	 */
	protected $_defaultModel = null;

	/**
	 * Layout name
	 *
	 * @var		string
	 * @access 	protected
	 */
	protected $_layout = 'default';

	/**
	 * Layout extension
	 *
	 * @var		string
	 * @access 	protected
	 */
	protected $_layoutExt = 'php';

	/**
	 * The set of search directories for resources (tempslates)
	 *
	 * @var array
	 * @access protected
	 */
	protected $_path = array(
			'template' => array(),
			'helper' => array()
	);

	/**
	 * The name of the default template source file.
	 *
	 * @var string
	 * @access private
	 */
	protected $_template = null;

	/**
	 * The output of the template script.
	 *
	 * @var string
	 * @access private
	 */
	protected $_output = null;

	/**
	 * Callback for escaping.
	 *
	 * @var string
	 * @access private
	 */
	protected $_escape = 'htmlspecialchars';

	/**
	 * Charset to use in escaping mechanisms; defaults to urf8 (UTF-8)
	 *
	 * @var string
	 * @access private
	 */
	protected $_charset = 'UTF-8';

	/**
	 * Constructor
	 *
	 * @access	protected
	 */
	public function __construct( $component = DEFAULT_COMPONENT, $layout = 'default' )
	{
		$this->_name = $component;
		$this->_basePath = PATH_BASE . DS . 'components';
		$this->_baseComponent = $this->_basePath . DS . $this->getName() . DS . 'tmpl';
		$this->template = X_PATH_TEMPLATE . DS . 'html' . DS . $this->getName() . DS . 'tmpl';

		if ( $layout )
		{
			$this->setLayout( $layout );
		}
		else
		{
			$this->setLayout( 'default' );
		}

		$this->baseurl = URI::base( true );

	}

	/**
	 * Execute and display a template script.
	 *
	 * @param string $tpl The name of the template file to parse;
	 * automatically searches through the template paths.
	 *
	 * @throws object An JError object.
	 * @see fetch()
	 */
	public function display( $tpl = null )
	{
		$result = $this->loadTemplate( $tpl );
		echo $result;

	}

	/**
	 * Assigns variables to the view script via differing strategies.
	 *
	 * This method is overloaded; you can assign all the properties of
	 * an object, an associative array, or a single value by name.
	 *
	 * You are not allowed to set variables that begin with an underscore;
	 * these are either private properties for JView or private variables
	 * within the template script itself.
	 *
	 * <code>
	 * $view = new JView();
	 *
	 * // assign directly
	 * $view->var1 = 'something';
	 * $view->var2 = 'else';
	 *
	 * // assign by name and value
	 * $view->assign('var1', 'something');
	 * $view->assign('var2', 'else');
	 *
	 * // assign by assoc-array
	 * $ary = array('var1' => 'something', 'var2' => 'else');
	 * $view->assign($obj);
	 *
	 * // assign by object
	 * $obj = new stdClass;
	 * $obj->var1 = 'something';
	 * $obj->var2 = 'else';
	 * $view->assign($obj);
	 *
	 * </code>
	 *
	 * @access public
	 * @return bool True on success, false on failure.
	 */
	public function assign()
	{
		// get the arguments; there may be 1 or 2.
		$arg0 = @func_get_arg( 0 );
		$arg1 = @func_get_arg( 1 );

		// assign by object
		if ( is_object( $arg0 ) )
		{
			// assign public properties
			foreach ( get_object_vars( $arg0 ) as $key => $val )
			{
				if ( substr( $key, 0, 1 ) != '_' )
				{
					$this->$key = $val;
				}
			}
			return true;
		}

		// assign by associative array
		if ( is_array( $arg0 ) )
		{
			foreach ( $arg0 as $key => $val )
			{
				if ( substr( $key, 0, 1 ) != '_' )
				{
					$this->$key = $val;
				}
			}
			return true;
		}

		// assign by string name and mixed value.
		// we use array_key_exists() instead of isset() becuase isset()
		// fails if the value is set to null.
		if ( is_string( $arg0 ) && substr( $arg0, 0, 1 ) != '_' && func_num_args() > 1 )
		{
			$this->$arg0 = $arg1;
			return true;
		}

		// $arg0 was not object, array, or string.
		return false;

	}

	/**
	 * Assign variable for the view (by reference).
	 *
	 * You are not allowed to set variables that begin with an underscore;
	 * these are either private properties for JView or private variables
	 * within the template script itself.
	 *
	 * <code>
	 * $view = new JView();
	 *
	 * // assign by name and value
	 * $view->assignRef('var1', $ref);
	 *
	 * // assign directly
	 * $view->ref =& $var1;
	 * </code>
	 *
	 * @access public
	 *
	 * @param string $key The name for the reference in the view.
	 * @param mixed &$val The referenced variable.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function assignRef( $key, &$val )
	{
		if ( is_string( $key ) && substr( $key, 0, 1 ) != '_' )
		{
			$this->$key = & $val;
			return true;
		}

		return false;

	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is one of htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
	 * @param  mixed $var The output to escape.
	 * @return mixed The escaped value.
	 */
	public function escape( $var )
	{
		if ( in_array( $this->_escape, array( 'htmlspecialchars', 'htmlentities' ) ) )
		{
			return call_user_func( $this->_escape, $var, ENT_COMPAT, $this->_charset );
		}

		return call_user_func( $this->_escape, $var );

	}

	/**
	 * Method to get data from a registered model or a property of the view
	 *
	 * @access	public
	 * @param	string	The name of the method to call on the model, or the property to get
	 * @param	string	The name of the model to reference, or the default value [optional]
	 * @return mixed	The return value of the method
	 */
	public function get( $property, $default = null )
	{

		// If $model is null we use the default model
		if ( is_null( $default ) )
		{
			$model = $this->_defaultModel;
		}
		else
		{
			$model = mb_strtolower( $default );
		}

		// First check to make sure the model requested exists
		if ( isset( $this->_models[$model] ) )
		{
			// Model exists, lets build the method name
			$method = 'get' . ucfirst( $property );

			// Does the method exist?
			if ( method_exists( $this->_models[$model], $method ) )
			{
				// The method exists, lets call it and return what we get
				$result = $this->_models[$model]->$method();
				return $result;
			}
		}

		// degrade to JObject::get
		$result = parent::get( $property, $default );
		return $result;

	}

	/**
	 * Method to get the model object
	 *
	 * @access	public
	 * @param	string	$name	The name of the model (optional)
	 * @return	mixed			JModel object
	 */
	public function getModel()
	{
		$params = (object) get_object_vars( $this );
		$AltModelFileOverride = X_PATH_BASE . DS . 'override' . DS . X_DOMAIN . DS . $this->getName() . DS . 'model.php';
		$AltModelFileTemplate = X_PATH_TEMPLATE . DS . 'html' . DS . $this->getName() . DS . 'model.php';
		$modelFile = $this->_basePath . DS . $this->_name . DS . 'model.php';
		if ( is_file( $AltModelFileOverride ) )
		{
			require_once $AltModelFileOverride;
		}
		else if ( is_file( $AltModelFileTemplate ) )
		{
			require_once $AltModelFileTemplate;
		}
		else if ( is_file( $modelFile ) )
		{
			require_once $modelFile;
		}

		$name = mb_strtolower( $this->_name . 'Model' );
		if ( class_exists( $name ) )
		{
			return new $name( $params );
		}
		return NULL;

	}

	/**
	 * Get the layout.
	 *
	 * @access public
	 * @return string The layout name
	 */
	public function getLayout()
	{
		return $this->_layout;

	}

	/**
	 * Method to get the view name
	 *
	 * The model name by default parsed using the classname, or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @access	public
	 * @return	string The name of the model
	 * @since	1.5
	 */
	public function getName()
	{
		$name = $this->_name;

		if ( empty( $name ) )
		{
			$r = null;
			if ( !preg_match( '/View((view)*(.*(view)?.*))$/i', get_class( $this ), $r ) )
			{
				JError::raiseError( 500, "JView::getName() : Cannot get or parse class name." );
			}
			if ( strpos( $r[3], "view" ) )
			{
				JError::raiseWarning( 'SOME_ERROR_CODE', "JView::getName() : Your classname contains the substring 'view'. " .
								"This causes problems when extracting the classname from the name of your objects view. " .
								"Avoid Object names with the substring 'view'." );
			}
			$name = mb_strtolower( $r[3] );
		}

		return $name;

	}

	/**
	 * Method to add a model to the view.  We support a multiple model single
	 * view system by which models are referenced by classname.  A caveat to the
	 * classname referencing is that any classname prepended by JModel will be
	 * referenced by the name without JModel, eg. JModelCategory is just
	 * Category.
	 *
	 * @access	public
	 * @param	object	$model		The model to add to the view.
	 * @param	boolean	$default	Is this the default model?
	 * @return	object				The added model
	 */
	public function setModel( $model, $default = false )
	{
		$name = mb_strtolower( $model->getName() );
		$this->_models[$name] = $model;

		if ( $default )
		{
			$this->_defaultModel = $name;
		}
		return $model;

	}

	/**
	 * Sets the layout name to use
	 *
	 * @access	public
	 * @param	string $template The template name.
	 * @return	string Previous value
	 * @since	1.5
	 */
	public function setLayout( $layout )
	{
		$previous = $this->_layout;
		$this->_layout = $layout;
		return $previous;

	}

	/**
	 * Allows a different extension for the layout files to be used
	 *
	 * @access	public
	 * @param	string	The extension
	 * @return	string	Previous value
	 * @since	1.5
	 */
	public function setLayoutExt( $value )
	{
		$previous = $this->_layoutExt;
		$value = preg_replace( '#[^A-Za-z0-9]#', '', trim( $value ) );
		if ( $value )
		{
			$this->_layoutExt = $value;
		}
		return $previous;

	}

	/**
	 * Sets the _escape() callback.
	 *
	 * @param mixed $spec The callback for _escape() to use.
	 */
	public function setEscape( $spec )
	{
		$this->_escape = $spec;

	}

	/**
	 * Load a tempslate file -- first look in the templateds folder for an override
	 *
	 * @access	public
	 * @param string $tpl The name of the template source file ...
	 * automatically searches the template paths and compiles as needed.
	 * @return string The output of the the template script.
	 */
	public function loadTemplate( $tpl = null )
	{
		// clear prior output
		$this->_output = null;

		//create the template file name based on the layout
		$file_ = isset( $tpl ) ? $this->_layout . '_' . $tpl : $this->_layout;
		// clean the file name
		$file = preg_replace( '/[^A-Z0-9_\.-]/i', '', $file_ );
		$tpl = preg_replace( '/[^A-Z0-9_\.-]/i', '', $tpl );

		// load the template script


		$filetofind = $this->_createFileName( 'template', array( 'name' => $file ) );
		$Override = $this->_template = X_PATH_BASE . DS . 'override' . DS . X_DOMAIN . DS . $this->getName() . DS . 'tmpl' . DS . $filetofind;

		if ( is_file( $Override ) )
		{
			$this->_template = $Override;
		}
		else if ( is_file( $this->template . DS . $filetofind ) )
		{
			$this->_template = $this->template . DS . $filetofind;
		}
		else
		{
			$this->_template = $this->_baseComponent . DS . $filetofind;
		}
		if ( $this->_template != false )
		{
			// unset so as not to introduce into template scope
			unset( $tpl );
			unset( $file );

			// never allow a 'this' property
			if ( isset( $this->this ) )
			{
				unset( $this->this );
			}

			// start capturing output into a buffer
			ob_start();
			// include the requested template filename in the local scope
			// (this will execute the view logic).
			include $this->_template;
			if ( $this->get( 'disable_config' ) == false )
			{
				Helper::getConfigToolbar( $this->getName(), '<i class="bi bi-gear gear-ico"></i>' . Text::_( 'configuration' ) );
			}
			SessionHelper::getSessionHelper();
			// done with the requested template; get the buffer and
			// clear it.
			$this->_output = ob_get_contents();
			ob_end_clean();
			return $this->_output;
		}
		else
		{
			echo 'Layout "' . $file . '" not found';
		}

	}

	/**
	 * Load a helper file
	 *
	 * @access	public
	 * @param string $tpl The name of the helper source file ...
	 * automatically searches the helper paths and compiles as needed.
	 * @return boolean Returns true if the file was loaded
	 */
	public function loadHelper( $hlp = null )
	{
		// clean the file name
		$file = preg_replace( '/[^A-Z0-9_\.-]/i', '', $hlp );

		// load the template script
		jimport( 'joomla.filesystem.path' );
		$helper = JPath::find( $this->_path['helper'], $this->_createFileName( 'helper', array( 'name' => $file ) ) );

		if ( $helper != false )
		{
			// include the requested template filename in the local scope
			include_once $helper;
		}

	}

	/**
	 * Sets an entire array of search paths for templsates or resources.
	 *
	 * @access protected
	 * @param string $type The type of path to set, typically 'template'.
	 * @param string|array $path The new set of search paths.  If null or
	 * false, resets to the current directory only.
	 */
	protected function _setPath( $type, $path )
	{
		global $mainframe, $option;

		// clear out the prior search dirs
		$this->_path[$type] = array();

		// actually add the user-specified directories
		$this->_addPath( $type, $path );

		// always add the fallback directories as last resort
		switch ( mb_strtolower( $type ) )
		{
			case 'template':
				{
					// set the alternative template search dir
					if ( isset( $mainframe ) )
					{
						$option = preg_replace( '/[^A-Z0-9_\.-]/i', '', $option );
						$fallback = JPATH_BASE . DS . X_TEMPLATE . DS . $mainframe->getTemplate() . DS . 'html' . DS . $option . DS . $this->getName();
						$this->_addPath( 'template', $fallback );
					}
				} break;
		}

	}

	/**
	 * Adds to the search path for tempslates and resources.
	 *
	 * @access protected
	 * @param string|array $path The directory or stream to search.
	 */
	protected function _addPath( $type, $path )
	{
		// just force to array
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
			array_unshift( $this->_path[$type], $dir );
		}

	}

	/**
	 * Create the filename for a resource
	 *
	 * @access private
	 * @param string 	$type  The resource type to create the filename for
	 * @param array 	$parts An associative array of filename information
	 * @return string The filename
	 * @since 1.5
	 */
	protected function _createFileName( $type, $parts = array() )
	{
		$filename = '';

		switch ( $type )
		{
			case 'template' :
				$filename = mb_strtolower( $parts['name'] ) . '.' . $this->_layoutExt;
				break;

			default :
				$filename = mb_strtolower( $parts['name'] ) . '.php';
				break;
		}
		return $filename;

	}

	public function setHelp( $file = '', $title = 'Help' )
	{
		return '';
		/* 	if ( $this->_show_help )
		  {
		  if ( empty( $file ) )
		  {
		  $file = $this->_name;
		  }
		  ?>
		  <div class="help_content">
		  <div class="help_block" >
		  <div class="help_bot">
		  <div class="help_top">
		  <div class="help_tiltle">
		  <?php echo Text::_( $title ); ?>
		  </div>
		  <div class="help_close_ct">
		  <div class="help_close">
		  </div>
		  <div class="close_tooltip">
		  <?php echo Text::_( 'Close' ); ?>
		  </div>
		  </div>
		  <div class="help_body">
		  <?php
		  $Lfile = PATH_BASE . DS . 'help' . DS . $file . '.php';
		  if ( !File::exists( $Lfile ) )
		  {
		  require_once PATH_BASE . DS . 'libraries' . DS . 'Help.php';
		  $helpContent = new HelpContent( $file, HELP_CONTENT_LINK );
		  $helpContent->getHelpContent();
		  file_put_contents( $Lfile, $helpContent->getContent() );
		  }
		  ?>
		  <iframe class="helpframe" src="help/index.html" id="frame<?php echo $file; ?>">
		  </iframe>
		  <input type="hidden" value="help/<?php echo $file; ?>.php" class="helpsource" />
		  </div>
		  </div>
		  </div>
		  </div>
		  </div>
		  <?php
		  }
		  /* */

	}

	public function CheckTaskPermision( $task, $RedirectView = false )
	{
		$ID = Users::GetUserID();
		$Role = Users::GetUserData( 'USER_ROLE' );
		if ( $ID == -500 )
		{
			return true;
		}
		$Menus = MenuConfig::getInstance();
		$Active = $Menus->getActive();
		$Tasks = Helper::getRolesConfig( $Role, 'MENU' );
		$XMLFile = PATH_BASE . DS . 'components' . DS . $this->getName() . DS . 'config.xml';
		if ( is_file( $XMLFile ) )
		{
			$XMLDoc = Helper::loadXMLFile( $XMLFile );
			$DefaultTask = $XMLDoc->getElementByPath( 'defaulttask' )->attributes( 'value' );
			$AllowTasksData = $XMLDoc->getElementByPath( 'allowtasks' );
			if ( $AllowTasksData )
			{
				$AllowTasks = $AllowTasksData->children();
				$AllowTaskItems = array();
				foreach ( $AllowTasks as $AllowTaskItem )
				{
					$name = $AllowTaskItem->attributes( 'name' );
					$AllowTaskItems[$name] = $name;
				}
				if ( isset( $AllowTaskItems[$task] ) )
				{
					return true;
				}
			}
			if ( empty( $task ) )
			{
				$task = $DefaultTask;
			}
			$TaskData = $XMLDoc->getElementByPath( 'tasks' )->children();
			/* @var $Column SimpleXMLElements  */
			$TaskItems = array();
			foreach ( $TaskData as $TaskItem )
			{
				$name = $TaskItem->attributes( 'name' );
				$TaskItems[$name] = $name;
			}
			if ( !isset( $TaskItems[$task] ) )
			{
				$this->TaskAccessRedirect( $RedirectView );
			}
			$Status = Collection::get( $Active->ID . '.PARAMS.' . $task, $Tasks, false );
			if ( empty( $Status ) )
			{
				$this->TaskAccessRedirect( $RedirectView );
			}
		}
		return true;

	}

	public function TaskAccessRedirect( $RedirectView )
	{
		if ( $RedirectView )
		{
//			XError::setError( 'you cannot access task' );
			Users::Redirect( '?ref=noAccess&option=' . $RedirectView, 'you cannot access task', 'error' );
		}
		else
		{
			Users::Redirect( '?ref=noAccess', 'you cannot access task', 'error' );
		}

	}

}
