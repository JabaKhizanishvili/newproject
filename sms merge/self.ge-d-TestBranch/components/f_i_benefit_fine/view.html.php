<?php

class f_i_benefit_fineView extends View
{
	protected $_option = 'f_i_benefits_fines';
	protected $_option_edit = 'f_i_benefit_fine';

	function display( $tmpl = null )
	{
		/* @var $model f_i_benefit_fineModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$ORG = (int) trim( Request::getState( 'f_i_benefits_fines.display', 'org', '' ) );
		$data = array();
		if ( empty( $ORG ) )
		{
			$link = '?option=' . $this->_option;
			XError::setError( 'PLEASE, CHOOSE ORG!' );
			Users::Redirect( $link );
		}
		switch ( $task )
		{
			case 'save':
				$data = Request::getVar( 'params', array() );
				if ( $model->SaveData( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;

			case 'delete':
				$data = Request::getVar( 'nid', array() );
				$link = '?option=' . $this->_option;
				if ( $model->Delete( $data ) )
				{
					XError::setMessage( 'Data Deleted!' );
					Users::Redirect( $link );
				}
				XError::setError( 'Data_Not_Deleted!' );
				Users::Redirect( $link );
				break;

			case 'nextstep':
				$data = Request::getVar( 'params', array() );
				if ( $data['WORKER'] <= 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE WORKER!' );
					break;
				}
				if ( $data['BENEFIT_ID'] <= 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE BENEFIT!' );
					break;
				}
				$tmpl = 'periodcost';
				break;

			case 'prev':
				$data = Request::getVar( 'params', array() );
				break;

			case 'final_prev':
				$data = Request::getVar( 'params', array() );
				$tmpl = 'periodcost';
				break;

			case 'edit':
				$data = $model->getItem();
				$data->OPTON_TASK = 'edit';
				$id = Request::getVar( 'nid', array() )[0];
				$data->ORG = $ORG;
				$data->ID = $id;
				$tmpl = 'periodcost';
				break;

			case 'copydata':
				$data = $model->getItem();
				$data->OPTON_TASK = 'edit';
				$data->ORG = $ORG;
				$data->ID = '';
				$tmpl = 'periodcost';
				break;

			case 'nextstep2':
				$data = Request::getVar( 'params', array() );
				if ( $data['PERIOD_ID'] <= 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE PERIODS!' );
					$tmpl = 'periodcost';
					break;
				}
				if ( $data['COST'] <= 0 )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE COST!' );
					$tmpl = 'periodcost';
					break;
				}
				$data['CALC'] = $model->Calculate( $data, true );
				$tmpl = 'final';
				break;

			case 'apply':
				$data = Request::getVar( 'params', array() );
				$ID = $model->SaveData( $data );
				if ( $ID )
				{
					$link = '?option=' . $this->_option_edit;
					XError::setMessage( 'Data Saved!' );
					$tmpl = 'final';
					break;
				}
				XError::setError( 'data_incorrect' );
				break;

			case 'confirm':
				$data = Request::getVar( 'params', array() );
				if ( $model->SendSMS( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'All SMS Sent!' );
					Users::Redirect( $link );
					break;
				}
				XError::setError( 'System Error' );
				break;

			case 'cancel':
				$link = '?option=' . $this->_option;
				XError::setMessage( 'action canceled!' );
				Users::Redirect( $link );
				break;

			default:
				$data = Request::getVar( 'params', array() );
				$data['ORG'] = $ORG;
				break;
		}
		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

}
