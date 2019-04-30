<?php
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");

?>
<html><?php include("menu.php"); ?>
    <h3 align="center">(4) District Wise Data Comparison between tbl_hf_data and <span style="color:red"> summary_district</span> . Only District Stores . Closing Balance</h3>
    <body>
        <form id="form1" name="form1" method="get" action="">
          <table width="100%" border="1">
            <tr>
              <td><label for="date">Date *</label></td>
              <td>
              <input type="text" name="date" id="date"  value="<?=(isset($_REQUEST['date'])?$_REQUEST['date']:'')?>"/></td>
              <td><label for="prov">Province *</label></td>
              <td>
              <input type="text" name="prov" id="prov"  value="<?=(isset($_REQUEST['prov'])?$_REQUEST['prov']:'')?>"/></td>
              <td><input type="checkbox" name="show_all" id="show_all" <?=((!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on')?' checked ':'')?> />
              <label for="show_all">Show all</label></td>
            </tr>
            <tr>
              <td><label for="dist">District ID</label></td>
              <td>
              <input type="text" name="dist" id="dist"  value="<?=(isset($_REQUEST['dist'])?$_REQUEST['dist']:'')?>"/></td>
              <td><label for="stk">Stakeholder</label></td>
              <td>
              <input type="text" name="stk" id="stk"  value="<?=(isset($_REQUEST['stk'])?$_REQUEST['stk']:'')?>"/></td>
              <td>
              <input type="submit" name="Submit" id="Submit" value="Submit" /></td>
            </tr>
            <tr><td colspan="5"><a target="_blank" href="../data_reconcile/reconcile_summary_district_stores.php">Fix this summary district data.</a></td></tr>

          </table>
        </form>
<?php
if(empty($_REQUEST['date'])) 
{
    echo 'Please enter date to view report';
    exit;
}
//if(empty($_REQUEST['prov'])) 
//{
//    echo 'Please enter Province ID to view report';
//    exit;
//}
if(!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on') $show_only_mismatch=false;
else $show_only_mismatch=true;
$date = $_REQUEST['date'];


$dist = $_REQUEST['dist'];
$stk = $_REQUEST['stk'];

$and_clause='';
$and_clause2='';

                            
if(!empty($dist)){
    $and_clause.=" AND tbl_warehouse.dist_id = $dist";
    $and_clause2.="  and district_id=$dist  ";
}
if(!empty($stk)){
    $and_clause.="  AND tbl_warehouse.stkid = $stk  ";  
    $and_clause2.="  and summary_district.stakeholder_id =$stk  ";      
    
}
if(!empty($_REQUEST['prov'])){
    $prov=$_REQUEST['prov'];
    $and_clause.="  and tbl_warehouse.prov_id = $prov   ";  
    $and_clause2.="  and summary_district.province_id = $prov  ";      
    
}

        
$qry_hf= "SELECT
                tbl_warehouse.dist_id,
                tbl_warehouse.stkid,
                tbl_wh_data.RptDate,
                itminfo_tab.itm_id as item_id,
                tbl_wh_data.wh_cbl_a as closing_balance,
                tbl_wh_data.wh_obl_a,
                tbl_warehouse.wh_id,
                tbl_warehouse.wh_name,
                tbl_warehouse.prov_id as province
            FROM
                tbl_wh_data
                INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
            WHERE
                
                tbl_wh_data.RptDate = '$date' AND
                stakeholder.lvl = 3
                $and_clause    
            GROUP BY
                tbl_warehouse.dist_id,

                tbl_wh_data.RptDate,
                tbl_warehouse.stkid,
                item_id
";
//Query result
//echo $qry_hf;
$Res =mysql_query($qry_hf);
$orig_data = array();

while($row = mysql_fetch_assoc($Res))
{
    $orig_data[$row['dist_id']][$row['RptDate']][$row['stkid']][$row['item_id']]=$row;
   //echo '<pre>';print_r($row);
}

$qry_summary_dist= "SELECT
                            summary_district.pk_id,
                            summary_district.item_id,
                            summary_district.stakeholder_id,
                            summary_district.reporting_date,
                            summary_district.province_id,
                            summary_district.district_id,
                            summary_district.consumption,
                            summary_district.avg_consumption,
                            summary_district.soh_district_store,
                            summary_district.soh_district_lvl,
                            summary_district.reporting_rate,
                            summary_district.total_health_facilities,
                            itminfo_tab.itm_id
                        FROM
                            summary_district
                            INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
                        WHERE
                            summary_district.reporting_date = '$date'
                            
                            $and_clause2
                        order by

                            summary_district.stakeholder_id,
                            summary_district.item_id
";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$summary_dist_data = array();

while($row = mysql_fetch_assoc($Res2))
{
    $summary_dist_data[$row['district_id']][$row['reporting_date']][$row['stakeholder_id']][$row['itm_id']]=$row;
   //echo '<pre>';print_r($row);
}
//echo '<pre>';print_r($summary_dist_data);
?>
<table border="1">
    <tr>
        <td>District</td>
        <td>Date</td>
        <td>Stk</td>
        <td>Item</td>
        <td>Closing Balance (tbl_wh_data District Store Only)</td>
        <td>Closing Balance (Summary District District Store Only)</td>
        <td>Result</td>
    </tr>
    <?php
    $tot_matches = $mismatches_count = 0;
    foreach($orig_data as $dist_id => $dist_data)
    {
        foreach($dist_data as $date => $date_data)
        {
            foreach($date_data as $stk_id =>$stk_data)
            {
                foreach($stk_data as $itm_id => $itm_data)
                {
                    
                    $tot_matches++;
                    $hf_val  = $itm_data['closing_balance'];
                    if(isset($summary_dist_data[$dist_id][$date][$stk_id][$itm_id]['soh_district_store']))
                        $summ_dist_val = $summary_dist_data[$dist_id][$date][$stk_id][$itm_id]['soh_district_store'];
                    else
                        $summ_dist_val = '';
                    
                    if($show_only_mismatch && (int)$hf_val==(int)$summ_dist_val) continue;
                    
                    echo '<tr>';
                    echo '<td>'.$dist_id.'</td>';
                    echo '<td>'.$date.'</td>';
                    echo '<td>'.$stk_id.'</td>';
                    echo '<td>'.$itm_id.'</td>';
                    echo '<td>'.$hf_val.'</td>';
                    echo '<td>'.$summ_dist_val.'</td>';
                    echo '<td '.(((int)$hf_val==(int)$summ_dist_val)?' >ok':'bgcolor="#ffbfbf" >MISMATCH').'</td>';
                    echo ' </tr>';
                    
                    
                    //inserting log in mismatches table...
                    if( (int)$hf_val!=(int)$summ_dist_val  )
                    {
                        $mismatches_count++;
                        $del="DELETE FROM data_mismatches WHERE "
                                . " match_type = 'hf_data_with_summary_district_store' AND  "
                                . " district = '".$dist_id."' AND "
                                . " reporting_date = '".$date."' AND  "
                                . " stakeholder = '".$stk_id."'AND "
                                . " item_id = '".$itm_id."' AND status='MISMATCH' ";
                        mysql_query($del);

                        $ins = "INSERT INTO `data_mismatches` 
                                ( `match_type`, `province`, `district`, `stakeholder`, `reporting_date`, `hf_type`, `item_id`,
                                `table_1`, `table_2`, `bad_value_1`, `bad_value_2`, `ok_value_1`, `ok_value_2`, `status`) 
                                VALUES 
                                ( 'hf_data_with_summary_district_store', '".$itm_data['province']."', '".$dist_id."', '".$stk_id."', '".$date."', NULL , '".$itm_id."', 
                                 'tbl_hf_data', 'tbl_hf_type_data', '".$hf_val."', '".$summ_dist_val."', NULL, NULL, 'MISMATCH');";
                        mysql_query($ins);
                    }
                    //end of inserting log...
                }
            }
        }
    }
    
    
    $ins = " INSERT INTO `data_mismatches_log` "
            . "( `month`, `province`, `district`, `stakeholder`, `mismatches_count`, `out_of_possibilites_checked`,`comparison_type` ) "
            . "VALUES ( '$date', '".$_REQUEST['prov']."', '".$dist."', '".$stk."', '".$mismatches_count."', '".$tot_matches."' ,'hf_data_with_summary_district_store'); ";
    mysql_query($ins);
    
    ?>
</table>
    </body>
</html>
