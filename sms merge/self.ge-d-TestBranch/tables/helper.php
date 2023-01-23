<?php
define( 'X_PATH_INTERFACES', PATH_BASE . DS . 'buffer' . DS . 'interfaces' );

class TableHelper
{
	public static $dropLog = array();
	public static $HistCols = array(
			'HIST_ID',
			'HIST_START_DATE',
			'HIST_END_DATE'
	);
	public static $HistColsData = array(
			'HIST_ID' => array( 'type' => 'number' ),
			'HIST_START_DATE' => array( 'type' => 'date' ),
			'HIST_END_DATE' => array( 'type' => 'date' )
	);
	public static $HistSuffix = '_hist';

	public static function getTableList()
	{
		$query = 'select lower(t.table_name) from user_tables t';
		$Result = DB::LoadList( $query );
		return $Result;

	}

	public static function getXMLTablesList()
	{
		$files = File::files( dirname( __FILE__ ) . DS . 'xml', '\.xml$', 1, 1 );
		$XMLTables = array();
		foreach ( $files as $file )
		{
			$table = self::loadXMLFile( $file );
			if ( $table->attributes( 'name' ) )
			{
				$XMLTables[strtolower( $table->attributes( 'name' ) )] = $table;
			}
		}

		return $XMLTables;

	}

	public static function loadXMLFile( $path )
	{
		if ( $path )
		{
			require_once PATH_BASE . DS . 'libraries' . DS . 'html' . DS . 'simplexml.php';
			$xml = new SimpleXML();
			if ( $xml->loadFile( $path ) )
			{
				return $xml->document;
			}
		}
		return false;

	}

	public static function ProcessXML( $XML, $TablesIN )
	{
		$Delete = Request::getInt( 'del', 0 );
		$Tables = array_flip( $TablesIN );
		echo '<pre>';
		/** @var SimpleXMLElements $Item */
		foreach ( $XML as $Table => $Item )
		{
			$Columns = $Item->getElementByPath( 'columns' );
			if ( $Columns )
			{
				$SQS = null;
				$HistSQS = null;
				$PKey_name = 'id';

				$SequenceData = $Item->getElementByPath( 'sequence' );
				$PKey = $Item->getElementByPath( 'primarykey' );
				if ( $PKey )
				{
					$PKey_name = mb_strtolower( $PKey->attributes( 'name', 'id' ) );
				}

				if ( $SequenceData )
				{
					$SQS = mb_strtolower( $SequenceData->attributes( 'name' ) );
					$SQSStart = (int) $SequenceData->attributes( 'start', 1 );
					self::ProcessSequesnce( $SQS, $SQSStart );
				}
				$HistSequenceData = $Item->getElementByPath( 'histsequence' );
				if ( $HistSequenceData )
				{
					$HistSQS = mb_strtolower( $HistSequenceData->attributes( 'name' ) );
					$HistSQSStart = (int) $HistSequenceData->attributes( 'start', 1 );
					self::ProcessSequesnce( $HistSQS, $HistSQSStart );
				}
				$History = ($Item->attributes( 'history', 'no' ) == 'yes') ? true : false;
				$HistTable = $Table . self::$HistSuffix;
				if ( isset( $Tables[$Table] ) )
				{
					self::UpdateTable( $Table, $Columns );
				}
				else
				{
					self::InsertTable( $Table, $Columns );
				}
				if ( $History )
				{
					if ( isset( $Tables[$HistTable] ) )
					{
						self::UpdateHistTable( $HistTable, $Columns );
					}
					else
					{
						self::InsertHistTable( $HistTable, $Columns );
					}
					$HistoryCols = $Item->getElementByPath( 'history' );
					if ( empty( $HistSQS ) )
					{
						$HistSQS = $SQS;
					}
					if ( !empty( $HistSQS ) )
					{
						$HistDateFieldName = false;
						$HistDateField = $Item->getElementByPath( 'histdatefield' );
						if ( $HistDateField )
						{
							$HistDateFieldName = $HistDateField->attributes( 'name', false );
						}
						self::ProcessTrigger( $Table, $Columns, $HistoryCols, $HistSQS, $HistDateFieldName, $PKey_name );
					}
				}
				$ColNames = array();
				$_TranslateColumns = array();
				foreach ( $Columns->children() as $Column )
				{
					$T = $Column->attributes( 'ti' );
					$ColName = strtoupper( $Column->attributes( 'name' ) );
					$ColNames[] = $ColName;
					if ( $T == 1 )
					{
						$_TranslateColumns[] = $ColName;
					}
				}

				if ( $History )
				{
					self::WriteHistoryTableFile( $HistTable, $ColNames );
				}
				self::WriteTableFile( $Table, $ColNames, $_TranslateColumns );
			}
			else
			{
				if ( isset( $Tables[$Table] ) )
				{
					self::UpdateTable( $Table, $Item );
				}
				else
				{
					self::InsertTable( $Table, $Item );
				}
				$Columns = $Item->children();
				$ColNames = array();
				foreach ( $Columns as $Column )
				{
					$ColNames[] = strtoupper( $Column->attributes( 'name' ) );
				}
				self::WriteTableFile( $Table, $ColNames );
			}
			if ( empty( $Delete ) )
			{
				echo 'Table ' . "\t" . $Table . "\t" . ' Processed!' . PHP_EOL;
			}
		}
		echo '</pre>';

	}

	/**
	 * 
	 * @param String $Table
	 * @param SimpleXMLElements $Item
	 */
	public static function InsertTable( $Table, $Item )
	{
		$query = 'create table ' . $Table . PHP_EOL;
		$query .= '( ' . PHP_EOL;
		$Columns = $Item->children();
		$ColCont = 1;
		$Comments = array();
		foreach ( $Columns as $Column )
		{
			$ColData = self::GenColumnData( $Column );
			if ( !$ColData )
			{
				continue;
			}
			if ( $ColCont == 1 )
			{
				$query .= $ColData;
			}
			else
			{
				$query .= ', ' . PHP_EOL . $ColData;
			}
			$ColComment = $Column->attributes( 'comment' );
			if ( $ColComment )
			{
				$ColName = $Column->attributes( 'name' );
				$Comments [$ColName] = $ColComment;
			}
			++$ColCont;
		}
		$query .= PHP_EOL . ' )';
		DB::Query( $query );
		foreach ( $Comments as $ColName => $ColComment )
		{
			self::SetColumnComment( $Table, $ColName, $ColComment );
		}

	}

	public static function UpdateTable( $Table, $Item )
	{
		$TableColumns = self::getTableCollumns( $Table );
		$Columns = $Item->children();
		foreach ( $Columns as $Column )
		{
			$ColName = $Column->attributes( 'name' );
			if ( isset( $TableColumns[strtoupper( $ColName )] ) )
			{
				self::UpdateColumnData( $Column, $TableColumns[strtoupper( $ColName )], $Table );
				unset( $TableColumns[strtoupper( $ColName )] );
			}
			else
			{
				$ColData = self::GenColumnData( $Column );
				$query = 'alter table ' . $Table . ' add ' . $ColData;
				DB::Query( $query );
				$ColComment = $Column->attributes( 'comment' );
				if ( $ColComment )
				{
					$ColName = $Column->attributes( 'name' );
					self::SetColumnComment( $Table, $ColName, $ColComment );
				}
			}
		}
		foreach ( $TableColumns as $RCol )
		{
			self::DropColumn( $Table, $RCol->COLUMN_NAME );
		}

	}

	public static function InsertHistTable( $Table, $Item )
	{
		$query = 'create table ' . $Table . PHP_EOL;
		$query .= '( ' . PHP_EOL;
		$Columns = $Item->children();
		$Comments = array();
		$ColData = self::GenHistColumnData();
		foreach ( $Columns as $Column )
		{
			$ColDataX = self::GenColumnData( $Column );
			if ( !$ColDataX )
			{
				continue;
			}
			$ColData[] = $ColDataX;
			$ColComment = $Column->attributes( 'comment' );
			if ( $ColComment )
			{
				$ColName = $Column->attributes( 'name' );
				$Comments [$ColName] = $ColComment;
			}
		}
		$query .= implode( ', ' . PHP_EOL, $ColData );
		$query .= PHP_EOL . ' ) ';
		DB::Query( $query );
		foreach ( $Comments as $ColName => $ColComment )
		{
			self::SetColumnComment( $Table, $ColName, $ColComment );
		}

	}

	public static function UpdateHistTable( $Table, $Item )
	{
		$TableColumns = self::getTableCollumns( $Table );
		$Columns = $Item->children();
		$HistCols = array_flip( self::$HistCols );
		foreach ( $Columns as $Column )
		{
			$ColName = $Column->attributes( 'name' );
			if ( isset( $TableColumns[strtoupper( $ColName )] ) )
			{
				self::UpdateColumnData( $Column, $TableColumns[strtoupper( $ColName )], $Table );
				unset( $TableColumns[strtoupper( $ColName )] );
			}
			else
			{
				$ColData = self::GenColumnData( $Column );
				$query = 'alter table ' . $Table . ' add ' . $ColData;
				DB::Query( $query );
				$ColComment = $Column->attributes( 'comment' );
				if ( $ColComment )
				{
					$ColName = $Column->attributes( 'name' );
					self::SetColumnComment( $Table, $ColName, $ColComment );
				}
			}
		}

		foreach ( $TableColumns as $RCol )
		{
			$Name = strtoupper( C::_( 'COLUMN_NAME', $RCol ) );
			if ( isset( $HistCols[$Name] ) )
			{
				continue;
			}
			self::DropColumn( $Table, $RCol->COLUMN_NAME );
		}

	}

	public static function getTableCollumns( $Table )
	{
		static $Tables = [];
		if ( !count( $Tables ) )
		{
			$Query = 'select '
							. ' cc.table_name, '
							. ' tc.column_name, '
							. ' tc.data_type, '
							. ' tc.data_length, '
							. '( case '
							. ' when tc.data_type = \'NUMBER\' and tc.data_precision is not null then '
							. ' tc.data_precision || \',\' || tc.data_scale '
							. ' when tc.data_type like \'%CHAR%\' then '
							. ' to_char(tc.data_length) '
							. ' else null end) DATA_LENGTH,'
							. ' tc.nullable, '
							. ' tc.data_default, '
							. ' cc.comments '
							. ' from user_col_comments cc '
							. ' join user_tab_columns tc on cc.column_name = tc.column_name '
							. '         and cc.table_name = tc.table_name '
//						. ' where lower(cc.table_name) = ' . DB::Quote( mb_strtolower( $Table ) )
			;
			$Data = DB::LoadObjectList( $Query );
			foreach ( $Data as $Col )
			{
				$T = strtolower( C::_( 'TABLE_NAME', $Col, null ) );
				if ( empty( $T ) )
				{
					continue;
				}
				$C = C::_( 'COLUMN_NAME', $Col );
				$Tables[$T] = C::_( $T, $Tables, [] );
				$Tables[$T][$C] = $Col;
			}
		}

		return $Tables[$Table];

	}

	public static function GenColumnData( $Column )
	{
		$ColName = $Column->attributes( 'name' );
		$ColType = $Column->attributes( 'type' );
		if ( empty( $ColName ) || empty( $ColType ) )
		{
			return false;
		}
		$ColData = $ColName . ' ' . $ColType;
		$ColLength = (int) $Column->attributes( 'length' );
		if ( $ColLength )
		{
			$ColData .= '(' . $ColLength . ')';
		}
		$ColDefault = $Column->attributes( 'default' );
		if ( $ColDefault )
		{
			$ColData .= ' default ' . DB::Quote( $ColDefault );
		}
		$ColNull = strtoupper( $Column->attributes( 'null' ) );
		if ( $ColNull == 'N' )
		{
			$ColData .= ' not null';
		}

		return $ColData;

	}

	public static function GenHistColumnData()
	{
		$ColData = array();
		foreach ( self::$HistColsData as $ColName => $ColdataX )
		{
			$ColType = C::_( 'type', $ColdataX, 'number' );
			$ColD = mb_strtolower( $ColName . ' ' . $ColType );
			$ColLength = (int) C::_( 'length', $ColdataX, 0 );
			if ( $ColLength )
			{
				$ColD .= '(' . $ColLength . ')';
			}
			$ColDefault = C::_( 'default', $ColdataX, '' );
			if ( $ColDefault )
			{
				$ColD .= ' default ' . DB::Quote( $ColDefault );
			}
			$ColNull = strtoupper( C::_( 'null', $ColdataX, '' ) );
			if ( $ColNull == 'N' )
			{
				$ColData .= ' not null';
			}
			$ColData[] = $ColD;
		}
		return $ColData;

	}

	public static function UpdateColumnData( $Column, $TableColumn, $Table )
	{
		$ColName = $Column->attributes( 'name' );
		$ColType = mb_strtolower( $Column->attributes( 'type' ) );
		$ColLength = (int) $Column->attributes( 'length' );
		$ColNull = strtoupper( $Column->attributes( 'null' ) );
		$ColDefault = $Column->attributes( 'default' );
		$ColComment = $Column->attributes( 'comment' );
		$Query = 'alter table ' . $Table . ' modify ' . $ColName . ' ';
		$Modify = '';
		if ( mb_strtolower( $TableColumn->DATA_TYPE ) != $ColType || intval( $TableColumn->DATA_LENGTH ) != $ColLength )
		{
			$Modify .= $ColType . '(' . $ColLength . ')';
		}
		$TableColumn->DATA_DEFAULT = str_replace( array( 'null', '\'' ), '', $TableColumn->DATA_DEFAULT );
		if ( $TableColumn->DATA_DEFAULT != $ColDefault )
		{
			if ( empty( $ColDefault ) )
			{
				$Modify .= ' default null';
			}
			else
			{
				$Modify .= ' default ' . DB::Quote( $ColDefault );
			}
		}
		if ( !empty( $ColNull ) && strtoupper( $TableColumn->NULLABLE ) != $ColNull )
		{
			if ( $ColNull == 'N' )
			{
				$Modify .= ' not null ';
			}
			else
			{
				$Modify .= ' null ';
			}
		}
		if ( $TableColumn->COMMENTS != $ColComment )
		{
			self::SetColumnComment( $Table, $ColName, $ColComment );
		}

		if ( empty( $Modify ) )
		{
			return false;
		}
		$Query .= $Modify;
		return DB::Query( $Query );

	}

	public static function SetColumnComment( $Table, $ColName, $Comment )
	{
		$Query = 'comment on column '
						. $Table . '.' . $ColName
						. ' is ' . DB::Quote( $Comment );
		return DB::Query( $Query );

	}

	public static function DropColumn( $Table, $ColName )
	{
		self::$dropLog[] = array(
				'table' => $Table,
				'column' => $ColName
		);

	}

	public static function DropColumnGo()
	{
		$dropLog = self::$dropLog;
		if ( !empty( $dropLog ) )
		{
			foreach ( $dropLog as $key => $drop )
			{
				$Query = ' alter table '
								. $drop['table'] . ' drop column ' . $drop['column'];
				self::$dropLog[$key]['result'] = DB::Query( $Query );
			}
		}

	}

	public static function WriteHistoryTableFile( $Table, $ColNames )
	{
		$Cols = array_merge( self::$HistCols, $ColNames );
		$Name = 'Table' . ucfirst( $Table ) . 'Interface';
		$FileName = X_PATH_INTERFACES . DS . $Name . '.php';
		$FileContent = '<?php' . "\n";
		$FileContent .= 'class ' . $Name . ' extends Table' . "\n";
		$FileContent .= '{ ' . "\n";
		foreach ( $Cols as $v )
		{
			if ( empty( $v ) )
			{
				continue;
			}
			$FileContent .= "        " . 'public $' . $v . ' = NULL;' . "\n";
		}
		$FileContent .= '}' . "\n";
		return file_put_contents( $FileName, $FileContent );

	}

	public static function WriteTableFile( $Table, $ColNames, $_TranslateColumns = [] )
	{
		if ( !Folder::exists( X_PATH_INTERFACES ) )
		{
			Folder::create( X_PATH_INTERFACES, 0777 );
		}
		$Name = 'Table' . ucfirst( $Table ) . 'Interface';
		$FileName = X_PATH_INTERFACES . DS . $Name . '.php';
		$FileContent = '<?php' . "\n";
		$FileContent .= 'class ' . $Name . ' extends Table' . "\n";
		$FileContent .= '{ ' . "\n";
		if ( count( $_TranslateColumns ) )
		{
			$FileContent .= "        " . 'public $_TRANSLATE_FIELDS = [\'' . implode( '\', \'', $_TranslateColumns ) . '\'];' . "\n";
		}
		foreach ( $ColNames as $v )
		{
			if ( empty( $v ) )
			{
				continue;
			}
			$FileContent .= "        " . 'public $' . $v . ' = NULL;' . "\n";
		}
		$FileContent .= '}' . "\n";
		return file_put_contents( $FileName, $FileContent );

	}

	public static function ProcessTable2XML( $XML, $Tables )
	{
		$XMLTables = array_flip( array_keys( $XML ) );

		foreach ( $Tables as $Table )
		{
			$ColNames = array();
			echo '<pre>';
			if ( !isset( $XMLTables[$Table] ) )
			{
				$TableData = self::Table2XML( $Table );
				foreach ( $TableData as $Column )
				{
					$ColNames[] = strtoupper( $Column->COLUMN_NAME );
				}
				self::WriteTableFile( $Table, $ColNames );
			}
			echo 'Table <b>' . $Table . '</b> Processed!';
			echo '</pre>';
		}

	}

	public static function Table2XML( $Table )
	{
		$TableData = self::getTableCollumns( $Table );
		$FileContent = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
						. '<!DOCTYPE table SYSTEM "http://apps.magticom.ge/pda/defines/xml/table.dtd">' . PHP_EOL
		;
		$FileContent .= '<table name="' . $Table . '">' . PHP_EOL;
		foreach ( $TableData as $Column )
		{
			$FileContent .= self::GenColumnXML( $Column );
		}
		$FileContent .= '</table>';
		$FileName = PATH_BASE . DS . 'tables' . DS . 'xml' . DS . $Table . '.xml';
		file_put_contents( $FileName, $FileContent );
		return $TableData;

	}

	public static function GenColumnXML( $Column )
	{
		$ColumnString = '    <column';
		if ( empty( $Column->COLUMN_NAME ) )
		{
			return '';
		}
		$ColumnString .= ' name="' . mb_strtolower( $Column->COLUMN_NAME ) . '"';
		if ( !empty( $Column->DATA_TYPE ) )
		{
			$ColumnString .= ' type="' . mb_strtolower( $Column->DATA_TYPE ) . '"';
		}
		if ( !empty( $Column->DATA_LENGTH ) )
		{
			$ColumnString .= ' length="' . $Column->DATA_LENGTH . '"';
		}
		if ( !empty( $Column->NULLABLE ) && strtoupper( $Column->NULLABLE ) == 'N' )
		{
			$ColumnString .= ' null="' . mb_strtolower( $Column->NULLABLE ) . '"';
		}
		if ( !empty( $Column->DATA_DEFAULT ) )
		{
			$ColumnString .= ' default="' . $Column->DATA_DEFAULT . '"';
		}
		if ( !empty( $Column->COMMENTS ) )
		{
			$ColumnString .= ' comment="' . $Column->COMMENTS . '"';
		}
		$ColumnString .= ' />' . PHP_EOL;
		return $ColumnString;

	}

	public static function ProcessSequesnce( $SQS, $HistSQSStart = 1 )
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{

			$Query = 'select '
							. '  lower(us.SEQUENCE_NAME) '
							. ' from user_sequences us '
			;

			$Data = array_flip( DB::LoadList( $Query ) );
		}
		if ( isset( $Data[$SQS] ) )
		{
			return true;
		}
		$Insert = 'create '
						. ' sequence ' . mb_strtolower( $SQS )
						. ' minvalue  ' . $HistSQSStart
						. ' maxvalue 9999999999999999999999999999 '
						. ' start with ' . $HistSQSStart
						. ' increment by 1 '
						. ' cache 20 ';
		return DB::CallStatement( $Insert );

	}

	public static function ProcessTrigger( $Table, $Columns, $HistoryCols, $HistSQS, $HistDateFieldName = false, $PKey_name = 'id' )
	{
		$Triggers = self::GetTriggers();
		$HistTable = $Table . self::$HistSuffix;
		$ChangeCols = array();
		if ( empty( $HistoryCols ) )
		{
			$HistoryCols = $Columns;
		}
		foreach ( $HistoryCols->children() as $Col )
		{
			$CName = $Col->attributes( 'name' );
			$Type = $Col->attributes( 'type' );
			if ( empty( $CName ) )
			{
				continue;
			}
			if ( $Type == 'date' )
			{
				$ChangeCols [] = ':new.' . $CName . ' = :old.' . $CName;
			}
			else
			{
				$ChangeCols [] = 'nvl(:new.' . $CName . ', -1) = nvl(:old.' . $CName . ', -1)';
			}
		}
		$AddChangeIf = '';
		if ( count( $ChangeCols ) )
		{
			$AddChangeIf = ' if updating then '
							. ' if '
							. implode( ' and ', $ChangeCols )
							. ' then '
							. ' return; '
							. ' end if; '
							. ' end if; ';
		}
		$NewCols = array();
		foreach ( $Columns->children() as $Col )
		{
			$CName = $Col->attributes( 'name' );
			if ( empty( $CName ) )
			{
				continue;
			}
			$NewCols [] = ':new.' . $CName;
		}
		$TrgName = substr( 'trg_' . $HistTable, 0, 25 );
		$STTMNT = 'create or replace trigger ' . $TrgName
						. ' after insert or update or delete on ' . $Table
						. ' for each row '
						. ' declare '
						. ' dateSE date; '
						. ' begin '
		;
		if ( $HistDateFieldName )
		{
			$STTMNT .= ' if :new.' . $HistDateFieldName . ' is not null then '
							. ' dateSE := :new.' . $HistDateFieldName . '; '
							. ' else '
							. ' dateSE := sysdate; '
							. ' end if; '
			;
		}
		else
		{
			$STTMNT .= ' dateSE := sysdate; ';
		}

		$STTMNT .= $AddChangeIf
						. ' if inserting then '
						. ' insert into ' . $HistTable
						. ' select ' . $HistSQS . '.nextval, '
						. ' dateSE, '
						. ' null, '
						. implode( ', ', $NewCols )
						. ' from dual; '
						. ' end if; '
						. ' '
						. ' if updating then '
						. ' update ' . $HistTable . ' h '
						. ' set '
						. ' h.hist_end_date = dateSE '
						. ' where '
						. ' h.' . $PKey_name . ' = :old.' . $PKey_name . ' '
						. ' and h.hist_end_date is null; '
						. ' insert into ' . $HistTable
						. ' select ' . $HistSQS . '.NEXTVAL, '
						. ' dateSE, '
						. ' null, '
						. implode( ', ', $NewCols )
						. ' from dual; '
						. ' end if; '
						. ' end ' . $TrgName . '; '
		;
		$PrevTriggerBody = 'create or replace trigger ' . C::_( $TrgName . '.DESCRIPTION', $Triggers, '' ) . C::_( $TrgName . '.TRIGGER_BODY', $Triggers, '' );
		if ( $PrevTriggerBody != $STTMNT )
		{
			return DB::CallStatement( $STTMNT );
		}
		return true;

	}

	public static function ProcessSQL()
	{
		$QPath = dirname( __FILE__ ) . DS . 'xml' . DS . 'sql';
		$Files = File::files( $QPath, '\.sql$' );
		$Queries = array();
		foreach ( $Files as $File )
		{
			$Queries[] = file_get_contents( $QPath . DS . $File );
		}

		$db_conn = DB::getInstance();

		try
		{
			foreach ( $Queries as $Key => $Q1 )
			{
				echo 'File - ' . $Key . ' - ';
				$stmt = oci_parse( $db_conn, $Q1 );
				var_dump( oci_execute( $stmt, OCI_DEFAULT ) );
				oci_commit( $db_conn );
			}
		}
		catch ( Exception $exc )
		{
			echo '<pre>';
			print_r( $exc );
			echo '</pre>';
			echo $exc->getTraceAsString();
		}
		echo 'Done!' . PHP_EOL . PHP_EOL;

	}

	public static function GetSQLList()
	{
		$QPath = dirname( __FILE__ ) . DS . 'sql' . DS;
		return File::files( $QPath, '\.sql$' );

	}

	public static function ProcessSQLs( $Files )
	{
		$Delete = Request::getInt( 'del', 0 );
		echo '<pre>' . PHP_EOL . PHP_EOL;
		$QPath = dirname( __FILE__ ) . DS . 'sql' . DS;
		$Queries = array();
		foreach ( $Files as $File )
		{
			$Queries[$File] = file_get_contents( $QPath . DS . $File );
		}

		$db_conn = DB::getInstance();
		try
		{
			foreach ( $Queries as $Key => $Q1 )
			{
				$stmt = oci_parse( $db_conn, $Q1 );
				$R = oci_execute( $stmt, OCI_DEFAULT );
				oci_commit( $db_conn );
				if ( empty( $Delete ) )
				{
					echo 'File - ' . trim( $Key ) . ' - ';
					echo var_dump( $R );
//					echo PHP_EOL;
				}
			}
		}
		catch ( Exception $exc )
		{
			echo $exc->getTraceAsString();
			return false;
		}
		echo '</pre>';
		return true;

	}

	public static function ProcessInvalidItems()
	{
		$Q = 'SELECT '
						. ' owner, '
						. ' object_type, '
						. ' object_name '
						. ' FROM all_objects o '
						. ' WHERE '
						. ' status = \'INVALID\' '
						. ' and (lower(o.OWNER) =\'' . mb_strtolower( DB_USER ) . '\') '
		;
		echo '<pre>Process Invalid Items:' . PHP_EOL . PHP_EOL;
		$Invalid = DB::LoadObjectList( $Q );
		foreach ( $Invalid as $Object )
		{
			$Type = C::_( 'OBJECT_TYPE', $Object );
			$Name = C::_( 'OBJECT_NAME', $Object );
			$Query = '';
			$Query2 = '';
			switch ( $Type )
			{
				case 'VIEW':
					$Query = 'ALTER VIEW ' . $Name . ' COMPILE ';
					break;
				case 'PROCEDURE':
					$Query = 'ALTER PROCEDURE ' . $Name . ' COMPILE ';
					break;
				case 'FUNCTION':
					$Query = 'ALTER FUNCTION ' . $Name . ' COMPILE ';
					break;
				case 'TRIGGER':
					$Query = 'ALTER TRIGGER ' . $Name . ' COMPILE ';
					break;
				case 'PACKAGE BODY':
				case 'PACKAGE':
					$Query = 'ALTER PACKAGE ' . $Name . ' COMPILE ';
					$Query2 = 'ALTER PACKAGE ' . $Name . ' COMPILE BODY ';
					break;
				default:
					break;
			}

			$db_conn = DB::getInstance();
			if ( $Query )
			{
				echo 'Object - ' . $Name . ' - ' . $Type;
				$stmt = oci_parse( $db_conn, $Query );
				var_dump( oci_execute( $stmt, OCI_DEFAULT ) );
				oci_commit( $db_conn );
			}
			if ( $Query2 )
			{
				echo PHP_EOL;
				echo 'Object - ' . $Name . ' - ' . $Type;
				$stmt = oci_parse( $db_conn, $Query2 );
				var_dump( oci_execute( $stmt, OCI_DEFAULT ) );
				oci_commit( $db_conn );
			}
			echo PHP_EOL;
		}
		echo '</pre>';
		return true;

	}

	public static function GetTriggers()
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{

			$Query = 'select '
							. '  lower(us.trigger_name) Name,'
							. ' us.DESCRIPTION, '
							. ' us.TRIGGER_BODY,'
							. ' us.* '
							. ' from user_triggers us '
			;

//							$Data = array_flip( DB::LoadObjectList( $Query ) );
			$Data = DB::LoadObjectList( $Query, 'Name' );
		}
		return $Data;

	}

}
