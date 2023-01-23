<?php

class Person_orgView extends View
{
	protected $_option = 'person_orgs';
	protected $_option_edit = 'person_org';
	protected $_option_persons = 'persons';

	function display( $tmpl = null )
	{
		/* @var $model WorkerModel */
		$params = (object) get_object_vars( $this );
		$model = $this->getModel( $params );
		$task = Request::getVar( 'task', '' );
		$data = array();
		$confirm = array();
		$ORGdata = array();
		switch ( $task )
		{
//	Edit  ____________________________________________
			case 'edit':
				$data = $model->getItems();
				if ( empty( $data ) )
				{
					$link = '?option=' . $this->_option;
					Users::Redirect( $link );
				}
				$tmpl = 'changing';
				break;
//	Display  ____________________________________________
			case 'display':
				$DATA = [];
				$data = Request::getVar( 'params', array() );
				if ( isset( $data['ATTRIBUTES'] ) && is_array( $data['ATTRIBUTES'] ) )
				{
					$data['ATTRIBUTES'] = implode( ',', array_filter( $data['ATTRIBUTES'], function ( $i )
									{
										return !empty( $i );
									} ) );
				}
				$before = C::_( 'CONFIRMATION', $data );
				switch ( $before )
				{
					case 'save_assignment':
						$collect = $model->collect_assignment( $data );
						if ( empty( $collect ) )
						{
							continue;
						}

						$data2 = [];
						if ( $collect )
						{
							$data2 = $collect[explode( ',', C::_( 'PERSON', $data ) )[0]];
						}
						$cut = [
								'PERSON',
								'ACCOUNTING_OFFICES'
						];
						$DATA = Xhelp::bind( $data, $data2, $cut );
						$data['display_origin'] = $DATA;
						$data['display_changed'] = [];
						break;
					case 'save_changing':
						$w = Xhelp::getWorkerData( C::_( 'WORKERS', $data ) );
						$data['PERSON'] = !empty( $w[0] ) ? C::_( 'PERSON', $w[0] ) : '';
						if ( $DATA = $model->collect_changes( $data ) )
						{
							$data['display_origin'] = $w;
							$data['display_changed'] = $DATA;
						}
						else
						{
							$tmpl = 'changing';
						}
						break;
					case 'save_release':
						$ORG = (int) trim( Request::getState( $this->_option . '.display', 'org', '' ) );
						$data['ORG'] = $ORG;
						$collect = $model->collect_release( $data );
						$data = Xhelp::ConfirmationData( $data, $collect, 'PERSON' );
						$tmpl = 'confirm';
						break;
				}
				if ( $DATA )
				{
					$tmpl = 'display';
				}
				else
				{
					if ( !C::_( 'TASK', $data, null ) )
					{
						$data['ACCOUNTING_OFFICES'] = implode( ',', (array) $data['ACCOUNTING_OFFICES'] );
						XError::setError( 'data_incorrect' );
					}
				}
				break;

//	Assignment  ____________________________________________
			case 'assignment':
				$nid = Request::getVar( 'nid', array() );
				if ( count( $nid ) )
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'you need to refer persons for assignment!' );
					Users::Redirect( $link );
				}
				$data = $model->getItem();
				break;

			case 'save_assignment':
				/** @var Person_orgModel $model */
				$data = Request::getVar( 'params', array() );
				if ( $model->save_assignment( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				break;

//	Change  _______________________________________________
			case 'changing':
				$ORG = (int) trim( Request::getState( $this->_option . '.display', 'org', '' ) );
				if ( !empty( $ORG ) )
				{
					$data = $model->getItems();
					if ( !$data )
					{
						$link = '?option=' . $this->_option;
						Users::Redirect( $link );
					}
					$tmpl = 'changing';
				}
				else
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE ORG!' );
					Users::Redirect( $link );
				}
				break;

			case 'save_changing':
				$data = Request::getVar( 'params', array() );
				if ( $model->save_changing( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				$tmpl = 'changing';
				break;

//	Release  _______________________________________________
			case 'release':
				$ORG = (int) trim( Request::getState( $this->_option . '.display', 'org', '' ) );
				if ( !empty( $ORG ) )
				{
					if ( empty( $model->getItems() ) )
					{
						$link = '?option=' . $this->_option;
						Users::Redirect( $link );
					}
					$data = Request::getVar( 'nid', array() );
					$tmpl = 'release';
				}
				else
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE ORG!' );
					Users::Redirect( $link );
				}
				break;

			case 'save_release':
				$data = Request::getVar( 'params', array() );
				if ( $model->save_release( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				$tmpl = 'release';
				break;

//	Schedule Change  _______________________________________________
			case 'schedulechanging':
				$ORG = (int) trim( Request::getState( $this->_option . '.display', 'org', '' ) );
				if ( !empty( $ORG ) )
				{
					$ids = (array) C::_( 0, Request::getVar( 'nid', array() ) );

                    if ($model->existPendingScheduleChanging($ids)) {
                        $link = '?option=' . $this->_option;
                        XError::setError('For selected workers exist pending schedule changing!');
                        Users::Redirect($link);
                    }

					$data = $model->getItems();
					if ( count( $ids ) > 1 )
					{
						$data = (object) [];
					}

					if ( empty( $data ) )
					{
						$link = '?option=' . $this->_option;
						Users::Redirect( $link );
					}

					$data->WORKERS = is_array( $ids ) ? implode( ',', $ids ) : $ids;
					$data->ORG = $ORG;
					$data->STAFF_SCHEDULE = '';
					$tmpl = 'schedulechanging';
				}
				else
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE ORG!' );
					Users::Redirect( $link );
				}
				break;

			case 'save_schedulechanging':
				$data = Request::getVar( 'params', array() );
				if ( is_array( $data['ATTRIBUTES'] ) )
				{
					$data['ATTRIBUTES'] = implode( ',', array_filter( $data['ATTRIBUTES'], function ( $i )
									{
										return !empty( $i );
									} ) );
				}
				if ( $model->save_schedulechanging( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				$data['ACCOUNTING_OFFICES'] = implode( ',', (array) $data['ACCOUNTING_OFFICES'] );
				XError::setError( 'data_incorrect' );
				$tmpl = 'schedulechanging';
				break;
//	Rollback  _______________________________________________
			case 'rollback':
				$ORG = (int) trim( Request::getState( $this->_option . '.display', 'org', '' ) );
				$link = '?option=' . $this->_option;
				if ( !empty( $ORG ) )
				{
					$Items = $model->getItem();
					$active = C::_( 'ACTIVE', $Items );
					if ( in_array( $active, [ -6, -3, 0 ] ) )
					{
						XError::setError( 'you cannot change data of this type worker!' );
						Users::Redirect( $link );
					}
					if ( empty( $Items ) )
					{
						Users::Redirect( $link );
					}
					$data['ID'] = Request::getVar( 'nid', array() )[0];
					$data['ORG'] = $ORG;

					$current = $model->getCurrentItem();
					$sch_id = C::_( 'CHANGE_SCHEDULE_WORKER', $current, false );
					$not_id = false;
					if ( empty( $current ) && $active == 1 )
					{
						$current = $model->getPreviousItem();
						$not_id = C::_( 'ID', $current );
					}

					$data['CURRENT'] = $current;
					$data['PREVIOUS'] = $model->getPreviousItem( $sch_id, ($sch_id ? true : false ), $not_id );

					$tmpl = 'rollback';
				}
				else
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE ORG!' );
					Users::Redirect( $link );
				}
				break;

			case 'save_rollback':
				$data = Request::getVar( 'params', array() );
				if ( $model->save_rollback( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				$tmpl = 'rollback';
				break;
//	Benefits _______________________________________________
			case 'benefits':
				$ORG = (int) trim( Request::getState( $this->_option . '.display', 'org', '' ) );
				if ( !empty( $ORG ) )
				{
					$data = $model->getItems();
					if ( empty( $data ) )
					{
						$link = '?option=' . $this->_option;
						Users::Redirect( $link );
					}
					$tmpl = 'benefits';
					$data->step = 'step1';
				}
				else
				{
					$link = '?option=' . $this->_option;
					XError::setError( 'PLEASE, CHOOSE ORG!' );
					Users::Redirect( $link );
				}
				break;

			case 'benefits_back':
				$data = Request::getVar( 'params', array() );
				$tmpl = 'benefits';
				$data['step'] = 'step1';
				break;

			case 'benefits_next':
				$data = Request::getVar( 'params', array() );
				$action = C::_( 'CHANGE_SUB_TYPE', $data );
				if ( $action != 1 )
				{
					$data['WORKERS'] = Benefits::check_workers( C::_( 'WORKERS', $data ), $action );
					$B = Benefits::load_benefits( C::_( 'WORKERS', $data ) );
					if ( !empty( $B ) )
					{
						$data['BENEFIT_TYPES'] = json_encode( $B );
					}
					else
					{
						$data['WORKERS'] = '';
					}
				}

				if ( empty( C::_( 'WORKERS', $data ) ) || C::_( 'CHANGE_SUB_TYPE', $data ) < 0 )
				{
					XError::setError( 'data_incorrect' );
					$tmpl = 'benefits';
					$data['step'] = 'step1';
					break;
				}

				$tmpl = 'benefits';
				$data['step'] = 'step2';
				break;

			case 'save_benefits':
				$data = Request::getVar( 'params', array() );
				$action = C::_( 'CHANGE_SUB_TYPE', $data );
				if ( $model->save_benefits( $data ) )
				{
					$link = '?option=' . $this->_option;
					XError::setMessage( 'Data Saved!' );
					Users::Redirect( $link );
				}
				XError::setError( 'data_incorrect' );
				$data['BENEFIT_TYPES']['ERROR'] = 1;
				$B = $action == 3 ? Benefits::load_benefits( C::_( 'WORKERS', $data ) ) : $data['BENEFIT_TYPES'];
				$data['BENEFIT_TYPES'] = json_encode( $B );
				$tmpl = 'benefits';
				$data['step'] = 'step2';
				break;

//Defaults  ____________________________
			case 'cancel':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option;
				XError::setMessage( 'Action Canceled!' );
				Users::Redirect( $link );
				break;
			case 'miss':
				$data = Request::getVar( 'params', array() );
				$link = '?option=' . $this->_option_persons;
				Users::Redirect( $link );
				break;
			default:
				$data = $model->getItem();
//				$ORGdata = $model->getOrgData();
				break;
		}
		if ( !is_object( $data ) )
		{
			$data = (object) $data;
		}

		$this->assignRef( 'data', $data );
//		$this->assignRef( 'orgdata', $ORGdata );
		parent::display( $tmpl );

	}

	public function getValue( $Key, $SalaryData )
	{
		$Value = trim( C::_( $Key, $SalaryData ) );
		if ( mb_strtolower( $Value ) == 'null' )
		{
			$Value = null;
		}
		return $Value;

	}

}
