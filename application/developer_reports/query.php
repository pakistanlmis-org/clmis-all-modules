<?php
//echo '<pre>';print_r($_REQUEST);exit;
//
//include AllClasses
include("../includes/classes/AllClasses.php");
?>
<html>
    <h3 align="center">Manual Query Data</h3>
    <body>
<?php

$qry_summary_dist= "
    SELECT
YEAR (
		tbl_wh_data.RptDate
	) AS `Year`,
	MONTH (
		tbl_wh_data.RptDate
	) AS `Month`,
	DATE_FORMAT(
		tbl_wh_data.RptDate,
		'%Y-%m'
	) AS `Reporting Date`,
	Province.LocName AS Province,
	tbl_locations.LocName AS District,
	itminfo_tab.itm_name AS Item,
	stakeholder.stkname AS Stakeholder,
	Sum(tbl_wh_data.wh_cbl_a) AS Consumption
FROM
	tbl_warehouse
INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id
INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
INNER JOIN itminfo_tab ON tbl_wh_data.item_id = itminfo_tab.itmrec_id
WHERE
	DATE_FORMAT(
		tbl_wh_data.RptDate,
		'%Y-%m'
	) = '2016-11'
AND itminfo_tab.itm_category = 1
AND tbl_wh_data.item_id = 'IT-001'
AND tbl_warehouse.prov_id = '1'

AND MainStk.MainStakeholder = '1'
AND stakeholder.lvl = 3
GROUP BY
	tbl_warehouse.dist_id,
	tbl_warehouse.wh_id,
	tbl_warehouse.stkid
ORDER BY
	DATE_FORMAT(
		tbl_wh_data.RptDate,
		'%Y-%m'
	) ASC,
	Province ASC,
	District ASC,
	Item ASC,
	stakeholder.stkorder ASC

";
//Query result
//echo $qry_summary_dist;
$Res2 =mysql_query($qry_summary_dist);
$display_data  = $columns_data = array();

while($row = mysql_fetch_assoc($Res2))
{
   $display_data[] = $row;
   $row2=$row;
   //echo '<pre>';print_r($row);
}

foreach($row2 as $k=>$v)
{
   $columns_data[] = $k;
}
//echo '<pre>';print_r($columns_data);print_r($display_data);
?>
<table border="1" class="table table-condensed table-striped left" >
    <tr>
        <?php
        foreach($columns_data as $k=>$v)
        {
           echo '<td>'.$v.'</td>';
        }
        ?>
    </tr>
    
    <?php
        foreach($display_data as $k => $disp)
        {
           echo '<tr>';
           foreach($columns_data as $k2=>$col)
           {
            echo ' <td>'.$disp[$col].'</td>';
           }   
           echo '<tr>';
        }
        ?>
</table>
    </body>
    
<script src="<?php echo PUBLIC_URL;?>js/jquery-1.4.4.js" type="text/javascript"></script>
<script src="<?php echo PUBLIC_URL;?>js/custom_table_sort.js" type="text/javascript"></script>
</html>
