<?php

class LanguagesView extends View
{
	protected $_option = 'languages';
	protected $_option_edit = 'languages';
	protected $_order = 'lib_title';
	protected $_dir = '0';
	protected $_space = 'languages.display';

	function display( $tmpl = null )
	{
		/* @var $model LanguagesModel */
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'publish':
				$data = Request::getVar( 'nid', array() );
				$link = '?option=' . $this->_option;
				if ( $model->ChangeLangState( $data, 1 ) )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;
			case 'unpublish':
				$data = Request::getVar( 'nid', array() );
				$link = '?option=' . $this->_option;
				if ( $model->ChangeLangState( $data, 0 ) )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;
			case 'default':
				$data = Request::getVar( 'nid', array() );
				$link = '?option=' . $this->_option;
				if ( $model->SetDefault( $data ) )
				{
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				Users::Redirect( $link );
				break;

			default:
				$data = $model->getList();
				break;
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
