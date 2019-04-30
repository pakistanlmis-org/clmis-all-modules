<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;
$parent_id = $_REQUEST['forecasted_id'];

$input_fields_arr = array();
foreach($_REQUEST as $field_name => $field_val)
{
    $temp = array();
    $temp = explode('_',$field_name);
    if($temp[0] == 'input')
    {
        $col_id     =  $temp[1];
        $item_group =  $temp[2];
        $prod_id    =  $temp[3];
        $year       =  $temp[4];
        
        $input_fields_arr[$item_group][$prod_id][$year][$col_id]=$field_val;
    }
}
//echo '<pre>';print_r($input_fields_arr);exit;
foreach($input_fields_arr as $item_group => $item_group_data)
{
    foreach($item_group_data as $prod_id => $prod_data)
    {
       
        foreach($prod_data as $year => $year_data)
        {
             $cols = '';
            foreach($year_data as $col_id => $field_val)
            {
               //if(!empty($field_val))
                if(TRUE)
                {
                 $field_val= str_replace(',','',$field_val);
                 $field_val= mysql_real_escape_string ($field_val);
                 $cols .= ", `$col_id`='$field_val' "; 
                }
                 $strSql2 = " INSERT INTO `fq_quantification_child` 
                        SET 
                        `quantification_master_id`= $parent_id, 
                        `product_id`= '$prod_id' , 
                        `year`= '$year' 

                        $cols;
               ";
            }
            //echo '<br/>'.$strSql2;
            $rsSql2 = mysql_query($strSql2) or die('Err inserting quantification C') ;
        }
    }
}
//exit;
$_SESSION['err']['msg']='Data Saved Successfully.';
$_SESSION['err']['type']='success';
header("location:quantification.php?forecasted_id=".$parent_id);
exit;

?>