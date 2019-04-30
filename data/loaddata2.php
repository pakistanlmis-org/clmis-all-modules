<?php
include("db.php");
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$pr = $_POST['province'];
$district=$_POST['district'];
$count_pr =count($pr);

if($count_pr>1)
{
    $province = implode("','",$pr);
}
else
{
    $province = $pr[0];
}
$s = $_POST['stakeholder'];
$count1 = count($s);
if ($count1 > 1) {
    $stakeholder = implode("','", $s);
} else {
    $stakeholder = $s[0];
}
$p = $_POST['product'];
$count = count($p);

if ($count > 1) {
    $product = implode("','", $p);
} else {
    $product = $p[0];
}
$mos = $_POST['mos'];
if ($mos >= 0 && $mos != '') {
    $w = "WHERE IFNULL(
		ROUND(
			(A.closing_balance / A.AMC),
			2
		),
		0
	)= '$mos'";
} else {
    $w = "";
}

$soh = $_POST['soh'];
if ($soh >= 0 && $soh != '') {
    $w1 = "HAVING closing_balance = '$soh'";
} else {
    $w1 = "";
}



if($province == 1 && $stakeholder == 109){
    $lvl = '6,7';
    $stakeholder = '109,216';
    $gb2 = "dhis_code,
	A.reporting_date,
	A.itm_name ";

    $gb1 = "tbl_warehouse.dhis_code,
            itminfo_tab.itmrec_id,
            tbl_hf_data.reporting_date";

} else {
    $lvl = '7';
    $gb2 = "A.wh_id,
	A.reporting_date,
	A.itm_name ";

    $gb1 = "tbl_warehouse.wh_id,
            itminfo_tab.itmrec_id,
            tbl_hf_data.reporting_date";

}

//if($province=all || $product=all || $stakeholder=all)
//{
//    echo($province);
//    echo($product);
//    echo($stakeholder);
//    $query=$conn->query("SELECT * FROM `data` WHERE (Reporting_Date >='$start_date' AND Reporting_Date<='$end_date' AND Stakeholder='$stakeholder' AND Product='$product' AND  Province='$province')");
//}
//else{
//    $query=$conn->query("SELECT * FROM `data` WHERE (Reporting_Date >='$start_date' AND Reporting_Date<='$end_date' AND Stakeholder='$stakeholder' AND Product='$product' AND  Province='$province')");
//}
//$query=$conn->query("SELECT * FROM `data` WHERE (Reporting_Date >='$start_date' AND Reporting_Date<='$end_date' AND Stakeholder='$stakeholder' AND Product='$product' AND  Province='$province')");
 $sql = "
SELECT
	YEAR (A.reporting_date) `Year`,
	MONTH (A.reporting_date) `Month`,
	A.reporting_date,
	A.stkname,
	A.province,
	A.LocName AS District,
        A.issue_balance,
	A.wh_name,
	A.itm_name,
A.closing_balance SOH,
A.AMC,
	IFNULL(
		ROUND(
			(A.closing_balance / A.AMC),
			2
		),
		0
	) AS MOS
FROM
	(
		SELECT
			tbl_warehouse.wh_id,
                        tbl_warehouse.dhis_code,
			tbl_warehouse.wh_name,
			tbl_locations.LocName,
			prov.LocName AS province,
                        tbl_hf_data.issue_balance,
			tbl_warehouse.wh_rank,
			tbl_hf_data.reporting_date,
			stakeholder.stkname,
			itminfo_tab.itm_name,
			SUM(IFNULL(
				tbl_hf_data.closing_balance,
				0
			)) AS closing_balance,
			IFNULL(
				tbl_hf_data.avg_consumption,
				0
			) AS AMC
		FROM
			tbl_warehouse
		INNER JOIN stakeholder ON stakeholder.stkid = tbl_warehouse.stkofficeid
                
		INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
		INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
		INNER JOIN tbl_locations AS prov ON tbl_warehouse.prov_id = prov.PkLocID
		INNER JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
		INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
		WHERE
                tbl_warehouse.stkofficeid IN ('$stakeholder')
                AND itminfo_tab.itmrec_id IN ('$product')
			AND stakeholder.lvl IN ($lvl)
                        AND tbl_warehouse.hf_type_id NOT IN (5, 2, 3, 9, 6, 7, 8, 12, 10, 11)
		AND tbl_warehouse.wh_id NOT IN (
			SELECT
				warehouse_status_history.warehouse_id
			FROM
				warehouse_status_history
			INNER JOIN tbl_warehouse ON warehouse_status_history.warehouse_id = tbl_warehouse.wh_id
			WHERE
				warehouse_status_history.reporting_month BETWEEN '$start_date'
			AND '$end_date'
			AND warehouse_status_history.`status` = 0
                        
		)
		AND tbl_warehouse.prov_id IN ('$province')
                    AND tbl_warehouse.dist_id IN (".implode(',',$district).")
		AND tbl_hf_data.reporting_date BETWEEN '$start_date'
		AND '$end_date'
                    GROUP BY
                    $gb1
                    $w1
	) A
$w
GROUP BY
        $gb2
ORDER BY
	A.LocName,
	A.reporting_date,

IF (
	A.wh_rank = ''
	OR A.wh_rank IS NULL,
	1,
	0
),
 A.province,
 A.wh_rank,
 A.wh_name ASC";

//echo($sql);exit;
//exit();
$query = $conn->query($sql);

$rowcount = mysqli_num_rows($query);

if ($rowcount > 1) {
    ?>
    <div class="row">

        <button type="button" style="margin-bottom:10px !important; margin-right: 30px !important; " class="btn btn-default pull-right" onClick="tableToExcel('export', 'sheet 1', '<?php echo 'Data'; ?>')" alt="Excel" style="cursor:pointer;">Save Excel</button><br>
    </div>

    <div  id="export">
        <table  id="myTable" >
            <thead>
                <tr>
                    <th>No</th>
                    <th>Year</th>
                    <th>Month</th>
                    <th>Reporting Date</th>
                    <th>Stakeholder</th>
                    <th>Province</th>
                    <th>District</th>
                    <th>Warehouse Name</th>
                    <th>Item</th>
                    <th>Consumption</th>
                    <th>SOH</th>
                    <th>AMC</th>
                    <th>MOS</th>


                </tr>
            </thead>


            <tbody>
    <?php
    $no = 1;

    while ($row = $query->fetch_assoc()) {
        ?>
                    <tr>
                        <td style="text-align: left;"><?php echo $no++ ?></td>
                        <td style="text-align:center;"><?php echo $row['Year'] ?></td>
                        <td style="text-align: center;"><?php echo $row['Month'] ?></td>
                        <td style="text-align: center;"><?php echo $row['reporting_date'] ?></td>
                        <td style="text-align: left;"><?php echo $row['stkname']; ?></td>
                        <td style="text-align: center;"><?php echo $row['province'] ?></td>
                        <td style="text-align: left;"><?php echo $row['District'] ?></td>
                        <td style="text-align: left;"><?php echo $row['wh_name']; ?></td>
                        <td style="text-align: center;"><?php echo $row['itm_name'] ?></td>
                        <td style="text-align: right;"><?php echo $row['issue_balance']; ?></td>  
                        <td style="text-align: right;"><?php echo $row['SOH']; ?></td>
                        <td style="text-align: right;"><?php echo $row['AMC']; ?></td>  
                        <td style="text-align: right;"><?php echo $row['MOS']; ?></td>
        <!--                        <td style="text-align: right;"><?php //echo number_format($row['Consumption']);  ?></td>-->

                    </tr>
        <?php
    }
    ?>


            </tbody>
        </table>
    </div>


    <?php
} else if ($rowcount < 1 && isset($start_date)) {
    echo "Data Does Not Exist. Using this Filter";
}
  
  