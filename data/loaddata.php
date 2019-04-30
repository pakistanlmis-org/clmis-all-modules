
<?php
include("db.php");

$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$pr = $_POST['province'];
$count_pr =count($pr);

if($count_pr>1)
{
    $province = implode("','",$pr);
}
else
{
    $province = $pr[0];
}
$s= $_POST['stakeholder'];
$count1=count($s);
 if($count1>1)
{
    $stakeholder = implode("','",$s);
}
else
{
    $stakeholder = $s[0];
}       
$p=$_POST['product'];
$count =count($p);

if($count>1)
{
    $product = implode("','",$p);
}
else
{
    $product = $p[0];
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

$sql = "SELECT *
FROM
 (
  SELECT DISTINCT
  summary_district.soh_district_store as soh_district_lvl,
  summary_district.avg_consumption,
   summary_district.pk_id,
   YEAR (
    summary_district.reporting_date
   ) AS `Year`,
   MONTH (
    summary_district.reporting_date
   ) AS `Month`,
   DATE_FORMAT(
    summary_district.reporting_date,
    '%Y-%m'
   ) AS `Reporting Date`,
   Province.LocName AS Province,
   tbl_locations.LocName AS District,
   itminfo_tab.itm_name AS Item,
   stakeholder.stkname AS Stakeholder,
   summary_district.consumption Consumption

  FROM
   summary_district
  INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
  INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
  INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
  INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
  
  WHERE
   summary_district.province_id IN ('$province')
  
  AND DATE_FORMAT(
   summary_district.reporting_date,
   '%Y-%m-%d'
  ) BETWEEN '$start_date'
  AND '$end_date'
  AND (
   (
    (
     (
      summary_district.soh_district_store / summary_district.avg_consumption
     ) IS NOT NULL
    )
   )
  )
  AND summary_district.item_id IN ('$product')
      and summary_district.stakeholder_id IN ('$stakeholder')
  
  ORDER BY
   DATE_FORMAT(
    summary_district.reporting_date,
    '%Y-%m'
   ) ASC,
   Province ASC,
   District ASC,
   Item ASC,
   stakeholder.stkorder ASC
 ) A
ORDER BY
 A.`Reporting Date` ASC,
 A.Province ASC,
 A.District ASC,
 A.Item ASC,
 A.stakeholder ASC";

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
<!--                    <th>Reporting Date</th>-->
                    <th>Province</th>
                    <th>District</th>
                    <th>Item</th>
                    <th>Stakeholder</th>
                    <th>AMC</th>
                    <th>SOH</th>
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
                        <td style="text-align: center;"><?php echo date('M',strtotime($row['Reporting Date'].'-01')) ?></td>
<!--                        <td style="text-align: center;"><?php // echo $row['Reporting Date'] ?></td>-->
                        <td style="text-align: center;"><?php echo $row['Province'] ?></td>
                        <td style="text-align: left;"><?php echo $row['District'] ?></td>
                        <td style="text-align: center;"><?php echo $row['Item'] ?></td>
                        <td style="text-align: center;"><?php echo $row['Stakeholder']; ?></td>
                        <td style="text-align: right;"><?php echo $row['avg_consumption']; ?></td>
                        <td style="text-align: right;"><?php echo $row['soh_district_lvl']; ?></td>
                        
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
  
  