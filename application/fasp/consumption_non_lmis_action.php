<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;

$parent_id = $_REQUEST['master_id'];

foreach($_REQUEST as $field_name => $field_val)
{
    $temp = array();
    $temp = explode('_',$field_name);
    if($temp[0] == 'input')
    {
        $item_group =  $temp[1];
        $location   =  $temp[2];
        $col_id     =  $temp[3];
        if(empty($_REQUEST['already_saved'])){
        $strSql2 = " INSERT INTO `fq_non_lmis_consumption_child` 
                    SET 
                    `master_id`= $parent_id
                    , `location_id`= $location
                    , `product_id`= '$col_id' ,
                    `value`= '$field_val' 
           ";
        }
        else
        {
            $strSql2 = " UPDATE `fq_non_lmis_consumption_child`  SET 
                        `value`= '$field_val' 
                        WHERE `master_id`= $parent_id
                        AND `location_id`= $location
                        AND `product_id`= '$col_id'   ";
        }
        //echo $strSql2;exit;
        $rsSql2 = mysql_query($strSql2) or die('Err inserting consumption C') ;
    }
}
//exit;
header("location:forecasting_list.php?err=0");
exit;

?>