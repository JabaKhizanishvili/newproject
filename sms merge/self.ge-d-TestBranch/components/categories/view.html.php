<?php

class CategoriesView extends View
{

    protected $_option = 'categories';
    protected $_option_edit = 'category';
    protected $_order = 'lib_title';
    protected $_dir = '0';
    protected $_space = 'categories.display';

    function display($tmpl = null)
    {
        /* @var $model CategoriesModel */
        $model = $this->getModel();
        $data = $model->getList();
        $this->assignRef('data', $data);
        parent::display($tmpl);
    }

}
