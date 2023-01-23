<?php

class Pagination
{
	public $html = '';
	public $total_records = 0;
	public $current_page = 1;
	public $page_first = '';
	public $page_last = '';
	public $page_fwd = '';
	public $page_count = '';
	public $page_back = '';
	public $URL = NULL;
	//--------------------------------------------------
	//This will be used at links like example.php?start=5
	//Default: page # Type: string
	//--------------------------------------------------
	protected $page_url_var = 'start';
	//---------------------------------------
	//How many links will be showed?
	//Like 4-5-6-7-8-9-10-11-12
	//Except first, last, back, forward links
	//Default: 9# Type: integer
	//---------------------------------------
	protected $align_links_count = '9';
	//------------------------------------------------------------------
	//If you give this at assign function this value will be overwritten
	//This value will have been used if you use assign function like
	//$paging->assign ( 'example.php?' , 100 );
	//Default: 15 # Type: integer
	//------------------------------------------------------------------
	protected $records_per_page = 15;
	//-----------------------------------------
	//Do we want to use back and forward links?
	//Back: � ---- Forward: �
	//-----------------------------------------
	protected $use_back_forward = true;
	protected $back_link_icon = '&laquo;'; // &laquo; = �
	protected $fwd_link_icon = '&raquo;'; // &raquo; = �
	#######
	//--------------------------------------------
	//Do we want to use first and last page links?
	//First: 1... ---- Last: ...[Last_Page]
	//--------------------------------------------
	protected $use_first_last = true;

	function __construct( $total_records, $start = 0, $records_per_page = 50 )
	{
		$this->total_records = $total_records;
		if ( $records_per_page != false )
		{
			$this->records_per_page = $records_per_page;
		}
		/* @var $this->URL URI */
		$this->URL = URI::getInstance();
		$this->URL->delVar( 'image_x' );
		$this->URL->delVar( 'image_y' );
		$this->URL->delVar( 'start' );
		//Which page at we are?
		if ( $start == 0 )
		{
			$this->current_page = 1;
		}
		else
		{
			$this->current_page = intval( $start / $records_per_page ) + 1;
		}

	}

	public function fetch()
	{
		$this->generate_pages();
		$this->generate_html();
		return $this->html;

	}

	public function generate_pages()
	{
		$page_count = $this->total_records / $this->records_per_page;
		if ( $page_count != intval( $page_count ) )
		{
			$page_count = intval( $page_count ) + 1;
		}
		$max_link = $page_count > $this->align_links_count ? $this->align_links_count : $page_count;
		$this->page_count = $page_count;
		//Make start and end page equal first
		$start_page = $this->current_page;
		$end_page = $this->current_page;
		//Now start start_page decreasing
		//and end page increasing
		while ( $max_link > '0' )
		{
			$looped = false;
			if ( $end_page < $page_count )
			{
				$end_page++;
				$max_link--;
				$looped = true;
			}
			if ( $start_page > '1' && $max_link != '0' )
			{
				$start_page--;
				$max_link--;
				$looped = true;
			}
			if ( $looped == false )
			{
				break;
			}
		}
		//---------------------------------
		//Let's make the page number links
		//From start page to end page
		//---------------------------------
		$i = $start_page;
		while ( $i <= $end_page )
		{
			if ( $i != $this->current_page )
			{
				$pagearray[] = $this->generate_link( $i, $i );
			}
			else
			{
				$pagearray[] = $this->generate_active( $i );
			}
			$i++;
		}

		#######
		//Do we want to use first and last page links?
		if ( $this->use_first_last == true )
		{
			//Just make the first page url if we need
			if ( $start_page > 1 )
			{
				$threedot_first = ( $start_page != '2' ) ? '...' : false;
				if ( $threedot_first )
				{
					$this->page_first = $this->generate_link( '1', '1' ) . $this->generate_list( $threedot_first, 1, $start_page );
				}
				else
				{
					$this->page_first = $this->generate_link( '1', '1' );
				}
			}
			//Just make the last page url if we need
			if ( $end_page < $page_count )
			{
				$threedot_last = ( $end_page != $page_count - 1 ) ? '...' : false;
				if ( $threedot_last )
				{
					$this->page_last = $this->generate_list( $threedot_last, $end_page, $this->page_count ) . $this->generate_link( $page_count, $page_count );
				}
				else
				{
					$this->page_last = $this->generate_link( $page_count, $page_count );
				}
			}
		}
		//Do we want to use back and forward links?
		if ( $this->use_back_forward == true )
		{
			//Let's make "back" � link
			//if page is not the first
			if ( $this->current_page != '1' )
			{
				$this->page_back = $this->generate_link( $this->back_link_icon, $this->current_page - 1, '_pna' ) . ' ';
			}
			else
			{
				$this->page_back = $this->generate_notlink( $this->back_link_icon, '_pn' );
			}
			//Let's make "forward" � link
			//if page is not the last
			if ( $this->current_page != $page_count )
			{
				$this->page_fwd = $this->generate_link( $this->fwd_link_icon, $this->current_page + 1, '_pna' );
			}
			else
			{
				$this->page_fwd = $this->generate_notlink( $this->fwd_link_icon, '_pn' );
			}
		}
		//Let's make them global class variable
		$this->page_count = $page_count;
		$this->pagearray = $pagearray;

	}

	public function generate_html()
	{

		$render = '<div class="pagination">';
		if ( $this->total_records )
		{
			if ( $this->page_count > 1 )
			{
				$render .= '<div class="pagination_items">';
				$html = implode( ' ', $this->pagearray );
				$render .= $this->page_back . $this->page_first . $html . $this->page_last . $this->page_fwd;
				$render .= '<div class="cls"></div></div>';
			}
			$render .= '<div class="row text-center form-horizontal">';
			$render .= '<div class="pagination_rows col-sm-12 col-md-4">';
			$render .= Text::sprintf( 'Number Of Rows : %s', $this->total_records );
			$render .= '</div>';
			$render .= '<div class="form-group col-xs-12  col-sm-12 col-md-4 nopadding">';
			$render .= '<label class="control-label control-label-pagenation col-xs-7 col-sm-6 col-md-8  text-right " for="pagination_limit">'
							. Text::_( 'Number Of Items Per Pages' )
							. ' : </label>';
			$render .= '<div class="pagination_limit col-xs-12 col-sm-12 col-md-4">';
			$render .= $this->RenderLimitbox();
			$render .= '</div>';
			$render .= '</div>';
			$render .= '<div class="pagination_pages col-sm-12 col-md-4">';
			$render .= Text::sprintf( 'Number Of Pages : %s', $this->page_count );
			$render .= '</div>';
			
			$render .= '</div>';
		}
		$render .= '</div>';
		$this->html = $render;

	}

	function generate_notlink( $inner, $classadd = '' )
	{
		$item = '<span class="pagination_span' . $classadd . '"><span>' . $inner . '</span></span>';
		return $item;

	}

	function generate_list( $inner, $start, $end )
	{
		++$start;
		$items = '<span class="pagination_inp_span" id="pagination_c' . $start . '"><span>' . $inner . '</span><span class="pagination_drop_list" id="pagination_l' . $start . '">';
		for ( $a = $start; $a < $end; ++$a )
		{
			$items .= $this->generate_link( $a, $a, '_list' );
		}
		$items .= '</span></span>';
		return $items;

	}

	function generate_active( $inner )
	{
		$item = '<span class="pagination_active"><span>' . $inner . '</span></span>';
		return $item;

	}

	function generate_link( $inner, $page_number, $classadd = '' )
	{
		$recs = ($page_number - 1) * $this->records_per_page;
		$this->URL->setVar( 'start', $recs );
		$url = $this->URL->toString( array( 'path', 'query', 'fragment' ) );
		$link = '<a href="' . $url . '" class="pagination_link' . $classadd . '"><span>' . $inner . '</span></a>';
		return $link;

	}

	public static function Generate( $total, $start )
	{
		$Limit = Request::getState( 'items.limit.per.Page', 'pagination_limit', PAGE_ITEMS_LIMIT );
		$paging = new Pagination( $total, $start, $Limit );
		$paging->fwd_link_icon = Text::_( 'Next' );
		$paging->back_link_icon = Text::_( 'Prev' );
		return $paging->fetch();

	}

	public function RenderLimitbox()
	{
		$Limits = array(
				10,
				25,
				50,
				100,
				250,
				500,
				1000
		);
		foreach ( $Limits as $Limit )
		{
			$options[] = HTML::_( 'select.option', $Limit, $Limit );
		}

		return HTML::_( 'select.genericlist', $options, 'pagination_limit', ' class="form-control"  onchange="setFilter();" ', 'value', 'text', $this->records_per_page, 'pagination_limit' );

	}

}
