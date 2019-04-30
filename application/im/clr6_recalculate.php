<?php
//recalculation of clr6
//include AllClasses
include("../includes/classes/AllClasses.php");

$master_id = $_REQUEST['id'];
$qry= " SELECT
        clr_master.pk_id,
        clr_master.requisition_num,
        clr_master.requisition_to,
        clr_master.wh_id,
        clr_master.stk_id,
        clr_master.date_from,
        clr_master.date_to,
        tbl_warehouse.wh_id,
        tbl_warehouse.wh_name,
        tbl_warehouse.dist_id,
        tbl_warehouse.prov_id,
        tbl_warehouse.stkid,
        clr_master.approval_status
        FROM
        clr_master
        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
        where pk_id='".$master_id."'  ";
$qryRes = mysql_fetch_assoc(mysql_query($qry));

$calculate_month = date('Y-m-d', strtotime("-1 month", strtotime($qryRes['date_from'])));
$reportingDate = $calculate_month;
$distId = $qryRes['dist_id'];
$stkid = $qryRes['stkid'];
                  
//select query

  $qry1 = "SELECT
                itminfo_tab.itm_id,
                itminfo_tab.itmrec_id,
                itminfo_tab.itm_name,
                itminfo_tab.itm_type,
                itminfo_tab.method_type,
                itminfo_tab.generic_name,
                itminfo_tab.frmindex,
                SUM(IF(stakeholder.lvl = 4, tbl_wh_data.wh_issue_up, 0)) AS Consumption,
                SUM(IF(stakeholder.lvl = 3 AND tbl_wh_data.RptDate = '$reportingDate', tbl_wh_data.wh_cbl_a, 0)) AS SOHDistrict,
                SUM(IF(stakeholder.lvl = 4 AND tbl_wh_data.RptDate = '$reportingDate', tbl_wh_data.wh_cbl_a, 0)) AS SOHField
        FROM
                tbl_warehouse
        INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
        INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
        WHERE
                tbl_warehouse.dist_id = $distId
        AND tbl_warehouse.stkid = $stkid
        AND tbl_wh_data.RptDate BETWEEN DATE_ADD('$reportingDate', INTERVAL -2 MONTH) AND '$reportingDate'
        AND stakeholder.lvl IN (3, 4)
        AND itminfo_tab.itm_category = 1
        GROUP BY
                itminfo_tab.itm_id
        ORDER BY
                itminfo_tab.frmindex ASC";
 

    //query result
    $qryRes = mysql_query($qry1);
    //number of record
    $num = mysql_num_rows($qryRes);
    //chech if record exists
    if ($num > 0) {
        //fetch results

       while ($row = mysql_fetch_array($qryRes)) {

            //item ids
            $itemIds[] = $row['itm_id'];
            $product[$row['method_type']][] = $row['itm_name'];
            if ($row['itm_id'] == 8) {
                        //consumption 
                        $consumption = '0';
                        //SOH District 
                        $SOHDistrict = '0';
                        //SOH Field 
                        $SOHField = '0';
            } else {
                        //consumption 
                        $consumption = (!empty($row['Consumption'])) ? round($row['Consumption']) : 0;
                        //SOH District 
                        $SOHDistrict = (!empty($row['SOHDistrict'])) ? round($row['SOHDistrict']) : 0;
                        //SOH Field 
                        $SOHField = (!empty($row['SOHField'])) ? round($row['SOHField']) : 0;
            }

            $desired = $consumption * 2;
            $total   = $SOHDistrict+$SOHField;
            $replenishment = $desired - $total ;


           $qry2= " UPDATE clr_details SET   
                            avg_consumption = '".$consumption."',   
                            soh_dist = '".$SOHDistrict."',   
                            soh_field = '".$SOHField."',   
                            total_stock = '".$total."',   
                            desired_stock = '".$desired."',   
                            replenishment = '".$replenishment."'
                          WHERE
                                pk_master_id = '".$master_id."' 
                                AND itm_id = '".$row['itm_id']."' 
                                AND approval_status <> 'Approved'
                        ";
             mysql_query($qry2);
        }

    } 
    
    $ref_page = $_REQUEST['referral'];
    $str  = $_SERVER['QUERY_STRING'];
    $ex = explode('&',$str);
    foreach ($ex as $k=>$v)
    {
        $e = explode('=',$v);
        if($e[0] == 'referral' )
            unset($ex[$k]);
    }
    $str1 = implode('&',$ex);

   
    $url = "im/".$ref_page.".php?".$str1;
    header("location: " . APP_URL . $url);
?>