<?php
/*
 */
include_once BASE_PATH . DS . 'libraries' . DS . 'sms' . DS . 'oneway.php';

/**
 * Description of Alerts
 *
 * @author teimuraz
 */
class XAlerts
{
	protected $_TMPL_DIR = '';
	protected $_SMS_TMPL = '';
	protected $_EMAIL_TMPL = '';

	public function __construct()
	{
		$this->_TMPL_DIR = dirname( __FILE__ ) . DS . 'tmpl';
		$this->_SMS_TMPL = 'sms';
		$this->_EMAIL_TMPL = 'email';

	}

	public static function GetInstance()
	{
		static $Instance = null;
		if ( is_null( $Instance ) )
		{
			$Instance = new self();
		}
		return $Instance;

	}

	public function SendAlert( $Type, $Keys, $Workers )
	{

		$WorkersIDx = explode( ',', $Workers );
		$RegEx = $this->ToRegEx( $Keys );
		$Results = true;
		foreach ( $WorkersIDx as $Worker )
		{
			$WorkerData = Users::getUser( $Worker );
			$SMS = $this->SendSMS( $Type, Helper::CleanArray( $RegEx, 'str' ), C::_( 'MOBILE_PHONE_NUMBER', $WorkerData ) );
			$Email = $this->SendMail( $Type, Helper::CleanArray( $RegEx, 'str' ), C::_( 'EMAIL', $WorkerData ) );
			$Results = $Results && ($Email || $SMS);
		}
		return $Results;

	}

	public function ToRegEx( $Keys )
	{
		$Result = array();
		foreach ( $Keys as $Key => $Value )
		{
			$Result['/\$\{' . $Key . '\}/i'] = $Value;
		}
		$Result['/\$\{' . 'URL' . '\}/i'] = URI::root();
		return $Result;

	}

	public function SendSMS( $Type, $RegEx, $PhoneNumber )
	{
		$Phone = trim( $PhoneNumber );
		if ( empty( $Phone ) )
		{
			return false;
		}
		$SMSTMPL = $this->loadTMPL( $Type, $this->_SMS_TMPL );
		if ( !$SMSTMPL )
		{
			return false;
		}
		$Patterns = array_keys( $RegEx );
		$Replacements = array_values( $RegEx );
		$SMS = preg_replace( $Patterns, $Replacements, $SMSTMPL );
		return $this->_SendSMS( $SMS, $Phone );

	}

	public function loadTMPL( $Type, $Mode )
	{
		$Key = $Type . '_' . $Mode;
		static $TMP = array();
		if ( !isset( $TMP[$Key] ) )
		{
			$File = $this->_TMPL_DIR . DS . $Type . DS . $Mode . '.ini';
			if ( is_file( $File ) )
			{
				ob_start();
				require_once $File;
				$TMP[$Key] = ob_get_clean();
			}
			else
			{
				$TMP[$Key] = false;
			}
		}
		return $TMP[$Key];

	}

	public function _SendSMS( $SMS, $Phone )
	{
		$GateWay = new oneWaySMS( );
		return $GateWay->SendSMS( $SMS, $Phone );

	}

	public function SendMail( $Type, $RegEx, $MailAddress )
	{
		$Mail = trim( $MailAddress );
		if ( empty( $Mail ) )
		{
			return false;
		}
		$EmailTMPL = $this->loadTMPL( $Type, $this->_EMAIL_TMPL );
		if ( !$EmailTMPL )
		{
			return false;
		}
		$Patterns = array_keys( $RegEx );

		$Replacements = array_values( $RegEx );
		$EMail = preg_replace( $Patterns, $Replacements, $EmailTMPL );
		$Data = explode( '###', $EMail );
		return $this->_SendEmail( trim( C::_( '0', $Data ) ), trim( C::_( '1', $Data ) ), $Mail );

	}

	public function _SendEmail( $Subject, $Message, $Mail )
	{
		$headers = array();
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';
		$headers[] = 'From: HRMS <hr@self.ge>';
		$email = trim( $Mail );
		return Cmail( $email, $Subject, nl2br( $Message ) );

	}

}
