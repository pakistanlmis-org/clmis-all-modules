<?php
include("../includes/classes/AllClasses.php");
//echo '<pre>';print_r($_REQUEST);exit;

$yr_sql="";
$years_arr = explode(',',$_REQUEST['base_years']);
$c=1;
foreach($years_arr as $year){
    $yr_sql .=" , `base_year_".$c++."`='".$year."' ";
}


$strSql = " INSERT INTO `fq_fp_products_data` 
            SET 
            `master_id`='".$_REQUEST['master_id']."'
            , `prod_id`='".$_REQUEST['prod_id']."'
            ".$yr_sql."
            , `average_amc_of_base_years`='".round($_REQUEST['amc'])."' 
   ";
//echo $strSql;exit;
$rsSql = mysql_query($strSql) or die('Err inserting fq_fp_products_data')  ;
$parent_id = mysql_insert_id();
?>
<script>
window.opener.location.reload();    
window.close();
</script>
<?php
//header("location:forecasting_master_demo.php?master_id=".$_REQUEST['master_id']);
//exit;

?>