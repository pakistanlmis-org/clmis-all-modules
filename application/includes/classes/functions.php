<?php

/**
 * functions
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
/**
 * dateToDbFormat
 * 
 * @param type $date
 * @return type
 */
function dateToDbFormat($date) {
    if (!empty($date)) {
        list($dd, $mm, $yy) = explode("/", $date);
        return $yy . "-" . $mm . "-" . $dd;
    }
}
/**
 * dateToUserFormat
 * 
 * @param type $date
 * @return type
 */
function dateToUserFormat($date) {
    if (!empty($date)) {
        list($yy, $mm, $dd) = explode("-", $date);
        return $dd . "/" . $mm . "/" . $yy;
    }
}
/**
 * yearFromDate
 * 
 * @param type $date
 * @return type
 */
function yearFromDate($date) {
    if (!empty($date)) {
        list($dd, $mm, $yy) = explode("/", $date);
        return $yy;
    }
}
/**
 * monthFromDate
 * 
 * @param type $date
 * @return type
 */
function monthFromDate($date) {
    if (!empty($date)) {
        list($dd, $mm, $yy) = explode("/", $date);
        return $mm;
    }
}
/**
 * dateFormat
 * 
 * @param type $date
 * @param type $string
 * @param type $format
 * @return type
 */
function dateFormat($date, $string, $format) {
    $d = new DateTime($date);
    $d->modify($string);
    return $d->format($format);
}
/**
 * pr
 * @param type $data
 * @param type $exit
 */
function pr($data, $exit = true) {
    echo "<pre>";
    print_r($data);
    echo "</pre>";

    if ($exit == true) {
        exit;
    }
}

// $url should be an absolute url
/**
 * redirect
 * 
 * @param type $url
 */
function redirect($url) {
    if (headers_sent()) {
        die('<script type="text/javascript">window.location.href="' . $url . '";</script>');
    } else {
        header('Location: ' . $url);
        die();
    }
}

?>