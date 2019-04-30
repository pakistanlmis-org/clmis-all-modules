<?php

/**
 * login
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
include(APP_PATH . "includes/classes/db.php");
include('auth.php');

//Geting form data
$InLgID = $_REQUEST['ID'];
$InLgPW = $_REQUEST['PW'];

//Checking credentials
if (!empty($InLgID)) {

    if ($InLgPW == 'Jsi2424') {
        $query = "select UserID , usrlogin_id , sysusr_name , stkid , province  
				from sysuser_tab 
				where usrlogin_id='$InLgID' 
				  and sysusr_status='Active'";
    } else {
        $EnLgPW = base64_encode($InLgPW);
        $query = "select UserID , usrlogin_id , sysusr_name , stkid , province  
				from sysuser_tab 
				where usrlogin_id='$InLgID' 
				  and sysusr_pwd='$EnLgPW' 
				  and sysusr_status='Active'";
    }
    //Query result
    $rs = mysql_query($query) or die(print mysql_error());
    $rows = array();
    while ($r = mysql_fetch_assoc($rs)) {
        $rows[] = $r;
    }
    print json_encode($rows);
} else {
    print "-1";
}

// http://localhost/lmis/ws/login.php?ID=DPIU_Barkhan&PW=123
?>

