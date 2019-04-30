<?php
//echo '<pre>';print_r($_REQUEST);exit;
ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH."includes/classes/db.php");
include(PUBLIC_PATH."/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption='';

$prov_name= $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$stakeholder = $_REQUEST['stakeholder'];
$stk_name = $_REQUEST['stk_name'];
$prod_id = $_REQUEST['prod_id'];
$prod_name = $_REQUEST['prod_name'];

   $qry = "
            SELECT
              summary_district.district_id,
              tbl_locations.LocName,
              Province.LocName as province_name,
              stakeholder.stkname,
              itminfo_tab.itmrec_id,
	      summary_district.soh_district_store,
	      summary_district.soh_district_store / summary_district.avg_consumption as mos
            FROM
                      summary_district
              INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
              INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
              INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
              INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
            WHERE
              summary_district.reporting_date = '".$from_date."' AND
              stakeholder.stkid = $stakeholder AND
              Province.PkLocID = $province
              

              AND itminfo_tab.itm_category = 1
              AND summary_district.item_id='".$prod_id."'
            ORDER BY
            tbl_locations.LocName
            
 ";
    //echo $qry;
    $qryRes = mysql_query($qry);
    $c=1;
     $itm_arr   = $so_arr = array();
    
     echo '<h3 align="center">MOS at districts of '.$prov_name.'  - '.$stk_name.'  - '.$prod_name.'</h3>';
     echo '<table width="80%" class="table table-condensed table-striped left">';
     
     //legend
//     echo '<tr bgcolor="#FFFFFF">
//                <td colspan="2" style="font-family: Arial, Verdana, Helvetica, sans-serif;font-size: 12px;"><br>';
//     echo    '<span class="sb1NormalFont">MOS: </span>';
//     echo    '<div style="display:inline-block;width:15px; height:15px; vertical-align: bottom; background-color:#ff370f;margin-left:20px;"></div><div style="display:inline-block;margin-left:5px;">Stock Out</div>';
//     echo    '<div style="display:inline-block;width:15px; height:15px; vertical-align: bottom; background-color:#0000ff;margin-left:20px;"></div> <div style="display:inline-block;margin-left:5px;">Under Stock</div>';
//     echo    '<div style="display:inline-block;width:15px; height:15px; vertical-align: bottom; background-color:#008000;margin-left:20px;"></div> <div style="display:inline-block;margin-left:5px;">Satisfactory</div>';
//     echo    '<div style="display:inline-block;width:15px; height:15px; vertical-align: bottom; background-color:#6bceff;margin-left:20px;"></div> <div style="display:inline-block;margin-left:5px;">Over Stock</div>';
//     echo    '<div style="display:inline-block;width:15px; height:15px;vertical-align: bottom; background-color:#000;margin-left:20px;"></div> ';
//    echo    '<div style="display:inline-block;margin-left:0px;">Un-known</div><br><br>';
//    echo    '</td>
//            </tr>';
     
     
     echo '<tr>';
     echo '<th>Sr No  <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Province <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>District <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Stakeholder <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>SOH<i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th>Months of Stock (MOS) <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
     echo '<th></th>';
     echo '</tr>';
     $c=1;
    while($row = mysql_fetch_assoc($qryRes))
    {
       
        $mos_1 = ((isset($row['mos'])&&$row['mos']>0)?number_format($row['mos'],2):'0');
        
        $q_mos = "SELECT getMosColor('".$mos_1."', '".$row['itmrec_id']."','".$stakeholder."',3)";
        $rs_mos = mysql_query($q_mos);
        $bgcolor = mysql_result($rs_mos, 0, 0);
        
        echo '<tr>';
        echo '<td>'.$c++.'</td>';
        echo '<td>'.$row['province_name'].'</td>';
        echo '<td>'.$row['LocName'].'</td>';
        echo '<td>'.$row['stkname'].'</td>';
        echo '<td align="right">'.number_format($row['soh_district_store']).'</td>';
        echo '<td align="right">'.$mos_1.'</td>';
        echo '<td><div style="width:10px; height:12px; background-color:'.$bgcolor.';" title=""></div></td>';
        echo '</tr>';
       
        
    }  
    echo '</table>';
    ?>

<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
    