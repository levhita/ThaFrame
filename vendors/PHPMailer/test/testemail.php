<?php
/**
* Simple example script using PHPMailer with exceptions enabled
* @package phpmailer
* @version $Id$
*/

require '../class.phpmailer-lite.php';

try {
  $mail = new PHPMailerLite(true); //New instance, with exceptions enabled

  $body             = file_get_contents('contents.html');
  $body             = preg_replace('/\\\\/','', $body); //Strip backslashes

  $mail->AddReplyTo("name@domain.com","First Last");

  $mail->SetFrom('you@yourdomain.com', 'Your Name');

  $to = "someone@example...com";

  $mail->AddAddress($to);

  $mail->Subject  = "First PHPMailer Message";

  $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
  $mail->WordWrap   = 80; // set word wrap

  $mail->MsgHTML($body);

  $mail->IsHTML(true); // send as HTML

  $mail->Send();
  echo 'Message has been sent.';
} catch (phpmailerException $e) {
  echo $e->errorMessage();
}
?>