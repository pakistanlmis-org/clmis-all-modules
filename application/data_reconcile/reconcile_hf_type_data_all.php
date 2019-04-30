<?php

ini_set('max_execution_time', 600);
//2minutes max time
include("../includes/classes/Configuration.inc.php");
include("../includes/classes/AllClasses.php");

if(empty($_REQUEST['pro']) ||empty($_REQUEST['stk']) ||empty($_REQUEST['date']) ){
    echo 'Please provide : pro , stk ,date';
    exit;
}
echo '---Started reconciling ALL---<br/>';
$pro    = $_REQUEST['pro'];
$stk    = $_REQUEST['stk'];
$date    = $_REQUEST['date'];
@$last_dist = $_REQUEST['last_dist'];
@$dist = $_REQUEST['dist'];

$products = array(1,3,5,7,9);

$limit = 30;
if(!empty($_REQUEST['limit'])) $limit = $_REQUEST['limit'];

// Get all districts
$qry = "
    SELECT
        tbl_warehouse.stkid,
        tbl_warehouse.prov_id,
        tbl_warehouse.dist_id,
        tbl_warehouse.hf_type_id,
        tbl_warehouse.wh_name,
        tbl_warehouse.wh_id
        FROM
        tbl_warehouse
        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
        WHERE
        tbl_warehouse.prov_id = $pro AND
        tbl_warehouse.stkid = $stk AND
        stakeholder.lvl = 7 ";

    if(!empty($dist)){ $qry .=" AND tbl_warehouse.dist_id = $dist ";}
    elseif(!empty($last_dist)) $qry .=" AND tbl_warehouse.dist_id > $last_dist ";
    
        

    $qry .="    GROUP BY
                    tbl_warehouse.stkid,
                    tbl_warehouse.prov_id,
                    tbl_warehouse.dist_id,
                    tbl_warehouse.hf_type_id
                ORDER BY
                    tbl_warehouse.dist_id ASC,
                    tbl_warehouse.hf_type_id ASC

";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$call_sp = $call_sp2 =$call_sp3 ='';
$summary = '';
$a=$mismatch_ids=array();
while ($row = mysql_fetch_array($qryRes)) {
    foreach($products as $itm_id){
    
    $upd=" INSERT INTO `data_mismatches` (`match_type`, `province`, `district`, `stakeholder`, `reporting_date`, 
                `hf_type`, `item_id`, `table_1`, `table_2`, `bad_value_1`, `bad_value_2`, `ok_value_1`, `ok_value_2`, `status`) 
            VALUES ( 'hf_type_data_all', '".$pro."', '".$row['dist_id']."', ".$stk.", '".$date."',
                '".$row['hf_type_id']."', '".$itm_id."', 'tbl_hf_data', 'tbl_hf_type_data', NULL, NULL, NULL, NULL, 'Processing');
        ";
    //echo $upd;
    mysql_query($upd);
    $mm_id = mysql_insert_id();
    $mismatch_ids[$mm_id] = $mm_id;
    $a[] = $row;
    
    $warehouse_id = $row['wh_id'];
    $item_id = $itm_id;
    $reporting_date = $date;

    $call_sp = " CALL REPUpdateHFTypeFromHF('".$warehouse_id."', '".$item_id."', '".$reporting_date."');  ";
    mysql_query($call_sp);
    $call_sp2 = " CALL REPUpdateHFData('".$warehouse_id."', '".$item_id."', '".$reporting_date."');  ";
    mysql_query($call_sp2);
    $call_sp3 = " CALL REPUpdateDistrictStock('".$warehouse_id."', '".$item_id."', '".$reporting_date."');  ";
    mysql_query($call_sp3);
    
    $act = addslashes($call_sp).addslashes($call_sp2).addslashes($call_sp3);
    
    ob_start();
    echo $call_sp.' For ID '.$mm_id.'<br/>';
    ob_flush();
    $upd=" UPDATE data_mismatches SET  status='Processed', action_taken =  '".$act."' WHERE pk_id='".$mm_id."' ";
    //echo $upd;
    mysql_query($upd);
    }
}

echo '<br/>----END-----';
ob_end_flush(); 
