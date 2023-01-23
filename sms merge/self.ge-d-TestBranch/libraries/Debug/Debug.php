<?php

class XDebug extends XObject
{
	public $_CSS = '';
	public $_JS = '';
	private $_params = '';
	private static $_instance = NULL;

	public function _RenderProfile()
	{
		global $_PROFILER;
		$roundTimes = 5;
		ob_start();
		?>
		<div class="debugc_container">
			<div  class="debugc_title" onclick="toggleContainer('debug_profile_body');">
				<?php echo Text::_( 'Profile Information' ); ?> 
			</div>
			<div class="debugc_body" id="debug_profile_body">
				<div class="debugc_body_in">
					<?php
					global $GLOBALTIME;
					$Time = microtime( true ) - $GLOBALTIME;
					$Profile = [ 'Page Time Sum:' . $Time ];
					foreach ( $Profile as $mark )
					{
						$exp = explode( ':', $mark );
						?>
						<div  class="debugc_item">
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $exp[0]; ?>
								</div>
								<div class="debugc_value">
									<?php echo round( $exp[1], $roundTimes ); ?>
								</div>
								<div class="debugc_cls"></div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public function _RenderQueries()
	{
		$geshi = new GeSHi( '', 'sql' );
		$geshi->set_header_type( GESHI_HEADER_DIV );
		$newlineKeywords = '/<span style="color: #993333; font-weight: bold;">'
						. '(FROM|LEFT|INNER|OUTER|WHERE|SET|VALUES|ORDER|GROUP|HAVING|LIMIT|AND|UNION|UNION ALL|END|ELSE|INSERT|SELECT|IF[\s]+)'
						. '<\\/span>/i';
		$log = DB::$debug;
		$roundTimes = 5;

		$showTimes = true;
		$seconds = array();
		$summary = 0;
		if ( $showTimes && $log )
		{
			$seconds = DB::$debugTime;
			$summary = 0;
		}
		ob_start();
		?>
		<div class="debugc_container">
			<div  class="debugc_title" onclick="toggleContainer('debug_queries_body');">
				<?php echo Text::sprintf( 'DataBase Queries logged: %s', count( $log ) ); ?>
			</div>
			<div class="debugc_body" id="debug_queries_body">
				<div class="debugc_body_in">
					<?php
					if ( $log )
					{
						foreach ( $log as $k => $sql )
						{
							?>
							<div  class="debugc_item_query">
								<div class="debugc_c_1">
									<?php echo $k + 1; ?>.
								</div>
								<div class="debugc_c_2">
									<?php
									if ( $showTimes )
									{
										$sec = isset( $seconds[$k] ) ? round( $seconds[$k], $roundTimes ) : '';
										if ( $sec )
										{
											$summary += $sec;
											$color = '';
											if ( $sec >= 0.1 )
											{
												$color = '_warn';
											}
										}
										?>
										<span class="debugc_c_time<?php echo $color; ?>">
											<?php echo Text::_( 'Time' ); ?> : <?php echo $sec; ?>
										</span>
										<?php
									}
									?>
								</div>
								<div class="debugc_c_3">
									<?php
									$geshi->set_source( $sql );
									echo preg_replace( $newlineKeywords, '<br />\\0', $geshi->parse_code() );
									?>
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}

						if ( $showTimes )
						{
							$color = '';
							if ( $summary >= 0.1 )
							{
								$color = '_warn';
							}
							?>
							<div  class="debugc_item_summary">
								<div class="debugc_key_summary">
									<?php echo Text::_( 'Summary' ); ?>
								</div>
								<div class="debugc_value_summary<?php echo $color; ?>">
									<?php echo $summary; ?>&nbsp;
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}
					}
					else
					{
						?>
						<div  class="debugc_item">
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo Text::_( 'Database Queries' ); ?> :
								</div>
								<div class="debugc_value">
									<?php echo Text::_( 'No Database Queries' ); ?>
								</div>
								<div class="debugc_cls"></div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public function _RenderRedis()
	{
		$log = XRedis::$Query;
		$roundTimes = 5;
		$showTimes = true;
		$summary = 0;
		$seconds = XRedis::$Times;
		ob_start();
		?>
		<div class="debugc_container">
			<div  class="debugc_title" onclick="toggleContainer('debug_redis_queries_body');">
				<?php echo Text::sprintf( 'Redis Query logged: %s', count( $log ) ); ?>
			</div>
			<div class="debugc_body" id="debug_redis_queries_body">
				<div class="debugc_body_in">
					<?php
					if ( $log )
					{
						foreach ( $log as $k => $text )
						{
							?>
							<div  class="debugc_item_query">
								<div class="debugc_c_1">
									<?php echo $k + 1; ?>.
								</div>
								<div class="debugc_c_2">
									<?php
									if ( $showTimes )
									{
										$sec = isset( $seconds[$k] ) ? round( $seconds[$k], $roundTimes ) : '';
										if ( $sec )
										{
											$summary += $sec;
											$color = '';
											if ( $sec >= 0.005 )
											{
												$color = '_warn';
											}
										}
										?>
										<span class="debugc_c_time<?php echo $color; ?>">
											<?php echo Text::_( 'Time' ); ?> : <?php echo $sec; ?>
										</span>
										<?php
									}
									?>
								</div>
								<div class="debugc_c_3">
									<?php
									echo $text;
									?>
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}

						$color = '';
						if ( $summary >= 0.1 )
						{
							$color = '_warn';
						}
						?>
						<div  class="debugc_item_summary">
							<div class="debugc_key_summary">
								<?php echo Text::_( 'Summary' ); ?>
							</div>
							<div class="debugc_value_summary<?php echo $color; ?>">
								<?php echo $summary; ?>&nbsp;
							</div>
							<div class="debugc_cls"></div>
						</div>
						<?php
					}
					else
					{
						?>
						<div  class="debugc_item">
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo Text::_( 'Redis Queries' ); ?> :
								</div>
								<div class="debugc_value">
									<?php echo Text::_( 'No Redis Queries' ); ?>
								</div>
								<div class="debugc_cls"></div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public function _RenderFiles()
	{
		$files = get_included_files();
		ob_start();
		?>
		<div class="debugc_container">
			<div  class="debugc_title" onclick="toggleContainer('debug_files_body');">
				<?php echo Text::sprintf( 'Loaded Files : %s', count( $files ) ); ?> 
			</div>
			<div class="debugc_body" id="debug_files_body">
				<div class="debugc_body_in">
					<?php
					foreach ( $files as $k => $v )
					{
						?>
						<div  class="debugc_item">
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $k + 1; ?> 
								</div>
								<div class="debugc_value">
									<?php echo $v; ?>
								</div>
								<div class="debugc_cls"></div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public function _RenderExtensions()
	{
		$extensions = get_loaded_extensions();
		array_walk( $extensions, function ( &$value )
		{
			$value = ucfirst( $value );
		}
		);
		sort( $extensions );
		ob_start();
		?>
		<div class="debugc_container">
			<div  class="debugc_title" onclick="toggleContainer('debug_extensions_body');">
				<?php echo Text::sprintf( 'Loaded Extensions : %s', count( $extensions ) ); ?> 
			</div>
			<div class="debugc_body" id="debug_extensions_body">
				<div class="debugc_body_in">
					<?php
					foreach ( $extensions as $k => $v )
					{
						?>
						<div  class="debugc_item">
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $k + 1; ?> 
								</div>
								<div class="debugc_value">
									<?php echo $v; ?>
								</div>
								<div class="debugc_cls"></div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public function _RenderLangs()
	{
		$lang = Language::getInstance( SYSTEM_LANG );
		if ( !method_exists( $lang, 'getLogTimes' ) )
		{
//			return;
		}
		$extensions = $lang->getPaths();

		$seconds = $lang->getLogTimes();
		ob_start();
		?>
		<div class="debugc_container">
			<div  class="debugc_title" onclick="toggleContainer('debug_lang_body');">
				<?php echo Text::sprintf( 'Loaded Language Files : %s', count( $extensions ) ); ?> 
			</div>
			<div class="debugc_body" id="debug_lang_body">
				<div class="debugc_body_in">
					<?php
					$summary = 0;
					foreach ( $extensions as $files )
					{
						foreach ( $files as $file => $status )
						{
							$sec = isset( $seconds[$file] ) ? round( $seconds[$file], $this->_params->get( 'roundTimes', 5 ) ) : '';
							if ( $sec )
							{
								$summary += $sec;
								$color = '';
								if ( $sec >= 0.1 )
								{
									$color = '_warn';
								}
							}
							$Nstatus = '';
							if ( !$status )
							{
								$Nstatus = ' debugc_value_no';
							}
							?>
							<div  class="debugc_item">
								<div  class="debugc_item">
									<div class="debugc_key<?php echo $color; ?>">
										<?php
										if ( $sec )
										{
											echo Text::_( 'Time' ) . ' : ' . $sec;
										}
										?> 
									</div>
									<div class="debugc_value<?php echo $Nstatus; ?>">
										<?php echo $file; ?>
									</div>
									<div class="debugc_cls"></div>
								</div>
							</div>
							<?php
						}
					}
					if ( $summary )
					{
						$color = '';
						if ( $summary >= 0.1 )
						{
							$color = '_warn';
						}
						?>
						<div  class="debugc_item_summary">
							<div class="debugc_key_summary">
								<?php echo Text::_( 'Summary' ); ?> 
							</div>
							<div class="debugc_value_summary<?php echo $color; ?>">
								<?php echo $summary; ?>&nbsp;
							</div>
							<div class="debugc_cls"></div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	public function _SetCSS()
	{
		ob_start();
		?>
		<style type="text/css">
			.system_debug_info
			{
				margin:30px 0px 60px;
				font-size: 13px;
				padding: 10px;
				background-color: #FFFFFF;
				font-family: Arial;
				display: none;
			}
			.system_debug_info pre
			{
				margin: 2px 0px;
			}
			.debug_show_hide
			{
				background-color:#FFFFFF;
				border: 4px solid #FFFFFF;
				bottom: 10px;
				position: fixed;
				right: 10px;
				width: 150px;
				z-index: 9999999;
			}
			.debug_show_hide a
			{
				border: 4px solid #373656;
				color: #373656;
				font-weight: bold;
				line-height: 25px;
				display: block;
				text-align: center;
			}
			.debugc_container
			{
				width: 90%;
				margin: 0px auto 10px;
			}
			.debugc_title
			{
				cursor: pointer;
				background-color: #373656;
				padding: 10px;
				color: #FFFFFF;
				margin: 0px;
				font-size: 14px;
				font-weight: bold;
			}
			.debugc_body
			{
				display: none;
				margin: 0px 0px 30px;
				width: 100%;
				overflow: auto;
			}
			.debugc_body_in
			{
				padding: 10px;
				border: 1px solid #373656;
				line-height: 1.5em;
			}
			.debugc_subtitle
			{
				background-color:#00756A;
				padding: 5px 10px;
				margin: 0px 0px 5px;
				cursor: pointer;
				font-weight: bold;
				color: #FFFFFF;
			}
			.debugc_subbody
			{
				margin: 0px 0px 5px;
				display: none;
			}
			.debugc_key_summary
			{
				width: 30%;
				float: left;
				background-color: #F6F6F6;
				border-bottom: 1px solid #E9E9E9;
				border-right: 1px solid #E9E9E9;
				color: #666666;
				font-style: italic;
				font-weight: bold;
				text-align: right;
				font-size: 14px;
				padding: 5px;
			}
			.debugc_value_summary
			{
				width: 67%;
				float: left;
				padding: 5px;
				font-size: 14px;
				border-bottom: 1px solid #E9E9E9;
			}
			.debugc_value_summary_warn
			{
				width: 67%;
				float: left;
				padding: 5px;
				font-size: 14px;
				color: #bb0093;
				font-style: italic;
				font-weight: bold;
				border-bottom: 1px solid #E9E9E9;
			}
			.debugc_key_warn
			{
				width: 30%;
				float: left;
				background-color: #F6F6F6;
				border-bottom: 1px solid #E9E9E9;
				border-right: 1px solid #E9E9E9;
				color: #bb0093;
				font-weight: bold;
				font-style: italic;
				text-align: right;
				font-size: 13px;
				padding: 5px;
			}
			.debugc_key
			{
				width: 30%;
				float: left;
				background-color: #F6F6F6;
				border-bottom: 1px solid #E9E9E9;
				border-right: 1px solid #E9E9E9;
				color: #666666;
				font-weight: bold;
				text-align: right;
				padding: 5px;
			}
			.debugc_value
			{
				width: 67%;
				float: left;
				padding: 5px;
				border-bottom: 1px solid #E9E9E9;
			}

			.debugc_value_no
			{
				color: #cccccc;
			}
			.debugc_cls
			{
				clear: both;
			}
			.debugc_c_1
			{
				width: 5%;
				font-weight: bold;
				float: left;
				text-align: center;
				color: #666666;
			}
			.debugc_c_2
			{
				width:15%;
				font-weight: bold;
				float: left;
				text-align: left;
				color: #666666;
			}
			.debugc_c_time
			{
				display: block;
				padding: 0px 5px;
			}
			.debugc_c_time_warn
			{
				display: block;
				color: #bb0093;
				font-weight: bold;
				padding: 0px 5px;
			}
			.debugc_c_3
			{
				width:67%;
				font-weight: bold;
				float: left;
				text-align: left;
				color: #666666;
			}
			.debugc_c_ex
			{
				width: 10%;
				float: right;
			}
			.debugc_c_ex a
			{
				display: block;
				font-weight: bold;
				text-align: center;
				line-height: 30px;
				color:#000066;
				border: 1px solid #000066;
				text-decoration: none;
			}
			.debugc_c_ex a:hover
			{
				color: #bb0093;
				border: 1px solid #bb0093;
				text-decoration: underline;
			}
			.debugc_item_query
			{
				border-bottom:1px solid #E9E9E9;
				padding: 10px 0px;
				line-height: normal;
			}
			.debug_history
			{
				display: none;
			}
			.debug_navigation
			{
				cursor: default;
				width: 90%;
				margin: 0px auto 10px;
			}
			.debug_navigation a
			{
				color: #FFFFFF;
			}
			.debug_home_page
			{
				float: left;
				width: auto;
			}
			.debug_history_pages
			{
				float: right;
				width: auto;
			}
			.debug_history_title
			{
				float: left;
				width: auto;
				font-style: italic;
			}
			.debug_history_nonum,
			.debug_history_pagenum
			{
				float: left;
				width: auto;
			}
			.debug_active_n a
			{
				border: 1px solid #bb0093;
				background-color: #FFFFFF;
				color: #bb0093;
			}
			.debugc_navbar
			{
				background-color: #00756A;
				padding:10px;
				color: #FFFFFF;
				margin: 0px;
				font-size: 14px;
				font-weight: bold;
			}
			.debug_history_pagenum a,
			.debugc_navbar a
			{
				padding: 5px;
				font-size: 14px;
				font-weight: bold;
				line-height: 25px;
				height: 25px;
			}
			.debug_history_title
			{
				padding: 0px 10px;
				font-size: 14px;
				font-weight: bold;
				line-height: 25px;
				height: 25px;
			}
		</style>
		<?php
		$this->_CSS = ob_get_clean();

	}

	public function _SetJS()
	{
		$js = '<script type="text/javascript">
	  <!--
	  var debug_active_page = "debug_page_main";
	  function togglePage(name)
	  {
	  if(debug_active_page == name){return false;}
	  var oe = document.getElementById(debug_active_page);
	  var oet = document.getElementById(debug_active_page+"_n");
	  var e = document.getElementById(name);
	  var et = document.getElementById(name+"_n");
	  debug_addClass(et, "debug_active_n");
	  debug_removeClass(oet, "debug_active_n");
	  e.style.display = "block";
	  oe.style.display = "none";
	  debug_active_page = name;
	  }
	  function toggleContainer(name)
	  {
	  var e = document.getElementById(name);
	  e.style.display = (e.style.display == "" || e.style.display == "none" ) ? "block" : "none";
	  setCookie("debug_cookie_|" + name,e.style.display,10);}
	  function SetDebugState(){
	  var i,x,y,ARRcookies=document.cookie.split(";");
	  for (i=0;i<ARRcookies.length;i++){
	  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
	  x=x.replace(/^\s+|\s+$/g,"");
	  var name = x.split("|");
	  if(name[1]){
	  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
	  if(document.getElementById(name[1]))
	  {document.getElementById(name[1]).style.display = y;}}}}
	  function setCookie(c_name,value,exdays){
	  var exdate=new Date();
	  exdate.setDate(exdate.getDate() + exdays);
	  var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	  document.cookie=c_name + "=" + c_value;
	  }
	  window.onload=SetDebugState;
	  function debug_hasClass(ele, cls) {
	  return ele.className.match(new RegExp(\'(\\s|^)\'+cls+\'(\\s|$)\'));
	  }
	  function debug_addClass(ele,cls)
	  {
	  if (!debug_hasClass(ele,cls)) ele.className += " "+cls;
	  }
	  function debug_removeClass(ele,cls)
	  {
	  var reg = new RegExp(\'(\\s|^)\'+cls+\'(\\s|$)\');
	  var keys =ele.className.split(" ");
	  var newClass = "";
	  for (var j = 0; j < keys.length; j++)
	  {
	  if(newClass !=""){newClass = newClass + " ";}if(keys[j] != cls){
	  newClass = newClass + keys[j];}
	  }
	  ele.className=newClass;
	  }
	  -->
	  </script>'
		;
		$this->_JS = $js;

	}

	public function GenHTMLHead()
	{
		ob_start();
		?>
		<div class="debug_show_hide">
			<a onclick="toggleContainer('system_debug_info');document.getElementById('system_debug_info').scrollIntoView();" href="javascript:void(0);">
				<?php echo Text::_( 'Show / Hide Debug' ); ?>
			</a>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;

	}

	public function GenHistory( $current )
	{
		$history = array();
		if ( isset( $_SESSION['DEBUG_HISTORY'] ) )
		{
			$history = $_SESSION['DEBUG_HISTORY'];
			unset( $_SESSION['DEBUG_HISTORY'] );
		}
		else
		{
			$_SESSION['DEBUG_HISTORY'] = array();
		}
		$history = array_slice( $history, 0, 4 );
		ob_start();
		$k = 1;
		foreach ( $history as $t )
		{
			$t = str_replace( 'debug_', 'debug' . $k . '_', $t );
			?>
			<div id="debug_page_<?php echo $k; ?>" class="debug_history">
				<?php echo $t; ?>
			</div>
			<?php
			$k++;
		}
		$html = ob_get_clean();
		array_unshift( $history, $current );
		$_SESSION['DEBUG_HISTORY'] = $history;
		return $html;

	}

	public function GenHistoryNav()
	{
		$tab = 0;
		if ( isset( $_SESSION['DEBUG_HISTORY'] ) )
		{
			$tab = count( $_SESSION['DEBUG_HISTORY'] ) - 1;
		}
		ob_start();
		?>
		<div class="debug_history_pages">
			<div class="debug_history_title">
				<?php echo Text::_( 'Debug History' ); ?>
			</div>
			<?php
			if ( $tab > 0 )
			{
				for ( $a = 0; $a < $tab; $a++ )
				{
					$k = $a + 1;
					?>
					<div class="debug_history_pagenum" id="debug_page_<?php echo $k; ?>_n">
						<a href="javascript:void(0);" onclick="togglePage('debug_page_<?php echo $k; ?>');">
							<?php
							echo Text::_( 'Page #' );
							echo $k;
							?>
						</a>
					</div>
					<?php
				}
			}
			else
			{
				?>
				<div class = "debug_history_title">
					<?php echo Text::_( 'No History Page(s)' ); ?>
				</div>
				<?php
			}
			?>
			<div class="debugc_cls"></div>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;

	}

	public function _RenderTranslate()
	{
		$lang = Language::getInstance( SYSTEM_LANG );
		$orphans = $lang->untranslated;
		$o = count( $orphans );
		ob_start();
		?>
		<div class="debugc_container">
			<div  class="debugc_title" onclick="toggleContainer('debug_translate_body');">
				<?php echo Text::sprintf( 'Untranslated Strings Diagnostic : %s', $o ); ?>
			</div>
			<div class="debugc_body" id="debug_translate_body">
				<div class="debugc_body_in">
					<?php
					if ( $o )
					{
						ksort( $orphans, SORT_STRING );
						foreach ( $orphans as $key => $info )
						{
							?>
							<div  class="debugc_item">
								<div  class="debugc_item">
									<!--<div class="debugc_key">-->
									<?php
//										echo $key;
									?>
									<!--</div>-->
									<div class="debugc_value" style="float: right; width: 70%;">
										<?php echo $info; ?>
									</div>
									<div class="debugc_cls"></div>
								</div>
							</div>
							<?php
						}
					}
					else
					{
						?>
						<div  class="debugc_item">
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo Text::_( 'Untranslated Strings' ); ?> :
								</div>
								<div class="debugc_value">
									<?php echo Text::_( 'No Untranslated Strings' ); ?>
								</div>
								<div class="debugc_cls"></div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

	/*


	  public function _RenderStringDesigner()
	  {
	  $lang = JFactory::getLanguage();
	  $orphans = $lang->getOrphans();
	  $o = count( $orphans );
	  ob_start();
	  ?>
	  <div class="debugc_container">
	  <div  class="debugc_title" onclick="toggleContainer('debug_strdesign_body');">
	  <?php echo Text::sprintf( 'Untranslated Strings Designer : %s', $o ); ?>
	  </div>
	  <div class="debugc_body" id="debug_strdesign_body">
	  <div class="debugc_body_in">
	  <?php
	  if ( $o )
	  {
	  ksort( $orphans, SORT_STRING );
	  $guesses = array();
	  foreach ( $orphans as $key => $occurance )
	  {
	  if ( is_array( $occurance ) AND isset( $occurance[0] ) )
	  {
	  $info = &$occurance[0];
	  $file = @$info['file'];
	  if ( !isset( $guesses[$file] ) )
	  {
	  $guesses[$file] = array();
	  }

	  $guess = str_replace( '_', ' ', $info['string'] );
	  $strip = $this->_params->get( 'language_prefix' );
	  if ( $strip )
	  {
	  $guess = trim( preg_replace( chr( 1 ) . '^' . $strip . chr( 1 ), '', $guess ) );
	  }
	  $guesses[$file][] = trim( strtoupper( $key ) ) . '=' . $guess;
	  }
	  }
	  $string = '';
	  foreach ( $guesses as $file => $keys )
	  {
	  $string .= "\n\n# " . ($file ? $file : Text::_( 'Unknown file' )) . "\n\n";
	  $string .= implode( "\n", $keys );
	  }
	  ?>
	  <div  class="debugc_item">
	  <div  class="debugc_item">
	  <div class="debugc_key"><?php echo Text::_( 'Untranslated Strings Designer' ); ?> : </div>
	  <div class="debugc_value"><pre><?php echo $string; ?></pre></div>
	  <div class="debugc_cls"></div>
	  </div>
	  </div>
	  <?php
	  }
	  else
	  {
	  ?>
	  <div  class="debugc_item">
	  <div  class="debugc_item">
	  <div class="debugc_key">
	  <?php echo Text::_( 'Untranslated Strings' ); ?> :
	  </div>
	  <div class="debugc_value">
	  <?php echo Text::_( 'No Untranslated Strings' ); ?>
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  </div>
	  <?php
	  }
	  ?>
	  </div>
	  </div>
	  </div>
	  <?php
	  $content = ob_get_clean();
	  return $content;

	  }

	  public function _RenderMemory()
	  {
	  global $_PROFILER;
	  $MemoryB = $_PROFILER->getMemory();
	  $MemoryK = round( $MemoryB / 1024, 2 );
	  $MemoryM = round( $MemoryK / 1024, 2 );
	  ob_start();
	  ?>
	  <div class="debugc_container">
	  <div class="debugc_title"  onclick="toggleContainer('debug_memory_body');">
	  <?php echo Text::_( 'Memory Usage' ); ?>
	  </div>
	  <div class="debugc_body" id="debug_memory_body">
	  <div class="debugc_body_in">
	  <div  class="debugc_item">
	  <div class="debugc_key">
	  <?php echo Text::_( 'Memory In Bytes' ); ?>
	  </div>
	  <div class="debugc_value">
	  <?php echo $MemoryB; ?> Byte
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  <div  class="debugc_item">
	  <div class="debugc_key">
	  <?php echo Text::_( 'Memory In KiloBytes' ); ?>
	  </div>
	  <div class="debugc_value">
	  <?php echo $MemoryK; ?> KB
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  <div  class="debugc_item">
	  <div class="debugc_key">
	  <?php echo Text::_( 'Memory In MegaBytes' ); ?>
	  </div>
	  <div class="debugc_value">
	  <?php echo $MemoryM; ?> MB
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  </div>
	  </div>
	  </div>
	  <?php
	  $content = ob_get_clean();
	  return $content;

	  }



	  public function _RenderElements()
	  {
	  ob_start();
	  ?>
	  <div class="debugc_container">
	  <div  class="debugc_title" onclick="toggleContainer('debug_elements_body');">
	  <?php echo Text::_( 'Elements Render' ); ?>
	  </div>
	  <div class="debugc_body" id="debug_elements_body">
	  <div class="debugc_body_in">
	  <?php
	  if ( class_exists( 'JComponentHelper' ) && !empty( JComponentHelper::$componentTimes ) )
	  {
	  $components = JComponentHelper::$componentTimes;
	  }
	  else
	  {
	  $components = array();
	  }
	  ?>
	  <div  class="debugc_subtitle" onclick="toggleContainer('debug_request_componentbody');">
	  <?php echo Text::sprintf( 'Components Rendered: %s', count( $components ) ); ?>
	  </div>
	  <div class="debugc_subbody" id="debug_request_componentbody">
	  <?php
	  $summary = 0;
	  foreach ( $components as $obj )
	  {
	  $sec = round( $obj->time, $this->_params->get( 'roundTimes', 5 ) );
	  $summary += $sec;
	  $color = '';
	  if ( $sec >= 0.1 )
	  {
	  $color = '_warn';
	  }
	  ?>
	  <div  class="debugc_item">
	  <div class="debugc_key<?php echo $color; ?>">
	  <?php echo $obj->component; ?>
	  </div>
	  <div class="debugc_value<?php echo $color; ?>">
	  <?php echo $sec; ?>&nbsp;
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  <?php
	  }
	  $color = '';
	  if ( $summary >= 0.1 )
	  {
	  $color = '_warn';
	  }
	  ?>
	  <div  class="debugc_item_summary">
	  <div class="debugc_key_summary">
	  <?php echo Text::_( 'Summary' ); ?>
	  </div>
	  <div class="debugc_value_summary<?php echo $color; ?>">
	  <?php echo $summary; ?>&nbsp;
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  </div>
	  <?php
	  if ( class_exists( 'JDocumentRendererModule' ) && !empty( JDocumentRendererModule::$moduleTimes ) )
	  {
	  $modules = JDocumentRendererModule::$moduleTimes;
	  }
	  else
	  {
	  $modules = array();
	  }
	  ?>
	  <div  class="debugc_subtitle" onclick="toggleContainer('debug_request_modulebody');">
	  <?php echo Text::sprintf( 'Modules Rendered: %s', count( $modules ) ); ?>
	  </div>
	  <div class="debugc_subbody" id="debug_request_modulebody">
	  <?php
	  $summary = 0;
	  foreach ( $modules as $obj )
	  {
	  $sec = round( $obj->time, $this->_params->get( 'roundTimes', 5 ) );
	  $summary += $sec;
	  $color = '';
	  if ( $sec >= 0.1 )
	  {
	  $color = '_warn';
	  }
	  ?>
	  <div  class="debugc_item">
	  <div class="debugc_key<?php echo $color; ?>">
	  <?php echo $obj->module; ?>
	  </div>
	  <div class="debugc_value<?php echo $color; ?>">
	  <?php echo $sec; ?>&nbsp;
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  <?php
	  }
	  $color = '';
	  if ( $summary >= 0.1 )
	  {
	  $color = '_warn';
	  }
	  ?>
	  <div  class="debugc_item_summary">
	  <div class="debugc_key_summary">
	  <?php echo Text::_( 'Summary' ); ?>
	  </div>
	  <div class="debugc_value_summary<?php echo $color; ?>">
	  <?php echo $summary; ?>&nbsp;
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  </div>
	  <?php
	  if ( class_exists( 'JPluginHelper' ) && !empty( JPluginHelper::$pluginsTimes ) )
	  {
	  $plugins = JPluginHelper::$pluginsTimes;
	  }
	  else
	  {
	  $plugins = array();
	  }
	  ?>
	  <div  class="debugc_subtitle" onclick="toggleContainer('debug_request_pluginsbody');">
	  <?php echo Text::sprintf( 'Plugins Rendered: %s', count( $plugins ) ); ?>
	  </div>
	  <div class="debugc_subbody" id="debug_request_pluginsbody">
	  <?php
	  $summary = 0;
	  $wsdebug = NULL;
	  foreach ( $plugins as $obj )
	  {
	  if ( strpos( $obj->plugin, 'wsdebug' ) !== false )
	  {
	  $wsdebug = $obj;
	  continue;
	  }
	  $sec = round( $obj->time, $this->_params->get( 'roundTimes', 5 ) );
	  $summary += $sec;
	  $color = '';
	  if ( $sec >= 0.1 )
	  {
	  $color = '_warn';
	  }
	  ?>
	  <div  class="debugc_item">
	  <div class="debugc_key<?php echo $color; ?>">
	  <?php echo $obj->plugin; ?>
	  </div>
	  <div class="debugc_value<?php echo $color; ?>">
	  <?php echo $sec; ?>&nbsp;
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  <?php
	  }
	  $color = '';
	  if ( $summary >= 0.1 )
	  {
	  $color = '_warn';
	  }
	  ?>
	  <div  class="debugc_item_summary">
	  <div class="debugc_key_summary">
	  <?php echo Text::_( 'Summary' ); ?>
	  </div>
	  <div class="debugc_value_summary<?php echo $color; ?>">
	  <?php echo $summary; ?>&nbsp;
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  <div  class="debugc_item_summary">
	  <div class="debugc_key_summary">
	  <?php echo Text::_( 'WS Debug Plugin' ); ?>
	  </div>
	  <div class="debugc_value_summary<?php echo $color; ?>">
	  <?php
	  if ( isset( $wsdebug->time ) )
	  {
	  echo $wsdebug->time;
	  }
	  ?>&nbsp;
	  </div>
	  <div class="debugc_cls"></div>
	  </div>
	  </div>
	  </div>
	  </div>
	  </div>
	  <?php
	  $content = ob_get_clean();
	  return $content;

	  }



	 */
	public function _RenderRequest()
	{
		ob_start();
		?>
		<div class="debugc_container">
			<div  class="debugc_title" onclick="toggleContainer('debug_request_body');">
				<?php echo Text::_( 'Request Variables' ); ?>
			</div>
			<div class="debugc_body" id="debug_request_body">
				<div class="debugc_body_in">
					<?php
					/*
					  //					$REQUEST = Request::get();
					  //					foreach ( $_REQUEST as $key => $v )
					  //					{
					  //						if ( strpos( $key, 'debug_cookie_' ) !== false )
					  //						{
					  //							continue;
					  //						}
					  //						$REQUEST[$key] = $v;
					  //					}
					  ?>
					  <div  class="debugc_subtitle" onclick="toggleContainer('debug_request_requestbody');">
					  <?php echo Text::sprintf( 'REQUEST Variables: %s', count( $REQUEST ) ); ?>
					  </div>
					  <div class="debugc_subbody" id="debug_request_requestbody">
					  <?php
					  foreach ( $REQUEST as $key => $v )
					  {
					  ?>
					  <div  class="debugc_item">
					  <div class="debugc_key">
					  <?php echo $key; ?>
					  </div>
					  <div class="debugc_value">
					  <?php echo htmlspecialchars( print_r( $v, 1 ), ENT_QUOTES ); ?>&nbsp;
					  </div>
					  <div class="debugc_cls"></div>
					  </div>
					  <?php
					  }
					 */
					?>
					<!--</div>-->
					<div  class="debugc_subtitle" onclick="toggleContainer('debug_request_getbody');">
						<?php echo Text::sprintf( 'GET Variables: %s', count( $_GET ) ); ?>
					</div>
					<div class="debugc_subbody" id="debug_request_getbody">
						<?php
						foreach ( $_GET as $key => $v )
						{
							?>
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $key; ?>
								</div>
								<div class="debugc_value">
									<?php echo htmlspecialchars( print_r( $v, 1 ), ENT_QUOTES ); ?>&nbsp;
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}
						?>
					</div>
					<div  class="debugc_subtitle" onclick="toggleContainer('debug_request_postbody');">
						<?php echo Text::sprintf( 'POST Variables: %s', count( $_POST ) ); ?>
					</div>
					<div class="debugc_subbody" id="debug_request_postbody">
						<?php
						foreach ( $_POST as $key => $v )
						{
							?>
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $key; ?>
								</div>
								<div class="debugc_value">
									<?php echo htmlspecialchars( print_r( $v, 1 ), ENT_QUOTES ); ?>&nbsp;
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}
						?>
					</div>
					<div  class="debugc_subtitle" onclick="toggleContainer('debug_request_filesbody');">
						<?php echo Text::sprintf( 'FILES Variables: %s', count( $_FILES ) ); ?>
					</div>
					<div class="debugc_subbody" id="debug_request_filesbody">
						<?php
						foreach ( $_FILES as $key => $v )
						{
							?>
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $key; ?>
								</div>
								<div class="debugc_value">
									<?php echo $v; ?>&nbsp;
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
					$COOKIE = array();
					foreach ( $_COOKIE as $key => $v )
					{
						if ( strpos( $key, 'debug_cookie_' ) !== false )
						{
							continue;
						}
						$COOKIE[$key] = $v;
					}
					?>
					<div  class="debugc_subtitle" onclick="toggleContainer('debug_request_cookiebody');">
						<?php echo Text::sprintf( 'COOKIE Variables: %s', count( $COOKIE ) ); ?>
					</div>
					<div class="debugc_subbody" id="debug_request_cookiebody">
						<?php
						foreach ( $COOKIE as $key => $v )
						{
							?>
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $key; ?>
								</div>
								<div class="debugc_value">
									<?php echo htmlspecialchars( print_r( $v, 1 ), ENT_QUOTES ); ?>&nbsp;
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}
						?>
					</div>
					<div  class="debugc_subtitle" onclick="toggleContainer('debug_request_serverbody');">
						<?php echo Text::sprintf( 'SERVER Variables: %s', count( $_SERVER ) ); ?>
					</div>
					<div class="debugc_subbody" id="debug_request_serverbody">
						<?php
						foreach ( $_SERVER as $key => $v )
						{
							?>
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $key; ?>
								</div>
								<div class="debugc_value">
									<pre><?php echo trim( htmlspecialchars( print_r( $v, true ), ENT_QUOTES ) ); ?>&nbsp;</pre>
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}
						?>
					</div>
					<?php
					$Session = isset( $_SESSION ) ? $_SESSION : [];
					?>
					<div  class="debugc_subtitle" onclick="toggleContainer('debug_request_sessionbody');">
						<?php echo Text::sprintf( 'SESSION Variables: %s', count( $Session ) ); ?>
					</div>
					<div class="debugc_subbody" id="debug_request_sessionbody">
						<?php
						foreach ( $Session as $key => $v )
						{
							if ( 'DEBUG_HISTORY' == $key )
							{
								continue;
							}
							?>
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $key; ?>
								</div>
								<div class="debugc_value">
									<pre>			
										<?php
										echo trim( htmlspecialchars( print_r( $v, true ), ENT_QUOTES ) );
										?>
									</pre>
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}
						?>
					</div>
					<div  class="debugc_subtitle" onclick="toggleContainer('debug_request_envbody');">
						<?php echo Text::sprintf( 'ENV Variables: %s', count( $_ENV ) ); ?>
					</div>
					<div class="debugc_subbody" id="debug_request_envbody">
						<?php
						foreach ( $_ENV as $key => $v )
						{
							?>
							<div  class="debugc_item">
								<div class="debugc_key">
									<?php echo $key; ?>
								</div>
								<div class="debugc_value">
									<?php echo htmlspecialchars( print_r( $v, 1 ), ENT_QUOTES ); ?>&nbsp;
								</div>
								<div class="debugc_cls"></div>
							</div>
							<?php
						}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
		$content = ob_get_clean();
		return $content;

	}

}
