<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * Manage Location ction
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including required file
include("../includes/classes/AllClasses.php");

$master_id  = $_REQUEST['master_id'];
$item_name  = isset($_REQUEST['item_name']) ? $_REQUEST['item_name'] : '';
$item_desc  = isset($_REQUEST['item_desc']) ? $_REQUEST['item_desc'] : '';
$rank       = isset($_REQUEST['rank']) ? $_REQUEST['rank'] : '';

$strDo = $_REQUEST['hdnToDo'];

if ($strDo == "Edit") {
    $detail_id = $_REQUEST['detail_id'];

    $strSql = " UPDATE `list_detail` SET `list_master_id`='$master_id' , `list_value`='$item_name', `description`='$item_desc', `rank`='$rank'  WHERE `pk_id`='$detail_id'  ";
    //query result
    $rsSql = mysql_query($strSql) or die("Error Addlocations");
    if (mysql_affected_rows()) {
        $_SESSION['err']['text'] = 'Data has been successfully updated.';
        $_SESSION['err']['type'] = 'success';
    }
    else
    {
        $_SESSION['err']['text'] = 'Could not update , some error occured.';
        $_SESSION['err']['type'] = 'error';
    }
}


if ($strDo == "Add") {
    
	if(empty($rank)) $rank=0;
   $strSql = "INSERT INTO `list_detail` ( list_master_id , `list_value`, `description`, `rank`,created_date  ) VALUES ($master_id,'$item_name','$item_desc','$rank',now()) ";
    //query result
	//echo $strSql;exit;
    $rsSql = mysql_query($strSql) or die("Error Addlocations");
    if (mysql_insert_id() > 0) {
        $_SESSION['err']['text'] = 'Data has been successfully added.';
        $_SESSION['err']['type'] = 'success';
    }
    else
    {
        $_SESSION['err']['text'] = 'Could not add , some error occured.';
        $_SESSION['err']['type'] = 'error';
    }
}

//Redirecting to ManageLocations
header("location:Manage_list_master.php");
exit;
