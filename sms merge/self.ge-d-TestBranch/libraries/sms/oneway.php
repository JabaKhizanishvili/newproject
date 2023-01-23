<?php
define( 'ONE_PASSWORD', 'SF775' );
define( 'ONE_USERNAME', 'self' );
define( 'ONE_CLIENT_ID', '775' );
define( 'ONE_SERVICE_ID', '1' );
require_once PATH_BASE . DS . 'libraries' . DS . 'tables' . DS . 'smslog.php';

class oneWaySMS
{
	public function __construct()
	{
		
	}

	public function SendLeaveSMS( $firstname, $lastname, $start, $end, $phone )
	{
		$msg = ucfirst( $this->TranslitToLat( $firstname ) ) . ' ' . ucfirst( $this->TranslitToLat( $lastname ) ) . PHP_EOL;
		$msg .= 'Tqveni Svebulebis ganacxadi damtkicebulia.' . PHP_EOL;
		$msg .= 'Svebulebis dasawyisi: ' . $start . PHP_EOL;
		$msg .= 'Svebulebis dasasruli: ' . $end . PHP_EOL;
		$this->Send( $phone, $msg );

	}

	public function SendPrivateTimeSMS( $firstname, $lastname, $start, $end, $phone, $private_time )
	{
		$msg = ucfirst( $this->TranslitToLat( $firstname ) ) . ' ' . ucfirst( $this->TranslitToLat( $lastname ) ) . PHP_EOL;
		$msg .= 'Tqveni piradi drois ganacxadi damtkicebulia.' . PHP_EOL;
		$msg .= 'gasvlis dro: ' . $start . PHP_EOL;
		$msg .= 'Semosvlis dro: ' . $end . PHP_EOL;
		$msg .= 'DARCHENILI PIRADI DRO: ' . $this->TranslitToLat( $private_time ) . PHP_EOL;
		$this->Send( $phone, $msg );

	}

	public function SendMissionTimeSMS( $firstname, $lastname, $start, $end, $phone, $private_time )
	{
		$msg = ucfirst( $this->TranslitToLat( $firstname ) ) . ' ' . ucfirst( $this->TranslitToLat( $lastname ) ) . PHP_EOL;
		$msg .= 'Tqveni samsaxurebrivi gasvlis ganacxadi damtkicebulia.' . PHP_EOL;
		$msg .= 'gasvlis dro: ' . $start . PHP_EOL;
		$msg .= 'Semosvlis dro: ' . $end . PHP_EOL;
		$msg .= 'DARCHENILI PIRADI DRO: ' . $this->TranslitToLat( $private_time ) . PHP_EOL;
		$this->Send( $phone, $msg );

	}

	public function Send( $phone, $msg )
	{
		if ( empty( $phone ) )
		{
			$this->logResponse( -1, $msg, -1, null );
			return false;
		}

//		$SMSText = str_replace( '__________________________', '@', urlencode( str_replace( '@', '__________________________', $msg ) ) );
		$SMSText = urlencode( $msg );

		$Fphone = urlencode( '+995' . $phone );
		$URL = 'http://81.95.160.47/mt/oneway?username=' . ONE_USERNAME . '&password=' . ONE_PASSWORD . '&client_id=' . ONE_CLIENT_ID . '&service_id=' . ONE_SERVICE_ID . '&to=' . $Fphone . '&text=' . $SMSText;
		if ( strpos( $msg, '_' ) != false )
		{
			$URL .= '&coding=2';
		}


		$result = file_get_contents( $URL );
		$this->logResponse( $phone, $msg, $result, null );
		$Status = preg_match( '/0000/', $result );
		return $Status;

	}

	public static function chekStatusSMS( $message_id )
	{
		$URL = 'http://81.95.160.47/bi/track?username=namespace&password=NM76&client_id=729&service_id=1&message_id=' . urlencode( $message_id );
		$Result = file_get_contents( $URL );
		return $Result;

	}

	public function Call( $Params )
	{
		$Link = 'https://smsoffice.ge/api/v2/send/?';
		$PostData = array();
		foreach ( $Params as $Key => $Value )
		{
			$PostData[] = $Key . '=' . $Value;
		}
		$PostFields = implode( '&', $PostData );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $Link );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
//		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );
//		curl_setopt( $ch, CURLINFO_HEADER_OUT, 1 );
//		curl_setopt( $ch, CURLOPT_HEADER, 1 );
		curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
		curl_setOpt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $PostFields );
		$Response = curl_exec( $ch );
		return $Response;

	}

	public function TranslitToLat( $text )
	{
		$str_from = 'ა, ბ, გ, დ, ე, ვ, ზ, თ, ი, კ, ლ, მ, ნ, ო, პ, ჟ, რ, ს, ტ, უ, ფ, ქ, ღ, ყ, შ, ჩ, ც, ძ, წ, ჭ, ხ, ჯ, ჰ';
		$str_to = 'a, b, g, d, e, v, z, t, i, k, l, m, n, o, p, zh, r, s, t, u, f, q, gh, k, sh, ch, c, dz, ts, tc, kh, j, h';

		if ( !empty( $text ) )
		{
			$from = explode( ', ', $str_from );
			$to = explode( ', ', $str_to );
			$trans = str_replace( $from, $to, trim( $text ) );
			return $trans;
		}
		return $text;

	}

	private function logResponse( $phone, $msg, $response, $Reference )
	{
		$Table = new SMSLogTable();

		$Now = new PDate();
		$Table->LOG_DATE = $Now->toFormat();
		$Table->LOG_PHONE = $phone;
		$Table->LOG_MSG = $msg;
		$Table->LOG_RESULT = $response;
		$Table->LOG_REFERENCE = $Reference;
		return $Table->store();

	}

	public function SendSMS( $msgIN, $phone )
	{
		$msg = $this->TranslitToLat( $msgIN );
		return $this->Send( $phone, $msg );

	}

	public function GUID()
	{
		return mb_strtolower( sprintf( '%04X%04X%04X', mt_rand( 0, 65535 ), mt_rand( 0, 65535 ), mt_rand( 0, 65535 ), mt_rand( 16384, 20479 ) ) );

	}

}
