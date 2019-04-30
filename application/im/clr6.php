<?php
/**
 * cls6
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses file
include("../includes/classes/AllClasses.php");
//Including header file
include(PUBLIC_PATH . "html/header.php");
//echo '<pre>';print_r($_SESSION);exit;
$where = " 1=1 ";
if(!empty($_SESSION['user_level']) && $_SESSION['user_level'] == '1')
{
    $where .= " AND tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1'];
}

if(!empty($_SESSION['user_level']) && $_SESSION['user_level'] == '2')
{
    $where .= " AND tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1'];
    $where .= " AND tbl_warehouse.prov_id = ".$_SESSION['user_province1'];
}

if(!empty($_SESSION['user_level']) && $_SESSION['user_level'] == '3')
{
    $where .= " AND tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1'];
    $where .= " AND tbl_warehouse.dist_id = ".$_SESSION['user_district'];
}




//Query
$qry = "SELECT
			CONCAT(DATE_FORMAT(clr_master.date_from, '%b-%Y'),' to ',DATE_FORMAT(clr_master.date_to, '%b-%Y')) AS duration,
			DATE_FORMAT(clr_master.requested_on,'%d/%m/%Y') AS requested_on,
			MONTH (clr_master.date_to) AS clrMonth,
			YEAR (clr_master.date_to) AS clrYear,
			clr_master.pk_id,
			clr_master.wh_id,
			clr_master.approval_status,
			clr_master.requisition_num,
                        DATE_FORMAT(clr_master.date_from, '%b-%Y') as date_from,
                        DATE_FORMAT(clr_master.date_to, '%b-%Y') as date_to
		FROM
			clr_master
                        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
		WHERE
			".$where."

		GROUP BY
			clr_master.date_from
		ORDER BY
			clr_master.requisition_num DESC,
			clrYear DESC,
			clrMonth DESC";
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);
?>
<style>
    body {
        margin: 0px !important;
        font-family: Arial, Helvetica, sans-serif;
    }
    table#myTable {
        margin-top: 20px;
        border-collapse: collapse;
        border-spacing: 0;
    }
    table#myTable tr td, table#myTable tr th {
        font-size: 13px;
        padding-left: 5px;
        text-align: left;
        border: 1px solid #999;
    }
    table#myTable tr td.TAR {
        text-align: right;
        padding: 5px;
        width: 50px !important;
    }
    .sb1NormalFont {
        color: #444444;
        font-family: Verdana, Arial, Helvetica, sans-serif;
        font-size: 13px;
        font-weight: bold;
        text-decoration: none;
    }
    p {
        margin-bottom: 5px;
        font-size: 13px !important;
        line-height: 1 !important;
        padding: 0 !important;
    }
    table#headerTable tr td {
        font-size: 13px;
    }
</style>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //Including top file
        include PUBLIC_PATH . "html/top.php";
        //Including top_im file
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->

                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">View Requisition</h3>
                            </div>
                            <div class="widget-body">
                                <table width="100%" align="center" cellspacing="3" cellpadding="5" id="myTable" class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th width="80">Sr. No.</th>
                                            <th>Requisition Number</th>
                                            <th>From</th>
                                            <th>To</th>
                                            <th>Requested On</th>
                                            <th>Status</th>
                                            <th width="50" style="text-align:center;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($num > 0) {
                                            $counter = 1;
                                            while ($row = mysql_fetch_array($qryRes)) {
                                                ?>
                                                <tr>
                                                    <td style="text-align:center;"><?php echo $counter++; ?></td>
                                                    <td><?php echo $row['requisition_num']; ?></td>
                                                    <td><?php echo $row['date_from']; ?></td>
                                                    <td><?php echo $row['date_to']; ?></td>
                                                    <td><?php echo $row['requested_on']; ?></td>
                                                    <td><?php echo str_replace("_", " ", $row['approval_status']); ?></td>
                                                    <td style="text-align:center;"><a href="clr_view.php?id=<?php echo $row['pk_id']; ?>&wh_id=<?php echo $row['wh_id']; ?>">View</a></td>
                                                </tr>
                                                <?php
                                            }
                                        } else {
                                            echo '<tr><td colspan="4">No record found</td></tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
//Including footer file
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    <script>
        $(function () {
            $('#sector').change(function (e) {
                var val = $('#sector').val();
                getStakeholder(val, '');
            });
            getStakeholder('<?php echo $rptType; ?>', '<?php echo $sel_stk; ?>');
        })
    </script>
    <?php
    if (isset($_REQUEST['e']) && $_REQUEST['e'] == '1') {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'CLR-6 is successfully saved',
                type: 'success',
                layout: self.data('layout')
            });
        </script>
    <?php } ?>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>