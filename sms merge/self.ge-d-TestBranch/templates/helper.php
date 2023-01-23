<?php

class TemplateHelper
{
	private static $update = null;

	public static function getCssLink( $template = null )
	{
		self::Update();
		$Domain = basename( PATH_LOGS );
		require X_PATH_BUFFER . DS . 'css' . DS . $Domain . '-css-config.php';
		if ( isset( $CSSFile ) )
		{
			return 'buffer/css/' . $CSSFile;
		}
		else
		{
			return 'buffer/css/style.css';
		}

	}

	public static function getJSLink( $template = NULL )
	{
		self::Update();

		require X_PATH_BUFFER . DS . 'js' . DS . 'js-config.php';
		if ( isset( $JSFile ) )
		{
			return 'buffer/js/' . $JSFile;
		}
		else
		{
			return 'buffer/js/script.js';
		}

	}

	private static function Update()
	{
		if ( SYSTEM_STATUS == 1 )
		{
			if ( is_null( self::$update ) )
			{
				defined( 'UPDATE_CSS_JS_DEBUG' ) or define( 'UPDATE_CSS_JS_DEBUG', 0 );
				require dirname( __FILE__ ) . DS . 'update.php';
				self::$update = true;
			}
		}

	}

}
