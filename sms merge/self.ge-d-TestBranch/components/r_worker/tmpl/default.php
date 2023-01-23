<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$config = get_object_vars( $this );
?>
    <div class="page_title">
        <?php echo Helper::getPageTitle(); ?>
        <div class="toolbar">
            <?php
            Helper::getJSToolbar( 'Print', 'window.print', array() );
            //		Helper::getJSToolbar( 'Export To Exel', 'exportTableToExcel', array( '#exportable' ) );
            if ( Helper::CheckTaskPermision( 'admin', $this->_option ) ) {
                Helper::getToolbarExport('Export To Exel', $this->_option, 'export', 0, 0);
            }
            ?>
        </div>
        <div class="cls"></div>
    </div>

    <div class="page_content">
        <form action="" method="get" name="fform" id="fform" class="form-horizontal">
            <?php
            echo HTML::renderFilters( '', dirname( __FILE__ ) . DS . 'default.xml', $config );
            echo ( count($_GET)>4)?HTML::renderGrid( $this->data->items, dirname( __FILE__ ) . DS . 'default.xml', $config ):'';
            ?>

            <input type="hidden" value="<?php echo Request::getVar( 'option', DEFAULT_COMPONENT ); ?>" name="option" />
            <input type="hidden" value="<?php echo $this->data->order; ?>" name="order" id="order" />
            <input type="hidden" value="<?php echo $this->data->dir; ?>" name="dir"  id="dir"/>
            <input type="hidden" value="<?php echo $this->data->start; ?>" name="start"  id="start"/>
            <input type="hidden" value="" name="task" />
        </form>
    </div>
<?php
$this->setHelp();
