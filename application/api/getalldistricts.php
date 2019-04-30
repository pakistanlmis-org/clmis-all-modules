<?php

/**
 * Family Planning Dashboard cLMIS Web Services
 * @package api
 *
 * @author     Ahmad Saib
 * @email <ahmad.saib@outlook.com>
 *
 * @version    2.2
 *
 */
//Including required files
include("../../application/includes/classes/Configuration.inc.php");
//Including db file
include(APP_PATH . "includes/classes/db.php");
require_once("../../application/includes/classes/clsFp.php");


$fp = new clsFp();
$result = $fp->getAllDistricts();
echo $result;
?>