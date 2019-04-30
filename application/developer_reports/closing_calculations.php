<?php
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");
?>
<html>
<html><?php include("menu.php"); ?>
    <h3 align="center">(8) Closing Balance Calculations  :  <span style="color:green">(OB + R - I + Adj_a - Adj_b = CB )   tbl_hf_data</span></h3>
    <body>
        <form id="form1" name="form1" method="get" action="">
          <table width="100%" border="1">
            <tr>
              <td><label for="date">Year *</label></td>
              <td>
              <input type="text" name="date" id="date"  value="<?=(isset($_REQUEST['date'])?$_REQUEST['date']:'')?>"/></td>
              <td><label for="prov">Province *</label></td>
              <td>
              <input type="text" name="prov" id="prov"  value="<?=(isset($_REQUEST['prov'])?$_REQUEST['prov']:'')?>"/></td>
              <td><input type="checkbox" name="show_all" id="show_all" <?=((!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on')?' checked ':'')?> />
              <label for="show_all">Show all</label></td>
            </tr>
            <tr>
              <td> </td>
              <td> </td>
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
//if(empty($_REQUEST['prov'])) 
//{
//    echo 'Please enter Province ID to view report';
//    exit;
//}
if(!empty($_REQUEST['show_all']) && $_REQUEST['show_all']=='on') $show_only_mismatch=false;
else $show_only_mismatch=true;
$date = $_REQUEST['date'];
$date_start = $date.'-01-01';
$date_end   = $date.'-12-01';
        

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






$qry_summary_dist= "SELECT
                            tbl_wh_data.item_id,
                            tbl_wh_data.wh_obl_a as opening,
                            tbl_wh_data.wh_received as rcvd,
                            tbl_wh_data.wh_issue_up as issued,
                            tbl_wh_data.wh_cbl_a as closing,
                            tbl_wh_data.RptDate,
                            tbl_warehouse.wh_name,
                            itminfo_tab.itm_name,
tbl_warehouse.dist_id,
tbl_warehouse.prov_id,
tbl_warehouse.stkid,
itminfo_tab.itm_id,
tbl_wh_data.wh_adja,
tbl_wh_data.wh_adjb,
tbl_warehouse.wh_id
                        FROM
                        tbl_wh_data
                        INNER JOIN tbl_warehouse ON tbl_wh_data.wh_id = tbl_warehouse.wh_id
                        INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                        WHERE
                             tbl_wh_data.RptDate between '$date_start' AND '$date_end'
                            and itminfo_tab.itm_category = 1 AND
stakeholder.lvl = 4
                            $and_clause
                        order by
                            tbl_warehouse.wh_name,
                            tbl_wh_data.RptDate,
                            itminfo_tab.itm_id
                            
                            ";
//Query result
//echo $qry_summary_dist;exit;
$Res2 =mysql_query($qry_summary_dist);
$raw_data = $months  =$wh_arr = $itm_arr = array();

while($row = mysql_fetch_assoc($Res2))
{
    $raw_data[$row['wh_id']][$row['itm_id']][$row['RptDate']]=$row;
    $months[$row['RptDate']]=$row['RptDate'];
    $wh_arr[$row['wh_id']]=$row['wh_name'];
    $itm_arr[$row['itm_id']]=$row['itm_name'];
   //echo '<pre>';print_r($row);
}






//echo '<pre>';print_r($raw_data);exit;
?>
<table border="1">
    <tr>
        <td>District</td>
        <td>Product</td>
        <?php
        foreach($months as $month1 => $month2)
        {
           echo '<td><b>'.$month1.'</b></td>';
        }
        ?>
    </tr>
    <?php
    $mismatches_count=$total_count = 0;
    foreach($raw_data as $wh_id => $wh_data)
    {
        foreach($wh_data as $itm_id => $itm_data)
        {
             echo '<tr>';
               echo '<td><b>'.$wh_id.' - '.$wh_arr[$wh_id].'</b></td>';
               echo '<td><b>'.$itm_id.' - '.$itm_arr[$itm_id].'</b></td>';
                foreach($itm_data as $month => $row)
                {
                    $total_count++;
                   ///$calc = $row['opening'] + $row['rcvd'] - $row['issued'] ;
                   $calc = $row['opening'] + $row['rcvd'] - $row['issued'] +$row['wh_adja'] - $row['wh_adjb'];
                   $cb = $row['closing'];
                   
                   if($calc == $cb){
                     //echo '<td>'.$cb.'</td>';
                     echo '<td>'.$calc.' : '.$cb.'</td>';
                   }
                   else
                   {
                       echo '<td style="color:red">'.$calc.' : '.$cb.' , '.$row['wh_adja'].' : '.$row['wh_adjb'].'</td>';
                       $mismatches_count++;
                   }
                }
            
               echo '</tr>';
        }
    }
   
    ?>
</table>
        <div><h3>Mismatches in this data : <?=$mismatches_count?> / <?=$total_count?></h3></div>
    </body>
</html>
