<?php

class App_typeView extends View
{
	protected $_option = 'app_types';
	protected $_option_edit = 'app_type';

	function display( $tmpl = null )
	{
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
		switch ( $task )
		{
			case 'save':
				$data = Request::getVar( 'params', array() );
				if($data['ID'] == '0')
				{
					$data['ID'] = '-0';
				}
				
				if ( $model->SaveData( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
            case 'apply':
                $data = Request::getVar( 'params', array() );
                $ID = $model->SaveData( $data );
                if ( $ID )
                {
                    $link = '?option=' . $this->_option_edit . '&task=edit&nid[]=' . $ID;
                    XError::setMessage( 'Data Saved!' );
                    Users::Redirect( $link );
                }
                XError::setError( 'data_incorrect' );
                break;

			case 'delete':
				$link = '?option=' . $this->_option;
				$data = Request::getVar( 'nid', array() );
				if ( empty( $data ) )
				{
					XError::setError( 'items_not_selected' );
					Users::Redirect( $link );
				}
				foreach ($data as $key=>$row)
				{
					if($row == 0)
					{
						$data[$key] = '-0';
					}
				}
				if ( $model->D_Delete( $data ) )
				{
					XError::setMessage( 'Data Deleted!' );
					Users::Redirect( $link );
				}
				XError::setError( 'Data_Not_Deleted!' );
				Users::Redirect( $link );
				break;

			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setInfo( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			case 'changestate':
				$model->ChangeState();
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Status Changed!' );
				Users::Redirect( $link );
				break;

			default:
				$data = $model->getItem();
				break;
		}
		if ( !is_object( $data ) )
		{
			$data = (object) $data;
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
