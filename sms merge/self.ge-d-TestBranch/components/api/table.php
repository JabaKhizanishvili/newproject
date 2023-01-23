<?php

class apiTable extends TableLib_apisInterface
{
	public $_DATE_FIELDS = [
			'CHANGE_DATE' => 'yyyy-mm-dd hh24:mi:ss'
	];

	public function __construct()
	{
		parent::__construct( 'lib_apis', 'ID', 'sqs_apis.nextval' );

	}

	public function check()
	{
		$this->LIB_TITLE = trim( $this->LIB_TITLE );
		$this->LIB_DESC = trim( $this->LIB_DESC );
		$this->ACTIVE = (int) trim( $this->ACTIVE );
		$this->APITIME = (int) trim( $this->APITIME );
		if ( empty( $this->APITIME ) )
		{
			return false;
		}
		if ( empty( $this->LIB_TITLE ) )
		{
			return false;
		}
		return true;

	}

	public function SplitText( $SDATA, $delimiter = ' ' )
	{
		$TDATAx = array();
		$Index = 0;
		$MaxLength = 3900;
		$length = (int) strlen( $SDATA );
		if ( $length > $MaxLength )
		{
			$MData = explode( $delimiter, $SDATA );
			foreach ( $MData as $Ph )
			{
				$TDATAxT = C::_( $Index, $TDATAx ) . ($Index > 0 ? $delimiter : '') . $Ph;
				$TDATAx[$Index] = $TDATAxT;
				if ( strlen( $TDATAxT ) >= $MaxLength )
				{
					$Index++;
				}
			}
		}
		else
		{
			$TDATAx[] = $SDATA;
		}
		return $TDATAx;

	}

}
