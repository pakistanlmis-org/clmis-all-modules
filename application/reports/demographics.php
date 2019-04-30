<?php
/**
 * stock_status
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses
include("../includes/classes/AllClasses.php");
//Including FunctionLib
include(APP_PATH . "includes/report/FunctionLib.php");
//Including header
include(PUBLIC_PATH . "html/header.php");
//Initialing variable report_id
//$report_id = "STOCKISSUANCE";
//Checking date
$fileName = "Provincial Population";
$provinceID = 0;
$where_prov = '';
if (isset($_REQUEST['search'])) {
    //Getting date_from

    $provinceID = $_REQUEST['province'];
    if ($provinceID != 'all')
        $where_prov = "AND tbl_locations.ParentID = $provinceID";


    //Getting warehouse
}
?>

</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <div class="page-container">
        <?php
//Including top
        include PUBLIC_PATH . "html/top.php";
//Including top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">

                        <h3 class="page-title row-br-b-wp">Demographics Data</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <form method="POST" name="frm" id="frm" action="">
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-md-12">



                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <select required  name="province" id="province" class="form-control input-sm" >

                                                            <option value="all" <?php echo ($provinceID == 'all') ? 'selected="selected"' : ''; ?>>All</option>
                                                            <?php
//Province query
//gets
//Province id
//Province name
                                                            $qry = "SELECT
																	tbl_locations.PkLocID,
																	tbl_locations.LocName
																FROM
																	tbl_locations
																WHERE
																	tbl_locations.LocLvl = 2
																AND tbl_locations.ParentID IS NOT NULL";
                                                            $qryRes = mysql_query($qry);
                                                            if ($qryRes != FALSE) {
                                                                while ($row = mysql_fetch_object($qryRes)) {
                                                                    ?>
                                                                    <?php
                                                                    //Populate province combo

                                                                    if ($row->PkLocID == $provinceID) {
                                                                        $sel = "selected='selected'";
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    ?>

                                                                    <option value="<?php echo $row->PkLocID; ?>"<?php echo $sel; ?>><?php echo $row->LocName; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="margin-top:1%;">
                                                    <label for="firstname">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary input-sm">Search</button>
                                                    </div>
                                                </div>




                                            </div>



                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">


                        <div class="widget">
                            <div class="widget-body">
                                <?php include('sub_dist_reports.php'); ?>
                                <div class="row"><br></div>
                                <?php
                                $qry = "SELECT
tbl_locations.LocName as district_name,
tbl_locations.pop_male_rural,
tbl_locations.growth_rate_rural,
tbl_locations.growth_rate_urban,
tbl_locations.pop_female_urban,
tbl_locations.pop_male_urban,
tbl_locations.pop_female_rural,
prov.LocName as prov_name,
tbl_locations.pop_male_urban+tbl_locations.pop_male_rural as pop_male_tot,
tbl_locations.pop_female_urban+tbl_locations.pop_female_rural as pop_female_tot 
FROM
tbl_locations
INNER JOIN tbl_locations AS prov ON tbl_locations.ParentID = prov.PkLocID
WHERE
tbl_locations.LocLvl = 3 $where_prov
ORDER BY
tbl_locations.LocName ASC

  
";
//                                    print_r($qry);
//                                    exit;
                                $res = mysql_query($qry);
                                $num = mysql_num_rows($res);

                                if ($num > 0) {
                                    ?>
                                
                                    <div align="center" class="col-md-12">
                                        <h4 class="center">
                                            Demographics Data <br>
                                        </h4>
                                        <br> 
                                    </div>
                                    <table style="width:95%;margin-left: 2%;" align="center"   id="myTable" class="table table-striped table-bordered table-condensed">
                                        <thead style="background-color:lightgray">
                                            <tr>
                                                <th rowspan="2">S. No.</th>

                                                <th rowspan="2">District</th>
                                                <th rowspan="2">Province</th>
                                                <th colspan="3">Rural</th>
                                                <th colspan="3">Urban</th>
                                                <th colspan="2">Total</th>
                                            </tr>
                                            <tr>
                                                 
                                                <th >Male Population Rural</th>
                                                <th>Female Population Rural</th>
                                                <th>Growth Rate Rural</th>
                                                <th>Male Population Urban</th>
                                                <th>Female Population Urban</th>
                                                <th>Growth Rate Urban</th>
                                                <th>Total Male Population </th>
                                                <th>Total Female Population </th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $counter = 1;
                                        $total_male_urban=$total_female_urban=$total_male_rural=$total_female_rural=$total_pop_male=$total_pop_female=0;
                                        while ($row = mysql_fetch_assoc($res)) {
                                            ?>
                                            <tbody>

                                                <tr>
                                                    <td><?php echo $counter++; ?></td>

                                                    <td><?php echo $row['district_name']; ?></td>
                                                    <td><?php echo $row['prov_name']; ?></td>
                                                    <td style="text-align: right"><?php if($row['pop_male_rural']==null) echo NULL;else  echo number_format($row['pop_male_rural']); ?></td>
                                                    <td style="text-align: right"><?php if($row['pop_female_rural']==null) echo NULL;else echo number_format($row['pop_female_rural']); ?></td>
                                                    <td style="text-align: right"><?php if($row['growth_rate_rural']==null) echo NULL;else  echo number_format($row['growth_rate_rural'],2); ?></td>
                                                    <td style="text-align: right"><?php if($row['pop_male_urban']==null) echo NULL;else  echo number_format($row['pop_male_urban']); ?></td>
                                                    <td style="text-align: right"><?php if($row['pop_female_urban']==null) echo NULL;else  echo number_format($row['pop_female_urban']); ?></td>
                                                    <td style="text-align: right"><?php if($row['growth_rate_urban']==null) echo NULL;else  echo number_format($row['growth_rate_urban'],2); ?></td>
                                                    <td style="text-align: right"><?php if($row['pop_male_tot']==null) echo NULL;else  echo number_format($row['pop_male_tot']); ?></td>
                                                    <td style="text-align: right"><?php if($row['pop_female_tot']==null) echo NULL;else  echo number_format($row['pop_female_tot']); ?></td>
                                                </tr>

                                            </tbody>
                                            
                                        <?php 
                                        $total_male_urban=$total_male_urban+$row['pop_male_urban'];
                                        $total_female_urban=$total_female_urban+$row['pop_female_urban'];
                                        $total_male_rural=$total_male_rural+$row['pop_male_rural'];
                                        $total_female_rural=$total_female_rural+$row['pop_female_rural'];
                                        $total_pop_male=$total_pop_male+$row['pop_male_tot'];
                                        $total_pop_female=$total_pop_female+$row['pop_female_tot'];
                                        }
                                        ?>
<tfoot>
                                                <tr style="background-color: lightgray;">
                                                    <td colspan="3" >Total </td>
                                                    <td  style="text-align: right;"><?php echo number_format($total_male_rural)?></td>
                                                    
                                                     <td  style="text-align: right;"><?php echo number_format($total_female_rural)?></td>
                                                     <td ></td>
                                                      <td  style="text-align: right;"><?php echo number_format($total_male_urban)?></td>
                                                       <td  style="text-align: right;"><?php echo number_format($total_female_urban)?></td>
                                                       <td ></td>
                                                       <td  style="text-align: right;"><?php echo number_format($total_pop_male)?></td>
                                                       <td  style="text-align: right;"><?php echo number_format($total_pop_female)?></td>
                                                </tr>
                                                
                                            </tfoot>

                                    </table>
                                <?php } else {
                                    ?><div style="margin-left: 15px;"><label> <?php echo 'No record found'; ?>  </label> </div><?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>

    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>



</body>
<!-- END BODY -->
</html>