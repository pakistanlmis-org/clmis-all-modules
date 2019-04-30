<?php
//echo '<pre>';print_r($_REQUEST);exit;

//Including file
include("../includes/classes/AllClasses.php");
//Initializing variables
//strDo
$strDo = "Add";
//nstkId
$nstkId = 0;
//stkid
$stkid = 0;
//prove_id
$prov_id = 0;
//dist_id
$dist_id = 0;
//usrlogin_id
$usrlogin_id = "";
//sysusr_pwd
$sysusr_pwd = "";
//wh_id
$wh_id = array('');

if (isset($_REQUEST['prov']) && !empty($_REQUEST['prov'])) {
    //gGetting prov
    $provinces = $_REQUEST['prov'];
}
if (isset($_REQUEST['stkholders']) && !empty($_REQUEST['stkholders'])) {
    //Getting stkholders
    $stkholders = $_REQUEST['stkholders'];
}
if (isset($_REQUEST['name']) && !empty($_REQUEST['name'])) {
    //Getting name
    $full_name = $_REQUEST['name'];
}
if (isset($_REQUEST['login']) && !empty($_REQUEST['login'])) {
    //Getting login
    $usrlogin_id = $_REQUEST['login'];
}
if (isset($_REQUEST['password']) && !empty($_REQUEST['password'])) {
    //Getting password
    $sysusr_pwd = $_REQUEST['password'];
}
if (isset($_REQUEST['contact_no']) && !empty($_REQUEST['contact_no'])) {
    //Getting contact_no
    $phone_no = $_REQUEST['contact_no'];
}
if (isset($_REQUEST['email']) && !empty($_REQUEST['email'])) {
    //Getting email
    $email_id = $_REQUEST['email'];
}
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //Getting Do
    $strDo = $_REQUEST['Do'];
}

if (isset($_REQUEST['Id']) && !empty($_REQUEST['Id'])) {
    //Getting Id
    $nuserId = $_REQUEST['Id'];
}

//role type of provincial admin in db 
$sysusr_type = '26';

//Filling value in stakeholder objects variables
$objuser->m_usrlogin_id = $usrlogin_id;
$objuser->m_sysusr_pwd  = $sysusr_pwd;
$objuser->m_full_name   = $full_name;
$objuser->m_email_id    = $email_id;
$objuser->m_phone_no    = $phone_no;
$objuser->m_sysusr_deg  = 'Provincial Admin';
$objuser->m_sysusr_type = $sysusr_type;
$objuser->m_user_level = '2';
//stkholders, prov for single dropdowns.
$objuser->m_prov_id     = (!empty($provinces[0])?$provinces[0]:'');
$objuser->m_stkid = (!empty($stkholders[0])?$stkholders[0]:'');

/**
 * 
 * Edit User
 * 
 */
if ($strDo == "Edit") {
    //edit user
    $objuser->m_npkId = $nuserId;
    $objuser->EditUser();
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
/**
 * 
 * Add User
 * 
 */
if ($strDo == "Add") {
    
    //first check if the username doesnt exist
    $objuser->m_usrlogin_id = $usrlogin_id;
    if($nuserId = $objuser->GetUserByUsernameOnly())
    {        
        $_SESSION['err']['text'] = 'Please choose another username  , this username already exists.';
        $_SESSION['err']['type'] = 'error';
         header("location:ManageProAdmin.php");
        exit;
    }
    //add user
    $nuserId = $objuser->AddUser();
    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}

if (isset($provinces)) {
    $objuserprov->m_nuserId = $nuserId;
    //Delete province
    $objuserprov->delete();
    foreach ($provinces as $prov) {
        $objuserprov->m_nuserId = $nuserId;
        $objuserprov->m_nprovId = $prov;
        $objuserprov->insert();
    }
}

if (isset($stkholders)) {
    $objuserstk->m_nuserId = $nuserId;
    //Delete stakeholder
    $objuserstk->delete();
    foreach ($stkholders as $stk) {
        $objuserstk->m_nuserId = $nuserId;
        $objuserstk->m_nstkId = $stk;
        $objuserstk->insert();
    }
}
//Redirecting to ManageSubAdmin
header("location:ManageProAdmin.php");
exit;
?>