<html>
<head>
<title>PHPMailer - MySQL Database - SMTP basic test with authentication</title>
</head>
<body>

<?php

//error_reporting(E_ALL);
error_reporting(E_STRICT);

date_default_timezone_set('America/Toronto');

require_once('../class.phpmailer-lite.php');

$mail             = new PHPMailerLite(); // defaults to using php "Sendmail" (or Qmail, depending on availability)

$body                = file_get_contents('contents.html');
$body                = eregi_replace("[\]",'',$body);

$mail->SetFrom('list@mydomain.com', 'List manager');

$mail->Subject       = "PHPMailer Test Subject via Sendmail";

@MYSQL_CONNECT("localhost","root","password");
@mysql_select_db("my_company");
$query  = "SELECT full_name, email, photo FROM employee WHERE id=$id";
$result = @MYSQL_QUERY($query);

while ($row = mysql_fetch_array ($result)) {
  $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
  $mail->MsgHTML($body);
  $mail->AddAddress($row["email"], $row["full_name"]);
  $mail->AddStringAttachment($row["photo"], "YourPhoto.jpg");

  if(!$mail->Send()) {
    echo "Mailer Error (" . str_replace("@", "&#64;", $row["email"]) . ') ' . $mail->ErrorInfo . '<br />';
  } else {
    echo "Message sent to :" . $row["full_name"] . ' (' . str_replace("@", "&#64;", $row["email"]) . ')<br />';
  }
  // Clear all addresses and attachments for next loop
  $mail->ClearAddresses();
  $mail->ClearAttachments();
}
?>

</body>
</html>
