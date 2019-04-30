<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;

if(empty($_REQUEST['master_id'])){
    $strSql = " INSERT INTO `fq_demographics_master` 
            SET 
            `date_of_data_entry`= now(), 
            `year`= '".$_REQUEST['year']."', 
            `source`= '".$_REQUEST['source']."', 
            `level`= '".$_REQUEST['office_level']."',
            `item_group`= '".$_REQUEST['task_order']."',
            `level_id`= '".$_REQUEST['province']."', 
            `created_by`= ".$_SESSION['user_id'].", 
            `created_at`= now()
   ";
    $rsSql = mysql_query($strSql) or die('Err inserting demographics P')  ;
    $parent_id = mysql_insert_id();
}
else{
    $parent_id = $_REQUEST['master_id'];
}
//echo $strSql;

foreach($_REQUEST as $field_name => $field_val)
{
    $temp = array();
    $temp = explode('_',$field_name);
    if($temp[0] == 'input')
    {
        $item_group =  $temp[1];
        $location   =  $temp[2];
        $col_id     =  $temp[3];
        if(empty($_REQUEST['master_id'])){
            $strSql2 = " INSERT INTO `fq_demographics_child` 
                        SET 
                        `master_id`= $parent_id, 
                        `location_id`= $location, 
                        `col_id`= '$col_id', 
                        `value`= '$field_val' ";
        }
        else{
            $strSql2 = " UPDATE `fq_demographics_child`  SET 
                        `value`= '$field_val' 
                        WHERE master_id = '".$_REQUEST['master_id']."'
                        AND `master_id`= $parent_id
                        AND `location_id`= $location
                        AND `col_id`= '$col_id'   ";
        }
        
        //echo $strSql2;exit;
        $rsSql2 = mysql_query($strSql2) or die('Err inserting demographics C') ;
    }
}
header("location:demographics_list.php?err=0");
exit;

?>