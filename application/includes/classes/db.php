<?php

/**
 * db
 * @package includes/class
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
include_once("clsConfiguration.php");
$objConfiguration = new clsConfiguration();
$nStat = $objConfiguration->GetDB($db_host, $db_name, $db_user, $db_password);

include_once ("userClickPaths.php");
if($_SERVER['SERVER_ADDR'] != '::1' && $_SERVER['SERVER_ADDR'] != 'localhost'  && $_SERVER['SERVER_ADDR'] != '127.0.0.1') {
    include_once ("acl.php");
}
?>