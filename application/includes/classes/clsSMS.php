<?php

/**
 * It is a class to send sms
 * @author ajmal hussain <ahussain@ghsc-psm.org>
 *
 */
class clsSMS {

    static $url = "";
    static $username = "";
    static $password = "";

    /**
     * send sms using zong api
     * @param array $options for sending sms
     * @return response
     */
    public static function send($options) {
        $is_sms_enable = Zend_Registry::get('is_sms_enable');
        if ($is_sms_enable) {
            $client = new SoapClient(static::$url, array("trace" => 1, "exception" => 0));
            $result = $client->QuickSMS(
                    array('obj_QuickSMS' =>
                        array('loginId' => static::$username,
                            'loginPassword' => static::$password,
                            'Destination' => $options['to'],
                            'Mask' => 'LMIS Alert',
                            'Message' => $options['message'],
                            'UniCode' => 0,
                            'ShortCodePrefered' => 'n'
                        )
                    )
            );
            $response = $result->QuickSMSResult;
            $log_arr = array(
                'to' => $options['to'],
                'cc' => '',
                'subject' => 'LMIS Alerts',
                'message' => $options['message'],
                'response' => $response
            );
            self::addLog($log_arr);
            return true;
        } return false;
    }

    public static function addLog($log_arr) {
        $em = Zend_Registry::get('doctrine');
        $str_sql = "INSERT INTO alerts_log (
        alerts_log.`to`,
        alerts_log.cc,
        alerts_log.`subject`,
        alerts_log.body,
        alerts_log.response,alerts_log.type,alerts_log.interface ) VALUES ('" . $log_arr['to'] . "','" . $log_arr['cc'] . "','" . $log_arr['subject'] . "','" . $log_arr['message'] . "','" . $log_arr['response'] . "','SMS','vIMAlerts')";
        $row = $em->getConnection()->prepare($str_sql);
        $row->execute();
    }

}