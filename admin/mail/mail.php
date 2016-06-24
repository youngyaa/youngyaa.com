<?php
require 'PHPMailerAutoload.php';



function signup($guid,$rec,$url)
{



$mail = new PHPMailer;

//$mail->SMTPDebug = 3;                               // Enable verbose debug output

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'mail.youngdecadeprojects.website';  // Specify main and backup SMTP servers
$mail->SMTPAuth = true;                               // Enable SMTP authentication
$mail->Username = 'youngdecede@youngdecadeprojects.website';                 // SMTP username
$mail->Password = 'youngdecade';                           // SMTP password
$mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
$mail->Port = 587;                                    // TCP port to connect to

$mail->From = 'youngdecede@youngdecadeprojects.website';
$mail->FromName = 'Mailer';
//$mail->addAddress('sumit.taskmanager@gmail.com', 'sumit ji');  
$mail->addAddress($rec);    // Add a recipient
/*
$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addReplyTo('info@example.com', 'Information');
$mail->addCC('cc@example.com');
$mail->addBCC('bcc@example.com');

*/

//$mail->addAttachment('PHPMailer-master.zip');         // Add attachments
//$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name



$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Account Activation Mail From book me pitch web';
$mail->Body    = '<table cellspacing="0" cellpadding="0" width="100%" border="0" align="center" bgcolor="#fff" style="padding:4%;color:#000;  border: 2px dashed;" >

<tbody>
<tr>
	<td class="center" align="center" valign="top">
		<!-- BEGIN: Header -->
		<table class="page-header" align="center" style="background: #fff; width:100%">
		<tr>
			<td class="center" align="center">
			<p>Your Verification URL:-</p><a href="'.$url.'/activate1.php?guid='.$guid.'&rec='.$rec.'">	

           "'.$url.'/activate.php?guid='.$guid.'&rec='.$rec.'"</a>
			</td>
		</tr>
		</table>
		
	</td>
</tr>
</tbody>
</table>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
     echo 'Mailer Error: ' . $mail->ErrorInfo;
   echo '<p class="error" style="display: block;">mail Not Send</p>';
} 
else
{

echo '<p class="success" style="display: block;">Thank You Please Check Your Mail For Verify Your Account</p>';
}

}



?>