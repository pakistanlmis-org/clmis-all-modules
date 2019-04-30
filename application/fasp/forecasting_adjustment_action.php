<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;

$parent_id = $_REQUEST['id'];

$qry = "   SELECT * FROM `fq_forecasting_child` 
           WHERE 
                `master_id`     = $parent_id ";
$rsQry = mysql_query($qry) or die();
$fq_master = mysql_fetch_assoc($rsQry);


$input_fields_arr = array();
foreach($_REQUEST as $field_name => $field_val)
{
    $temp = array();
    $temp = explode('_',$field_name);
    if($temp[0] == 'input')
    {
        $item_group =  $temp[1];
        $location   =  $temp[2];
        $prod_id    =  $temp[3];
        $year       =  $temp[4];
        $col_id1    =  $temp[5];
        $col_id2    =  isset($temp[6])?$temp[6]:'';
        
        if(!empty($col_id2)) $col_id = $col_id1.'_'.$col_id2;
        else $col_id = $col_id1;
        
        $input_fields_arr[$item_group][$location][$prod_id][$year][$col_id]=$field_val;
    }
}

//echo '<pre>';print_r($input_fields_arr);exit;
foreach($input_fields_arr as $item_group => $item_group_data)
{
    foreach($item_group_data as $location => $loc_data)
    {
        foreach($loc_data as $prod_id => $prod_data)
        {
            
            foreach($prod_data as $year => $year_val)
            {
                $cols = '';
                $cols_arr = array();
                foreach($year_val as $col_id => $field_val)
                {
                   //if(!empty($field_val))
                   if(TRUE)
                   {
                        $field_val= str_replace(',','',$field_val);
                        $field_val= mysql_real_escape_string ($field_val);
                        $cols .= ", `$col_id`='$field_val' "; 
                        $cols_arr[] = " `$col_id`='$field_val' "; 
                   }
                }
            
                if(!empty($fq_master)){
                    $strSql2 = " UPDATE `fq_forecasting_child` 
                                    SET ".implode(',',$cols_arr)."
                                 WHERE 
                                    `master_id`     = $parent_id AND 
                                    `location_id`   = $location AND
                                    `product_id`    = '$prod_id' AND 
                                    `year`          = '$year' 
                       ";
                }
                else{
                    $strSql2 = " INSERT INTO `fq_forecasting_child` 
                                 SET 
                                `master_id`     = $parent_id, 
                                `location_id`   = $location, 
                                `product_id`    = '$prod_id', 
                                `year`          = '$year', 
                                ".implode(',',$cols_arr).";
                       ";
                }
                //echo $strSql2.'</br>';
                $rsSql2 = mysql_query($strSql2) or die('Err inserting forecasting C') ;
            }
        }
    }
}
//exit;
$_SESSION['err']['msg']='Data Saved Successfully.';
$_SESSION['err']['type']='success';
header("location:forecasting_list.php");
exit;

?>