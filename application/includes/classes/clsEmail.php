<?php

//require_once APPLICATION_PATH . "/../../public_html/PHPMailer/class.phpmailer.php";

/**
 * It is a class to send email
 * @author ajmal.h
 *
 */
class clsEmail {

    static $mail;

    /**
     * send email using smtp.gmail.com
     * @param array $options for sending mail
     * @return boolean
     */
    public static function smtpStart() {
        self::$mail = new PHPMailer();
        self::$mail->IsSMTP(); // telling the class to use SMTP
        self::$mail->Host = "mail.lmis.gov.pk";
        self::$mail->SMTPAuth = true;                  // enable SMTP authentication
        self::$mail->SMTPKeepAlive = true;                  // SMTP connection will not close after each email sent
        self::$mail->Port = 25;                    // set the SMTP port for the GMAIL server
        self::$mail->Username = ""; // SMTP account username
        self::$mail->Password = "";        // SMTP account password
        self::$mail->SetFrom('', '');
        self::$mail->AddReplyTo('', '');
    }

    public static function smtpSend($options) {
        self::$mail->Subject = $options['subject'];
        self::$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        self::$mail->MsgHTML($options['body']);

        foreach ($options['to'] as $to) {
            self::$mail->AddAddress($to, '');
        }
        foreach ($options['cc'] as $cc) {
            self::$mail->AddCC($cc, '');
        }

        if (!self::$mail->Send()) {
            $response = "Mailer Error (" . self::$mail->ErrorInfo . ")";
        } else {
            $response = "Email Sent";
        }

        return true;
    }

    public static function send($options) {
        $subject = $options['subject'];
        $body = $options['body'];

        $to = implode(",", $options['to']);
        $cc = implode(",", $options['cc']);

        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=iso-8859-1" . "\r\n";
        $headers .= "From: feedback@lmis.gov.pk" . "\r\n" .
                "Reply-To: feedback@lmis.gov.pk" . "\r\n" .
                "Cc: $cc" . "\r\n" .
                "X-Mailer: PHP/" . phpversion();

        if (!empty($to)) {
            mail($to, $subject, $body, $headers);

            $response = 'Email Sent';
            $log_arr = array(
                'to' => $to,
                'cc' => $cc,
                'subject' => $subject,
                'message' => $body,
                'response' => $response
            );
            self::addLog($log_arr);
        }

        return true;
    }

    public static function clear() {
        // Clear all addresses and attachments for next loop
        self::$mail->ClearAddresses();
        self::$mail->ClearCCs();
    }

    public static function smtpClose() {
        self::$mail->SmtpClose();
    }

    public static function addLog($log_arr) {
        $em = Zend_Registry::get('doctrine');
        $str_sql = "INSERT INTO alerts_log (
        alerts_log.`to`,
        alerts_log.cc,
        alerts_log.`subject`,
        alerts_log.body,
        alerts_log.response,alerts_log.type,alerts_log.interface ) VALUES ('" . $log_arr['to'] . "','" . $log_arr['cc'] . "','" . $log_arr['subject'] . "','" . $log_arr['message'] . "','" . $log_arr['response'] . "','Email','vIMAlerts')";
        $row = $em->getConnection()->prepare($str_sql);
        $row->execute();
    }

}