<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

class XShutdown extends XObject
{
	private $callbacks; // array to store user callbacks

	public function __construct()
	{
		$this->callbacks = array();
		register_shutdown_function( array( $this, 'callRegisteredShutdown' ) );

	}

	public function registerShutdownEvent()
	{
		$callback = func_get_args();
		if ( empty( $callback ) )
		{
			trigger_error( 'No callback passed to ' . __FUNCTION__ . ' method', E_USER_ERROR );
			return false;
		}
		if ( !is_callable( $callback[0] ) )
		{
			trigger_error( 'Invalid callback passed to the ' . __FUNCTION__ . ' method', E_USER_ERROR );
			return false;
		}
		$this->callbacks[] = $callback;
		return true;

	}

	public function callRegisteredShutdown()
	{
		foreach ( $this->callbacks as $arguments )
		{
			$callback = array_shift( $arguments );
			call_user_func_array( $callback, $arguments );
		}

	}

	// test methods:
	public function Debug()
	{
		if ( $this->_CheckPermision() )
		{
			return;
		}
		/** @var XDebug $Debug */
		$Debug = XDebug::getInstance();
		$Debug->_SetCSS();
		$Debug->_SetJS();
		$DebugData = '';
		$DebugData .= $Debug->_RenderProfile();
//		$DebugData .= $Debug->_RenderMemory();
		$DebugData .= $Debug->_RenderRequest();
//		$DebugData .= $Debug->_RenderElements();
		$DebugData .= $Debug->_RenderQueries();
		$DebugData .= $Debug->_RenderRedis();
		$DebugData .= $Debug->_RenderTranslate();
//		$DebugData .= $Debug->_RenderLangs();
		$DebugData .= $Debug->_RenderFiles();
		$DebugData .= $Debug->_RenderExtensions();
//		$DebugData .= $Debug->_RenderStringDesigner();
		$html = '<div id="debug_container_block">';
		$html .= $Debug->GenHTMLHead();
		$History = $Debug->GenHistory( $DebugData );
		$html .= '<div id="system_debug_info" class="system_debug_info">';
		$html .= '<div class="debug_navigation">';
		$html .= '<div class="debugc_navbar">';
		$html .= '<div class="debug_home_page debug_active_n" id="debug_page_main_n">';
		$html .= '<a href="javascript:void(0);" onclick="togglePage(\'debug_page_main\');">';
		$html .= Text::_( 'Current Page' );
		$html .= '</a>';
		$html .= '</div>';
		$html .= $Debug->GenHistoryNav();
		$html .= '<div class="debugc_cls"></div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '<div id="debug_page_main">';
		$html .= $DebugData;
		$html .= '</div>';
		$html .= $History;
		$html .= '</div>';
		$html .= '</div>';
		$html .= $Debug->_JS;
		$html .= $Debug->_CSS;
		echo $html;
//		$body = JResponse::getBody();
//		$body = str_replace( '</head>', $Debug->_CSS . '</head>', $body );
//		$body = str_replace( '</body>', $html . '</body>', $body );
//		JResponse::setBody( $body );

	}

	private function _CheckPermision()
	{
		global $DisableSF;
		$USER_AGENT = Request::getVar( 'HTTP_USER_AGENT', false, 'server' );
		if ( $USER_AGENT == DEBUG_STRING && empty( $DisableSF ) )
		{
			$IPs = array_flip( Helper::CleanArray( explode( ',', DEBUG_IPS ), 'Str' ) );
			if ( count( $IPs ) )
			{
				$IP = Request::getVar( 'REMOTE_ADDR', false, 'server' );
				if ( empty( $IP ) )
				{
					return true;
				}
				if ( !isset( $IPs[$IP] ) )
				{
					return true;
				}
				return false;
			}
			return true;
		}
		return true;

	}

}

function shutdown()
{
	global $DisableSF;
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] == LIMITED_DEBUG_STRING )
	{
		console_ig::RUN();
	}
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && $_SERVER['HTTP_USER_AGENT'] == DEBUG_STRING && empty( $DisableSF ) )
	{
		console_ig::RUN();
		$history = '';
		if ( isset( $_SESSION['debug_info'] ) )
		{
			$history = $_SESSION['debug_info'];
		}
		ob_start();
		echo '<div style="width:90%;overflow:hidden;margin:0 auto; padding:15px; border: 1px solid #111111;">';
		echo '<pre style="display:block;width:1100px;overflow:auto;">';
		print_r( DB::$debug );
		echo '</pre><pre>';
		print_r( DB::$debugTime );
		$Sum = 0.0;
		echo '</pre><pre> DB Time Sum: ';
		foreach ( DB::$debugTime as $value )
		{
			$Sum += $value;
		}
		print_r( $Sum );
		echo '</pre><pre> Page Time Sum: ';
		global $GLOBALTIME;
		print_r( microtime( true ) - $GLOBALTIME );
		echo '</pre>Redis Queries:<pre>';
		print_r( XRedis::$Query );
		echo '</pre>Redis Queries Times:<pre>';
		print_r( XRedis::$Times );
		echo '</pre><pre>';
		print_r( DB::$Results );
		echo '</pre><pre><span style="padding:30px;display:block;">';
		$lang = Language::getInstance( SYSTEM_LANG );
		foreach ( $lang->untranslated as $term )
		{
			file_put_contents( PATH_LOGS . DS . 'lang.ini', $term . "\n", FILE_APPEND );
			echo $term . "\n";
		}
		echo '</span></pre>';
		echo '<br /><pre><b>$_GET:</b>:<br />';
		print_r( $_GET );
		echo '</pre>';
		echo '<br /><pre><b>$_POST:</b>:<br />';
		print_r( $_POST );
		echo '</pre>';
		echo '<br /><pre><b>$_SESSION:</b><br />';
		if ( isset( $_SESSION[SESSION_SPASE] ) )
		{
			print_r( $_SESSION[SESSION_SPASE] );
		}
		echo '</pre>';
		echo '<pre>';
		print_r( get_included_files() );
		echo '</pre><pre>';
		?>
		<script type="text/javascript">
			function debug_current()
			{
				document.getElementById('debug_current').style.display = 'block';
				document.getElementById('debug_history').style.display = 'none';
			}
			function debug_history()
			{
				document.getElementById('debug_current').style.display = 'none';
				document.getElementById('debug_history').style.display = 'block';
			}
		</script>
		<?php
		echo '</div>';
		$t = ob_get_contents();
		ob_clean();
		$_SESSION['debug_info'] = $t;
		?>
		<div class="debug_navigation">
			<div class="debug_navigation_item" onclick="debug_current();">
				Current Page
			</div>
			<div class="debug_navigation_item" onclick="debug_history();">
				Previews Page
			</div>
			<div class="debug_info">
				<div class="debug_info_item" id='debug_current'>
					<?php echo $t; ?>
				</div>
				<div class="debug_info_item" id='debug_history' style="display: none;">
					<?php echo $history; ?>
				</div>
			</div>
		</div>
		<?php
	}

}

$Shutdown = XShutdown::GetInstance();

// schedule a global scope function:
$Shutdown->registerShutdownEvent( 'DB::DB_Destruct' );
// try to schedule a dyamic method:
$Shutdown->registerShutdownEvent( array( $Shutdown, 'Debug' ) );
//register_shutdown_function( 'shutdown' );
/**
 * 
 * @param type $number
 * @param type $string
 * @param type $file
 * @param type $line
 * @param type $context
 * @return boolean
 */
function errorHandler( $number, $string, $file = 'Unknown', $line = 0, $context = array() )
{
	if ( ($number == E_NOTICE) || ($number == E_STRICT) )
	{
		return false;
	}

	if ( !error_reporting() )
	{
		return false;
	}

//    throw new Exception($string, $number);

}

set_error_handler( 'errorHandler' );
