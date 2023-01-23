<?php

/**
 * Description of TMPL
 *
 * @author teimuraz
 */
class XTMPL extends XObject
{
	protected $FIRSTNAME = null;
	protected $LASTNAME = null;
	protected $WORKER = null;
	protected $START_DATE = null;
	protected $END_DATE = null;
	protected $TYPE = null;
	protected $LIB_TITLE = null;

	/**
	 * 
	 * @staticvar XTMPL $Instance
	 * @return \XTMPL
	 */
	public static function GetInstance()
	{
		static $Instance = null;
		if ( is_null( $Instance ) )
		{
			$Instance = new Self();
		}
		return $Instance;

	}

	public function GetTerms()
	{
		$Attrs = array_keys( $this->GetProperties() );
		$Return = array();
		foreach ( $Attrs as $A )
		{
			$Key = '{' . $A . '}';
			$Return[] = array(
					'KEY' => $Key,
					'LABEL' => Text::_( $A )
			);
		}
		return $Return;

	}

}
