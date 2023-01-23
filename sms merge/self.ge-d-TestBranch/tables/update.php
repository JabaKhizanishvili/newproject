<?php
define( 'DS', DIRECTORY_SEPARATOR );
$base = realpath( dirname( __FILE__ ) . DS . '..' . DS );
ini_set( 'display_errors', 1 );
define( 'PATH_BASE', $base );
global $GLOBALTIME;
$GLOBALTIME = microtime( true );
include(PATH_BASE . DS . 'define.php');
require_once PATH_BASE . DS . 'libraries/x.php';
require_once PATH_BASE . DS . 'libraries/request.php';
require_once PATH_BASE . DS . 'libraries/collection.php';
require_once PATH_BASE . DS . 'libraries/file.php';
require_once PATH_BASE . DS . 'libraries/folder.php';
require_once PATH_BASE . DS . 'libraries/uri.php';
require_once PATH_BASE . DS . 'libraries/DB.php';
require_once PATH_BASE . DS . 'libraries/shutdown.php';
require_once PATH_BASE . DS . 'libraries/autoload.php';
require_once PATH_BASE . DS . 'menu.php';
require_once PATH_BASE . DS . 'components/helper.php';
require_once PATH_BASE . DS . 'components/controller.php';

require_once PATH_BASE . DS . 'multi.php';

require_once './helper.php';

$Tables = TableHelper::getTableList();
$XMLTables = TableHelper::getXMLTablesList();
$Delete = Request::getInt( 'del', 0 );
TableHelper::ProcessXML( $XMLTables, $Tables );

if ( Request::getVar( 'dropConfirm', false, 'post' ) )
{
	TableHelper::DropColumnGo();
}
if ( !empty( $dropLog = TableHelper::$dropLog ) && $Delete )
{
	TableHelper::DropColumnGo();
}
if ( !empty( $dropLog = TableHelper::$dropLog ) )
{
	?>
	<br>=========== DELETE COLUMNS ==============<br><br>
	<?php
	foreach ( $dropLog as $log )
	{
		echo $log['table'];
		?> -----> <?php
		echo $log['column'];
		if ( isset( $log['result'] ) && $log['result'] )
		{
			?>
			&nbsp;&nbsp;&nbsp; [ Processed! ] 
			<?php
		}
		?>
		<br>
		<?php
	}

	if ( !Request::getVar( 'dropConfirm', false, 'post' ) && !$Delete )
	{
		?>
		<br>
		<form action='' method='post'>
			<input type='hidden' name='dropConfirm' value='1'>
			<button type='confirm'>Confirm</button>
		</form>
		<?php
	}
	?>

	<br>=========== END DELETE COLUMNS ===========<br><br><br>
	<?php
}

$SqlFiles = TableHelper::GetSQLList();

TableHelper::ProcessSQLs( $SqlFiles );
TableHelper::ProcessInvalidItems();
echo '</pre>';
