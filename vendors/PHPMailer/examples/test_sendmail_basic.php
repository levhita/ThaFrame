<html>
<head>
<title>PHPMailer - Sendmail basic test</title>
</head>
<body>

<?php

require_once('../class.phpmailer-lite.php');

$mail             = new PHPMailerLite(); // defaults to using php "Sendmail" (or Qmail, depending on availability)

$body             = file_get_contents('contents.html');
$body             = eregi_replace("[\]",'',$body);

$mail->SetFrom('name@yourdomain.com', 'First Last');

$address = "whoto@otherdomain.com";
$mail->AddAddress($address, "John Doe");

$mail->Subject    = "PHPMailer Test Subject via Sendmail, basic";

$mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test

$mail->MsgHTML($body);

$mail->AddAttachment("images/phpmailer.gif");      // attachment
$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}

?>

</body>
</html>
