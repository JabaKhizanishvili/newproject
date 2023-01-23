<?php

/**
 * Description of Mail
 *
 * @author teimuraz.kevlishvili
 */
class Mail
{
	public static function SendInfoToCheff( $Worker, $User, $message, $Subject = null )
	{
		$Query = 'select w.email, w.mobile_phone_number '
						. ' from rel_worker_chief wc '
						. ' left join slf_persons w on w.id = wc.chief_pid '
						. ' where wc.worker_opid = ' . (int) $Worker
//						. '  and w.org in (select m.org from hrs_workers m where m.id = ' . (int) $Worker . ') '
						. ' and w.active=1'
//						. ' and w.email is not null '
		;
		$Chiefs = DB::LoadObjectList( $Query );

		foreach ( $Chiefs as $Chief )
		{
			if ( empty( C::_( 'EMAIL', $Chief ) ) )
			{
				continue;
			}
			$Params = json_decode( C::_( 'PARAMS', $Chief, '{}' ) );
			switch ( C::_( 'some', $Params, 0 ) )
			{
//Send Email
				default:
				case '0':
					self::SendEmail( C::_( 'EMAIL', $Chief ), $message, $Subject );
					break;
				case '1':

					break;
				case '2':
					continue;
			}
		}

	}

	public static function SendSMSToCheff( $Worker, $User, $message )
	{
		$Query = 'select w.email, w.mobile_phone_number '
						. ' from rel_worker_chief wc '
						. ' left join slf_persons w on w.id = wc.chief_pid '
						. ' where wc.worker_opid = ' . (int) $Worker
						. ' and w.active=1'
		;
		$Chiefs = DB::LoadObjectList( $Query );
		$SMS = new oneWaySMS();
		foreach ( $Chiefs as $Chief )
		{
			$Params = json_decode( C::_( 'PARAMS', $Chief, '{}' ) );
			switch ( C::_( 'some', $Params, 0 ) )
			{
				default:
				case '0':
					$SMS->Send( C::_( 'MOBILE_PHONE_NUMBER', $Chief ), $SMS->TranslitToLat( $message ) );
					break;
				case '1':

					break;
				case '2':
					continue;
			}
		}

	}

	public static function SendEmail( $to, $message, $Subject )
	{
		$headers = array();
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset=utf-8';
		$headers[] = 'From: HRMS <hr@self.ge>';
		$email = trim( $to );
		Cmail( $email, $Subject, $message );

	}

	public static function sendLeaveEditMail( $Worker, $start, $end, $type, $User, $IDx )
	{
		if ( $type )
		{
			$leave_type = C::_( 'LIB_TITLE', Xhelp::getLimitType( $type ), 'უხელფასო' );
		}
		else
		{
			$leave_type = 'ხელფასიანი';
		}
		$Subject = 'Leave Request Edit - ' . self::GenTransaction( $IDx, 0 );
		$message = 'შვებულების განაცხადის რედაქტირება.' . "\n<br>\n<br>"
						. 'თანამშრომელი : ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . "\n<br>"
						. 'თარიღი (-დან) : ' . $start . "\n<br>"
						. 'თარიღი (-ჩათვლით) : ' . $end . "\n<br>"
						. 'შვებულების ტიპი : ' . $leave_type . "\n<br>"
						. self::GenTransaction( $IDx, 1 )
		;
		self::SendInfoToCheff( $Worker, $User, $message, $Subject, 'edit_leave' );

	}

	public static function senLeaveEditSMS( $Worker, $start, $end, $type, $User, $IDx )
	{
		if ( $type )
		{
			$leave_type = C::_( 'LIB_TITLE', Xhelp::getLimitType( $type ), 'უხელფასო' );
		}
		else
		{
			$leave_type = 'ხელფასიანი';
		}
		$message = 'შვებულების განაცხადის რედაქტირება.' . "\n\n"
						. 'თანამშრომელი : ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . "\n"
						. 'თარიღი (-დან) : ' . $start . "\n"
						. 'თარიღი (-ჩათვლით) : ' . $end . "\n"
						. 'შვებულების ტიპი : ' . $leave_type . "\n"

		;
		self::SendSMSToCheff( $Worker, $User, $message );

	}

	public static function sendNewLeaveMail( $Worker, $start, $end, $type, $User, $IDx )
	{
		if ( $type )
		{
			$leave_type = C::_( 'LIB_TITLE', Xhelp::getLimitType( $type ), 'უხელფასო' );
		}
		else
		{
			$leave_type = 'ხელფასიანი';
		}
		$Subject = 'New Leave Request - ' . self::GenTransaction( $IDx, 0 );
		$message = 'ახალი შვებულების განაცხადი.' . "\n<br>\n<br>"
						. 'თანამშრომელი : ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . "\n<br>"
						. 'თარიღი (-დან) : ' . $start . "\n<br>"
						. 'თარიღი (-ჩათვლით) : ' . $end . "\n<br>"
						. 'შვებულების ტიპი : ' . $leave_type . "\n<br><br>"
						. "\n<br><br>"
						. Uri::getInstance()->base() . '?option=holidayreghrs'
						. "\n<br><br>"
						. self::GenTransaction( $IDx, 1 )
		;
		self::SendInfoToCheff( $Worker, $User, $message, $Subject, 'edit_leave' );

	}

	public static function sendNewLeaveSMS( $Worker, $start, $end, $type, $User )
	{
		if ( $type )
		{
			$leave_type = C::_( 'LIB_TITLE', Xhelp::getLimitType( $type ), 'უხელფასო' );
		}
		else
		{
			$leave_type = 'ხელფასიანი';
		}
		$message = 'ახალი შვებულების განაცხადი.' . "\n\n"
						. 'თანამშრომელი : ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . "\n"
						. 'თარიღი (-დან) : ' . $start . "\n"
						. 'თარიღი (-ჩათვლით) : ' . $end . "\n"
						. 'შვებულების ტიპი : ' . $leave_type . "\n"
						. Uri::getInstance()->base() . '?option=holidayreghrs' . "\n"

		;
		self::SendSMSToCheff( $Worker, $User, $message, 'edit_leave' );

	}

	public static function sendPTimeMail( $Worker, $start, $end, $User, $IDx )
	{
		$Subject = 'New Private Time Request - ' . self::GenTransaction( $IDx, 0 );
		$message = 'ახალი პირადი დროის განაცხადი.' . "\n<br>\n<br>"
						. 'თანამშრომელი : ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . "\n<br>"
						. 'თარიღი (-დან) : ' . $start . "\n<br>"
						. 'თარიღი (-ჩათვლით) : ' . $end . "\n<br><br>"
						. self::GenTransaction( $IDx, 1 )
		;
		self::SendInfoToCheff( $Worker, $User, $message, $Subject, 'edit_leave' );

	}

	public static function sendPTimeEditMail( $Worker, $start, $end, $User, $IDx )
	{
		$Subject = 'Private Time Edit Request - ' . self::GenTransaction( $IDx, 0 );
		$message = 'პირადი დროის განაცხადის რედაქტირება.' . "\n<br>\n<br>"
						. 'თანამშრომელი : ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . "\n<br>"
						. 'თარიღი (-დან) : ' . $start . "\n<br>"
						. 'თარიღი (-ჩათვლით) : ' . $end . "\n<br><br>"
						. self::GenTransaction( $IDx, 1 )
		;
		self::SendInfoToCheff( $Worker, $User, $message, $Subject, 'edit_leave' );

	}

	public static function SendLeaveMail( $mails, $transID, $start, $end, $FIRSTNAME = null, $LASTNAME = null, $leave_type = 0, $Addstring = null )
	{
		$Subject = 'New Leave Request - ' . self::GenTransaction( $transID, 0 );
		$message = 'თანამშრომელმა მოითხოვა შვებულება.' . PHP_EOL . PHP_EOL
						. 'თანამშრომელი : ' . $FIRSTNAME . ' ' . $LASTNAME . PHP_EOL . PHP_EOL
						. 'თარიღი (-დან) : ' . $start . PHP_EOL . PHP_EOL
						. 'თარიღი (-მდე) : ' . $end . PHP_EOL . PHP_EOL
						. 'შვებულების ტიპი : ' . $leave_type . PHP_EOL . PHP_EOL
		;
		if ( $Addstring )
		{
			$message .= $Addstring . PHP_EOL . PHP_EOL;
		}
		$message .= self::GenTransaction( $transID, 1 );
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain;charset=utf-8' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n";
		$headers .= 'From:<hr@self.ge>' . "\r\n";
//		$headers .= 'Temo:<hr@self.ge>' . "\r\n";
		foreach ( $mails as $email )
		{
			Cmail( $email, $Subject, $message, $headers );
		}

	}

	/**
	 * 
	 * @param type $email
	 * @param type $Subject
	 * @param type $message
	 */
	public static function Send( $email, $Subject, $message )
	{
		$headers = 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
		$headers .= 'From:<hr@self.ge>' . "\r\n";
		$headers .= 'Subject:=?UTF-8?B?' . base64_encode( $Subject ) . '?=' . "\r\n";
		Cmail( $email, $Subject, $message, $headers );

	}

	public static function SendPrivateTimeMail( $mails, $transID, $start, $end, $FIRSTNAME, $LASTNAME, $Addstring = null )
	{
		$Transaction = '';
		if ( !empty( $transID ) )
		{
			$Transaction = strtoupper( self::generateRandomString( 10 ) . '-' . $transID . '-' . self::generateRandomString( 10 ) );
		}
		$Subject = 'New Private Time Request - ' . $Transaction;
		$message = 'თანამშრომელმა მოითხოვა პირადი დრო.' . PHP_EOL . PHP_EOL
						. 'თანამშრომელი : ' . $FIRSTNAME . ' ' . $LASTNAME . PHP_EOL . PHP_EOL
						. 'გასვლის თარიღი, დრო (-დან) : ' . $start . PHP_EOL . PHP_EOL
						. 'დაბრუნების თარიღი, დრო (-მდე) : ' . $end . PHP_EOL . PHP_EOL
		;
		if ( $Addstring )
		{
			$message .= $Addstring . PHP_EOL . PHP_EOL;
		}

		if ( !empty( $transID ) )
		{
			$message .= 'ტრანზაქცია: ' . $Transaction;
		}
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain;charset=utf-8' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n";
		$headers .= 'From:<hr@self.ge>' . "\r\n";
//		$headers .= 'Temo:<hr@self.ge>' . "\r\n";


		foreach ( $mails as $Mail )
		{
			$email = trim( $Mail );
			Cmail( $email, $Subject, $message, $headers );
		}

	}

	public static function generateRandomString( $length = 10 )
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen( $characters );
		$randomString = '';
		for ( $i = 0; $i < $length; $i++ )
		{
			$randomString .= $characters[rand( 0, $charactersLength - 1 )];
		}
		return $randomString;

	}

	public static function SendSMSLeaveMail( $mails, $transID, $start, $end, $FIRSTNAME, $LASTNAME, $leave_type, $Addstring = null )
	{
		$Subject = 'New Leave Request - ' . self::GenTransaction( $transID, 0 );
		$message = 'თანამშრომელმა მოითხოვა შვებულება.' . PHP_EOL . PHP_EOL
						. 'თანამშრომელი : ' . $FIRSTNAME . ' ' . $LASTNAME . PHP_EOL . PHP_EOL
						. 'თარიღი (-დან) : ' . $start . PHP_EOL . PHP_EOL
						. 'თარიღი (-მდე) : ' . $end . PHP_EOL . PHP_EOL
						. 'შვებულების ტიპი : ' . $leave_type . PHP_EOL . PHP_EOL
		;
		if ( $Addstring )
		{
			$message .= $Addstring . PHP_EOL . PHP_EOL;
		}
		$message .= self::GenTransaction( $transID, 1 );
		$headers = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/plain;charset=utf-8' . "\r\n";
		$headers .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n";
		$headers .= 'From:<hr@self.ge>' . "\r\n";
		$headers .= 'Temo:<hr@self.ge>' . "\r\n";
		foreach ( $mails as $value )
		{
			$email = trim( $value['EMAIL'] );
			Cmail( $email, $Subject, $message, $headers );
		}

	}

	public static function GenTransaction( $transID, $Prefix )
	{
		if ( empty( $transID ) )
		{
			return '';
		}
		$Transaction = strtoupper( self::generateRandomString( 10 ) . '-' . $transID . '-' . self::generateRandomString( 10 ) );
		if ( $Prefix )
		{
			return 'ტრანზაქცია: ' . $Transaction;
		}
		else
		{
			return $Transaction;
		}

	}

	public static function sendNewBulletinAlert( $Worker, $start, $end, $User )
	{
		$Subject = 'ახალი ბიულეტენის რეგისტრაცია ';
		$message = 'ახალი ბიულეტენის რეგისტრაცია.' . "\n<br>\n<br>"
						. 'თანამშრომელი : ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . "\n<br>"
						. 'თარიღი (-დან) : ' . $start . "\n<br>"
						. 'თარიღი (-ჩათვლით) : ' . $end . "\n<br>"
//						. self::GenTransaction( $IDx, 1 )
		;
		self::SendInfoToCheff( $Worker, $User, $message, $Subject, 'edit_leave' );
		self::SendSMSToCheff( $Worker, $User, strip_tags( $message ) );

	}

	public static function sendNewBulletinEditAlert( $Worker, $start, $end, $User )
	{
		$Subject = 'ბიულეტენის რედაქტირება';
		$message = 'ბიულეტენის რედაქტირება.' . "\n<br>\n<br>"
						. 'თანამშრომელი : ' . $User->FIRSTNAME . ' ' . $User->LASTNAME . "\n<br>"
						. 'თარიღი (-დან) : ' . $start . "\n<br>"
						. 'თარიღი (-ჩათვლით) : ' . $end . "\n<br>"
//						. self::GenTransaction( $IDx, 1 )
		;
		self::SendInfoToCheff( $Worker, $User, $message, $Subject, 'edit_leave' );
		self::SendSMSToCheff( $Worker, $User, strip_tags( $message ) );

	}

	public static function sendAppSMS( $Phone_Number = '', $TextLines = [] )
	{
		if ( empty( $Phone_Number ) || !count( $TextLines ) )
		{
			return false;
		}

		$message = implode( PHP_EOL, $TextLines );
		$SMS = new oneWaySMS();
		$SMS->Send( $Phone_Number, $SMS->TranslitToLat( $message ) );

	}

	public static function sendAppEMAIL( $Email, $Subject, $TextLines = [], $ID = '' )
	{
		if ( empty( $Email ) || empty( $ID ) || !count( $TextLines ) || !filter_var( $Email, FILTER_VALIDATE_EMAIL ) )
		{
			return false;
		}

		$Subject .= ' - ' . self::GenTransaction( $ID, 0 );
		$message = implode( '<br>', $TextLines );
		self::SendEmail( $Email, $message, $Subject );

	}

	public static function ToChiefs( $Worker = 0, $Subject = '', $TextLines = [], $email = 0, $sms = 0 )
	{
		$chiefs = Xhelp::GetChiefsContacts( $Worker );
		foreach ( $chiefs as $chief )
		{
			$Email = C::_( 'EMAIL', $chief );
			$Phone_Number = C::_( 'MOBILE_PHONE_NUMBER', $chief );

			if ( $email == 1 )
			{
				self::sendAppEMAIL( $Email, $Subject, $TextLines, $Worker );
			}
			if ( $sms == 1 )
			{
				self::sendAppSMS( $Phone_Number, $TextLines );
			}
		}

		return true;

	}

	public static function multiSend( $Emails, $Numbers, $Subject, $TextLines, $id )
	{
		foreach ( $Emails as $Email )
		{
			if ( empty( $Email ) || !filter_var( trim( $Email ), FILTER_VALIDATE_EMAIL ) )
			{
				continue;
			}

			self::sendAppEMAIL( trim( $Email ), $Subject, $TextLines, $id );
		}

		foreach ( $Numbers as $Number )
		{
			if ( empty( $Number ) )
			{
				continue;
			}

			self::sendAppSMS( trim( $Number ), $TextLines );
		}

		return true;

	}

}
