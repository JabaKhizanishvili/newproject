<?php class f_benefit_typesView extends View
	{
	protected $_option = 'f_benefit_types';
		protected $_option_edit = 'f_benefit_type';
			protected $_order = 'ordering';
			protected $_dir = '0';
			protected $_space = 'f_benefit_types.display';

				/* @var $model f_benefit_typesModel */
					function display( $tmpl = null )
					{
					$model = $this->getModel();
					$data = $model->getList();
					$this->assignRef( 'data', $data );
					parent::display( $tmpl );

					}

					}