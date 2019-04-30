<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * Manage User Action
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
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

//Getting form data
if (isset($_REQUEST['hdnstkId']) && !empty($_REQUEST['hdnstkId'])) {
    // in case of edit
    $nstkId = $_REQUEST['hdnstkId'];
}
if (isset($_REQUEST['hdnToDo']) && !empty($_REQUEST['hdnToDo'])) {
    //add or edit
    $strDo = $_REQUEST['hdnToDo'];
}
if (isset($_REQUEST['select']) && !empty($_REQUEST['select'])) {
    // 1 stakeholder
    $stkid = $_REQUEST['select'];
} else {
    $stkid = 0;
}
if (isset($_REQUEST['select3']) && !empty($_REQUEST['select3'])) {
    //province
    $prov_id = $_REQUEST['select3'];
} else {
    $prov_id = 10;
}
if (isset($_REQUEST['select4']) && !empty($_REQUEST['select4'])) {
    //1 district
    $dist_id = $_REQUEST['select4'];
}
if (isset($_REQUEST['warehouses']) && !empty($_REQUEST['warehouses'])) {
    //1 or many warehouses
    $wh_id = $_REQUEST['warehouses'];
}
if (isset($_REQUEST['usrlogin_id']) && !empty($_REQUEST['usrlogin_id'])) {
    //login name
    $usrlogin_id = $_REQUEST['usrlogin_id'];
}
if (isset($_REQUEST['txtStkName2']) && !empty($_REQUEST['txtStkName2'])) {
    //password
    $sysusr_pwd = $_REQUEST['txtStkName2'];
}

if (isset($_REQUEST['full_name']) && !empty($_REQUEST['full_name'])) {
    //full name
    $full_name = $_REQUEST['full_name'];
}

if (isset($_REQUEST['level']) && !empty($_REQUEST['level'])) {
    //full name
    $level_combo = $_REQUEST['level'];
}

if (isset($_REQUEST['email_id']) && !empty($_REQUEST['email_id'])) {
    //email id
    $email_id = $_REQUEST['email_id'];
}

if (isset($_REQUEST['phone_no']) && !empty($_REQUEST['phone_no'])) {
    //phone no
    $phone_no = $_REQUEST['phone_no'];
}

if (isset($_REQUEST['fax_no']) && !empty($_REQUEST['fax_no'])) {
    //fax no
    $fax_no = $_REQUEST['fax_no'];
}

if (isset($_REQUEST['address']) && !empty($_REQUEST['address'])) {
    //address
    $address = $_REQUEST['address'];
}

if (isset($_REQUEST['sysusr_dept']) && !empty($_REQUEST['sysusr_dept'])) {
    //user department
    $sysusr_dept = $_REQUEST['sysusr_dept'];
}

if (isset($_REQUEST['sysusr_deg']) && !empty($_REQUEST['sysusr_deg'])) {
    //designation code
    $sysusr_deg = $_REQUEST['sysusr_deg'];
}
// User Role ID
if (isset($_REQUEST['role_id']) && !empty($_REQUEST['role_id'])) {
    //designation code
    $sysusr_type = $_REQUEST['role_id'];
    
    $qry = "SELECT
			roles.role_level
		FROM
			roles
		WHERE
			roles.pk_id = $sysusr_type ";
    $qryRes = mysql_fetch_array(mysql_query($qry));

    $objuser->m_user_level = $qryRes['role_level'];

}


//Filling value in objuser objects variables
$objuser->m_npkId = $nstkId;
$objuser->m_stkid = $stkid;
// stakeholder name
$objuser->m_stkname = $stkname;
//province id
$objuser->m_prov_id = $prov_id;
//district id
$objuser->m_dist_id = $dist_id;
//warehouse name
$objuser->m_wh_name = $wh_name;
$objuser->m_usrlogin_id = $usrlogin_id;
$objuser->m_sysusr_pwd = $sysusr_pwd;
//adding additional options
$objuser->m_full_name = $full_name;
$objuser->m_email_id = $email_id;

//formatting cell number for spaces, and adding 92 for standard
$phone_no = str_replace(' ', '', $phone_no);
$phone_no = '92'.substr($phone_no, -10); 
$objuser->m_phone_no = $phone_no;

$fax_no = str_replace(' ', '', $fax_no);
$fax_no = '92'.substr($fax_no, -10); 
$objuser->m_fax_no = $fax_no;
$objuser->m_address = $address;
$objuser->m_sysusr_photo = $sysusr_photo;
$objuser->m_sysusr_dept = $sysusr_dept;
$objuser->m_sysusr_deg = $sysusr_deg;
$objuser->m_sysusr_type = $sysusr_type;

/**
 * 
 * Edit User
 * 
 */
if ($strDo == "Edit") {
    $sql = "select sysusr_photo from sysuser_tab where UserID = '" . $nstkId . "'";
    $result = mysql_fetch_array(mysql_query($sql));

    //deleting previous image
    if (file_exists('images/'.$result['sysusr_photo'])) {
        deleteFile('images/'.$result['sysusr_photo']);
    }

    $ext = explode('.', $_FILES['sysusr_photo']['name']);
    $sysusrimg = time() . '.' . $ext[1];
    move_uploaded_file($_FILES['sysusr_photo']['tmp_name'], 'images/' . $sysusrimg);
    $objuser->m_sysusr_photo = $sysusrimg;
    //edit user 
    //echo '<pre>';print_r($wh_id);
    $objuser->EditUser();

    /**
     * 
     * Add wh_user
     * 
     */
    //If user changed the assigned warehouses
    if (!empty($wh_id[0])) {
        //editing values of warehouses
        //deleting already existing values
        $objwharehouse_user->m_npkId = $nstkId;
        $objwharehouse_user->level = $level_combo;
        $objwharehouse_user->wh_userdelete();
        foreach ($wh_id as $arec) {
            //save users warehouses
            $objwharehouse_user->m_wh_id = $arec;
            $objwharehouse_user->m_sysusrrec_id = $nstkId;
            $objwharehouse_user->Addwh_user();
        }
    }
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
/*
change password
 *  */
if ($strDo == "Change_Password") {
    $password = md5($_REQUEST['user_password']);
    
    $strSql ="  UPDATE sysuser_tab SET sysusr_pwd = '".$password."' WHERE UserID=" . $nstkId;
    $rsSql = mysql_query($strSql) or die("Error in changing password Query");

    $_SESSION['err']['text'] = 'Password has been updated.';
    $_SESSION['err']['type'] = 'success';
}
/*
Activate / deactivate user
 *  */
if ($strDo == "activate" || $strDo == "deactivate") {

    //edit user 
    //echo '<pre>';print_r($objuser);exit;
   
    if ($strDo == "activate") 
    {
        $sysusr_status = 'Active';
    }
    elseif ( $strDo == "deactivate") 
    {
        $sysusr_status = 'InActive';
    }
    
    $strSql ="  UPDATE sysuser_tab SET sysusr_status = '".$sysusr_status."' WHERE UserID=" . $nstkId;
    $rsSql = mysql_query($strSql) or die("Error in changing status Query");
   
    $strSql2 ="  INSERT INTO user_status_history SET status='".$sysusr_status."', user_id =" . $nstkId." ,comments = '".mysql_real_escape_string($_REQUEST['status_comments'])."', created_by =" . $_SESSION['user_id'];
    //echo $strSql2;exit;
    $rsSql = mysql_query($strSql2);

    $_SESSION['err']['text'] = 'User has been '.$strDo.'ed.';
    $_SESSION['err']['type'] = 'success';
   

}
/**
 * 
 * Add User
 * 
 */
if ($strDo == "Add") {
    /* $sql = "select usrlogin_id from sysuser_tab where usrlogin_id = '" . $usrlogin_id . "'";
      $result = mysql_query($sql);

      if ($result != FALSE && mysql_num_rows($result) > 0) {
      header("location:ManageUser.php?msg=username+is+not+available");
      exit;
      }
      //If warehouses is not assigned to user
      if (empty($wh_id[0])) {
      header("location:ManageUser.php?msg=Warehouse+not+selected");
      exit;
      } */

    // Add image
    $ext = explode('.', $_FILES['sysusr_photo']['name']);
    $sysusrimg = time() . '.' . $ext[1];
    move_uploaded_file($_FILES['sysusr_photo']['tmp_name'], 'images/' . $sysusrimg);
    $objuser->m_sysusr_photo = $sysusrimg;


    //first check if the username doesnt exist
    $objuser->m_usrlogin_id = $usrlogin_id;
    if($nuserId = $objuser->GetUserByUsernameOnly())
    {        
        $_SESSION['err']['text'] = 'Please choose another username  , this username already exists.';
        $_SESSION['err']['type'] = 'error';
         header("location:ManageProAdmin.php");
        exit;
    }
    //save user
    $usrID = $objuser->AddUser();
    if ($wh_id) {
        foreach ($wh_id as $arec) {
            //save users warehouses		
            $objwharehouse_user->m_wh_id = $arec;
            $objwharehouse_user->m_sysusrrec_id = $usrID;
            $objwharehouse_user->Addwh_user();
        }
    }
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}
unset($_SESSION['whArr']);
unset($_SESSION['distArr']);
//Redirecting to ManageUser
header("location:ManageUser.php");
exit;
?>