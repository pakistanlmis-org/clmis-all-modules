<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);
//exit;

$methods = 'Consumption';
if(!empty($_REQUEST['fc_methods']))$methods = $_REQUEST['fc_methods'];


$strSql = " INSERT INTO `fq_master_data` 
            SET 
            `base_year`='".(!empty($_REQUEST['base_year'])?$_REQUEST['base_year']:0)."'
            , `start_year`='".(!empty($_REQUEST['start_year'])?$_REQUEST['start_year']:0)."'
            , `end_year`='".(!empty($_REQUEST['end_year'])?$_REQUEST['end_year']:0)."'
            , `purpose`='".$_REQUEST['purpose']."'
            , `source`='".$_REQUEST['source']."'
            , `created_by`=".$_SESSION['user_id']."
            , `created_at`=now()
            , `stakeholders`='".implode(',',$_REQUEST['stakeholder'])."'
            , `province_id`='".$_REQUEST['province']."'
            , `level`='".(!empty($_REQUEST['office_level'])?$_REQUEST['office_level']:NULL)."'
            , `district_id`='".(!empty($_REQUEST['district'])?$_REQUEST['district']:0)."'
            , `forecasting_methods`='".$methods."'
            , `item_group`='".$_REQUEST['task_order']."'
   ";
//echo $strSql;
//exit;
$rsSql = mysql_query($strSql) or die('Err inserting demographics P')  ;
$parent_id = mysql_insert_id();

header("location:forecasting_master_demo.php?master_id=".$parent_id);
exit;

?>