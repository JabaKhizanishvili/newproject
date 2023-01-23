<?php
defined( 'PATH_BASE' ) or die( 'Restricted Access' );
/**
 * System Status
 * 
 * 0 - Production
 * 1 - Development
 */
define( 'SYSTEM_STATUS', 1 );

date_default_timezone_set( 'Asia/Tbilisi' );
defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
defined( 'CONFIG_DIR' ) or define( 'CONFIG_DIR', dirname( __FILE__ ) . DS . 'config' );
defined( 'BASE_PATH' ) or define( 'BASE_PATH', dirname( __FILE__ ) );
defined( 'X_PATH_BASE' ) or define( 'X_PATH_BASE', BASE_PATH );
defined( 'X_PATH_BUFFER' ) or define( 'X_PATH_BUFFER', X_PATH_BASE . DS . 'buffer' );

defined( 'X_PATH_TRANSLATE' ) or define( 'X_PATH_TRANSLATE', X_PATH_BUFFER . DS . 'Translates' );
defined( 'X_TRANSLATE_API' ) or define( 'X_TRANSLATE_API', 'https://translate.self.ge/api/Translation/Translate' );


define( 'X_PATH_LIBRARIES', PATH_BASE . DS . 'libraries' );
define( 'DEFAULT_COMPONENT', 'profile' );
define( 'X_PATH_LOGS', BASE_PATH . DS . 'logs' );
/*
 * Language Option
 */
define( 'SYSTEM_LANG', 'ka-ge' );
define( 'PAGE_ITEMS_LIMIT', 50 );
define( 'SESSION_PERIOD', 1800 ); //In minutes
define( 'EXTENDED_SESSION_PERIOD', 43200 ); //In minutes
define( 'COOKIE_PATH', '/' );
define( 'DEBUG_STRING', 'W442J4WRSCmtCMxDSrEC687gryTGV' );
define( 'DEBUG_IPS', '93.177.132.247, 85.114.225.85' );
define( 'LIMITED_DEBUG_STRING', 'Limited_hj3432234iji2i324iu23' );

define( 'X_LANGUAGES', array( 'ka' => 'ka', 'en' => 'en', 'ru' => 'ru' ) );
defined( 'X_PATH_TRANSLATE_USER' ) or define( 'X_PATH_TRANSLATE_USER', X_PATH_BUFFER . DS . 'UserTranslates' );

define( 'X_REDIS_HOST', '10.59.86.5' );
define( 'X_REDIS_PORT', '6379' );
define( 'X_REDIS_PASSWORD', 'aog4Airi3jech9mashiala<Yee=nga' );

