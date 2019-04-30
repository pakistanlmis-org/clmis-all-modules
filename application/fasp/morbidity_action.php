<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;

$strSql = " INSERT INTO `fq_morbidity_master` 
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
//echo $strSql;
$rsSql = mysql_query($strSql) or die('Err inserting Morbidity P')  ;
$parent_id = mysql_insert_id();
foreach($_REQUEST as $field_name => $field_val)
{
    $temp = array();
    $temp = explode('_',$field_name);
    if($temp[0] == 'input')
    {
        $item_group =  $temp[1];
        $location   =  $temp[2];
        $col_id     =  $temp[3];
        $strSql2 = " INSERT INTO `fq_morbidity_child` 
                    SET 
                    `master_id`= $parent_id, 
                    `location_id`= $location, 
                    `product_id`= '$col_id', 
                    `value`= '$field_val';
           ";
        //echo $strSql2;exit;
        $rsSql2 = mysql_query($strSql2) or die('Err inserting Morbidity C') ;
    }
}
header("location:morbidity.php?err=0");
exit;

?>