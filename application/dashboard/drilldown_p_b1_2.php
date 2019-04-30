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
    
    $qryRes = mysql_query($qry);
    $c=1;
     $itm_arr   = $so_arr = array();
    
     echo '<table width="60%" class="table table-condensed table-striped left">';
     
     echo '<tr>';
     echo '<th></th>';
     echo '<th>Province</th>';
     echo '<th>District</th>';
     echo '</tr>';
     $c=1;
    while($row = mysql_fetch_assoc($qryRes))
    {
       if(  $row['mos']==0)
       {
        echo '<tr>';
        echo '<td>'.$c++.'</td>';
        echo '<td>'.$row['province_name'].'</td>';
        echo '<td>'.$row['LocName'].'</td>';
        //echo '<td>'.$row['stkname'].'</td>';
       // echo '<td>'.number_format($row['mos'],2).'</td>';
        echo '</tr>';
       }
        
    }  
    echo '</table>';