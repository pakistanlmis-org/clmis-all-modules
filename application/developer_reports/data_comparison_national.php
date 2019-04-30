<?php
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");
?>
<html>
<html><?php include("menu.php"); ?>
    <h3 align="center">(6) NATIONAL Data Comparison between tbl_hf_data and  <span style="color:orange">summary_national</span></h3>
    <body>
        <form id="form1" name="form1" method="get" action="">
          <table width="100%" border="1">
            <tr>
              <td><label for="date">Date *</label></td>
              <td>
              <input type="text" name="date" id="date"  value="<?=(isset($_REQUEST['date'])?$_REQUEST['date']:'')?>"/></td>
              
              <td><input type="checkbox" name="show_all" id="show_all" <?=((!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on')?' checked ':'')?> />
              <label for="show_all">Show all</label></td>
            </tr>
            <tr>
              
              <td><label for="stk">Stakeholder</label></td>
              <td>
              <input type="text" name="stk" id="stk"  value="<?=(isset($_REQUEST['stk'])?$_REQUEST['stk']:'')?>"/></td>
              <td>
              <input type="submit" name="Submit" id="Submit" value="Submit" /></td>
            </tr>
          </table>
        </form>
<?php
if(empty($_REQUEST['date'])) 
{
    echo 'Please enter date to view report';
    exit;
}

if(!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on') $show_only_mismatch=false;
else $show_only_mismatch=true;
$date = $_REQUEST['date'];
//$prov=$_REQUEST['prov'];

//$dist = $_REQUEST['dist'];
$stk = $_REQUEST['stk'];

$and_clause='';
$and_clause2='';

                            

if(!empty($stk)){
    $and_clause.="  AND tbl_warehouse.stkid = $stk  ";  
    $and_clause2.="  and summary_national.stakeholder_id =$stk  ";      
    
}

$qry_hf= "SELECT
    
                tbl_hf_data.item_id,
                Sum(tbl_hf_data.opening_balance) as opening,
                Sum(tbl_hf_data.received_balance) as rcv,
                Sum(tbl_hf_data.issue_balance) as issued,
                Sum(tbl_hf_data.closing_balance) as closing,
                tbl_hf_data.reporting_date,
                tbl_warehouse.stkid
            FROM
                tbl_hf_data
                INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
            WHERE
                
                tbl_hf_data.reporting_date = '$date' 
                $and_clause    
            GROUP BY
                tbl_hf_data.reporting_date,
                tbl_warehouse.stkid,
                item_id
";
//Query result
//echo $qry_hf;
$Res =mysql_query($qry_hf);
$orig_data = array();

while($row = mysql_fetch_assoc($Res))
{
    $orig_data[$row['reporting_date']][$row['stkid']][$row['item_id']]=$row;
   //echo '<pre>';print_r($row);
}

$qry_summary_dist= "SELECT
                            summary_national.pk_id,
                            summary_national.item_id,
                            itminfo_tab.itm_id,
                            summary_national.stakeholder_id,
                            summary_national.reporting_date,
                            summary_national.consumption,
                            summary_national.avg_consumption,
                            summary_national.soh_national_store,
                            summary_national.soh_national_lvl,
                            summary_national.reporting_rate,
                            summary_national.total_health_facilities
                        FROM
                            itminfo_tab
                        INNER JOIN summary_national ON summary_national.item_id = itminfo_tab.itmrec_id
                        WHERE
                        
                            summary_national.reporting_date = '$date'                            
                            $and_clause2
                        order by
                            summary_national.reporting_date,
                            summary_national.stakeholder_id,
                            summary_national.item_id
";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$summary_national_data = array();

while($row = mysql_fetch_assoc($Res2))
{
    $summary_national_data[$row['reporting_date']][$row['stakeholder_id']][$row['itm_id']]=$row;
   //echo '<pre>';print_r($row);
}
//echo '<pre>';print_r($summary_national_data);print_r($orig_data);exit;
?>
<table border="1">
    <tr>
        
        <td>Date</td>
        <td>Stk</td>
        <td>Item</td>
        <td>Issuance (HFDATA)</td>
        <td>Consumption (Summary National)</td>
        <td>Result</td>
    </tr>
    <?php
    foreach($orig_data as $date => $date_data)
    {
            foreach($date_data as $stk_id =>$stk_data)
            {
                foreach($stk_data as $itm_id => $itm_data)
                {
                    
                    $hf_val  = $itm_data['issued'];
                    if(isset($summary_national_data[$date][$stk_id][$itm_id]['consumption']))
                        $summ_dist_val = $summary_national_data[$date][$stk_id][$itm_id]['consumption'];
                    else
                        $summ_dist_val = '';
                    
                    if($show_only_mismatch && (int)$hf_val==(int)$summ_dist_val) continue;
                    
                    echo '<tr>';
                    
                    echo '<td>'.$date.'</td>';
                    echo '<td>'.$stk_id.'</td>';
                    echo '<td>'.$itm_id.'</td>';
                    echo '<td>'.$hf_val.'</td>';
                    echo '<td>'.$summ_dist_val.'</td>';
                    echo '<td '.(((int)$hf_val==(int)$summ_dist_val)?' >ok':'bgcolor="#ffbfbf" >MISMATCH').'</td>';
                    echo ' </tr>';
                }
            }
        
    }
    ?>
</table>
    </body>
</html>
