<?php class f_regular_benefitsView extends View
	{
	protected $_option = 'f_regular_benefits';
		protected $_option_edit = 'f_regular_benefit';
			protected $_order = 'ordering';
			protected $_dir = '0';
			protected $_space = 'f_regular_benefits.display';

				/* @var $model f_regular_benefitsModel */
					function display( $tmpl = null )
					{
					$model = $this->getModel();
					$data = $model->getList();
					$this->assignRef( 'data', $data );
					parent::display( $tmpl );

					}

					}