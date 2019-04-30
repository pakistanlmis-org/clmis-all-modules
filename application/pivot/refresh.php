<?php
include("../includes/classes/AllClasses.php");
set_time_limit(0);

$strSql = "SELECT
	stakeholder.stkname Stakeholder,
	prov.LocName Province,
	dist.LocName District,
	itminfo_tab.itm_name Product,
	summary_district.reporting_date,
	summary_district.consumption,
	summary_district.avg_consumption,
	summary_district.soh_district_store,
	summary_district.soh_district_lvl,
	summary_district.reporting_rate,
	summary_district.total_health_facilities
FROM
	summary_district
INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
INNER JOIN tbl_locations AS prov ON summary_district.province_id = prov.PkLocID
INNER JOIN tbl_locations AS dist ON summary_district.district_id = dist.PkLocID
ORDER BY
	stakeholder.stkname,
	prov.LocName,
	dist.LocName,
	itminfo_tab.method_rank,
	summary_district.reporting_date";
$rsSql = mysql_query($strSql) or die("Error GetSummaryData");
$fp = fopen('Summary.csv', 'w');
$list = array('Stakeholder', 'Province', 'District', 'Product', 'Reporting Date', 'Consumption', 'AMC', 'soh_district_store', 'soh_district_lvl', 'reporting_rate', 'total_health_facilities');
fputcsv($fp, $list);
while ($row = mysql_fetch_array($rsSql)) {
    $list = array($row['Stakeholder'], $row['Province'], $row['District'], $row['Product'], $row['reporting_date'], $row['consumption'], $row['avg_consumption'], $row['soh_district_store'], $row['soh_district_lvl'], $row['reporting_rate'], $row['total_health_facilities']);
    fputcsv($fp, $list);
}
fclose($fp);
?>
<html>
   <head>
      <script type="text/javascript">
         window.location="index.php";
      </script>
   </head>
   <body>
       <p align='center' style='color:grey;'>(Please wait. Data is loading...)</p>
   </body>
</html>