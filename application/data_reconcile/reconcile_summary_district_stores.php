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
            (   SELECT DISTINCT
                    tbl_warehouse.wh_id
                FROM
                    tbl_warehouse
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                WHERE
                    tbl_warehouse.dist_id = data_mismatches.district AND
                    stakeholder.lvl = 4 AND
                    tbl_warehouse.stkid = data_mismatches.stakeholder

                limit 1
            ) as wh_id
    FROM
        data_mismatches
        INNER JOIN itminfo_tab ON data_mismatches.item_id = itminfo_tab.itm_id
    WHERE
        data_mismatches.match_type = 'hf_data_with_summary_district_store' AND
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

    $date_breakdown = explode('-',$reporting_date);
    $m = $date_breakdown[1];
    $y = $date_breakdown[0];
    
    $call_sp = " CALL REPUpdateSummaryDistrict('$warehouse_id', '$itmrec_id', '$m', '$y');  ";
    mysql_query($call_sp);
 
    
    $act = addslashes($call_sp).addslashes($call_sp2).addslashes($call_sp3);
    
    echo $call_sp.' For ID '.$row['pk_id'].'<br/>';
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
