<?php
defined( 'PATH_BASE' ) or die( 'Restricted access' );
$ParamsData = Request::getVar( 'params', array() );
$step = C::_( 'step', $this->data, '' );
$sub_type = C::_( 'CHANGE_SUB_TYPE', $this->data );
if ( !empty( $step ) )
{
	unset( $this->data->step );
}

if ( $step == 'step1' )
{
	$Workers = (array) Request::getVar( 'nid', C::_( 'WORKERS', $ParamsData, array() ) );
	$this->data->WORKERS = implode( '|', $Workers );
	if ( count( $Workers ) > 1 )
	{
		$this->data->BENEFIT_TYPES = '';
	}
}

$params = HTML::convertParams( $this->data );
$page_title = '';
switch ( $step . $sub_type )
{
	case 'step21':
		$page_title = Text::_( 'add_benefit' );
		break;
	case 'step22':
		$page_title = Text::_( 'change_benefit' );
		break;
	case 'step23':
		$page_title = Text::_( 'remove_benefit' );
		break;
	default:
		$page_title = Text::_( 'manage_benefits' );
}
?>
<div class="page_title">
	<?php
	echo $page_title;
	?>
	<div class="toolbar">
		<?php
		if ( $step == 'step1' )
		{
			Helper::getToolbar( 'Next', $this->_option_edit, 'benefits_next' );
			Helper::getToolbar( 'Cancel', $this->_option_edit, 'cancel' );
		}
		else
		{
			Helper::getToolbar( 'Save', $this->_option_edit, 'save_benefits', 0, 1 );
			Helper::getToolbar( 'Back', $this->_option_edit, 'benefits_back' );
		}
		?>
	</div>
	<div class="cls"></div>
</div>
<div class="page_content">
	<form action="?option=<?php echo $this->_option_edit; ?>" method="post" class="form-horizontal" name="fform" id="fform">
		<div class="row page_content">
			<?php
			if ( $step == 'step1' )
			{
				?>
				<div class="col-md-6">
				</div>
				<div class="col-md-6">
					<?php
				}
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_benefits.xml', 'params', 'hidden' );
				echo HTML::renderParams( $params, dirname( __FILE__ ) . DS . 'default_benefits.xml', 'params', $step );
				if ( $step == 'step1' )
				{
					?>
				</div>
			<?php } ?>
		</div>
		<input type="hidden" value="save" name="task" /> 
	</form>
</div>
<?php
$this->setHelp();

