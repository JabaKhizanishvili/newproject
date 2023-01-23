<?php

class f_salary_sheetView extends View
{
	protected $_option = 'f_salary_sheets';
	protected $_option_edit = 'f_salary_sheet';

	function display( $tmpl = null )
	{
		/* @var $model f_salary_sheetModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
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
				elseif ( C::_( 'ID', $data ) > 0 )
				{
					$data = $model->getItem( C::_( 'ID', $data ) );
				}
				else
				{
					$data['DATA_TYPE'] = json_encode( (object) $data['DATA_TYPE'] );
				}

				XError::setError( 'data_incorrect' );
				break;

			case 'save_each':
				$data = Request::getVar( 'params', array() );
				$sheet_id = (int) C::_( 'SHEET_ID', $data );
				$worker = (int) C::_( 'WORKER', $data );
				if ( $model->Edit( $data ) )
				{
					$link = '?option=' . $this->_option . '&task=view&id=' . $sheet_id;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				else
				{
					$data = $model->load_each( $worker, $sheet_id );
				}

				XError::setError( 'data_incorrect' );
				$tmpl = 'each';
				break;

			case 'delete_each':
				$nid = Request::getVar( 'nid', array() );
				$sheet_id = Request::getVar( 'sheet_id', 0 );
				$ex = (array) explode( '|', C::_( 0, $nid, 0 ) );
				if ( empty( $sheet_id ) )
				{
					$sheet_id = C::_( 1, $ex, 0 );
				}

				if ( !$model->Delete_each( $nid, $sheet_id ) )
				{
					XError::setError( 'data_incorrect' );
				}
				else
				{
					XError::setMessage( 'Data Deleted!' );
				}

				$link = '?option=' . $this->_option . '&task=view&id=' . $sheet_id;
				Users::Redirect( $link );
				break;

			case 'edit_each':
				$nid = Request::getVar( 'nid', array() );
				$sheet_id = Request::getVar( 'sheet_id', 0 );
				$ex = (array) explode( '|', C::_( 0, $nid, 0 ) );
				$worker = C::_( 0, $ex, 0 );
				if ( empty( $sheet_id ) )
				{
					$sheet_id = C::_( 1, $ex, 0 );
				}

				$data = $model->load_each( $worker, $sheet_id );
				$tmpl = 'each';
				break;

			case 'updateperiods':
				$link = '?option=' . $this->_option;
				if ( DailySalary::UpdatePeriods() )
				{
					XError::setMessage( 'Data Saved!' );
				}
				Users::Redirect( $link );
				break;

			case 'record_salary':
				$link = '?option=' . $this->_option;
				if ( DailySalary::Record() )
				{
					XError::setMessage( 'Data Saved!' );
				}
				Users::Redirect( $link );
				break;

			case 'record_benefits':
				if ( Benefits::Record() )
				{
					XError::setMessage( 'Data Saved!' );
				}
				$link = '?option=' . $this->_option;
				Users::Redirect( $link );
				break;

			case 'generateperiods':
				$link = '?option=' . $this->_option;
				if ( DailySalary::GeneratePeriods() )
				{
					XError::setMessage( 'Data Saved!' );
				}
				Users::Redirect( $link );
				break;

			case 'copydata':
				$data = $model->getItem();
				if ( !$data )
				{
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
				}
				$data->ID = '';
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
				if ( $model->D_elete( $data ) )
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

			case 'cancel_each':
				$data = Request::getVar( 'params', array() );
				$sheet_id = (int) C::_( 'SHEET_ID', $data );
				$link = '?option=' . $this->_option . '&task=view&id=' . $sheet_id;
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

	public function html( $type = '', $name = '', $value = '', $params = '' )
	{
		switch ( $type )
		{
			case 'spacer':
				return '<div class="form-group col-md-12 col-lg-4" id="form-item-' . $name . '">
					<div class="items_spacer form-group text-right">' . $value . '</div>
			   	  </div>';
				break;
			case 'text':
				return '<div class="form-group col-md-12 col-lg-4" id="form-item-' . $name . $value . '">
					<label class="control-label" for="paramid' . $name . $value . '">
					<label id="paramsWORKER_SHARE-lbl" for="params' . $name . '">' . Text::_( $name ) . '</label> </label>
					<input type="text" name="params' . $params . '" id="params' . $name . $value . '" value="' . $value . '" class="form-control">
				  </div>';
				break;
			case 'hidden':
				return '<input type="hidden" name="params' . $params . '" id="params' . $name . '" value="' . $value . '">';
				break;
		}

	}

}
