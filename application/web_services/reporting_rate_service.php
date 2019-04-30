<?php
//include("../includes/classes/AllClasses.php");
include("../includes/classes/Configuration.inc.php");
include("../includes/classes/db.php");
$dist           = $_REQUEST['district'];
$stakeholder    = $_REQUEST['stakeholder'];
$month          = $_REQUEST['month'];
$prov='';

$display_data = array();
//$display_data['reporting_rate_field']=0;
//$display_data['reporting_rate_district_store']=0;

$qry_s = "SELECT
            stakeholder.stkid,
            stakeholder.stkname
            FROM
            stakeholder
            WHERE
            stakeholder.stkid IN (1,2,7,73)
";
$Res4 =mysql_query($qry_s);
while($row_s = mysql_fetch_assoc($Res4))
{
    $display_data['reporting_rate_sdp'][$row_s['stkname']]=0;
    $display_data['reporting_rate_district'][$row_s['stkname']]=0;
} 



$qry_hf= "
   SELECT
        summary_district.province_id,
        summary_district.district_id,
        summary_district.stakeholder_id,
        summary_district.reporting_date,
        summary_district.reporting_rate,
        summary_district.total_health_facilities,
        stakeholder.stkname
        FROM
        summary_district
        INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
        WHERE
        summary_district.district_id = $dist AND
        summary_district.reporting_date = '".$month."' AND
        summary_district.item_id = 'IT-001' AND summary_district.stakeholder_id IN (1,2,7,73) ";
if(!empty($stakeholder)) $qry_hf.= " AND          summary_district.stakeholder_id = $stakeholder ";
//echo $qry_hf;
$Res2 =mysql_query($qry_hf);
while($row_hf = mysql_fetch_assoc($Res2))
{
    if($row_hf['total_health_facilities']==0 )
        $display_data['reporting_rate_sdp'][$row_hf['stkname']]=0;
    else
        $display_data['reporting_rate_sdp'][$row_hf['stkname']]=floatval($row_hf['reporting_rate']);
    $prov = $row_hf['province_id'];
}


$qry_dist= "
    SELECT
  
    Count(*) AS cc,
    s.stkname
    FROM
    tbl_wh_data
    RIGHT JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
    RIGHT JOIN stakeholder a ON tbl_warehouse.stkofficeid = a.stkid  
    INNER JOIN stakeholder s ON tbl_warehouse.stkid = s.stkid
    WHERE 
        tbl_warehouse.dist_id = $dist   AND
        tbl_wh_data.RptDate = '".$month."' AND
        tbl_wh_data.item_id = 'IT-001' AND
        a.lvl = 3 AND tbl_warehouse.stkid IN (1,2,7,73)
    group BY 
        a.stkname ";

if(!empty($stakeholder)) $qry_dist.= " AND tbl_warehouse.stkid = $stakeholder ";

//echo $qry_dist;
$Res3 =mysql_query($qry_dist);
while($row_dist = mysql_fetch_assoc($Res3))
{
    $perc = 0;
    if(!empty($row_dist['cc']) && $row_dist['cc']>0) $perc = 100;
    $display_data['reporting_rate_district'][$row_dist['stkname']]=$perc;
}
$display_data['district_id']=$dist;
$display_data['province_id']=$prov;




header('Content-Type: application/json');
echo json_encode($display_data);