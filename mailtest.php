<?php
/**
 * Sending an email test
 */
ini_set("SMTP","smtp.gmail.com");
ini_set("smtp_port","2525");
ini_set("sendmail_from","r52cqab@gmail.com");
ini_set("sendmail_path", "C:\wamp64\sendmail\sendmail.exe");
$to      = 'lpchungaa@gmail.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: webmaster@G11Auction.com' . "\r\n" .
    'Reply-To: webmaster@G11Auction.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();


$result = mail($to, $subject, $message, $headers);
if( $result ) {
   echo 'Success';
}else{
   echo 'Fail';
}
?>