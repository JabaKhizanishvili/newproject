<?php

class hlimitView extends View
{
	protected $_option = 'hlimits';
	protected $_option_edit = 'hlimit';

	function display( $tmpl = null )
	{
		/* @var $model hlimitModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', 'add' );
		$this->CheckTaskPermision( $task, $this->_option );
		$data = array();
		switch ( $task )
		{
			case 'save':
				$data = Request::getVar( 'params', array() );
				if ( $model->SaveData( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					$LimitsTable = new HolidayLimitsTable();
					$LimitsTable->SyncHolidayLimits();
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;

			case 'generation':
				if ( $model->Generate() )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
			case 'nextgeneration':
				if ( $model->Generate( true ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;
			case 'periodedit':
				$data = $model->GetPeriod();
				if ( !$data )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'data_incorrect' );
					Users::Redirect( $link );
				}
				$tmpl = 'period';
				break;
			case 'saveperiod':
				$data = Request::getVar( 'params', array() );
				if ( $model->SavePeriod( $data ) )
				{
					$link = '?option=' . $this->_option;
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
				if ( $model->Delete( $data, 'delete' ) )
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
			case 'trigger_holidays_cron':
				$link = '?option=' . $this->_option;
				$LimitsTable = new HolidayLimitsTable();
				$LimitsTable->SyncHolidayLimits();
				XError::setMessage( 'Limit Calculated Successfully' );

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
