<?php
echo '---Started reconciling---<br/>';
ini_set('max_execution_time', 150);
//2minutes max time
include("../includes/classes/Configuration.inc.php");
include("../includes/classes/AllClasses.php");

$limit = 30;
if(!empty($_REQUEST['limit'])) $limit = $_REQUEST['limit'];

// Get all districts
$qry = "
    SELECT
            data_mismatches.pk_id,
            data_mismatches.district,
            data_mismatches.reporting_date,
            data_mismatches.stakeholder,
            data_mismatches.item_id,
            data_mismatches.bad_value_1,
            data_mismatches.bad_value_2,
            data_mismatches.`status`,
            itminfo_tab.itmrec_id,
            (   SELECT
                    distinct tbl_warehouse.wh_id
                    FROM
                        tbl_warehouse
                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                    WHERE
                tbl_warehouse.dist_id = data_mismatches.district AND
                tbl_warehouse.is_active = 1 AND
                tbl_hf_data.reporting_date = data_mismatches.reporting_date 
                and tbl_warehouse.stkid = data_mismatches.stakeholder
                limit 1
            ) as wh_id
    FROM
        data_mismatches
        INNER JOIN itminfo_tab ON data_mismatches.item_id = itminfo_tab.itm_id
    WHERE
        data_mismatches.match_type = 'hf_data_with_wh_data' AND
        data_mismatches.`status` = 'MISMATCH'
    limit $limit

";
//echo $qry;exit;
$qryRes = mysql_query($qry);
$call_sp = $call_sp2 =$call_sp3 ='';
$summary = '';
$a=$mismatch_ids=array();
while ($row = mysql_fetch_array($qryRes)) {
    
    $mismatch_ids[$row['pk_id']]=$row['pk_id'];
    $a[]=$row;
    
    $warehouse_id = $row['wh_id'];
    $item_id = $row['item_id'];
    $itmrec_id = $row['itmrec_id'];
    $reporting_date = $row['reporting_date'];

    $call_sp2 = " CALL REPUpdateHFData('".$warehouse_id."', '".$item_id."', '".$reporting_date."');  ";
    mysql_query($call_sp2);
    $call_sp3 = " CALL REPUpdateDistrictStock('".$warehouse_id."', '".$item_id."', '".$reporting_date."');  ";
    mysql_query($call_sp3);
    
    $act = addslashes($call_sp).addslashes($call_sp2).addslashes($call_sp3);
    
    echo $act.' For ID '.$row['pk_id'].'<br/>';
    $upd=" UPDATE data_mismatches SET  status='Processed', action_taken =  '".$act."' WHERE pk_id='".$row['pk_id']."' ";
    //echo $upd;
    mysql_query($upd);
}
echo '<br/>----END-----';
//echo '<pre>';print_r($a);exit;
//echo $summary;exit;
//$b=mysql_query($summary);
//$c=mysql_fetch_array($b);
//echo $summary;
//echo '<pre>';print_r($b);
//mail("ahussain@ghsc-psm.org", 'Summary tables has been updated for last one hour health facility data', 'Summary tables are updated!');
