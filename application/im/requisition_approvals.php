<?php
/**
 * requisition
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//District level requisition approvals
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");

//echo '<pre>';print_r($_SESSION);exit;
// If delete request
if (isset($_REQUEST['did'])) {
    $id = $_REQUEST['did'];
    //delete query
    $qry = "DELETE FROM clr_master WHERE pk_id = $id";
    mysql_query($qry);
	echo "<script>window.location='requisitions.php'</script>";
    //header("Location: requisitions.php");
    $_SESSION['e'] = 0;
    exit;
}

$where = 'WHERE 1=1';
$is_provincial_user = false;
$req_num = '';
//requisition number
$requisitionNum = '';
//selected district
$sel_dist = '';
//status
$status = '';

// If Provincial User
if ($_SESSION['user_level'] == 2) {
    $is_provincial_user = true;
    //selected province
    $sel_prov = $_SESSION['user_province1'];
    //filter
    $where = " AND tbl_warehouse.prov_id = $sel_prov";
}
//if form sumitted
if (isset($_REQUEST['submit'])) {
    $where = 'WHERE 1=1';
    if ($is_provincial_user) {
        //get selected province
        $sel_prov = $_SESSION['user_province1'];
        $where .= " AND tbl_warehouse.prov_id = $sel_prov";
    } else if (isset($_REQUEST['province']) && !empty($_REQUEST['province'])) {
        //get selected province
        $sel_prov = $_REQUEST['province'];
        $where .= " AND tbl_warehouse.prov_id = $sel_prov";
    }
    //check district
    if (isset($_REQUEST['districts']) && !empty($_REQUEST['districts'])) {
        //get selected district
        $sel_dist = $_REQUEST['districts'];
        $where .= " AND tbl_warehouse.dist_id = $sel_dist";
    }
    //check stakeholder
    if (isset($_REQUEST['stakeholder']) && !empty($_REQUEST['stakeholder'])) {
        //get stakeholder
        $sel_stk = $_REQUEST['stakeholder'];
        $where .= " AND stakeholder.stkid = $sel_stk";
    }
    //check item
    if (isset($_REQUEST['item']) && !empty($_REQUEST['item'])) {
        //get item
        $sel_item = $_REQUEST['item'];
        $where .= " AND clr_details.itm_id = '$sel_item'";
    }
    //requisition number
    if (isset($_REQUEST['req_num']) && !empty($_REQUEST['req_num'])) {
        //get requisition number
        $req_num = $_REQUEST['req_num'];
        $where .= " AND clr_master.requisition_num = '$req_num'";
    }
    //check status
    if (isset($_REQUEST['status']) && !empty($_REQUEST['status']) && $_REQUEST['status'] != 'All') {
        //get status
        $status = $_REQUEST['status'];
        $where .= " AND clr_master.approval_status = '$status'";
    }
    //check date from
    if (isset($_REQUEST['date_from']) && !empty($_REQUEST['date_from']) && isset($_REQUEST['date_to']) && !empty($_REQUEST['date_to'])) {
        //get date from
        $date_from = $_REQUEST['date_from'];
        //get date to
        $date_to = $_REQUEST['date_to'];

        $date_from1 = dateToDbFormat($_REQUEST['date_from']);
        $date_to1 = dateToDbFormat($_REQUEST['date_to']);

        $where .= " AND DATE_FORMAT(clr_master.requested_on, '%Y-%m-%d') BETWEEN '$date_from1' AND '$date_to1'";
    }
} else {
    if (!empty ($_SESSION['user_level']) && $_SESSION['user_level'] == 3) {
        //filter
        $where .= " AND tbl_warehouse.dist_id = ".$_SESSION['user_district']." ";
        
        if (!empty ($_SESSION['user_stakeholder1']) )
        $where .= " AND tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1']. " ";
    }
    //date from
    $date_from = date('01/m/Y');
    //date to
    $date_to = date('d/m/Y');
    //db date from
    $date_from1 = dateToDbFormat($date_from);
    //db date to
    $date_to1 = dateToDbFormat($date_to);
    //filter
    $where .= " AND DATE_FORMAT(clr_master.requested_on, '%Y-%m-%d') BETWEEN '$date_from1' AND '$date_to1'";
    $where .= " AND stakeholder.stkid = " . $_SESSION['user_stakeholder1'];
}
//select query
//gets

 $qry = "SELECT
                stakeholder.stkname,
                clr_master.pk_id,
                clr_master.requisition_num,
                clr_master.wh_id,
                clr_master.fk_stock_id,
                clr_master.approval_status,
                MONTH (clr_master.date_to) AS clrMonth,
                YEAR (clr_master.date_to) AS clrYear,
                DATE_FORMAT(clr_master.date_from, '%b-%Y') mon_from,
                DATE_FORMAT(clr_master.date_to, '%b-%Y') mon_to,
                tbl_warehouse.wh_type_id,
                tbl_warehouse.wh_name,
                tbl_locations.LocName,
                CONCAT(DATE_FORMAT(clr_master.requested_on, '%d/%m/%Y'), ' ', TIME_FORMAT(clr_master.requested_on, '%h:%i:%s %p')) AS requested_on,
                tbl_warehouse.dist_id
        FROM
                clr_master
        INNER JOIN stakeholder ON clr_master.stk_id = stakeholder.stkid
        INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
        INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
        INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
        $where
        GROUP BY
                clr_master.requisition_num
        ORDER BY
                clr_master.requisition_num DESC,
                tbl_locations.LocName ASC,
                tbl_warehouse.wh_name ASC";
//echo '<pre>';
//print_r($_SESSION);exit;
// echo $qry;
//query result
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);


$qry2 = " SELECT
                requisition_module_flow.action_id,
                requisition_module_flow.can_submit_to,
                requisition_module_flow.is_active,
                requisition_module_flow.prov_id,
                requisition_module_flow.stk_id
            FROM
                requisition_module_flow
            WHERE
                requisition_module_flow.action_id = 1 AND
                requisition_module_flow.can_submit_to = 3 AND
                requisition_module_flow.is_active = 1 AND
                requisition_module_flow.prov_id = ".$_SESSION['user_province1']." AND
                requisition_module_flow.stk_id = ".$_SESSION['user_stakeholder1']." ";
$qryRes2 = mysql_query($qry2);
$num2 = mysql_num_rows($qryRes2);

if($num2>0)
{
    $approve_reqs_is_active = TRUE;
}
else
{
    $approve_reqs_is_active = FALSE;
}
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
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
                    <form name="frm" id="frm" action="" method="get">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Requisition Search</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?php
                                            if ($is_provincial_user) {
                                                $display = 'style="display:none"';
                                            } else {
                                                $display = 'style="display:block"';
                                            }
                                            ?>
                                            <div class="col-md-3" <?php echo $display; ?>>
                                                <div class="control-group ">
                                                    <label class="control-label">Province</label>
                                                    <div class="controls">
                                                        <select name="province" id="province" class="form-control input-medium" <?=((!empty($_SESSION['user_level']) && $_SESSION['user_level']>1)?'readonly':'') ?> >
                                                            
                                                            <?php
                                                            //select query
                                                            //gets
                                                            //province id
                                                            //province title
                                                            $where="";
                                                            if($_SESSION['user_level']>1) 
                                                            {
                                                                $where = " AND tbl_locations.PkLocID= ".$_SESSION['user_province1']." ";
                                                                
                                                            }
                                                            $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2
                                                                            $where
                                                                        AND parentid IS NOT NULL";
															//query result
															$rsprov = mysql_query($queryprov) or die();
															//fetch result
                                                            while ($row = mysql_fetch_array($rsprov)) {
                                                                if ($sel_prov == $row['prov_id']) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                //populate province combo
                                                                ?>
                                                                <option value="<?php echo $row['prov_id']; ?>" <?php echo $sel; ?>><?php echo $row['prov_title']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">District</label>
                                                    <div class="controls" id="districtsCol">
                                                        <select name="districts" id="districts" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
											if($_SESSION['user_warehouse'] == 123)
											{
											?>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stakeholder" id="stakeholder" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                            <?php
															//select query
															//gets
															//stakeholder id
															//stakeholder name
                                                            $querystk = "SELECT
                                                                                stkid,
                                                                                stkname
                                                                        FROM
                                                                                stakeholder
                                                                        WHERE
                                                                                ParentID IS NULL
                                                                                AND stakeholder.stk_type_id = 0
                                                                        ORDER BY
                                                                                stkorder";
															//query result
                                                            $rsstk = mysql_query($querystk) or die();
															//fetch result
                                                            while ($row = mysql_fetch_array($rsstk)) {
                                                                if ($sel_stk == $row['stkid']) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $row['stkid']; ?>" <?php echo $sel; ?>><?php echo $row['stkname']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
											}
											else
											{
												echo "<input type=\"hidden\" name=\"stakeholder\" id=\"stakeholder\" value=\"".$_SESSION['user_stakeholder1']."\">";
											}
											?>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">Status</label>
                                                    <div class="controls">
                                                        <select name="status" id="status" class="form-control input-medium">
                                                            <option value="All" <?php echo ($status == 'All') ? 'selected="selected"' : ''; ?>>All</option>
                                                            <option value="Pending" <?php echo ($status == 'Pending') ? 'selected="selected"' : ''; ?>>Pending</option>
                                                            <option value="Dist_Approved" <?php echo ($status == 'Dist_Approved') ? 'selected="selected"' : ''; ?>>Approved by District</option>
                                                            <option value="Prov_Approved" <?php echo ($status == 'Prov_Approved') ? 'selected="selected"' : ''; ?>>Approved by Province</option>
                                                            <option value="Denied" <?php echo ($status == 'Denied') ? 'selected="selected"' : ''; ?>>Denied</option>
                                                            <option value="Issued" <?php echo ($status == 'Issued') ? 'selected="selected"' : ''; ?>>Issued</option>
                                                            <option value="Issue in Process" <?php echo ($status == 'Issue in Process') ? 'selected="selected"' : ''; ?>>Issue in Process</option>
                                                            <option value="Approved" <?php echo ($status == 'Approved') ? 'selected="selected"' : ''; ?>>Approved</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-3" style="display:none">
                                                <div class="control-group ">
                                                    <label class="control-label">Product</label>
                                                    <div class="controls">
                                                        <select name="item" id="item" class="form-control input-medium">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">Date From</label>
                                                    <div class="controls">
                                                        <input type="text" name="date_from" id="date_from" class="form-control input-medium" value="<?php echo $date_from; ?>" readonly />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">Date To</label>
                                                    <div class="controls">
                                                        <input type="text" name="date_to" id="date_to" value="<?php echo $date_to; ?>" readonly class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group ">
                                                    <label class="control-label">Requisitions #</label>
                                                    <div class="controls">
                                                        <input type="text" name="req_num" id="req_num" value="<?php echo $req_num; ?>" class="form-control input-medium" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12 right">
                                            <div class="control-group">
                                                <label class="control-label">&nbsp;</label>
                                                <div class="controls">
                                                    <input type="submit" name="submit" value="Search" class="btn btn-primary" />
                                                    <input type="button" onClick="window.location = '<?php echo $_SERVER['PHP_SELF']; ?>'" value="Reset" class="btn btn-info" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Requisitions</h3>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="requisitions table table-striped table-bordered table-condensed">
                                            <thead>
                                                <tr>
                                                    <th>Sr. No.</th>
                                                    <th>Requisition No.</th>
                                                    <th>Stakeholder</th>
                                                    <th>Store Name</th>
                                                    <th>From</th>
                                                    <th>To</th>
                                                    <th>Requested On</th>
                                                    <th>Status</th>
                                                    <th>Issue Vouchers</th>
                                                    <?php if ($_SESSION['user_role'] != 5) { ?>
                                                        <th>Action</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                //if ($num > 0)
                                                {
                                                    $counter = 1;
                                                    //fetch result
                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                        ?>
                                                    <input type="hidden" name="warehouse" id="warehouse" value="<?php echo $row['wh_id'] ?>"/>
                                                    <input type="hidden" name="clr6_id" id="clr6_id" value="<?php echo $row['pk_id'] ?>"/>
                                                    <input type="hidden" name="rq_no" value="<?php echo $requisitionNum ?>"/>
                                                    <tr>
                                                        <td style="text-align:center;"><?php echo $counter++; ?></td>
                                                        <td>
                                                            <?php
                                                            echo $row['requisition_num'];
                                                            
                                                            ?>
                                                        </td>
                                                        <td><?php echo $row['stkname']; ?></td>
                                                        <td>
                                                            <?php
                                                            $whName = $row['wh_name'];
                                                            $whName .=!empty($row['wh_type_id']) ? " ($row[wh_type_id])" : '';
                                                            echo $whName;
                                                            ?>
                                                        </td>
                                                        
                                                        <td><?php echo $row['mon_from']; ?></td>
                                                        <td><?php echo $row['mon_to']; ?></td>
                                                        <td><?php echo $row['requested_on']; ?></td>
                                                        <td><?php echo str_replace("_", " ", $row['approval_status']); ?></td>
                                                        <td>
                                                            <?php
                                                            //select query
                                                            //gets
                                                            //transaction number
                                                            //pk stock id
                                                            $getStockIssues = mysql_query("SELECT DISTINCT
                                                                                                tbl_stock_master.TranNo,
                                                                                                tbl_stock_master.PKStockId
                                                                                        FROM
                                                                                                clr_details
                                                                                        INNER JOIN tbl_stock_master ON clr_details.stock_master_id = tbl_stock_master.PkStockID
                                                                                        WHERE
                                                                                                tbl_stock_master.TranTypeID = 2 AND
                                                                                                clr_details.pk_master_id = " . $row['pk_id'] . "
                                                                                        ORDER BY
                                                                                                tbl_stock_master.PkStockID ASC") or die("Err GetStockIssueId");


                                                            //chech if record exists
                                                            if (mysql_num_rows($getStockIssues) > 0) {
                                                                $issueVoucher = '';
                                                                //fetch results
                                                                while ($resStockIssues = mysql_fetch_assoc($getStockIssues)) {
                                                                    $issueVoucher[] = "<a onClick=\"window.open('" . APP_URL . "im/printIssue.php?id=" . $resStockIssues['PKStockId'] . "', '_blank', 'scrollbars=1,width=842,height=595')\" href=\"javascript:void(0);\">" . $resStockIssues['TranNo'] . "</a>";
                                                                }
                                                                echo implode(', ', $issueVoucher);
                                                            } else {
                                                                echo "N/A";
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php if ($_SESSION['user_role'] != 5) { ?>
                                                            <td class="left">
                                                                <a href="clr_view.php?id=<?php echo $row['pk_id']; ?>&wh_id=<?php echo $row['wh_id']; ?>">View</a>
                                                                <?php
                                                                //check if provincial user
                                                                
                                                                if ($_SESSION['user_level']=='3'){
                                                                    if ($row['approval_status'] == 'Pending') {
                                                                        if($row['dist_id']==$_SESSION['user_district'])
                                                                        {
                                                                        
                                                                            if($approve_reqs_is_active)
                                                                            {
                                                                        ?>
                                                                                | <a onClick="return confirm('Are you sure, you want to delete this record?')" href="clr6_list.php?did=<?php echo $row['pk_id']; ?>&wh_id=<?php echo $row['wh_id']; ?>">Delete</a>
                                                                                | <a href="<?php echo APP_URL ?>im/approve_clr6.php?id=<?php echo $row['pk_id']; ?>&wh_id=<?php echo $row['wh_id']; ?>&rq=<?php echo $row['requisition_num'] ?>">Approve</a> 
                                                                        <?php 
                                                                            }
                                                                        }
                                                                    }
                                                                    
                                                                }
                                                                ?>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                    <?php
                                                }
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
        </div>
    </div>

    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>

    <script type="text/javascript">
        $(function() {
            $("#date_from, #date_to").datepicker({
                dateFormat: 'dd/mm/yy',
                constrainInput: false,
                changeMonth: true,
                changeYear: true
            });
        })
    </script>
    <script>
<?php
//check districts
$dist='';
if(!empty($_SESSION['user_district']) && $_SESSION['user_level'] > 2)
{
    $dist = $_SESSION['user_district'];
    ?>
    //show districts    
    showDistricts('<?php echo $dist; ?>');
    <?php
}
else if (isset($_REQUEST['districts']) && !empty($_REQUEST['districts'])) 
{    
    $dist = $_REQUEST['districts'];
    ?>
    //show districts    
    showDistricts('<?php echo $dist; ?>');
    <?php
}
$sel_dist = $dist;
//check item
if (isset($_REQUEST['item']) && !empty($_REQUEST['item'])) {
    ?>
            //show products
            showProducts('<?php echo $_REQUEST['item']; ?>');
    <?php
}
?>

        $(function() {
            showDistricts();
            $('#province').change(function(e) {
                showDistricts();
            });
        })
        $(function() {
            $('#stakeholder').change(function(e) {
                $('#item').html('<option value="">Select</option>');
                showProducts('');
            });
        })
        $("#approve_status").click(function() {
            var id, warehouse;
            id = $("#clr6_id").val();
            warehouse = $("#warehouse").val();
            window.open('<?php echo APP_URL ?>im/print_approve_clr6.php?id=' + id + '&wh_id=' + warehouse, '_blank', 'scrollbars=1,width=842,height=595');
        });
        function showDistricts() {
            var pid = $('#province').val();
                pid = <?php echo $_SESSION['user_province1']; ?>;
            if (pid != '')
            {
                $.ajax({
                    url: 'fetchDistricts.php',
                    type: 'POST',
                    data: {pid: pid, distId: '<?php echo $sel_dist; ?>', user_level: '<?php echo (!empty($_SESSION['user_level'])?$_SESSION['user_level']:''); ?>'},
                    success: function(data) {
                        $('#districtsCol').html(data);
                        var test = '<option value="">All</option>' + $('#districts').html();
                        $('#districts').html(test);
                    }
                })
            }
        }
        function showProducts(pid) {
            var stk = $('#stakeholder').val();
            $.ajax({
                url: '<?php echo APP_URL; ?>reports/my_report_ajax.php',
                type: 'POST',
                data: {stakeholder: stk, productId: pid},
                success: function(data) {
                    $('#item').html(data);
                }
            })
        }
    </script>
    <?php
    //check session
    if (isset($_SESSION['e']) && $_SESSION['e'] == '0') {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'Requisition has been successfully deleted',
                type: 'success',
                layout: self.data('layout')
            });
        </script>
<?php 
	unset($_SESSION['e']);
} ?>

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