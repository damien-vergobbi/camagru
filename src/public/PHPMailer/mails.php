<?php
require_once "PHPMailer.php";
require_once "SMTP.php";
require_once "Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Chemin vers le fichier .env
$envFile = __DIR__ . '/../../.env';

if (file_exists($envFile)) {
  $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos($line, '=') !== false) {
      list($key, $value) = explode('=', $line, 2);
      $key = trim($key);
      $value = trim($value);

      // Remove " from value
      $value = str_replace('"', '', $value);
      putenv("$key=$value");
    }
  }
}


function sendTestMail($recipientMail, $recipientUsername) {
  $mail = new PHPMailer();

  $sender = getenv('MAIL_NAME');
  $passwd = getenv('MAIL_PASS');
  $host = getenv('MAIL_HOST');
  $port = getenv('MAIL_PORT');

  $mail->isSMTP();
  $mail->Host = $host;
  $mail->SMTPAuth = true;
  $mail->Username = $sender;
  $mail->Password = $passwd;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  $mail->Port = $port;

  // Paramètres de l'e-mail
  $mail->setFrom($sender, 'Camagru dvergobb');
  $mail->addAddress($recipientMail, $recipientUsername);
  $mail->Subject = 'Test Email';
  $mail->Body = 'This is another test email sent using PHPMailer';

  return $mail->send();
}

function sendTokenMail($recipientMail, $recipientUsername, $token) {
  $mail = new PHPMailer();

  $sender = getenv('MAIL_NAME');
  $passwd = getenv('MAIL_PASS');
  $host = getenv('MAIL_HOST');
  $port = getenv('MAIL_PORT');

  $mail->isSMTP();
  $mail->Host = $host;
  $mail->SMTPAuth = true;
  $mail->Username = $sender;
  $mail->Password = $passwd;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  $mail->Port = $port;

  // Paramètres de l'e-mail
  $mail->setFrom($sender, 'Camagru dvergobb');
  $mail->addAddress($recipientMail, $recipientUsername);
  $mail->Subject = 'Camagru - Confirm your email';

  $server_ip = getenv('SERVER_IP');

  $url = "http://$server_ip:80/confirm.php?token=$token&email=$recipientMail";

  $mail->Body = '
    <html>
      <body>
        <h1>Camagru</h1>
        <p>Hi ' . $recipientUsername . ',</p>
        <p>Thanks for signing up!</p>
        <p>Click the link below to confirm your email address:</p>
        <a href="' . $url . '">Confirm my email</a>
      </body>
    </html>
  ';

  $mail->isHTML(true);

  return $mail->send();
}

function sendRecoverMail($recipientMail, $username, $token) {
  $mail = new PHPMailer();

  $sender = getenv('MAIL_NAME');
  $passwd = getenv('MAIL_PASS');
  $host = getenv('MAIL_HOST');
  $port = getenv('MAIL_PORT');

  $mail->isSMTP();
  $mail->Host = $host;
  $mail->SMTPAuth = true;
  $mail->Username = $sender;
  $mail->Password = $passwd;
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
  $mail->Port = $port;

  // Paramètres de l'e-mail
  $mail->setFrom($sender, 'Camagru dvergobb');
  $mail->addAddress($recipientMail, $username);
  $mail->Subject = 'Camagru - Recover your password';

  $server_ip = getenv('SERVER_IP');

  $url = "http://$server_ip:80/recover.php?token=$token&email=$recipientMail";

  $mail->Body = '
    <html>
      <body>
        <h1>Camagru</h1>
        <p>Hi ' . $username . ',</p>
        <p>Click the link below to recover your password:</p>
        <a href="' . $url . '">Recover my password</a>
      </body>
    </html>
  ';

  $mail->isHTML(true);

  return $mail->send();
}

?>