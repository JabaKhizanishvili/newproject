<?php

class Helper
{
	private static $_js;
	private static $_js_files;
	private static $_js_no_init;

	public static function getTableHeader( $nameIN, $title, $orderIn, $dir )
	{
		$name = trim( mb_strtolower( $nameIN ) );
		$order = mb_strtolower( $orderIn );
		if ( empty( $name ) )
		{
			$html = Text::_( $title );
		}
		else
		{
			$html = '<a href="javascript:setOrder(\'' . $name . '\');">';
			if ( $name == $order )
			{
				$html .= Text::_( $title );
				if ( $dir )
				{
					$html .= '<span class="sort_image"><img src="' . X_TEMPLATE . '/images/sort_desc.png" alt="" /></span>';
				}
				else
				{
					$html .= '<span class="sort_image"><img src="' . X_TEMPLATE . '/images/sort_asc.png" alt="" /></span>';
				}
			}
			else
			{
				$html .= Text::_( $title );
			}
			$html .= '</a>';
		}

		return $html;

	}

	public static function getHeaderMenu()
	{
		$menu = MenuConfig::getInstance();
		$MenuData = $menu->getUserMenu();
		if ( isset( $MenuData[0] ) )
		{
			/* @var $m TableLib_menusInterface */
			foreach ( $MenuData [0] as $m )
			{
				if ( empty( $m->LIB_SHOW ) )
				{
					continue;
				}
				$active = '';
				if ( $menu->IsActiveMenuItem( C::getVarIf( 'LIB_OPTION', $m, '', 'ID' ) ) )
				{
					$active = ' menu_item_active';
				}
				if ( empty( $m->LIB_OPTION ) )
				{
					$m->LINK = 'javascript:void(0);';
				}
				else
				{
					$m->LINK = '?option=' . $m->LIB_OPTION;
				}
				?>
				<li class="menu_item<?php echo $active; ?>">
					<a href="<?php echo $m->LINK; ?>">
						<div class="parent-bi">
							<i class="bi <?php echo $m->ICON; ?>"></i>
							<div class="menu_title">
								<?php echo XTranslate::_( $m->LIB_TITLE, 'menu', 'ka' ); ?>
							</div>
						</div>
					</a>
					<?php
					if ( isset( $MenuData[$m->ID] ) )
					{
						self::_getSubMenu( $MenuData[$m->ID], $menu );
					}
					?>
				</li>
				<?php
			}
		}
		return;

	}

	/**
	 * 
	 * @return type 
	 */
	public static function getSubMenu()
	{
		/* @var $config MenuConfig */
		$config = MenuConfig::getInstance();
		$MenuData = $config->getUserMenu();
		$parent = $config->getParent();
		if ( isset( $MenuData[$parent] ) )
		{
			?>
			<ul class="submenu">
				<?php
				foreach ( $MenuData[$parent] as $m )
				{
					if ( empty( $m->LIB_SHOW ) )
					{
						continue;
					}
					$Sactive = '';
					if ( $config->IsActiveMenuItem( Collection::getVarIf( 'LIB_OPTION', $m, '', 'ID' ) ) )
					{
						$Sactive = ' menu_subitem_active';
					}
					/* @var $m TableLib_menusInterface */
					if ( empty( $m->LIB_OPTION ) )
					{
						$m->LINK = 'javascript:void(0);';
					}
					else
					{
						$m->LINK = '?option=' . $m->LIB_OPTION;
					}
					?>
					<li class="submenu_item<?php echo $Sactive; ?>">
						<a class="submenu_a" href="<?php echo $m->LINK; ?>">
							<?php echo $m->LIB_TITLE; ?>
						</a>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}

	}

	public static function SetJS( $js, $init = true )
	{
		if ( $init )
		{
			self::$_js .= $js;
		}
		else
		{
			self::$_js_no_init .= $js;
		}

	}

	public static function SetJSFile( $fileName )
	{
		if ( !is_array( self::$_js_files ) )
		{
			self::$_js_files = array();
		}
		self::$_js_files[] = $fileName;

	}

	public static function GetJS()
	{
		return self::$_js;

	}

	public static function GetJSFiles()
	{

		if ( is_array( self::$_js_files ) )
		{
			foreach ( self::$_js_files as $file )
			{
				if ( is_file( PATH_BASE . DS . $file ) )
				{
					echo '<script type="text/javascript" src = "' . $file . '"></script>';
				}
			}
		}

	}

	public static function GetNoInitJS()
	{
		return self::$_js_no_init . PHP_EOL;

	}

	/**
	 * Get Toolbar Items
	 * @param type $name
	 * @param type $option
	 * @param type $task
	 * @param type $check
	 * @param type $validate
	 */
	public static function getToolbar( $name, $option, $task = '', $check = 0, $validate = 0 )
	{
		?>
		<span class="toolbar_item">
			<button class="btn btn-primary" type="button" onclick="doAction('<?php echo $option; ?>', '<?php echo $task; ?>', <?php echo $check; ?>, <?php echo $validate; ?>);">
				<?php echo Text::_( $name ); ?>
			</button>
		</span>
		<?php

	}

	private static function _getSubMenu( $MenuData, $menu )
	{
		?>
		<ul class="submenu">
			<?php
			$Sactive = '';
			foreach ( $MenuData as $smenu )
			{
				if ( empty( $smenu->LIB_SHOW ) )
				{
					continue;
				}
				/* @var $smenu TableLib_menusInterface */
				$Sactive = '';
				if ( $menu->IsActiveMenuItem( Collection::getVarIf( 'LIB_OPTION', $smenu, '', 'ID' ) ) )
				{
					$Sactive = ' menu_subitem_active';
				}
				if ( empty( $smenu->LIB_OPTION ) )
				{
					$smenu->LINK = 'javascript:void(0);';
				}
				else
				{
					$smenu->LINK = '?option=' . $smenu->LIB_OPTION;
				}
				?>
				<li class="submenu_item<?php echo $Sactive; ?>">
					<a class="submenu_a" href="<?php echo $smenu->LINK; ?>">
						<?php echo XTranslate::_( $smenu->LIB_TITLE, 'menu', 'ka' ); ?>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
		<?php

	}

	public static function getPageTitle( $Add = '' )
	{
		$menu = MenuConfig::getInstance();
		$active = $menu->getActive();
		$PT = ($Add) ? ' - ' . $Add : '';
		if ( $active )
		{
			return '<p class="p_title">' . XTranslate::_( $active->LIB_TITLE, 'menu', 'ka' ) . $PT . '</p>';
		}
		return Text::_( 'Page Title' );

	}

	public static function FormatBalance( $amountIN, $round = 2, $delimiter = '.' )
	{
		$amount = floatval( $amountIN );
		$a = explode( '.', (string) $amount );
		if ( !isset( $a[1] ) )
		{
			$a[1] = '';
		}
		$r = 0;
		$len = strlen( $a[1] );
		if ( $round >= $len )
		{
			$r = $round - $len;
			$a[1] .= str_repeat( '0', $r );
		}
		else
		{
			$a[1] = substr( $a[1], 0, $round );
		}
		$ret = implode( $delimiter, $a );
		return $ret;

	}

	public static function CleanNumber( $number )
	{
		return preg_replace( "/[^0-9]/", "", $number );

	}

	public static function CheckPhoneNumber( $val )
	{
		if ( strlen( $val ) == 9 )
		{
			return 1;
		}
//For EVDO & Sat 
		if ( strlen( $val ) == 8 )
		{
			return 3;
		}
//For IMSI Check
		if ( strlen( $val ) > 9 )
		{
			return 2;
		}
		return 0;

	}

	/**
	 * Get getModalIframeToolbar Items
	 * @param type $name
	 * @param type $option
	 * @param type $task
	 * @param type $check
	 * @param type $validate
	 */
	public static function getModalIframeToolbar( $name, $url, $width = 400, $height = 350, $Translate = true )
	{
		$start = rand( 1, 20 );
		$id = substr( md5( rand( 0, 999999999 ) . microtime( 1 ) ), $start, 7 );
		$uri = URI::getInstance( $url );
		$uri->setVar( 'tmpl', 'modal' );
		$uri->setVar( 'iframe', 'true' );
		$uri->setVar( 'width', $width );
		$uri->setVar( 'height', $height );
		?>
		<span class="toolbar_item">
			<a class="btn btn-primary" href="<?php echo $uri->toString(); ?>" data-lity>
				<?php
				if ( $Translate )
				{
					echo Text::_( $name );
				}
				else
				{
					echo $name;
				}
				?>
			</a>
		</span>
		<?php
		Helper::SetJS( '$("a[rel^=\'iframe-' . $id . '\']").prettyPhoto();' );

	}

	public static function limitWords( $text, $num = 50, $saveTags = null, $more = '...', $wordSeparator = ' ' )
	{
		$cText = $saveTags !== null ? strip_tags( $text, $saveTags ) : $text;
		$words = explode( $wordSeparator, $cText );
		if ( count( $words ) > $num )
		{
			$words = array_slice( $words, 0, $num );
			$lText = implode( $wordSeparator, $words );
			$lText .= $more;
		}
		else
		{
			$lText = implode( $wordSeparator, $words );
		}
		return $lText;

	}

	/**
	 * Get limited letters
	 *
	 * @param string $text Input text.
	 * @param int $num letter count.
	 * @param mixed $saveTags Tags for strip_tags function or null.
	 * @param string $more string for end text
	 *
	 * @return string
	 */
	public static function limitLetters( $text, $num = 50, $saveTags = null, $more = '...' )
	{
		$cText = $saveTags !== null ? strip_tags( trim( $text ), $saveTags ) : trim( $text );
		$letters = strlen( utf8_decode( $cText ) );
		if ( $letters > $num )
		{
			if ( function_exists( 'mb_substr' ) )
			{
				$lText = mb_substr( $cText, 0, $num ); //, 'UTF-8');
			}
			else
			{
				$lText = substr( $cText, 0, $num ); //, 'UTF-8');
			}
			$lText .= $more;
		}
		else
		{
			$lText = $cText;
		}
		return $lText;

	}

	/**
	 * 
	 * @param type $text
	 * @param type $limit
	 * @param type $type
	 * @return string
	 */
	public static function MakeToolTip( $text, $limit = 5, $type = 0, $wordSeparator = ' ', $styles = [] )
	{
		switch ( $type )
		{
			default:
			case 0:
				$Ttext = self::limitWords( $text, $limit, null, '...', $wordSeparator );
				break;
			case 1:
				$Ttext = self::limitLetters( $text, $limit );
				break;
		}
		$content = '<div class="my_tooltip">'
						. '<span> '
						. $Ttext
						. '</span>'
						. '<div class="my_tip" style="' . implode( ';', $styles ) . '">'
						. $text
						. '</div>'
						. '</div>';
		return $content;

	}

	public static function MakeDoubleToolTip( $Ttext, $text )
	{
		$content = '<div class="my_tooltip">'
						. '<span> '
						. $Ttext
						. '</span>'
						. '<div class="my_tip">'
						. $text
						. '</div>'
						. '</div>';
		return $content;

	}

	public static function getSystemConfig()
	{
		static $Config = NULL;
		if ( empty( $Config ) )
		{
			$Query = 'select t.* from system_config t ';
			$Key = XRedis::GenScopeKey( 'system_config', md5( $Query ) );
			$Config = XRedis::GC( $Key );
			if ( empty( $Config ) )
			{
				$Data = DB::LoadObjectList( $Query );
				$Config = new stdClass();
				foreach ( $Data as $D )
				{
					$Config->{$D->KEY} = $D->VALUE;
				}
				XRedis::SC( $Config, $Key );
			}
		}
		return $Config;

	}

	public static function getSystemConfigValue( $key )
	{
		if ( empty( $key ) )
		{
			return false;
		}
		$config = self::getSystemConfig();
		if ( isset( $config->$key ) )
		{
			return $config->$key;
		}
		return false;

	}

	public static function getJSToolbar( $name, $JSfunction, $args = array() )
	{
		$argsT = '';
		if ( !empty( $args ) )
		{
			$argsT = '\'' . implode( '\',\'', $args ) . '\'';
		}
		?>
		<span class="toolbar_item">
			<button class="btn btn-primary" type="button" onclick="<?php echo $JSfunction; ?>(<?php echo $argsT; ?>);">
				<?php echo Text::_( $name ); ?>
			</button>
		</span>
		<?php

	}

	public static function getConfigToolbar( $option, $lbl = 'Configuration' )
	{
		if ( $option == 'config' )
		{
			return;
		}
		if ( file::exists( X_PATH_BASE . DS . 'components' . DS . $option . DS . 'configuration.xml' ) && Users::CanAccess( 'config' ) )
		{
			$url = '?option=config&c=' . $option;
			echo '<div class = "toolbar toolbar-bottom configurator">';
			Helper::getModalIframeToolbar( $lbl, $url, '100%', '100%', false );
			echo '</div>';
		}

	}

	public static function getConfig( $key, $default = null )
	{
		$config = Helper::getSystemConfig();
		return C::_( $key, $config, $default );

	}

	/**
	 * 
	 * @deprecated since version 1.0
	 * @return array
	 */
	private static function _getConfig()
	{
		$data = Helper::getSystemConfig();
		return $data;

	}

	public static function stringToTime( $stringIN, $position = 0 )
	{
		$string = trim( $stringIN );
		if ( empty( $string ) )
		{
			return '';
		}
		$time = strtotime( $string );
		$date = new PDate( $time );
		if ( strpos( $string, 'day' ) !== false )
		{
			return $date->toFormat( '%d.%m.%Y' );
		}
		else
		{
			if ( $position == 0 )
			{
				return $date->toFormat( '01.%m.%Y' );
			}
			else
			{
				$lastday = date( 't', strtotime( $string ) );
				return $date->toFormat( $lastday . '.%m.%Y' );
			}
		}

	}

	public static function TranslitToLat( $text )
	{
		$str_from = 'ა, ბ, გ, დ, ე, ვ, ზ, თ, ი, კ, ლ, მ, ნ, ო, პ, ჟ, რ, ს, ტ, უ, ფ, ქ, ღ, ყ, შ, ჩ, ც, ძ, წ, ჭ, ხ, ჯ, ჰ';
		$str_to = 'a, b, g, d, e, v, z, t, i, k, l, m, n, o, p, zh, r, s, t, u, f, q, gh, k, sh, ch, c, dz, ts, tc, kh, j, h';

		if ( !empty( $text ) )
		{
			$from = explode( ', ', $str_from );
			$to = explode( ', ', $str_to );
			$trans = str_replace( $from, $to, trim( $text ) );
			return $trans;
		}
		return $text;

	}

	public static function getDepartmentList( $key = null )
	{
		$query = 'select '
						. ' id, '
						. ' t.lib_title title '
						. ' from lib_departments t '
						. ' where t.active=1 '
						. ' order by title asc';
		return DB::LoadObjectList( $query, $key );

	}

	public static function GetGraphTimes( $name, $value, $id )
	{
		static $data = null;
		static $options = null;
		if ( is_null( $data ) )
		{
			$query = 'select '
							. ' t.id, '
							. ' t.lib_title '
							. ' from lib_graph_times t '
							. ' WHERE t.active =1 '
							. ' AND t.owner = ' . Users::GetUserData( 'SECTION_ID' )
							. ' order by lib_title asc';

			$data = DB::LoadObjectList( $query, 'ID' );
		}
		if ( is_null( $options ) )
		{
			$options[] = HTML::_( 'select.option', '-1', Text::_( 'Holiday' ) );
			foreach ( $data as $dat )
			{
				$val = $dat->ID;
				$text = $dat->LIB_TITLE;
				$options[] = HTML::_( 'select.option', $val, $text );
			}
		}
		return HTML::_( 'select.genericlist', $options, $name, ' class="graph_time skip_this" ', 'value', 'text', $value, $id );

	}

	/**
	 * 
	 * @param type $Worker
	 * @param type $StartDate
	 * @param type $EndDate
	 * @return integer
	 */
	public static function getDayCount( $Worker, $StartDate, $EndDate )
	{
		$Count = 0;
		$WorkerDataX = XGraph::GetOrgUserSchedule( $Worker, 1 );
		foreach ( $WorkerDataX as $WorkerData )
		{
			$GRAPHTYPE = (int) C::_( 'GRAPHTYPE', $WorkerData );
			if ( $GRAPHTYPE == 0 )
			{
				$W = C::_( 'ID', $WorkerData, 0 );
				$StartDateD = new PDate( $StartDate );
				$EndDateD = new PDate( $EndDate );
				$Query = ' select '
								. ' sum(gt.vacation_index) '
								. ' from hrs_graph g '
								. ' left join lib_graph_times gt on gt.id = g.time_id '
								. ' where '
								. ' g.time_id > 0'
								. ' and g.worker = ' . (int) $W
								. ' and g.real_date between to_date(\''
								. $StartDateD->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd hh24:mi:ss\' ) and  to_date(\''
								. $EndDateD->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\' ) '
				;
				$Count += DB::LoadResult( $Query );
			}
			else
			{
				$TimeGraph = DB::LoadObject( 'select * from LIB_STANDARD_GRAPHS t where id = ' . $GRAPHTYPE );
				$Count += self::CalculateWorkingDayCount( $StartDate, $EndDate, $TimeGraph );
			}
		}
		return $Count;

	}

	public static function CalculateDayCount( $Date1, $Date2 )
	{
		$date1Object = new PDate( $Date1 );
		$date2Object = new PDate( $Date2 );
		$date1Unix = new PDate( $date1Object->toFormat( '%Y-%m-%d' ) );
		$date2Unix = new PDate( $date2Object->toFormat( '%Y-%m-%d' ) );
		return ceil( abs( $date1Unix->toUnix() - $date2Unix->toUnix() ) / 86400 ) + 1;

	}

	public static function CalculateWorkingDayCount( $Start, $End, $TimeGraph )
	{
		$Holidays = Helper::GetAllHoldays();
		$StartDate = new PDate( $Start );
		$ENDTMP = new PDate( $End );
		$EndDate = new PDate( $ENDTMP->toformat( '%Y-%m-%d 23:59:59' ) );
		$Count = 0;
		while ( $StartDate->toUnix() < $EndDate->toUnix() )
		{
			$DayName = strtoupper( $StartDate->toFormat( '%A', true, false ) );
			$Date = $StartDate->toFormat( '%Y-%m-%d' );
			$StartDate = new PDate( $StartDate->toUnix() + 86400 );
			$Time = trim( C::_( $DayName, $TimeGraph ) );
			$TimeData = TKCalendar::GetGraphTimeData( $Time );
			$Index = C::_( 'VACATION_INDEX', $TimeData, 1 );
			if ( empty( $Time ) )
			{
				continue;
			}
			if ( isset( $Holidays[$Date] ) )
			{
				continue;
			}
			$Count += $Index;
		}
		return $Count;

	}

	public static function CheckGraphDays( $Worker, $StartDate, $EndDate )
	{
		$Count = self::getDayCount( $Worker, $StartDate, $EndDate );
		if ( $Count == 0 )
		{
			return false;
		}
		return true;
//		$dayCount = self::CalculateDayCount( $StartDate, $EndDate );
//		$StartDateD = new PDate( $StartDate );
//		$EndDateD = new PDate( $EndDate );
//		$Query = ' select count(1) '
//						. ' from hrs_graph '
//						. 'where '
//						. ' worker =' . (int) $Worker
//						. ' and real_date between to_date(\''
//						. $StartDateD->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd hh24:mi:ss\' ) and  to_date(\''
//						. $EndDateD->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\' ) '
//		;
//		$Count = DB::LoadResult( $Query );
//
//		return $dayCount == $Count;

	}

	public static function getHolidayDays( $Worker, $StartDate, $EndDate )
	{
		$WorkerData = XGraph::GetOrgUser( $Worker );
		$GRAPHTYPE = (int) C::_( 'GRAPHTYPE', $WorkerData );
		if ( $GRAPHTYPE == 0 )
		{
			$Query = ' select '
							. ' gt_day, '
							. ' gt_year, '
							. ' to_char(real_date, \'yyyy-mm-dd\' ) real_date '
							. ' from hrs_graph '
							. 'where '
							. ' time_id > 0'
							. ' and worker in (select sl.id from slf_worker sl where sl.orgpid =' . (int) $Worker . ')'
							. ' and real_date between to_date(\'' . $StartDate . '\', \'yyyy-mm-dd hh24:mi:ss\' ) and  to_date(\'' . $EndDate . '\', \'yyyy-mm-dd hh24:mi:ss\' ) '
			;
			$data = DB::LoadObjectList( $Query );
			return $data;
		}
		else
		{
			$TimeGraph = DB::LoadObject( 'select * from LIB_STANDARD_GRAPHS t where id = ' . $GRAPHTYPE );
			return $HolidayDays = self::GetStandardWorkingDays( $StartDate, $EndDate, $TimeGraph );
		}
		return false;

	}

	public static function RegisterHoliday( $SalaryID, $Type, $Date )
	{
//		require_once PATH_BASE . DS . 'libraries' . DS . 'tables' . DS . 'limits.php';
		$LimitsTable = new HolidayLimitsTable();
		$Days = Helper::getDayCount( $SalaryID, $Date, $Date );
		$LimitsTable->LoadUserLimits( $SalaryID, $Date, $Type );
		$LimitsTable->COUNT = $LimitsTable->COUNT - $Days;
		$LimitsTable->AWORKER = Users::GetUserID();
		return $LimitsTable->store();

	}

	public static function getWorkerGroups( $Restrict = false )
	{
//		$ChiefSections = self::getChiefGroups();
//		$first_value = reset( $ChiefSections );
//		if ( empty( $first_value ) )
//		{
//			$sectionID = Users::GetUserData( 'SECTION_ID' );
//		}
//		else
//		{
//			$sectionID = implode( ',', $ChiefSections );
//		}
		static $Data = array();
		$USerID = Users::GetUserID();
		if ( !isset( $Data[$USerID] ) )
		{
			$Add = '';
			if ( $Restrict )
			{
				$ChiefSections = self::getChiefGroups();
				if ( count( $ChiefSections ) )
				{
					$Add = ' and t.id in( ' . implode( ',', $ChiefSections ) . ' ) ';
				}
				else
				{
					$Data[$USerID] = array();
					return $Data[$USerID];
				}
			}
			$Query = 'select '
							. ' t.id, '
							. ' o.lib_title || \' - \' || t.lib_title lib_title '
							. ' from lib_workers_groups t '
							. ' left join lib_unitorgs o on o.id = t.org '
							. ' where '
							. ' t.active>-1 '
							. ' and o.active=1 '
//							. ' and t.id in( ' . $sectionID . ' ) '
							. $Add
							. 'order by t.lib_title ';
			$Data[$USerID] = (array) XRedis::getDBCache( 'lib_workers_groups', $Query );
//			$Data[$USerID] = DB::loadObjectList( $query );
		}
		return $Data[$USerID];

	}

	public static function getChiefsWorkerGroups()
	{
		$ChiefID = Users::GetUserID();
		static $Data = array();
		if ( !isset( $Data[$ChiefID] ) )
		{
			$query = 'select '
							. '  g.lib_title title, '
							. ' g.id '
							. ' from lib_workers_groups g '
							. ' where g.id in ('
							. ' select wg.group_id '
							. ' from rel_worker_chief wc '
							. ' left join rel_workers_groups wg on wg.worker = wc.worker '
							. ' where '
							. ' chief in (select m.id from hrs_workers m where m.PARENT_ID =' . $ChiefID . ') '
							. ' group by wg.group_id '
							. ' ) '
							. ' order by title ';
			$Data[$ChiefID] = DB::loadObjectList( $query );
		}
		return $Data[$ChiefID];

	}

	public static function getAllWorkerGroups( $Org = 0 )
	{
		static $Data = null;
		if ( is_null( $Data ) )
		{
			$S = '';
			if ( $Org )
			{
				$S = ' and t.org  =' . $Org;
			}
			$Query = 'select '
							. ' t.id, '
							. ' o.lib_title || \' - \' || t.lib_title title '
							. ' from lib_workers_groups t '
							. ' left join lib_unitorgs o on o.id = t.org '
							. ' where t.active > -1'
							. ' and o.active =1 '
							. $S
							. ' order by t.lib_title ';
			$Data = (array) XRedis::getDBCache( 'lib_workers_groups', $Query );
//			$Data = DB::loadObjectList( $query );
		}
		return $Data;

	}

	public static function getCurrentQuarter()
	{
		$date = new PDate();

		return self::getQuarterByMonth( $date->toFormat( '%m' ) );

	}

	public static function getQuarterByMonth( $month )
	{
		if ( $month <= 3 )
		{
			return 'I';
		}
		if ( $month <= 6 )
		{
			return 'II';
		}
		if ( $month <= 9 )
		{
			return 'III';
		}

		return 'IV';

	}

	public static function getPrivateTimeMInutes( $userID = null )
	{
		if ( is_null( $userID ) )
		{
			$userID = Users::GetUserID();
		}
		else
		{
			$userID = (int) $userID;
		}
		$QStart = null;
		$QEnd = null;
		self::getQuarterStartEnd( $QStart, $QEnd );
		$QStartDate = new PDate( $QStart );
		$QEndDate = new PDate( $QEnd );
		$TimeLimit = (int) Helper::getConfig( 'private_date_limit' );
		$Query = 'select '
						. ' nvl(' . $TimeLimit . ' - sum(e.time_min), ' . $TimeLimit . ') as time_sum '
						. ' from hrs_staff_events e '
						. ' where '
						. ' e.time_min >0'
						. ' and e.event_type in (0, 1, 3, 4, 5)'
						. ' and c_resolution = 0 '
						. ' and e.staff_id = ' . $userID
						. ' and e.event_date between to_date(' . DB::Quote( $QStartDate->toFormat( '%d-%m-%Y' ) ) . ', \'dd.mm.yyyy\') and to_date(' . DB::Quote( $QEndDate->toFormat( '%d-%m-%Y 23:59:59' ) ) . ', \'dd.mm.yyyy hh24:mi:ss\') '
		;

		$AllMinutes = DB::LoadResult( $Query );
		return $AllMinutes;

	}

	public static function getPrivateTime( $userID = null )
	{
		if ( is_null( $userID ) )
		{
			$userID = Users::GetUserID();
		}
		else
		{
			$userID = (int) $userID;
		}
		$QStart = null;
		$QEnd = null;
		self::getQuarterStartEnd( $QStart, $QEnd );
		$QStartDate = new PDate( $QStart );
		$QEndDate = new PDate( $QEnd );
		$TimeLimit = Helper::getConfig( 'hr_private_time_limit' );
		$Query = 'select '
						. ' nvl(' . $TimeLimit . ' - sum(e.time_min), ' . $TimeLimit . ') as time_sum '
						. ' from hrs_staff_events e '
						. ' where '
						. ' e.time_min > 0'
						. ' and e.event_type in (0, 1, 3, 4, 5)'
						. ' and c_resolution = 0 '
						. ' and e.staff_id = ' . $userID
						. ' and e.event_date between to_date(' . DB::Quote( $QStartDate->toFormat( '%d-%m-%Y' ) ) . ', \'dd.mm.yyyy\') and to_date(' . DB::Quote( $QEndDate->toFormat( '%d-%m-%Y 23:59:59' ) ) . ', \'dd.mm.yyyy hh24:mi:ss\') '
		;

		$AllMinutes = DB::LoadResult( $Query );
		$Hours = intval( $AllMinutes / 60 );
		$Minute = ($AllMinutes % 60);
		return $Hours . ' ' . Text::_( 'Hour' ) . ', ' . $Minute . ' ' . Text::_( 'minute' );

	}

	public static function getRemPrivateTime( $orgpid, $getMinutes = 0 )
	{
		static $PTimes = [];
		if ( !isset( $PTimes[$orgpid] ) )
		{

			$QStart = null;
			$QEnd = null;
			$Period = Helper::getConfig( 'private_date_period' );
			$consider_lateness = (int) Helper::getConfig( 'private_date_consider_lateness' );
			$ptimes_ids = self::getPtimesIDS();
			self::getPeriodStartEnd( $QStart, $QEnd, $Period );
			$QStartDate = new PDate( $QStart );
			$QEndDate = new PDate( $QEnd );
			$TimeLimit = Helper::getConfig( 'private_date_limit' );

			$Query = 'select '
							. ' w.orgpid, '
							. ' nvl(' . $TimeLimit . ' - sum(e.time_min), ' . $TimeLimit . ') as time_sum '
							. ' from slf_worker w '
							. ' left join hrs_staff_events e on e.staff_id = w.id '
							. ' where '
							. ' e.time_min >0 '
							. ' and w.active = 1 '
							. ($consider_lateness == 1 ? ' and e.c_resolution != 1 ' : '')
							. ' and e.event_date between to_date(' . DB::Quote( $QStartDate->toFormat( '%d-%m-%Y' ) ) . ', \'dd.mm.yyyy\') and to_date(' . DB::Quote( $QEndDate->toFormat( '%d-%m-%Y 23:59:59' ) ) . ', \'dd.mm.yyyy hh24:mi:ss\') '
							. ($consider_lateness == 0 ? ' and e.app_id in (' . $ptimes_ids . ')' : '')
							. ' group by w.orgpid '
			;

			$getRemPrivateTime = XRedis::getDBCache( 'hrs_applications', $Query, 'LoadObjectList', 'ORGPID', null, 600 );
//			$getRemPrivateTime = DB::LoadObjectList( $Query, 'ORGPID' );

			$PTimes[$orgpid] = (int) C::_( $orgpid . '.TIME_SUM', $getRemPrivateTime, $TimeLimit );
		}

		return $PTimes[$orgpid];

	}

	public static function getPtimesIDS()
	{
		$qq = 'select '
						. ' a.id, '
						. ' a.worker '
						. ' from '
						. ' hrs_applications a '
						. ' left join rel_person_org r on r.id = a.worker '
						. ' where '
						. ' a.type = ' . APP_PRIVATE_TIME
						. ' and status = 1 '
						. ' and r.person = ' . Users::GetUserID()
		;

		$ptimes_ids = (array) XRedis::getDBCache( 'hrs_applications', $qq, 'LoadList', null, 600 );
//		$ptimes_ids = DB::LoadList( $qq );
		$ptimes_ids[] = -555;
		return implode( ', ', $ptimes_ids );

	}

	public static function getPeriodStartEnd( &$QStartDate, &$QEndDate, $PeriodType = 1 )
	{
		$Month = PDate::Get()->toFormat( '%B', 1, false );
		$current_year = date( 'Y' );

		switch ( $PeriodType )
		{
			default:
			case '1': //month
				$QStartDate = strtotime( '1-' . $Month . '-' . $current_year );
				$QEndDate = strtotime( 'last day of ' . $Month . '-' . $current_year );
				break;
			case '2': //QUARTER
				$current_month = date( 'm' );
				if ( $current_month >= 1 && $current_month <= 3 )
				{
					$QStartDate = strtotime( '1-January-' . $current_year ); // timestamp or 1-Januray 12:00:00 AM
					$QEndDate = strtotime( '1-April-' . $current_year ); // timestamp or 1-April 12:00:00 AM means end of 31 March
				}
				else if ( $current_month >= 4 && $current_month <= 6 )
				{
					$QStartDate = strtotime( '1-April-' . $current_year ); // timestamp or 1-April 12:00:00 AM
					$QEndDate = strtotime( '1-July-' . $current_year ); // timestamp or 1-July 12:00:00 AM means end of 30 June
				}
				else if ( $current_month >= 7 && $current_month <= 9 )
				{
					$QStartDate = strtotime( '1-July-' . $current_year ); // timestamp or 1-July 12:00:00 AM
					$QEndDate = strtotime( '1-October-' . $current_year ); // timestamp or 1-October 12:00:00 AM means end of 30 September
				}
				else if ( $current_month >= 10 && $current_month <= 12 )
				{
					$QStartDate = strtotime( '1-October-' . $current_year ); // timestamp or 1-October 12:00:00 AM
					$QEndDate = strtotime( '1-January-' . ($current_year + 1) ); // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
				}
				break;
			case '3': //YEAR
				$QStartDate = strtotime( '1-JANUARY -' . $current_year );
				$QEndDate = strtotime( '31-DECEMBER -' . $current_year );
				break;
		}
		return;

	}

	public static function getUsedPrivateTime( $userID = null )
	{
		if ( is_null( $userID ) )
		{
			$userID = Users::GetUserID();
		}
		else
		{
			$userID = (int) $userID;
		}
		$QStart = null;
		$QEnd = null;
		self::getQuarterStartEnd( $QStart, $QEnd );

		$QStartDate = new PDate( $QStart );
		$QEndDate = new PDate( $QEnd );

//		$TimeLimit = Helper::getConfig( 'hr_private_time_limit' );
		$Query = 'select '
						. ' sum(e.time_min) time_sum '
						. ' from hrs_staff_events e '
						. ' where '
						. ' e.time_min >0'
						. ' and e.event_type in (0, 1, 3, 4, 5)'
						. ' and c_resolution = 0 '
						. ' and e.staff_id = ' . $userID
						. ' and e.event_date between to_date(' . DB::Quote( $QStartDate->toFormat( '%d-%m-%Y' ) ) . ', \'dd.mm.yyyy\') and to_date(' . DB::Quote( $QEndDate->toFormat( '%d-%m-%Y 23:59:59' ) ) . ', \'dd.mm.yyyy hh24:mi:ss\') '
		;

		$AllMinutes = DB::LoadResult( $Query );
		$Hours = floor( $AllMinutes / 60 );
		$Minute = ($AllMinutes % 60);
		return $Hours . ' ' . Text::_( 'Hour' ) . ', ' . $Minute . ' ' . Text::_( 'minute' );

	}

	public static function getUnknownTime( $userID = null )
	{
		if ( is_null( $userID ) )
		{
			$userID = Users::GetUserID();
		}
		else
		{
			$userID = (int) $userID;
		}
		$QStart = null;
		$QEnd = null;
		self::getQuarterStartEnd( $QStart, $QEnd );
		$QStartDate = new PDate( $QStart );
		$QEndDate = new PDate( $QEnd );
		$Query = 'select '
						. ' sum(e.time_min)  as time_sum '
						. ' from hrs_staff_events e '
						. ' where '
						. ' e.time_min >0  '
						. ' and trim(time_comment)  like \'%?%\'  '
						. ' and e.staff_id = ' . $userID
						. ' and e.event_date between to_date(' . DB::Quote( $QStartDate->toFormat( '%d-%m-%Y' ) ) . ', \'dd.mm.yyyy\') and to_date(' . DB::Quote( $QEndDate->toFormat( '%d-%m-%Y 23:59:59' ) ) . ', \'dd.mm.yyyy hh24:mi:ss\') '
		;

		$AllMinutes = DB::LoadResult( $Query );
		$Hours = floor( $AllMinutes / 60 );
		$Minute = ($AllMinutes % 60);
		return $Hours . ' ' . Text::_( 'Hour' ) . ', ' . $Minute . ' ' . Text::_( 'minute' );

	}

	public static function getWageCount( $userIDin = null, $date = 'now' )
	{
		$userID = (int) $userIDin;
		$Type = 0;
		$Date = new PDate( $date );
		$Table = new HolidayLimitsTable();
		$Table->LoadUserLimits( $userID, $Date->toFormat(), $Type );
		return (int) $Table->COUNT;

	}

	public static function getWageLessCount( $userID = null, $date = 'now' )
	{
		require_once PATH_BASE . DS . 'libraries' . DS . 'tables' . DS . 'limits.php';
		if ( is_null( $userID ) )
		{
			$userID = Users::GetUserData( 'ID' );
		}
		else
		{
			$userID = (int) $userID;
		}
		$Type = 1;
		$Date = new PDate( $date );
		$Table = new HolidayLimitsTable();
		$Table->LoadUserLimits( $userID, $Date->toFormat(), $Type );

		return (int) $Table->COUNT;

	}

	public static function getRolesConfig( $Role, $key = null )
	{
		static $Data = array();
		if ( !isset( $Data[$Role] ) )
		{
			$Query = 'select * from rel_roles_menus t '
							. ' where t.role = ' . $Role . ' '
			;
			$ItemsData = (array) XRedis::getDBCache( 'rel_roles_menus', $Query, 'loadObjectList', $key );
//			$ItemsData = DB::loadObjectList( $query, $key );
			foreach ( $ItemsData as $key => $Item )
			{
				$Item->PARAMS = json_decode( C::_( 'PARAMS', $Item, '[]' ) );
				$Data[$Role][$key] = $Item;
			}
		}
		return C::_( $Role, $Data, false );

	}

	/**
	 * 
	 * @param type string
	 * @return  SimpleXMLElements object
	 */
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

	public static function Base64ToImage( $imageSource, $name, $Directory = null, $Prefix = 'f' )
	{
		$imageSourceList = explode( ';', $imageSource );
		$info = C::_( 0, $imageSourceList, false );
		$ImgDataList = explode( ',', C::_( 1, $imageSourceList, false ) );
		$Image = C::_( 1, $ImgDataList );
		if ( empty( $info ) || empty( $Image ) )
		{
//			XError::setError( 'Image File Not Found!' );
			return false;
		}
		if ( !preg_match( '/data:image/', $info ) )
		{
			XError::setError( 'File is not Image Format!' );
			return false;
		}
		$Type = str_replace( 'data:image/', '', $info );
		$fileName = false;
		switch ( $Type )
		{
			case 'jpg':
			case 'jpeg':
				$fileName = $Prefix . $name . '.jpg';
				break;
			case 'png':
				$fileName = $Prefix . $name . '.png';
				break;
			case 'gif':
				$fileName = $Prefix . $name . '.gif';
				break;
			default:
				XError::setError( 'File is not Image Format!' );
				return null;
		}

		if ( is_null( $Directory ) )
		{
			$Directory = PATH_UPLOAD;
		}

		if ( file_put_contents( $Directory . DS . $fileName, base64_decode( $Image ) ) )
		{
			return $fileName;
		}
		return null;

	}

	public static function ImgToBase64( $Image )
	{
		if ( is_file( $Image ) )
		{
			$type = pathinfo( $Image, PATHINFO_EXTENSION );
			$data = file_get_contents( $Image );
			$base64 = 'data:image/' . $type . ';base64,' . base64_encode( $data );
			return $base64;
		}
		return null;

	}

	public static function getSectionList()
	{
		$query = 'select '
						. ' t.id, '
						. ' t.lib_title title, '
						. ' t.dept_id, '
						. ' d.lib_title dept_title '
						. ' from lib_sections t '
						. ' left join lib_departments d '
						. ' on d.id = t.dept_id '
						. ' where t.active = 1 '
						. ' order by t.dept_id asc, title asc';
		return DB::LoadObjectList( $query, 'ID' );

	}

	public static function getChiefGroups( $ChiefID = null )
	{
		if ( empty( $ChiefID ) )
		{
			$ChiefID = Users::GetUserID();
		}
		static $ChiefGroups = array();
		if ( !isset( $ChiefGroups[$ChiefID] ) )
		{
			$DirectTree = Helper::CheckTaskPermision( 'direct_subordianate_tree', 's' );
			$AdditionalTree = Helper::CheckTaskPermision( 'additional_subordianate_tree', 's' );
			$DirectTreeUnion = '';
			$AdditionalTreeUnion = '';
			if ( $DirectTree )
			{
				$IDx = XStaffSchedule::GetChiefSubordinationsTree();
				if ( $IDx )
				{
					$DirectTreeUnion = ' or ww.person in (' . XStaffSchedule::GetChiefSubordinationsTree() . ') ';
				}
			}
			if ( $AdditionalTree )
			{
				$IDx = XStaffSchedule::GetChiefSubordinationsTree( 1 );
				if ( $IDx )
				{
					$AdditionalTreeUnion = ' or ww.person in (' . XStaffSchedule::GetChiefSubordinationsTree( 1 ) . ') ';
				}
			}

			$query = 'select '
							. ' wg.group_id '
							. ' from REL_WORKERS_GROUPS wg '
							. ' left join slf_worker ww on ww.id = wg.worker  '
							. ' where '
							. ' wg.worker in (select m.worker from rel_worker_chief m where m.chief_pid =' . $ChiefID . ')'
			;
			$ChiefGroups[$ChiefID] = DB::LoadList( $query, true );
			if ( empty( $ChiefGroups[$ChiefID] ) )
			{
				$ChiefGroups[$ChiefID] = array( 0 );
			}
		}
		return $ChiefGroups[$ChiefID];

	}

	public static function getUserChiefs( $id = null )
	{
		$whereAnd = '';
		if ( is_null( $id ) )
		{
			$id = Users::GetUserID();
		}
		else
		{
			$whereAnd = ' and wc.chief not in (' . (int) $id . ')';
		}
		static $chiefs = array();
		if ( !isset( $chiefs[$id] ) )
		{
			$query = 'select * '
							. ' from rel_worker_chief wc '
							. ' left join slf_persons w on w.id = wc.chief_pid '
							. ' where '
							. ' w.active = 1 and '
							. ' wc.worker = ' . (int) $id
							. ' and wc.clevel in (0, 1) '
							. $whereAnd
			;
			$chiefs[$id] = DB::LoadObjectList( $query );
		}

		return $chiefs[$id];

	}

	public static function getAdvancePeriods( $number )
	{
		$params = array( ':number' => $number );
		$data = DB::callCursorFunction( 'getAdvancePeriods', $params, 'PERIOD_NUMBER' );
		return C::_( 'data', $data );

	}

	public static function getAdvanceDebits( $userID = null )
	{
		if ( empty( $userID ) )
		{
			$userID = Users::GetUserData( 'SALARY_EMPLOYEE_ID' );
		}
		$params = array( ':user_id' => $userID );
		$data = DB::callCursorFunction( 'getAdvancedept', $params );
		return C::_( 'data', $data );

	}

	public static function CanAdvance( $userID = null )
	{
		if ( empty( $userID ) )
		{
			$userID = Users::GetUserData( 'SALARY_EMPLOYEE_ID' );
		}
		if ( empty( $userID ) )
		{
			return false;
		}
		$Q = 'select erp.pkg_holiday.allow_emp_advance@erp_proxy(' . (int) $userID . ') can from dual';
		return (int) DB::LoadResult( $Q );

	}

	public static function CheckAdvanceRecord( $userID = null )
	{
		if ( empty( $userID ) )
		{
			$userID = Users::GetUserData( 'ID' );
		}
		if ( empty( $userID ) )
		{
			return false;
		}
		$query = 'select t.id from hrs_advances t where t.user_id = ' . (int) $userID . ' and t.status = 0 ';
		return (int) DB::LoadResult( $query );

	}

	public static function getRoles( $key = null )
	{
		static $Data = null;
		if ( empty( $Data ) )
		{
			$query = 'select * from lib_roles t '
							. ' where t.active >-1 '
							. ' order by t.ordering asc'
			;
			$Data = DB::loadObjectList( $query, $key );
		}
		return $Data;

	}

	public static function getHRMails()
	{
		$Q = 'select '
						. ' s.email '
						. ' from slf_persons s '
						. ' where '
						. ' s.user_role in '
						. ' ( '
						. ' select '
						. ' t.value '
						. ' from system_config t'
						. ' where '
						. ' t.key = \'hr_user_role\''
						. ' ) ';
		$data = DB::LoadList( $Q );

		return $data;

	}

	public static function getChiefsMails( $userID = null )
	{
		$chiefs = self::getUserChiefs( $userID );
		$return = array();
		foreach ( $chiefs as $chief )
		{
			$Email = C::_( 'EMAIL', $chief );
			if ( $Email )
			{
				$return[] = $Email;
			}
		}
		return $return;

	}

	public static function getHeadsMails()
	{
		$Q = 'select '
						. ' s.email '
						. ' from slf_persons s '
						. ' where '
						. ' s.active = 1 '
						. ' and s.user_role in '
						. ' ( '
						. ' select '
						. ' t.value '
						. ' from system_config t'
						. ' where '
						. ' t.key = \'head_user_role\''
						. ' ) ';
		$data = DB::LoadList( $Q );
		return $data;

	}

	public static function CheckHolidayLimit( $Day, $type, $Worker, $Date = 'now' )
	{
		$Limit = 0;
		switch ( $type )
		{
			case 1:
				$Limit = self::getWageLessCount( $Worker, $Date );
				break;

			default:
				$Limit = self::getWageCount( $Worker, $Date );
				break;
		}
		if ( $Day > $Limit )
		{
			return false;
		}
		return true;

	}

	public static function GetStandardWorkingDays( $Start, $End, $TimeGraph )
	{
		$StartDate = new PDate( $Start );
		$ENDTMP = new PDate( $End );
		$EndDate = new PDate( $ENDTMP->toformat( '%Y-%m-%d 23:59:59' ) );
		$Holidays = Helper::GetAllHoldays();
		$Days = array();
		while ( $StartDate->toUnix() < $EndDate->toUnix() )
		{
			$DayName = strtoupper( $StartDate->toFormat( '%A', true, false ) );
			$Date = $StartDate->toFormat( '%Y-%m-%d' );
			$Time = (int) C::_( $DayName, $TimeGraph );
			if ( empty( $Time ) )
			{
				$StartDate = new PDate( $StartDate->toUnix() + 86400 );
				continue;
			}
			if ( isset( $Holidays[$Date] ) )
			{
				$StartDate = new PDate( $StartDate->toUnix() + 86400 );
				continue;
			}

			$OBJ = new stdClass();
			$OBJ->REAL_DATE = $StartDate->toformat( '%Y-%m-%d' );
			$Days[] = $OBJ;
			$StartDate = new PDate( $StartDate->toUnix() + 86400 );
		}
		return $Days;

	}

	public static function GetDays( $Start, $End )
	{
		$StartDate = new PDate( $Start );
		$ENDTMP = new PDate( $End );
		$EndDate = new PDate( $ENDTMP->toformat( '%Y-%m-%d 23:59:59' ) );
		$Days = array();
		while ( $StartDate->toUnix() < $EndDate->toUnix() )
		{
			$Days[] = $StartDate->toformat( '%Y-%m-%d' );
			$StartDate = new PDate( $StartDate->toUnix() + 86400 );
		}
		return $Days;

	}

	public static function getQuarterStartEnd( &$QStartDate, &$QEndDate )
	{
		$Month = PDate::Get()->toFormat( '%B', 1, false );
		$current_year = date( 'Y' );
		$QStartDate = strtotime( '1-' . $Month . '-' . $current_year );
		$QEndDate = strtotime( 'last day of ' . $Month . '-' . $current_year );
		return;

		$current_month = date( 'm' );
		if ( $current_month >= 1 && $current_month <= 3 )
		{
			$QStartDate = strtotime( '1-January-' . $current_year ); // timestamp or 1-Januray 12:00:00 AM
			$QEndDate = strtotime( '1-April-' . $current_year ); // timestamp or 1-April 12:00:00 AM means end of 31 March
		}
		else if ( $current_month >= 4 && $current_month <= 6 )
		{
			$QStartDate = strtotime( '1-April-' . $current_year ); // timestamp or 1-April 12:00:00 AM
			$QEndDate = strtotime( '1-July-' . $current_year ); // timestamp or 1-July 12:00:00 AM means end of 30 June
		}
		else if ( $current_month >= 7 && $current_month <= 9 )
		{
			$QStartDate = strtotime( '1-July-' . $current_year ); // timestamp or 1-July 12:00:00 AM
			$QEndDate = strtotime( '1-October-' . $current_year ); // timestamp or 1-October 12:00:00 AM means end of 30 September
		}
		else if ( $current_month >= 10 && $current_month <= 12 )
		{
			$QStartDate = strtotime( '1-October-' . $current_year ); // timestamp or 1-October 12:00:00 AM
			$QEndDate = strtotime( '1-January-' . ($current_year + 1) ); // timestamp or 1-January Next year 12:00:00 AM means end of 31 December this year
		}

	}

	public static function getSalaryData( $SalaryID )
	{
//		ini_set( 'soap.wsdl_cache_enabled', 0 );
		$SOAP = new SoapClient( 'http://192.168.19.164:8081/SalaryWebServices/VacationWS?wsdl' );
		$loadEmpInfo = new stdClass();
		$loadEmpInfo->employeeId = $SalaryID;

		$ResponseEMP = $SOAP->loadEmpInfo( $loadEmpInfo );
		$Data = C::_( 'return', $ResponseEMP );
		$DataExp = explode( PHP_EOL, $Data );
		$Return = array();
		foreach ( $DataExp as $Item )
		{
			$Item = trim( $Item );
			if ( empty( $Item ) )
			{
				continue;
			}
			$ItemKV = explode( '=>', $Item );
			$Key = strtoupper( trim( C::_( '0', $ItemKV ) ) );
			$Value = trim( C::_( '1', $ItemKV ) );
			$Return[$Key] = $Value;
		}
		return $Return;

	}

	public static function getChiefsWorkersIDx( $ID = null )
	{
		if ( is_null( $ID ) )
		{
			$ID = Users::GetUserID();
		}
		static $Workers = array();
		if ( !isset( $Workers[$ID] ) )
		{
			$query = 'select '
							. ' wc.worker '
							. ' from rel_worker_chief wc '
							. ' left join slf_persons w on w.id = wc.chief '
							. ' where '
							. ' w.active = 1 '
							. ' and wc.chief  in (select m.id from hrs_workers m where m.PARENT_ID =' . (int) $ID . ' )'
			;
			$Workers[$ID] = DB::LoadList( $query, 'WORKER' );
		}
		return $Workers[$ID];

	}

	public static function GetAllHoldays()
	{
		$Query = 'select '
						. ' to_char(to_date(to_char(sysdate, \'yyyy\') || \'-\' || t.lib_month || \'-\' || t.lib_day,\'yyyy-mm-dd\'),\'yyyy-mm-dd\') holiday '
						. ' from lib_holidays t '
						. ' where '
						. ' t.active = 1 '
						. ' union all '
						. 'select '
						. ' to_char(to_date((to_char(sysdate, \'yyyy\') +1) || \'-\' || t.lib_month || \'-\' || t.lib_day,\'yyyy-mm-dd\'),\'yyyy-mm-dd\') holiday '
						. ' from lib_holidays t '
						. ' where '
						. ' t.active = 1 '
						. ' union all '
						. 'select '
						. ' to_char(to_date((to_char(sysdate, \'yyyy\') -1) || \'-\' || t.lib_month || \'-\' || t.lib_day,\'yyyy-mm-dd\'),\'yyyy-mm-dd\') holiday '
						. ' from lib_holidays t '
						. ' where '
						. ' t.active = 1 '
		;
		return (array) XRedis::getDBCache( 'lib_holidays', $Query, 'LoadList', 'HOLIDAY' );
//		return DB::LoadList( $Query, 'HOLIDAY' );

	}

	public static function getRowToolbar( $ID, $name, $option, $task = '', $check = 0 )
	{
		?>
		<span class="toolbar_item">
			<button class="btn btn-primary" type="button" onclick="SetDoAction('<?php echo $ID; ?>', '<?php echo $option; ?>', '<?php echo $task; ?>', <?php echo $check; ?>, <?php echo 0; ?>);">
				<?php echo Text::_( $name ); ?>
			</button>
		</span>
		<?php

	}

	public static function GetMonthHours( $Start, $End, $UserID )
	{
		$MonthStart = PDate::Get( $Start );
		$MonthEnd = PDate::Get( PDate::Get( $End )->toUnix() + 84600 );
		return self::GetWorkingHours( $MonthStart, $MonthEnd, $UserID );

	}

	public static function GetWorkingHours( $MonthStart, $MonthEnd, $Worker )
	{
		$GRAPHTYPE = (int) C::_( 'GRAPHTYPE', $Worker );
		if ( $GRAPHTYPE == 0 )
		{
			return 0;
			/*
			  //			$FullDayCount = (int) self::CalculateDayCount( $MonthStart, $MonthEnd );
			  //			$CountQuery = ' select abs(' . $FullDayCount . ' - count(1)) Count '
			  //							. ' from hrs_graph '
			  //							. 'where '
			  //							. ' worker = ' . (int) $HRSID
			  //							. ' and real_date between to_date(\''
			  //							. $MonthStart->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd hh24:mi:ss\' ) and  to_date(\''
			  //							. $MonthEnd->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\' ) '
			  //			;
			  //			$Missed = DB::LoadResult( $CountQuery );
			  //			if ( $Missed != 0 )
			  //			{
			  //				XError::setError( 'User Graph is not filled!' );
			  ////				Users::Redirect( '?ref=ERROR' );
			  //			}
			  //			$Query = ' select count(1) '
			  //							. ' from hrs_graph '
			  //							. 'where '
			  //							. ' time_id > 0'
			  //							. ' and worker = ' . (int) $HRSID
			  //							. ' and real_date between to_date(\''
			  //							. $MonthStart->toFormat( '%Y-%m-%d' ) . '\', \'yyyy-mm-dd hh24:mi:ss\' ) and  to_date(\''
			  //							. $MonthEnd->toFormat( '%Y-%m-%d 23:59:59' ) . '\', \'yyyy-mm-dd hh24:mi:ss\' ) '
			  //			;
			  //			$Count = DB::LoadResult( $Query );
			 */
		}
		else
		{
			$TimeGraph = self::LoadGraph( $GRAPHTYPE );
			$Hours = self::CalculateWorkingDayHours( $MonthStart, $MonthEnd, $TimeGraph );
		}
		return $Hours;

	}

	public static function LoadGraph( $GRAPHTYPE )
	{
		static $Graphs = array();
		if ( !isset( $Graphs[$GRAPHTYPE] ) )
		{
			$Graphs[$GRAPHTYPE] = DB::LoadObject( 'select * from lib_standard_graphs t where id = ' . $GRAPHTYPE );
		}
		return $Graphs[$GRAPHTYPE];

	}

	public static function CalculateWorkingDayHours( $Start, $End, $TimeGraph )
	{
		$Holidays = Helper::GetAllHoldays();
		$StartDate = new PDate( $Start );
		$EndDate = new PDate( PDate::Get( $End )->toformat( '%Y-%m-%d 23:59:59' ) );
		$Hours = 0;
		while ( $StartDate->toUnix() < $EndDate->toUnix() )
		{
			$DayName = strtoupper( $StartDate->toFormat( '%A', true, false ) );
			$Date = $StartDate->toFormat( '%Y-%m-%d' );
			$StartDate = new PDate( $StartDate->toUnix() + 86400 );
			$Time = (int) C::_( $DayName, $TimeGraph );
			if ( empty( $Time ) )
			{
				continue;
			}
			if ( isset( $Holidays[$Date] ) )
			{
				continue;
			}
			$Hours += self::GetGraphTimeHours( $Time );
		}
		return $Hours;

	}

	public static function GetGraphTimeHours( $Time )
	{
		static $Hours = array();
		if ( !isset( $Hours[$Time] ) )
		{
			$Query = 'select '
							. ' ( '
							. ' ( '
							. ' to_date(to_char(sysdate, \'yyyy-mm-dd\') || \' \' || t.end_time, \'yyyy-mm-dd hh24:mi\') - '
							. ' to_date(to_char(sysdate, \'yyyy-mm-dd\') || \' \' || t.start_time, \'yyyy-mm-dd hh24:mi\')'
							. ' ) '
							. ' - '
							. ' ( '
							. ' to_date(to_char(sysdate, \'yyyy-mm-dd\') || \' \' || nvl(t.end_break, \'00:00\'), \'yyyy-mm-dd hh24:mi\') - '
							. ' to_date(to_char(sysdate, \'yyyy-mm-dd\') || \' \' || nvl(t.start_break, \'00:00\'), \'yyyy-mm-dd hh24:mi\')'
							. ' ) '
							. ' ) * 24 hours '
							. ' from lib_graph_times t  '
							. ' where '
							. ' id = ' . (int) $Time
			;
			$Hours[$Time] = DB::LoadResult( $Query );
		}
		return $Hours[$Time];

	}

	public static function GetUserInOutStatus( $ID = null )
	{
		if ( is_null( $ID ) )
		{
			$ID = Users::GetUserID();
		}
		$Query = 'select '
						. ' nvl(m.real_type_id, 2) status_id, '
						. ' m.staff_id '
						. ' from ( '
						. ' select '
						. ' e.staff_id, '
						. ' e.real_type_id, '
						. ' e.event_date, '
						. ' row_number() over(partition by e.staff_id order by e.event_date desc) rn '
						. ' from hrs_staff_events e '
						. ' inner join slf_worker t on t.id = e.staff_id '
						. ' where '
						. ' e.event_date between sysdate - 5 and sysdate '
						. ' and e.real_type_id in (1, 2) '
						. ' and t.person = ' . DB::Quote( $ID )
						. '  AND t.active = 1 '
						. ' ) m '
						. ' where m.rn = 1 '
		;
		$Status = DB::LoadObjectList( $Query, 'STAFF_ID' );
		return $Status;

	}

	public static function GenerateTocken( $Length )
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString = '';
		for ( $i = 0; $i < $Length; $i++ )
		{
			$randomString .= $characters[rand( 0, $charactersLength - 1 )];
		}
		return $randomString;

	}

	public static function CleanArray( $ArrayIN, $type = 'int' )
	{
		$Result = array();
		$Array = (array) $ArrayIN;
		foreach ( $Array as $Key => $Value )
		{
			if ( $type == 'int' )
			{
				$Value = (int) $Value;
			}
			else
			{
				$Value = trim( $Value );
			}
			if ( $Value != '' )
			{
				$Result[$Key] = $Value;
			}
			else
			{
				continue;
			}
		}
		return $Result;

	}

	/**
	 * @assert ('saavt..pdf') == 'saavt_.pdf'
	 * @param type $file
	 * @return type
	 */
	public static function makeSafe( $file )
	{
		$ext = File::getExt( $file );
		$FileName = self::TranslitToLat( File::stripExt( $file ) );
		$regex = array( '/[^A-Za-z0-9\_\-]+/' );
		$NewName = preg_replace( $regex, '_', $FileName );
		if ( strlen( $NewName ) > 150 )
		{
			$NewName = substr( $NewName, 0, 150 );
		}
		$Fullname = $NewName . '.' . $ext;
		return $Fullname;

	}

	public static function GetDatesFromBillID( $BILLID )
	{
		$Year = '20' . substr( $BILLID, 0, 2 );
		$Month = substr( $BILLID, 2, 2 );
		$StartDay = '01';
		$StartDate = PDate::Get( $Year . '-' . $Month . '-' . $StartDay );
		$EndDate = $lastday = date( 'Y-m-t', $StartDate->toUnix() + 86400 * 15 );
		return self::GetDays( $Year . '-' . $Month . '-' . $StartDay, $EndDate );

	}

	public static function GetMarginDatesFromBillID( $BILLID )
	{
		$Year = '20' . substr( $BILLID, 0, 2 );
		$Month = substr( $BILLID, 2, 2 );
		$StartDay = '01';
		$StartDate = PDate::Get( $Year . '-' . $Month . '-' . $StartDay );
		$EndDate = date( 'Y-m-t', $StartDate->toUnix() + 86400 * 15 );
		$Return = array(
				'START' => $StartDate->toFormat( '%Y-%m-%d' ),
				'END' => $EndDate
		);
		return $Return;

	}

	public static function GetCurrentBillID( $Worker )
	{
		$BillID = (int) DB::LoadResult( 'select t.bill_id from HRS_TABLE t where t.worker = ' . $Worker . ' and t.status =0 ' );
		if ( empty( $BillID ) )
		{
			$BillID = PDate::Get()->toFormat( '%y%m' );
		}
		return $BillID;

	}

	public static function getWorkerGroups2()
	{
		$ChiefSections = self::getChiefGroups2();
		$first_value = reset( $ChiefSections );
		if ( empty( $first_value ) )
		{
			$sectionID = Helper::getUserGroup();
		}
		else
		{
			$sectionID = implode( ',', $ChiefSections );
		}

		if ( empty( $sectionID ) )
		{
			return array();
		}
		static $Data = array();
		if ( !isset( $Data[$sectionID] ) )
		{
			$query = 'select * from lib_workers_groups t '
							. ' where t.active>-1 '
							. ' and t.id in( ' . $sectionID . ' ) '
							. 'order by t.lib_title ';
			$Data[$sectionID] = DB::loadObjectList( $query );
		}
		return $Data[$sectionID];

	}

	public static function getUserGroup()
	{
		$UserID = Users::GetUserID();
		$Query = 'select t.group_id  
from REL_WORKERS_GROUPS t 
left join hrs_workers w on w.ID = t.worker
where w.PARENT_ID= ' . $UserID;
		return DB::LoadResult( $Query );

	}

	public static function getChiefGroups2( $ChiefID = null )
	{
		if ( empty( $ChiefID ) )
		{
			$ChiefID = Users::GetUserID();
		}
		static $ChiefGroups = array();
		if ( !isset( $ChiefGroups[$ChiefID] ) )
		{
			$query = 'select '
							. ' wg.group_id '
							. ' from rel_worker_chief wc '
							. ' left join hrs_workers w on w.id = wc.worker '
							. ' left join rel_workers_groups wg on wg.worker = w.id '
							. ' where chief in(select m.id from hrs_workers m where m.parent_id =' . $ChiefID . ' ) '
							. ' and nvl(wg.group_id, 0) > 0 '
							. ' and w.enable = 1 '
							. ' group by wg.group_id '
			;
			$ChiefGroups[$ChiefID] = DB::LoadList( $query, true );
			if ( empty( $ChiefGroups[$ChiefID] ) )
			{
				$ChiefGroups[$ChiefID] = array( 0 );
			}
		}
		return $ChiefGroups[$ChiefID];

	}

	/**
	 * 
	 * @param type $name
	 * @param type $option
	 * @param type $task
	 * @param type $check
	 * @param type $validate
	 */
	public static function getToolbarExport( $name, $option, $task = '', $check = 0, $validate = 0 )
	{
		?>
		<span class="toolbar_item">
			<button class="btn btn-primary" type="button" onclick="doAction('<?php echo $option;
		?>', '<?php echo $task; ?>', <?php echo $check; ?>, <?php echo $validate; ?>);">
							<?php echo Text::_( $name ); ?>
			</button>
		</span>
		<?php

	}

	public static function CheckTaskPermision( $task, $Option = null )
	{
		$Role = Users::GetUserData( 'USER_ROLE' );
		if ( $Role == -500 )
		{
			return true;
		}
		$Menus = MenuConfig::getInstance();
		if ( $Option )
		{
			$Active = $Menus->getItem( 'LIB_OPTION', $Option );
		}
		else
		{
			$Active = $Menus->getActive();
		}

		$Tasks = Helper::getRolesConfig( $Role, 'MENU' );
		$Item = C::_( 'ID', $Active );
		return (boolean) C::_( $Item . '.PARAMS.' . $task, $Tasks, 0 );

	}

	public static function CloseModal( $msg = '', $ReloadParent = false, $Js = '' )
	{
		if ( $msg )
		{
			self::SetJS( 'alert("' . htmlspecialchars( Text::_( $msg ) ) . '");' );
		}
		if ( $ReloadParent )
		{
			self::SetJS( 'window.parent.document.location.reload();' );
		}
		self::SetJS( 'window.parent.$(\'.lity-close\').click();' );
		self::SetJS( 'window.parent.$.prettyPhoto.close();' );
		echo '<script type="text/javascript">';
		echo $Js;
		echo self::GetJS();
		echo '</script>';
		die;

	}

	public static function getChangesByType( $typeId )
	{
		if ( !$typeId )
		{
			return [];
		}

		$order_by = ' order by tr.id asc';

		$where = [];
		$where[] = ' trunc(tr.change_date) <= trunc(sysdate) ';
		$where[] = ' tr.change_type = ' . DB::Quote( $typeId );
		$where[] = ' tr.status = 0 ';

		$whereQ = count( $where ) ? ' WHERE (' . implode( ') AND (', $where ) . ')' : '';
		$Q = 'select * from slf_changes tr '
						. $whereQ
						. $order_by
		;
		return DB::LoadObjectList( $Q );

	}

	public static function SetJSVars()
	{
		$CurrentLang = XTranslate::GetCurrentLang();
		$Month = [
				'იანვარი',
				'თებერვალი',
				'მარტი',
				'აპრილი',
				'მაისი',
				'ივნისი',
				'ივლისი',
				'აგვისტო',
				'სექტემბერი',
				'ოქტომბერი',
				'ნოემბერი',
				'დეკემბერი'
		];
		ob_start();
		?>
		<script>
			var BFHMonthsList = [<?php
		$MM = [];
		foreach ( $Month as $M )
		{
			$MM[] = XTranslate::_( $M, 'langfile' );
		}
		echo '"' . implode( '","', $MM ) . '"';
		?>];
			var BFHDaysList = [<?php
		$WeekDays = [
				'კვი',
				'ორშ',
				'სამ',
				'ოთხ',
				'ხუთ',
				'პარ',
				'შაბ'
		];
		$WW = [];
		foreach ( $WeekDays as $M )
		{
			$WW[] = XTranslate::_( $M, 'langfile' );
		}
		echo '"' . implode( '","', $WW ) . '"';
		?>];
			var GlobalAlertConfirm = "<?php echo XTranslate::_( 'დარწმუნებული ბრძანდებით, რომ გსურთ ამ მოქმედების შესრულება?', 'langfile' ); ?>";
			var GlobalAlertSelectRows = "<?php echo XTranslate::_( 'ჩანაწერები არ არის მონიშნული.', 'langfile' ); ?>";
			var GlobalAlertCheckData = "<?php echo XTranslate::_( 'მონაცემები არასწორია. გთხოვთ, გადაამოწმოთ!', 'langfile' ); ?>";
			var GlobalAlertBrowser = "<?php echo str_replace( '_', '<br />', XTranslate::_( 'გთხოვთ, გამოიყენოთ მხოლოდ _ Mozilla Firefox ან Google Chrome _ ბრაუზერი!', 'langfile' ) ); ?>";
			var GlobalAlertBrowserWork = "<?php echo XTranslate::_( 'გაითვალისწინეთ, რომ სხვა ბრაუზერებში სისტემა არასტაბილურად იმუშავებს!', 'langfile' ); ?>";
			var GlobalAlertClose = "<?php echo XTranslate::_( 'დახურვა', 'langfile' ); ?>";
		<?php
		if ( $CurrentLang == 'ka' )
		{
			?>
				var GlobalGeoKBD = true;
			<?php
		}
		else
		{
			?>
				var GlobalGeoKBD = false;
			<?php
		}
		$CrossHover = Helper::getConfig( 'graph_show_selected_row_and_columns', 0 );
		if ( $CrossHover )
		{
			?>
				var GlobalGraphCrossHover = true;
			<?php
		}
		else
		{
			?>
				var GlobalGraphCrossHover = false;
			<?php
		}
		?>
		</script>
		<?php
		return ob_get_clean();

	}

}
