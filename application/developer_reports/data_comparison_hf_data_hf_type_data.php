<?php
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");
?>
<html>
<html><?php include("menu.php"); ?>
    <h3 align="center">(1) District Wise Data Comparison between tbl_hf_data and  <span style="color:green">tbl_hf_type_data</span></h3>
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
            <tr><td colspan="5"><a target="_blank" href="../data_reconcile/reconcile_hf_type_data.php">Fix this data.</a></td></tr>
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
    $and_clause2.="  and tbl_hf_type_data.district_id=$dist  ";
}
if(!empty($stk)){
    $and_clause.="  AND tbl_warehouse.stkid = $stk  ";  
    $and_clause2.="  and tbl_hf_type_rank.stakeholder_id =$stk  ";      
    
}
if(!empty($_REQUEST['prov'])){
    $prov=$_REQUEST['prov'];
    $and_clause.="  and tbl_warehouse.prov_id = $prov   ";  
    $and_clause2.="   AND tbl_locations.ParentID  = $prov  ";      
    
}

        
$qry_hf= "SELECT
                tbl_warehouse.dist_id,
                tbl_hf_data.item_id,
                Sum(tbl_hf_data.opening_balance) as opening,
                Sum(tbl_hf_data.received_balance) as rcv,
                Sum(tbl_hf_data.issue_balance) as issued,
                Sum(tbl_hf_data.closing_balance) as closing,
                tbl_hf_data.reporting_date,
                tbl_warehouse.hf_type_id,
                tbl_hf_type.hf_type,
tbl_warehouse.prov_id AS province
            FROM
                tbl_hf_data
                INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
		INNER JOIN tbl_hf_type ON tbl_hf_type.pk_id = tbl_warehouse.hf_type_id
            WHERE
                
                tbl_hf_data.reporting_date = '$date' 
                $and_clause    
            GROUP BY
                tbl_warehouse.dist_id,
                tbl_hf_data.reporting_date,
                tbl_warehouse.hf_type_id,
                item_id
";
//Query result
//echo $qry_hf;exit;
$Res =mysql_query($qry_hf);
$orig_data = $hf_type_arr =  array();

while($row = mysql_fetch_assoc($Res))
{
    $orig_data[$row['dist_id']][$row['reporting_date']][$row['hf_type_id']][$row['item_id']]=$row;
    $hf_type_arr[$row['hf_type_id']] = $row['hf_type'];
   //echo '<pre>';print_r($row);
}
//echo '<pre>';print_r($hf_type_arr);
$qry_summary_dist= "SELECT
                        tbl_locations.LocName,
                        tbl_hf_type.hf_type,
                        tbl_hf_type_data.opening_balance AS opening,
                        tbl_hf_type_data.received_balance AS rcv,
                        tbl_hf_type_data.issue_balance AS issued,
                        tbl_hf_type_data.closing_balance AS closing,
                        tbl_hf_type_data.facility_type_id,
                        tbl_hf_type_data.item_id,
                        tbl_hf_type_data.district_id,
                        tbl_hf_type_data.reporting_date
                    FROM
                            tbl_hf_type_data
                    INNER JOIN tbl_hf_type ON tbl_hf_type.pk_id = tbl_hf_type_data.facility_type_id
                    INNER JOIN tbl_locations ON tbl_locations.PkLocID = tbl_hf_type_data.district_id
                    INNER JOIN tbl_hf_type_rank ON tbl_hf_type.pk_id = tbl_hf_type_rank.hf_type_id
                    WHERE
                        tbl_hf_type_data.reporting_date = '$date'
                        $and_clause2        
                        
                    ORDER BY
                        tbl_hf_type_data.item_id,
                        tbl_locations.LocName ASC,
                        tbl_hf_type_rank.hf_type_rank ASC

";
//Query result
//echo $qry_summary_dist;exit;
$Res2 =mysql_query($qry_summary_dist);
$summary_dist_data = array();

while($row = mysql_fetch_assoc($Res2))
{
    $summary_dist_data[$row['district_id']][$row['reporting_date']][$row['facility_type_id']][$row['item_id']]=$row;
    $hf_type_arr[$row['facility_type_id']] = $row['hf_type'];
   //echo '<pre>';print_r($row);
}
//echo '<pre>';print_r($orig_data);
//echo '<pre>';print_r($summary_dist_data);
?>
<table border="1">
    <tr>
        <td>District</td>
        <td>Date</td>
        <td>HF Type</td>
        <td>Itm</td>
        <td>(TBL_HF_DATA)</td>
        <td>(TBL_HF_TYPE_DATA)</td>
        <td>Result</td>
    </tr>
    <?php
    $tot_matches = $mismatches_count = 0;
    foreach($orig_data as $dist_id => $dist_data)
    {
        foreach($dist_data as $date => $date_data)
        {
            foreach($date_data as $hf_type =>$type_data)
            {
                foreach($type_data as $itm_id => $itm_data)
                {
                    $tot_matches++;
                    
                    $hf_val  = $itm_data['issued'];
                    if(!empty($summary_dist_data[$dist_id][$date][$hf_type][$itm_id]['issued']))
                        $summ_dist_val = $summary_dist_data[$dist_id][$date][$hf_type][$itm_id]['issued'];
                    else
                        $summ_dist_val = '';
                    
                    if($show_only_mismatch && (int)$hf_val==(int)$summ_dist_val) continue;
                    
                    echo '<tr>';
                    echo '<td>'.$dist_id.'</td>';
                    echo '<td>'.$date.'</td>';
                    echo '<td>'.$hf_type.'</td>';
                    echo '<td>'.$itm_id.'</td>';
                    echo '<td>'.$hf_val.'</td>';
                    echo '<td>'.$summ_dist_val.'</td>';
                    echo '<td '.(((int)$hf_val==(int)$summ_dist_val)?' >ok':'bgcolor="#ffbfbf" >MISMATCH').'</td>';
                    echo ' </tr>';
                    
                    
                    //inserting log in mismatches table...
                    if( (int)$hf_val!=(int)$summ_dist_val)
                    {
                        $mismatches_count++;
                        $del="DELETE FROM data_mismatches WHERE "
                                . " match_type = 'hf_data_with_hf_type_data' AND  "
                                . " district = '".$dist_id."' AND "
                                . " reporting_date = '".$date."' AND  "
                                . " hf_type = '".$hf_type."'AND "
                                . " item_id = '".$itm_id."' AND status='MISMATCH' ";
                        mysql_query($del);

                        $ins = "INSERT INTO `data_mismatches` 
                                ( `match_type`, `province`, `district`, `stakeholder`, `reporting_date`, `hf_type`, `item_id`,
                                `table_1`, `table_2`, `bad_value_1`, `bad_value_2`, `ok_value_1`, `ok_value_2`, `status`) 
                                VALUES 
                                ( 'hf_data_with_hf_type_data', '".$itm_data['province']."', '".$dist_id."', NULL, '".$date."', '".$hf_type."', '".$itm_id."', 
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
            . "VALUES ( '$date', '".$_REQUEST['prov']."', '".$dist."', '".$stk."', '".$mismatches_count."', '".$tot_matches."' ,'hf_data_with_hf_type_data'); ";
    mysql_query($ins);
    ?>
</table>
    </body>
</html>
