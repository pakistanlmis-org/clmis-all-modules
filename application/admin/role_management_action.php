<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * Role Management Action
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//including required files
include("../includes/classes/AllClasses.php");
//getting pk_id
$pk_id = mysql_real_escape_string(trim($_REQUEST['pk_id']));
//getting role_name
$role_name = mysql_real_escape_string(trim($_REQUEST['role_name']));
$role_level = mysql_real_escape_string(trim($_REQUEST['role_level']));
//getting description
$description = mysql_real_escape_string(trim($_REQUEST['description']));
//getting landing_resource_id
$landing_resource_id = mysql_real_escape_string(trim($_REQUEST['landing_resource_id']));
$status = 1;
$created_by = $modified_by = 1;
//getting hdnToDo 
$strDo = $_REQUEST['hdnToDo'];
//Add role
if ($strDo == "Add") {
    //query for adding roles
    $qry = "INSERT INTO roles
			SET
				roles.role_name = '" . $role_name . "',
				roles.role_level = '" . $role_level . "',
				roles.description = '" . $description . "',
				roles.landing_resource_id = '" . $landing_resource_id . "',
				roles.`status` = '" . $status . "',
				roles.created_by = '" . $created_by . "',
				roles.created_date = NOW(),
				roles.modified_by = '" . $modified_by . "',
				roles.modified_date = NOW() ";
   
    mysql_query($qry);

    $_SESSION['err']['text'] = 'Data has been successfully added.';
    $_SESSION['err']['type'] = 'success';
}
//Edit role
if ($strDo == "Edit") {
     //query for editing roles
    $qry = "UPDATE roles
			SET
				roles.role_name = '" . $role_name . "',
                                roles.role_level = '" . $role_level . "',
				roles.description = '" . $description . "',
				roles.landing_resource_id = '" . $landing_resource_id . "',
				roles.modified_by = '" . $modified_by . "',
				roles.modified_date = NOW()
			WHERE
				roles.pk_id = $pk_id";
    mysql_query($qry);
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    $_SESSION['err']['type'] = 'success';
}
//redirecting to role_management
header("location: role_management.php");
exit;
