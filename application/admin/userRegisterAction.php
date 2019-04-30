<?php

/**
 * Register User
 * @package Admin
 * 
 * 
 * 
 * @version    2.2
 * 
 */
include("../includes/classes/AllClasses.php");

//Initializing variables
$strDo = "Add";
$nstkId = 0;
$stkid = 0;
$prov_id = 0;
$dist_id = 0;
$usrlogin_id = "";
$sysusr_pwd = "";
$wh_id = array('');

/**
 * delete File
 * 
 * @param type $dir
 * @param type $fileName
 */
function deleteFile($dir, $fileName) {
    //open dir
    $handle = opendir($dir);

    while (($file = readdir($handle)) !== false) {
        if ($file == $fileName) {
            @unlink($dir . '/' . $file);
        }
    }
    //close dir
    closedir($handle);
}

if (isset($_REQUEST['full_name']) && !empty($_REQUEST['full_name'])) {
    //full name
    $full_name = $_REQUEST['full_name'];
}

if (isset($_REQUEST['email_id']) && !empty($_REQUEST['email_id'])) {
    //email id
    $email_id = $_REQUEST['email_id'];
}

if (isset($_REQUEST['contact']) && !empty($_REQUEST['contact'])) {
    //phone no
    $phone_no = $_REQUEST['contact'];
}


if (isset($_REQUEST['address']) && !empty($_REQUEST['address'])) {
    //address
    $address = $_REQUEST['address'];
}

if (isset($_REQUEST['Stakeholders']) && !empty($_REQUEST['Stakeholders'])) {
    //user department
    $stakeholders = $_REQUEST['Stakeholders'];
}

if (isset($_REQUEST['designation']) && !empty($_REQUEST['designation'])) {
    //designation code
    $designation = $_REQUEST['designation'];
}
// User Role ID
//designation code
$sysusr_type = 8;

$qry = "SELECT
			roles.role_level
		FROM
			roles
		WHERE
			roles.pk_id = $sysusr_type ";
$qryRes = mysql_fetch_array(mysql_query($qry));

$m_user_level = $qryRes['role_level'];




/**
 * 
 * Add User
 * 
 */
if ($strDo == "Add") {


    // Add image
    $ext = explode('.', $_FILES['photo']['name']);

    $sysusrimg = time() . '.' . $ext[1];
    // move_uploaded_file($_FILES['sysusr_photo']['tmp_name'], 'images/' . $sysusrimg);

    $sysusr_pwd = base64_encode($sysusr_pwd);
    $hash = md5(strtolower($usrlogin_id) . '' . $sysusr_pwd);
    try {
        $strSql = "INSERT INTO sysuser_tab(user_level,acopen_dt,UserID,stkid,province,usrlogin_id,
		sysusr_name,sysusr_pwd,sysusr_status,sysusr_email,sysusr_ph,sysusr_addr,sysusr_deg,sysusr_type,sysusr_photo,auth)
		VALUES('" . $m_user_level . "',NOW()," . ($objuser->GetMaxUserId() + 1) . "," . $stkid . "," . $prov_id . ",'" . $full_name . "','" . $full_name . "','" . 'MTIz' . "','InActive','" . $email_id . "','" . $phone_no . "','" . $address . "','" . $designation . "','" . $sysusr_type . "','" . $sysusrimg . "','" . $hash . "')";

        //query result
        $rsSql = mysql_query($strSql) or die("Error AddUser");
    } catch (Exception $e) {
        echo 'Message: ' . $e->getMessage();
        exit;
    }
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

//Redirecting to ManageUser
header("location:register.php");
exit;
?>