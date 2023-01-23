<?php
function Cmail( $To, $Subject, $Message, array $Headers = array() )
{
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->SMTPDebug = 0;
	$mail->IsHTML();
	$mail->Host = gethostbyname( 'mail.self.ge' ); // SMTP server
	$mail->SMTPAuth = true; // enable SMTP authentication
	$mail->Port = 25; // set the SMTP port for the GMAIL server
	$mail->SetFrom( 'hr@self.ge' );
	$mail->Username = 'hr@self.ge';
	$mail->Password = 'aGSXw5uyTBhrJv9';
	$mail->Subject = $Subject;
	$mail->MsgHTML( $Message );
	foreach ( $Headers as $Header )
	{
		if ( empty( $Header ) )
		{
			continue;
		}
		$mail->AddCustomHeader( $Header );
	}
	$mail->AddAddress( $To );
	$status = 0;
	$Result = $mail->Send();
	if ( !$Result )
	{
		print( "Mailer Error: " . $mail->ErrorInfo );
	}
	else
	{
		$status = 1;
	}
	return $status;

}
