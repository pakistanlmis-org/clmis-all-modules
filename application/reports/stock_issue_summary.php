<?php
/**
 * stock issue summary
 * @package reports
 * 

 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//report id
$rptId = 'issuesummary';
//if submitted
if (date('d') > 10) {
    $date = date('Y-m', strtotime("-1 month", strtotime(date('Y-m-d'))));
} else {
    $date = date('Y-m', strtotime("-2 month", strtotime(date('Y-m-d'))));
}
$selMonth = date('m', strtotime($date));
$selYear = date('Y', strtotime($date));
//Initialing variables
$date_from = $date_to = $product = $provinceID = $district = $stakeholder = $warehouse = $xmlstore = $selProv = '';
if (isset($_REQUEST['search'])) {
    //Getting date_from
    $date_from = $_REQUEST['date_from'];
    //Getting date_to
    $date_to = $_REQUEST['date_to'];
    //Setting date_from
    $dateFrom = dateToDbFormat($date_from);
    //Setting dateTo
    $dateTo = dateToDbFormat($date_to);

    //Getting stakeholder
    $stakeholder = $_REQUEST['stakeholder'];
    //Getting province
    $provinceID = $_REQUEST['province'];
}
?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Stock Issue Summary Report</h3>
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
                                                        <label class="control-label">Date From</label>
                                                        <input type="text" readonly class="form-control input-sm" name="date_from" id="date_from" value="<?php echo $date_from; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Date To</label>
                                                        <input type="text" readonly class="form-control input-sm" name="date_to" id="date_to" value="<?php echo $date_to; ?>"/>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Stakeholder</label>
                                                        <select name="stakeholder" id="stakeholder" class="form-control input-sm" required="required">
                                                            <option value="">Select</option>
                                                            <option value="all" <?php if ($stakeholder == 'all') {
                                                                                        echo 'selected="selected"';
                                                                                        $stk_name='All Stakeholders';
                                                                                } ?>
                                                                    >All</option>
                                                            <?php
//stakeholder query
//gets
//stkid
//stkname
                                                            $qry = "SELECT
                                                                            stakeholder.stkid,
                                                                            stakeholder.stkname
                                                                    FROM
                                                                            stakeholder
                                                                    WHERE
                                                                            stakeholder.ParentID IS NULL
                                                                    AND stakeholder.stk_type_id IN (0, 1)
                                                                    ORDER BY
                                                                            stakeholder.stkorder ASC";
                                                            $qryRes = mysql_query($qry);
                                                            if ($qryRes != FALSE) {
                                                                while ($row = mysql_fetch_object($qryRes)) {
                                                                     //Populate stakeholder combo 
                                                                    $sel='';
                                                                    if($stakeholder == $row->stkid)
                                                                    {
                                                                        $sel=' selected="selected" ';
                                                                        $stk_name=$row->stkname;
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $row->stkid; ?>" <?php echo $sel ?>><?php echo $row->stkname; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label class="control-label">Province</label>
                                                        <select name="province" id="province" class="form-control input-sm" required="required">
                                                            <option value="">Select</option>
                                                            <option value="all" <?php if($provinceID == 'all')
                                                                                {
                                                                                        echo 'selected="selected"';
                                                                                        $prov_name='All Provinces';
                                                                                }
                                                                                ?>
                                                                    >All</option>
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
                                                                    
                                                                    //Populate province combo 
                                                                    $sel='';
                                                                    if($provinceID == $row->PkLocID)
                                                                    {
                                                                        $sel=' selected="selected" ';
                                                                        $prov_name=$row->LocName;
                                                                    }
                                                                    ?>
                                                                    <option value="<?php echo $row->PkLocID; ?>" <?php echo ($provinceID == $row->PkLocID) ? 'selected="selected"' : ''; ?>><?php echo $row->LocName; ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4" style="">
                                                    <label for="firstname">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Go</button>

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
                <?php
                //if submitted
                if (isset($_POST['search'])) {
                    //select query
                    //gets
                    // Health Facility Type Wise
                    //B.hf_type_id,
                    //hf type,
                    //hf type rank,
                    //item id,
                    //item name,
                    //item category,
                    //item type,
                    //total outlets,
                    //performance,
                    //CYP,
                    //Users
                    if ($stakeholder == "all") {
                        $where_stakeholder = "";
                    } else {
                        $where_stakeholder = " stakeholder.stkid=$stakeholder AND ";
                    }
                    $qry = "SELECT
                        clr_master.pk_id,
                        (SELECT
                        count(*) total
                        FROM
                        tbl_warehouse
                        WHERE
                        tbl_warehouse.prov_id = 1 AND
                        tbl_warehouse.stkid = 1 AND
                        tbl_warehouse.stkofficeid = 17) as total,
                        clr_master.requisition_num,
                        stakeholder.stkname,
                        tbl_warehouse.wh_name,
                        tbl_locations.LocName
                        FROM
                        clr_master
                        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                        INNER JOIN stakeholder ON clr_master.stk_id = stakeholder.stkid
                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                        INNER JOIN tbl_locations ON tbl_warehouse.prov_id = tbl_locations.PkLocID
                        WHERE
                        
                        $where_stakeholder
                        clr_master.date_from >= '$dateFrom' AND
                        clr_master.date_to <= '$dateTo'"
                            . "GROUP BY stakeholder.stkid";
                    //query results
                    $qryRes = mysql_query($qry);
                    //get result
                    if (mysql_num_rows(mysql_query($qry)) > 0) {
                        ?>
                        <?php
                        include('sub_dist_reports.php');
                        ?>
                        <div class="col-md-12" style="overflow:auto;">

                            <table width="100%">
                                <tr>
                                    <td style="padding-top: 10px;" align="center">
                                        <h3 class="center bold">
                                            Stock Issue Summary Report
                                        </h3>
                                        <h4 class="center bold">
                                            <?php echo $stk_name.' - '.$prov_name.' From: '.date('M-Y',strtotime($dateFrom)).' To: '.date('M-Y',strtotime($dateTo));?>
                                        </h4>
                                        <table width="100%" id="myTable" cellspacing="0" align="center">
                                            <thead>
                                                <tr>
                                                    <th>Stakeholders</th>
                                                    <th >District Stores</th>
                                                    <th>CLR6 Received</th>
                                                    
                                                    <th >Dispatched</th>
                                                    <th >Balance</th>
                                                    <?php
                                                    $qry_pro = "SELECT
                                                        itminfo_tab.itm_id,
                                                        itminfo_tab.itm_name

                                                        FROM
                                                                itminfo_tab
                                                        WHERE
                                                                itminfo_tab.itm_category = 1
                                                        and itminfo_tab.itm_id NOT IN (11,10,2,30,33,14)";
                                                    //query results
                                                    $qryResPro = mysql_query($qry_pro);


                                                    while ($rowPro = mysql_fetch_array($qryResPro)) {
                                                        ?>
                                                        <th><?php echo $rowPro['itm_name']; ?></th>

                                                    <?php } ?>
                                                </tr>

                                            </thead>
                                            <tbody>

                                                <?php
                                                if ($provinceID == "all") {
                                                    $where_pro = "";
                                                } else {
                                                    $where_pro = " AND tbl_locations.PkLocID=$provinceID";
                                                }
                                                $qry_loc = "SELECT
                                                        tbl_locations.PkLocID,
                                                        tbl_locations.LocName
                                                        FROM
                                                        tbl_locations
                                                        WHERE

                                                        tbl_locations.LocType = 2
                                                        $where_pro";
                                                //query results
                                                $qryResLoc = mysql_query($qry_loc);
                                                while ($rowLoc = mysql_fetch_array($qryResLoc)) {
                                                    $province_id = $rowLoc['PkLocID'];
                                                    ?>

                                                    <tr>
                                                        <td colspan="6"><?php echo $rowLoc['LocName']; ?></td>
                                                    </tr>
                                                    <?php
                                                    if ($stakeholder == "all") {
                                                        $where_stakeholder = "";
                                                        $where_in_stak = "";
                                                    } else {
                                                        $where_stakeholder = "AND stakeholder.stkid=$stakeholder";
                                                        $where_in_stak = " AND tbl_warehouse.stkid = $stakeholder AND
                                                        tbl_warehouse.stkofficeid = 17";
                                                    }
                                                    $qryStakeholder = "SELECT
                                                                            stakeholder.stkid,
                                                                            stakeholder.stkname
                                                                    FROM
                                                                            stakeholder
                                                                    WHERE
                                                                            stakeholder.ParentID IS NULL
                                                                    AND stakeholder.stk_type_id IN (0, 1)
                                                                    $where_stakeholder
                                                                    ORDER BY
                                                                            stakeholder.stkorder ASC";
                                                    $qryStak = mysql_query($qryStakeholder);


                                                    while ($row = mysql_fetch_array($qryStak)) {
                                                        $stakeholder_id = $row['stkid'];
                                                        ?>
                                                        <tr>
                                                            <td class="center"><?php echo $row['stkname']; ?></td>

                                                            <?php
                                                            $qry_main_stakeholder = "SELECT
                                                                stakeholder.stkid
                                                                FROM
                                                                stakeholder
                                                                WHERE
                                                                stakeholder.MainStakeholder = $stakeholder_id AND
                                                                stakeholder.lvl = 3";
                                                            $qryms = mysql_query($qry_main_stakeholder);
                                                            $rowms = mysql_fetch_array($qryms);
                                                            $main_stak = $rowms['stkid'];
                                                            $qry_total = "SELECT
                                                            count(*) total
                                                            FROM
                                                            tbl_warehouse
                                                            WHERE
                                                            tbl_warehouse.prov_id = '$province_id'
                                                            AND tbl_warehouse.stkid = '$stakeholder_id'"
                                                                    . "AND tbl_warehouse.stkofficeid = '$main_stak' ";

                                                            $qrytot = mysql_query($qry_total);
                                                            $rowtot = mysql_fetch_array($qrytot);
                                                            $this_tot_dist = $rowtot['total'];
                                                            ?>

                                                            <td class="center"><?php echo $this_tot_dist; ?></td>
                                                            <?php
                                                                 $qry="SELECT

                                                                        COUNT(*) as clr_rec
                                                                      FROM
                                                                        clr_master
                                                                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                                                                      WHERE
                                                                        approval_status in ('Approved','Issued','Issue In Process') AND
                                                                        tbl_warehouse.prov_id = '$province_id'
                                                                        AND tbl_warehouse.stkid = '$stakeholder_id' AND
                                                                        clr_master.date_to BETWEEN '$dateFrom' AND '$dateTo'
                                                                ";
                                                               $res1 = mysql_query($qry);
                                                               $row1 = mysql_fetch_assoc($res1);
                                                               $this_clr_rec = $row1['clr_rec'];
                                                            ?>
                                                            <td class="center"><?php echo $this_clr_rec; ?></td>
                                                            
                                                            <?php
                                                                $qry="SELECT

                                                                        COUNT(*) as clr_rec
                                                                      FROM
                                                                        clr_master
                                                                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                                                                      WHERE
                                                                        tbl_warehouse.prov_id = '$province_id'
                                                                        AND tbl_warehouse.stkid = '$stakeholder_id' AND
                                                                        clr_master.date_to BETWEEN '$dateFrom' AND '$dateTo'  AND
                                                                        clr_master.approval_status in ('Issued','Issue In Process')

                                                                ";
                                                               $res1 = mysql_query($qry);
                                                               $row1 = mysql_fetch_assoc($res1);
                                                               $this_dispatched = $row1['clr_rec'];
                                                            ?>
                                                            <td class="center"><?php echo $this_dispatched; ?></td>
                                                            <td class="center"><?php echo $this_clr_rec-$this_dispatched; ?></td>
                                                            <?php
                                                            $qry_pro = "SELECT
                                                                itminfo_tab.itm_id,
                                                                itminfo_tab.itm_name

                                                                FROM
                                                                        itminfo_tab
                                                                WHERE
                                                                        itminfo_tab.itm_category = 1
                                                                and itminfo_tab.itm_id NOT IN (11,10,2,30,33,14)";
                                                            //query results
                                                            $qryResPro = mysql_query($qry_pro);


                                                            while ($rowPro = mysql_fetch_array($qryResPro)) {
                                                                $item_id = $rowPro['itm_id'];
                                                                $qry_issuance = "SELECT
                                                            tbl_stock_master.WHIDFrom,
                                                            SUM(tbl_stock_detail.Qty) as qty,
                                                            tbl_warehouse.prov_id
                                                            FROM
                                                            tbl_stock_master
                                                            INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
                                                            INNER JOIN tbl_warehouse ON tbl_stock_master.WHIDTo = tbl_warehouse.wh_id
                                                            INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
                                                            WHERE
                                                            tbl_stock_master.TranTypeID = 2 AND
                                                            tbl_warehouse.prov_id = $province_id AND
                                                            stock_batch.item_id = $item_id AND
                                                            tbl_warehouse.stkid = $stakeholder_id AND
                                                            DATE_FORMAT(tbl_stock_master.TranDate,'%Y-%m-%d') BETWEEN '$dateFrom' AND '$dateTo'";
                                                                $qryResIsu = mysql_query($qry_issuance);
                                                                $rowIsu = mysql_fetch_array($qryResIsu)
                                                                ?>
                                                                <td class="right"><?php echo number_format(ABS($rowIsu['qty'])); ?></td>

                                                            <?php } ?>

                                                        </tr>

                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </tbody>

                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "No record found";
                }
            }
            ?>
        </div>
    </div>
</div>

<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
?>

<script>
    $(function () {
        var startDateTextBox = $('#date_from');
        var endDateTextBox = $('#date_to');

        startDateTextBox.datepicker({
            minDate: "-10Y",
            maxDate: 0,
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                if (endDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        endDateTextBox.datepicker('setDate', testStartDate);
                } else {
                    endDateTextBox.val(dateText);
                }

            },
            onSelect: function (selectedDateTime) {
                endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
            }
        });
        endDateTextBox.datepicker({
            maxDate: 0,
            dateFormat: 'dd/mm/yy',
            changeMonth: true,
            changeYear: true,
            onClose: function (dateText, inst) {
                if (startDateTextBox.val() != '') {
                    var testStartDate = startDateTextBox.datepicker('getDate');
                    var testEndDate = endDateTextBox.datepicker('getDate');
                    if (testStartDate > testEndDate)
                        startDateTextBox.datepicker('setDate', testEndDate);
                } else {
                    startDateTextBox.val(dateText);
                }

            },
            onSelect: function (selectedDateTime) {
                startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
            }
        });
    })
    $(function () {
        showDistricts('<?php echo $district; ?>');

        $('#province').change(function (e) {
            $('#warehouse').html('<option value="">Select</option>');
            showDistricts('');
        });
        $('#product').change(function (e) {
            showWarehouses('');
        });
        $(document).on('change', '#district', function () {
            $('#warehouse').html('<option value="">Select</option>');
            showWarehouses('');
        });
        $('#stakeholder').change(function (e) {
            $('#province').val('');
            $('#district').html('<option value="">Select</option>');
            $('#warehouse').html('<option value="">Select</option>');
        });
    })


</script>
</body>
</html>