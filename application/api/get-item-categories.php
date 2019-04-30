<?php
/**
 * get-item-categories
 * @package api
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required files
include("../../application/includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include('auth.php');

//for item categories
$rows = array('pk_id'=>'2','item_category_name'=>'Contraceptives');

print json_encode($rows);

// example: http://localhost/lmis/ws/locations.php?ID=4
?>