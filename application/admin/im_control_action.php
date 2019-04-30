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

if(isset($_REQUEST['submit']) && $_REQUEST['submit']=='Save') {
    if(isset($_REQUEST['enable']))
    {
         $strSql = " DELETE FROM im_control ";
         $rsSql = mysql_query($strSql);
            
        foreach($_REQUEST['enable'] as $k=>$v)
        {
            $ids = array();
            $ids = explode('_',$k);
            
            $dist_id = $ids[0];
            $stk_id = $ids[1];

            $strSql = " INSERT into 
                            im_control
                        SET 
                            dist_id = '".$dist_id."',
                            stk_id = '".$stk_id."',
                            im_enabled = '1'
                    ";
            $rsSql = mysql_query($strSql);
        }
    }
    $_SESSION['err']['text'] = 'Changes have been successfully saved.';
    $_SESSION['err']['type'] = 'success';
}
//Redirecting to ManageSubAdmin
header("location:im_control.php");
exit;
?>