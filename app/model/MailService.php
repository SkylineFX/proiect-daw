<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService {
    public static function getSmtpConfig() {
        return [
            'host' => $_ENV['SMTP_HOST'],
            'user' => $_ENV['SMTP_USER'],
            'pass' => $_ENV['SMTP_PASS'],
            'port' => $_ENV['SMTP_PORT'],
            'from_email' => $_ENV['SMTP_FROM_EMAIL'],
            'from_name' => $_ENV['SMTP_FROM_NAME']
        ];
    }

    public static function sendOrderConfirmation($toEmail, $username, $orderId, $total, $address, $items) {
        $mail = new PHPMailer(true);
        $config = self::getSmtpConfig();

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['user'];
            $mail->Password   = $config['pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $config['port'];

            // Fix for Certificate Mismatch on shared hosting
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->addAddress($toEmail, $username);

            // Content
            $mail->isHTML(true);
            $mail->Subject = "Confirmare Comanda #$orderId";

            // Build HTML Body
            $body = "<h1>Salut $username,</h1>";
            $body .= "<p>Comanda ta a fost inregistrata cu succes!</p>";
            $body .= "<p>Detalii Comanda #$orderId:</p>";
            $body .= "<p><strong>Total:</strong> " . number_format($total, 2) . " RON</p>";
            $body .= "<p><strong>Adresa de Livrare:</strong> $address</p>";
            
            $body .= "<h4>Produse:</h4><ul>";
            foreach ($items as $item) {
                $body .= "<li>{$item['name']} x{$item['qty']} - " . number_format($item['price'], 2) . " RON</li>";
            }
            $body .= "</ul>";
            $body .= "<br>";
            $body .= "<p><strong>Iti multumim!</strong></p>";

            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace(['<br>', '</li>'], ["\n", "\n- "], $body));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public static function sendPasswordReset($toEmail, $username, $newPassword) {
        $mail = new PHPMailer(true);
        $config = self::getSmtpConfig();

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $config['host'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $config['user'];
            $mail->Password   = $config['pass'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $config['port'];

            // Fix for Certificate Mismatch
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );

            // Recipients
            $mail->setFrom($config['from_email'], 'DAW Store Support');
            $mail->addAddress($toEmail, $username);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Resetare Parola';
            
            $body = "<h1>Salut $username,</h1>";
            $body .= "<p>O resetare de parola a fost solicitata pentru contul tau.</p>";
            $body .= "<p>Noua ta parola temporara este: <strong>$newPassword</strong></p>";
            $body .= "<p>Te rugam sa te autentifici si sa iti schimbi parola imediat.</p>";
            
            $mail->Body    = $body;
            $mail->AltBody = strip_tags(str_replace('<br>', "\n", $body));

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
