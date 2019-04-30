<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * Manage Sub Admin Action
 * @package Admin
 * 
 * @author     Muhammad Waqas Azeem 
 * @email <waqas@deliver-pk.org>
 * 
 * @version    2.2
 * 
 */
//Including file
include("../includes/classes/AllClasses.php");


if (isset($_REQUEST['submit']) && $_REQUEST['submit'] == 'Add') {
    
    $strSql = " INSERT into 
                    email_persons_list
                SET
                    person_name='".$_REQUEST['person_name']."',
                    email_address='".$_REQUEST['email_address']."',
                    stkid='".$_REQUEST['stk_sel']."',
                    prov_id='".$_REQUEST['prov_sel']."',
                    office_name='".$_REQUEST['office']."'
            ";
    $rsSql = mysql_query($strSql);
    
    $_SESSION['err']['text'] = 'New email address has been added.';
    $_SESSION['err']['type'] = 'success';
}
elseif (isset($_REQUEST['submit']) && $_REQUEST['submit']=='Save') {
    
    
    if(isset($_REQUEST['enable']))
    {
         $strSql = " DELETE FROM email_bridge ";
         $rsSql = mysql_query($strSql);
            
        foreach($_REQUEST['enable'] as $k=>$v)
        {
            $ids = array();
            $ids = explode('_',$k);
            
            $person_id = $ids[0];
            $action_id = $ids[1];

            $strSql = " INSERT into 
                            email_bridge
                        SET 
                            person_id = '".$person_id."',
                            action_id = '".$action_id."'
                    ";
            $rsSql = mysql_query($strSql);
            //$num = mysql_num_rows($rsSql);
 
        }
    }
    $_SESSION['err']['text'] = 'Changes have been successfully saved.';
    $_SESSION['err']['type'] = 'success';
}
//Redirecting to ManageSubAdmin
header("location:email_control.php");
exit;
?>