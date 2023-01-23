<?php

/**
 * Description of MainController
 *
 * @author sergo.beruashvili
 */
class Controller
{
	public function execute()
	{
		$option = 'login';
		if ( Users::isLogged() )
		{
			if ( !Users::CanAccess() )
			{
				Users::Redirect( '?ref=CanAccess', 'you have not acccess', 'error' );
			}
			$option = Request::getVar( 'option', DEFAULT_COMPONENT );
		}

		if ( $this->LoadComponent( $option ) )
		{
			$this->CallComponent( $option );
		}

	}

	private function LoadComponent( $Component )
	{
		$Override = X_PATH_BASE . DS . 'override' . DS . X_DOMAIN . DS . $Component . DS . 'view.html.php';


		if ( is_file( $Override ) )
		{
			require_once $Override;
			return true;
		}
		else if ( is_file( X_PATH_TEMPLATE . DS . 'html' . DS . $Component . DS . 'view.html.php' ) )
		{
			require_once X_PATH_TEMPLATE . DS . 'html' . DS . $Component . DS . 'view.html.php';
			return true;
		}
		else if ( is_file( dirname( __FILE__ ) . DS . $Component . DS . 'view.html.php' ) )
		{
//            echo '<pre>';
//            var_dump($Override );
//            var_dump(X_PATH_TEMPLATE . DS . 'html' . DS . $Component . DS . 'view.html.php');
//            var_dump(dirname( __FILE__ ) . DS . $Component . DS . 'view.html.php' );
//            echo '</pre>';
//            exit;

			require_once dirname( __FILE__ ) . DS . $Component . DS . 'view.html.php';
			return true;
		}
		else
		{

			require_once dirname( __FILE__ ) . DS . 'default' . DS . 'view.html.php';
			return true;
		}

	}

	private function CallComponent( $option )
	{
		$viewName = $option . 'View';
		$method = 'display';
		$Layout = Request::getCmd( 'layout', null );
		if ( class_exists( $viewName ) )
		{
			$view = new $viewName( $option );
			$view->$method( $Layout );
		}
		else
		{
			$viewName = 'DefaultView';
			if ( class_exists( $viewName ) )
			{
				$view = new $viewName( 'default' );
				$view->$method( $Layout );
			}
		}

	}

}
