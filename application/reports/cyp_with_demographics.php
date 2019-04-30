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
$provinceID = 1;
$where_prov = $where_prov_wh='';
$reportingDate = date('Y-m');
if (isset($_REQUEST['search'])) {
    //Getting date_from

    $provinceID = $_REQUEST['province'];
    if ($provinceID != 'all'){
    $where_prov = "AND tbl_locations.ParentID = $provinceID";
    $where_prov_wh="AND tbl_warehouse.prov_id = $provinceID";
    
    }
$reportingDate=date('Y-m', strtotime($_REQUEST['from_date']));

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

                        <h3 class="page-title row-br-b-wp">Demographics and CYP</h3>
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
                                <label>Month</label>
                                <div class="form-group">
                                    <input name="from_date" id="from_date" class="form-control input-sm" readonly  value="<?php
                                                                if (isset($_REQUEST['from_date'])) {
                                                                    echo date('Y-m', strtotime($reportingDate));
                                                                } else {

                                                                    echo date('Y-m');
                                                                }
                                                                ?>" required readonly="true"/>
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
tbl_locations.PkLocID as district_id,
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

 tbl_locations.LocLvl = 3 
$where_prov
ORDER BY
tbl_locations.LocName ASC

  
";
//                                    print_r($qry);
//                                    exit;
                                $res = mysql_query($qry);
                                $num = mysql_num_rows($res);
                                $qry_cyp = "SELECT
                                    tbl_hf_type_data.district_id,
                                     
                                    tbl_hf_type_data.item_id,
                                    tbl_hf_type_data.reporting_date,
                                    Sum(
                                            tbl_hf_type_data.issue_balance
                                    ) AS issuance,
                                    itminfo_tab.itm_name,
                                    itminfo_tab.extra,
                                    tbl_hf_type.hf_type,
                                    tbl_locations.LocName AS dist_name
                            FROM
                                    tbl_hf_type_data
                            INNER JOIN tbl_locations ON tbl_hf_type_data.district_id = tbl_locations.PkLocID
                            INNER JOIN itminfo_tab ON tbl_hf_type_data.item_id = itminfo_tab.itm_id
                            INNER JOIN tbl_hf_type ON tbl_hf_type_data.facility_type_id = tbl_hf_type.pk_id

                            WHERE
                                    
                             DATE_FORMAT(tbl_hf_type_data.reporting_date,'%Y-%m') ='$reportingDate'
                            AND itminfo_tab.itm_category = 1
                            AND method_type IS NOT NULL 
                            $where_prov
                            GROUP BY
                                    tbl_hf_type_data.district_id,
                                    tbl_hf_type_data.facility_type_id,
                                    tbl_hf_type_data.item_id,
                                    tbl_hf_type_data.reporting_date
                            ORDER BY
                                    dist_name ASC,
                                    tbl_hf_type.hf_rank ASC,
                                    itminfo_tab.method_rank ASC";
                                $itm_array= $dist_wise_arr =array();
//print_r($qry_cyp);exit;
                                $qry_cyp_res = mysql_query($qry_cyp);

                                while ($row1 = mysql_fetch_array($qry_cyp_res)) {
                                    @$dist_wise_arr[$row1['district_id']][$row1['item_id']]  += ($row1['issuance']*$row1['extra']);
                                     
                                }
 
                                $qry_performance = "SELECT
                                            tbl_hf_data.item_id,
                                            
                                            Sum(tbl_hf_data_reffered_by.static + tbl_hf_data_reffered_by.camp) AS performance,
                                             
                                            itminfo_tab.extra AS CYPFactor,
                                            
                                            itminfo_tab.itm_name,
                                            tbl_warehouse.dist_id
                                    FROM
                                            tbl_warehouse
                                    INNER JOIN tbl_hf_data ON tbl_warehouse.wh_id = tbl_hf_data.warehouse_id
                                    INNER JOIN tbl_hf_data_reffered_by ON tbl_hf_data.pk_id = tbl_hf_data_reffered_by.hf_data_id
                                    INNER JOIN itminfo_tab ON tbl_hf_data.item_id = itminfo_tab.itm_id
                                    WHERE
                                            DATE_FORMAT(tbl_hf_data.reporting_date,'%Y-%m') ='$reportingDate'
                                                $where_prov_wh
                                            
                                    GROUP BY
                                            tbl_warehouse.dist_id,
                                            tbl_warehouse.hf_type_id,
                                            tbl_hf_data.item_id";
                              
                                $qry_perf_res = mysql_query($qry_performance);
 
                                while ($row_per = mysql_fetch_array($qry_perf_res)) {
                                    @$dist_wise_arr[$row_per['dist_id']][$row_per['item_id']]  += $row_per['performance'];
                                }
//                               echo'<pre>';
//                                print_r($dist_wise_arr); 
                                if ($num > 0) {
                                    ?>
                               
                                    <div align="center" class="col-md-12">
                                        <h4 class="center">
                                            Demographics and CYP Report <br>
                                            For the month of  <?php echo date('M-Y', strtotime($reportingDate))?> 
                                        </h4>
                                        <br> 
                                    </div>
                               
                                    <table style="width:95%;margin-left: 2%;" align="center"   id="myTable" class="table table-striped table-bordered table-condensed">
                                       
                                        
                                        <thead style="background-color:lightgray">
                                            <tr>
                                                <th rowspan="2">S. No.</th>

                                                <th rowspan="2">District</th>
                                                <th rowspan="2">Province</th>
                                                <th colspan="2">Rural</th>
                                                <th colspan="2">Urban</th>
                                                <th colspan="2">Total</th>
                                                 <th rowspan="2">CYP</th>
                                            </tr>
                                            <tr>

                                                <th >Male Population Rural</th>
                                                <th>Female Population Rural</th>
                                                
                                                <th>Male Population Urban</th>
                                                <th>Female Population Urban</th>
                                                 
                                                <th>Total Male Population </th>
                                                <th>Total Female Population </th>
                                            </tr>
                                        </thead>
                                        <?php
//                                            echo'<pre>';print_r($dist_wise_arr);
                                        $counter = 1;
                                        $total_cyp=$total_male_urban = $total_female_urban = $total_male_rural = $total_female_rural = $total_pop_male = $total_pop_female = 0;
                                        while ($row = mysql_fetch_assoc($res)) {
                                            ?>
                                            <tbody>

                                                <tr>
                                                    <td><?php echo $counter++; ?></td>

                                                    <td><?php echo $row['district_name']; ?></td>
                                                    <td><?php echo $row['prov_name']; ?></td>
                                                    <td style="text-align: right"><?php if ($row['pop_male_rural'] == null) echo NULL;
                                    else echo number_format($row['pop_male_rural']); ?></td>
                                                    <td style="text-align: right"><?php if ($row['pop_female_rural'] == null) echo NULL;
                                    else echo number_format($row['pop_female_rural']); ?></td>
                                                     
                                                    <td style="text-align: right"><?php if ($row['pop_male_urban'] == null) echo NULL;
                                    else echo number_format($row['pop_male_urban']); ?></td>
                                                    <td style="text-align: right"><?php if ($row['pop_female_urban'] == null) echo NULL;
                                    else echo number_format($row['pop_female_urban']); ?></td>
                                                     
                                                    <td style="text-align: right"><?php if ($row['pop_male_tot'] == null) echo NULL;
                                    else echo number_format($row['pop_male_tot']); ?></td>
                                                    <td style="text-align: right"><?php if ($row['pop_female_tot'] == null) echo NULL;
                                    else echo number_format($row['pop_female_tot']); ?></td>
                                                    <td style="text-align: right"><?php 
                                                    $cyp=array();
                                                    foreach ($dist_wise_arr as $key => $value) {
                                                        
                                                    
                                                     foreach ($value as $prod_key=>$prod_value)
                                                    
                                                     {
                                                         if($key==$row['district_id'])
                                                         @$cyp[$row['district_id']]+=$dist_wise_arr[$row['district_id']][$prod_key]; 
                                                         
                                                     }
                                                     }
                                                     echo number_format(@$cyp[$row['district_id']]);
                                                     ?></td>
                                                </tr>

                                            </tbody>

                                            <?php
                                            $total_male_urban += $row['pop_male_urban'];
                                            $total_female_urban += $row['pop_female_urban'];
                                            $total_male_rural+= $row['pop_male_rural'];
                                            $total_female_rural += $row['pop_female_rural'];
                                            $total_pop_male += $row['pop_male_tot'];
                                            $total_pop_female  += $row['pop_female_tot'];
                                            $total_cyp+=@$cyp[$row['district_id']];
                                        }
                                        ?>
                                        <tfoot>
                                            <tr style="background-color: lightgray;">
                                                <td colspan="3" >Total </td>
                                                <td  style="text-align: right;"><?php echo number_format($total_male_rural) ?></td>

                                                <td  style="text-align: right;"><?php echo number_format($total_female_rural) ?></td>
                                                
                                                <td  style="text-align: right;"><?php echo number_format($total_male_urban) ?></td>
                                                <td  style="text-align: right;"><?php echo number_format($total_female_urban) ?></td>
                                                 
                                                <td  style="text-align: right;"><?php echo number_format($total_pop_male) ?></td>
                                                <td  style="text-align: right;"><?php echo number_format($total_pop_female) ?></td>
                                                <td  style="text-align: right;"><?php echo number_format($total_cyp) ?></td>
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
    <script>
    $(function () {

 
           
            $('#from_date').datepicker({
                dateFormat: "yy-mm",
                constrainInput: false,
                changeMonth: true,
                changeYear: true,
                maxDate: ''
            });
        });
    
    </script>


</body>
<!-- END BODY -->
</html>