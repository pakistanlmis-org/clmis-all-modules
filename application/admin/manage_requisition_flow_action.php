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


if (isset($_REQUEST['submit'])) {
    
    
    $strSql = "  UPDATE requisition_module_flow   SET is_active=0 
                    WHERE  stk_id=".$_REQUEST['stk_id']." AND prov_id=".$_REQUEST['prov_id']."   ";
    //echo $strSql;exit;
    $rsSql = mysql_query($strSql) ;
    
    if(isset($_REQUEST['enable']))
    {
    foreach($_REQUEST['enable'] as $k=>$v)
    {
    $ids = array();
    $ids = explode('_',$k);
        
    $strSql = " SELECT
                requisition_module_flow.pk_id,
                requisition_module_flow.action_id,
                requisition_module_flow.can_submit_to,
                requisition_module_flow.is_active,
                requisition_module_flow.prov_id,
                requisition_module_flow.stk_id
                FROM
                requisition_module_flow
                WHERE
                requisition_module_flow.stk_id = $ids[0] AND
                requisition_module_flow.prov_id = $ids[1] AND
                requisition_module_flow.action_id = $ids[2] AND
                requisition_module_flow.can_submit_to = $ids[3] ";
    //echo $strSql;exit;
    $rsSql = mysql_query($strSql);
    //$res = mysql_fetch_assoc($rsSql);
    $num = mysql_num_rows($rsSql);
 
    if($num > 0)
    {
        $strSql = "  UPDATE requisition_module_flow   SET  is_active=1 
                        WHERE  stk_id=$ids[0] AND prov_id=$ids[1] AND action_id=$ids[2] AND can_submit_to=$ids[3] ";
        //echo $strSql;
        $rsSql = mysql_query($strSql) or die("Error Update");
    }
    else
    {
        $strSql =  " INSERT INTO requisition_module_flow (stk_id,prov_id,action_id,can_submit_to,is_active) VALUES($ids[0],$ids[1],$ids[2],$ids[3] , 1)  ";
        $rsSql = mysql_query($strSql) or die("Error insert");
    }   
    }
}
}
//Redirecting to ManageSubAdmin
header("location:manage_requisition_flow.php?submit=GO&stk_sel=".$_REQUEST['stk_id']."&prov_sel=".$_REQUEST['prov_id']);
exit;
?>