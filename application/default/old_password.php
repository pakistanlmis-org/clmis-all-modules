<?php

/**
 * ajax validate
 * used for validating ajax data
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../includes/classes/AllClasses.php");
//Validate 
$user_id=$_SESSION['user_id'];
$old_pass=md5($_REQUEST['old_pass']);
$qry = "SELECT UserID from sysuser_tab WHERE sysusr_pwd = '$old_pass'AND UserID = $user_id";
$qryRes = mysql_fetch_array(mysql_query($qry));
//Checking result count
if (!empty($qryRes['UserID'])) {
    $isValid = 'true';
} else {
    $isValid = 'false';
}
echo $isValid;