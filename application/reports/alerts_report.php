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


if (isset($_REQUEST['search'])) {
    $alerts_type = $_REQUEST['alerts_type'];
    $interface_type = $_REQUEST['interface'];
    $subject = addslashes($_REQUEST['subject']);
}
$fileName = "Alerts Report";
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

                        <h3 class="page-title row-br-b-wp">Alerts Log Report</h3>
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
                                                    <div class="form-group" id="districtsCol">
                                                        <label class="control-label">Alerts Type</label>
                                                        <select name="alerts_type" id="alerts_type" class="form-control input-sm" required="">
                                                            <option value="">Select</option>
                                                            <?php
                                                            $qry_type = "SELECT 
                                                                    DISTINCT alerts_log.type  
                                                                    FROM
                                                                    alerts_log
                                                                    ";
                                                            $res_type = mysql_query($qry_type);
                                                            while ($row1 = mysql_fetch_array($res_type)) {
                                                                ?>
                                                                <option value="<?php echo $row1['type'] ?>" <?php if (isset($_REQUEST['search'])) if ($alerts_type == $row1['type']) echo "selected=selected"; ?>><?php echo $row1['type'] ?></option>

                                                                <?php
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group" id="districtsCol">
                                                        <label class="control-label">Interface</label>
                                                        <select name="interface" id="interface" class="form-control input-sm"  >
                                                            <option value="">Select</option>
                                                            <?php
                                                            $qry_interface = "SELECT 
                                                                    DISTINCT alerts_log.interface  
                                                                    FROM
                                                                    alerts_log
                                                                    ";
                                                            $res_interface = mysql_query($qry_interface);
                                                            while ($row1 = mysql_fetch_array($res_interface)) {
                                                                ?>
                                                                <option value="<?php echo $row1['interface'] ?>" <?php if (isset($_REQUEST['search'])) if ($interface_type == $row1['interface']) echo "selected=selected"; ?> ><?php echo $row1['interface'] ?></option>

                                                                <?php
                                                            }
                                                            ?>

                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <div class="form-group" >
                                                        <label class="control-label">Subject</label>
                                                        <input class="form-control input-sm" id="subject" name="subject" <?php if (isset($_REQUEST['search'])) { ?>value="<?php echo $subject; ?>"<?php } ?> maxlength="255">
                                                    </div>
                                                </div>



                                                <div class="col-md-12" style="text-align:right;">
                                                    <label for="firstname">&nbsp;</label>
                                                    <div class="form-group">
                                                        <button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
                                                        <button type="reset" class="btn btn-info">Reset</button>
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
                <?php if (isset($_REQUEST['search'])) { ?>
                    <div class="row">
                        <div class="col-md-12">


                            <div class="widget">
                                <div class="widget-body">
                                    <?php include('sub_dist_reports.php'); ?>
                                    <div class="row"><br></div>
                                    <?php
                                    $qry = "SELECT
                                            alerts_log.pk_id, 
                                            alerts_log.`to`,
                                            alerts_log.cc,
                                            alerts_log.`subject`,
                                            alerts_log.body, 
                                            alerts_log.response,
                                            alerts_log.type,
                                            alerts_log.interface, 
                                            alerts_log.sent_by
                                            FROM
                                            alerts_log
                                            WHERE
                                            alerts_log.`body` LIKE '%$subject%'
                                            AND alerts_log.type='$alerts_type'";
                                    if (!empty($interface_type))
                                        $qry .= "  AND alerts_log.interface='$interface_type' ";
//                                    print_r($qry);
//                                    exit;
                                    $res = mysql_query($qry);
                                    $num = mysql_num_rows($res);
                                    $count = 0;
                                    $cons_array = array();
                                    if ($num > 0) {
                                        ?>
                                        <table style="width:95%;margin-left: 2%;" align="center"   id="myTable" class="table table-striped table-bordered table-condensed" style="border:none;">
                                            <thead style="background-color:lightgray">
                                            <th>S. No.</th>
                                            <th>To</th>
                                            <th>Copied To</th>
                                            <th width="40%">Subject</th>
                                            <th width="25%">Message</th>
                                            <th>Response</th>
                                            <th>Type</th>
                                            <th>Interface</th>
                                            <th>Body</th>


                                            </thead>
                                            <?php
                                            $counter = 1;
                                            while ($row = mysql_fetch_assoc($res)) {
                                                //echo '<pre>';print_r($row);exit;
                                                
                                                $m = strip_tags($row['body']);
                                                $msg = str_replace(" ", "", $m);
                                                $msg2 =substr($msg, 0, 50);
                                                ?>
                                                <tbody>

                                                    <tr>
                                                        <td><?php echo $counter++; ?></td>
                                                        <td><?php echo wordwrap($row['to'],50,'<br/>',true); ?></td>
                                                        <td><?php echo $row['cc']; ?></td>
                                                        <td ><?php echo $row['subject']; ?></td>
                                                        <td><?php echo $msg2; ?> ...</td>
                                                        <td><?php echo $row['response']; ?></td>
                                                        <td><?php echo $row['type']; ?></td>
                                                        <td><?php echo $row['interface']; ?></td>
                                                        <td><a class="btn btn-xs green" onclick="window.open('\ajax_alert.php?body_id=<?php echo $row['pk_id'] ?>', '_blank', 'width=700,height=900')" style="cursor:pointer;">View</a></td>
                                                    </tr>

                                                </tbody>
        <?php }
        ?>


                                        </table>
    <?php } else {
        ?><div style="margin-left: 15px;"><label> <?php echo 'No record found'; ?>  </label> </div><?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
<?php } ?>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
<?php include PUBLIC_PATH . "/html/footer.php"; ?>

    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>



</body>
<!-- END BODY -->
</html>