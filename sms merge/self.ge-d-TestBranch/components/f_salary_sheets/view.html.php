<?php

class f_salary_sheetsView extends View
{
	protected $_option = 'f_salary_sheets';
	protected $_option_edit = 'f_salary_sheet';
	protected $_order = 't.id';
	protected $_dir = '0';
	protected $_space = 'f_salary_sheets.display';

	/* @var $model f_salary_sheetsModel */
	function display( $tmpl = null )
	{
		$model = $this->getModel();
		$task = Request::getVar( 'task', '' );

		if ( $task == 'view' )
		{
			$id = Request::getVar( 'id', '' );
			$data = $model->SalarySheet( $id );
			$tmpl = 'view';
		}
		elseif ( $task == 'back' )
		{
			$link = '?option=' . $this->_option;
			XError::setInfo( 'Action Canceled!' );
			Users::Redirect( $link );
		}
		else
		{
			$data = $model->getList();
		}

		$this->assignRef( 'data', $data );
		parent::display( $tmpl );

	}

	public function Tpart( $tag = '', $value = null, $class = '', $span_type = '', $span = '', $translate = 0, $config = 0, $config_name = '', $config_index = 'salary_sheet_' )
	{
		if ( $config == 1 && Helper::getConfig( $config_index . (empty( $config_name ) && is_string( $value ) ? $value : $config_name ), 0 ) == 0 )
		{
			return '';
		}

		switch ( $tag )
		{
			case 'tr':
				echo '<tr class="' . $class . '">' . $value . '</tr>';
				break;
			case 'rh': //Radio header
				$html = '<div class="list_header_x radio"><input type="checkbox" id="checknid" name="nids"><label for="checknid"></label></div>';
				return '<th ' . $span_type . 'span="' . $span . '" class="' . $class . '">' . $html . '</th>';
				break;
			case 'r': //Radio
				$html = '<div class="radio"><input type="checkbox" value="' . $value . '" id="checknid' . $value . '" name="nid[]" class="self-color self-border checknid"><label for="checknid' . $value . '"></label></div>';
				return '<td class="' . $class . '">' . $html . '</td>';
				break;
			case 'th':
				return '<th ' . $span_type . 'span="' . $span . '"><div  class="' . $class . '">' . ($translate == 1 ? Text::_( $value ) : $value ) . '</div></th>';
				break;
			case 'td':
				return '<td class="' . $class . '">' . ($translate == 1 ? Text::_( $value ) : $value ) . '</td>';
				break;
			case 'a':
				$ex = (array) explode( '|', $value );
				$id = C::_( 0, $ex, 0 );
				$label = C::_( 1, $ex, '' );
				$task = C::_( 2, $ex, '' );
				$option = C::_( 3, $ex, '' );
				$html = '<a href="?option=' . $option . '&amp;task=' . $task . '&amp;nid[]=' . $id . '">' . ($translate == 1 ? Text::_( $label ) : $label ) . '</a>';
				return '<td class="' . $class . ' ahover">' . $html . '</td>';
				break;
			default:
				return $value;
		}

	}

}
