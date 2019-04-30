<?php
/**
 * add_stock
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//wh id
$wh_id = $_SESSION['user_warehouse'];
//loc id
$loc_id = $_REQUEST['loc_id'];
//select query
//gets
//A.PkStockID,
//PkDetailID,
//BatchID,
//TranDate,
//itm_name,
//qty_carton,
//itm_id,
//itm_type,
//TranNo,
//batch_no,
//batch_expiry,
//received,
//allocated,
//unallocated,
//receivedCarton,
// allocatedCarton,
// unallocatedCarton
$qry = "SELECT
			A.PkStockID,
			A.PkDetailID,
			A.BatchID,
			A.TranDate,
			A.itm_name,
			A.qty_carton,
			A.itm_id,
			A.itm_type,
			A.TranNo,
			A.batch_no,
			A.batch_expiry,
			A.Qty AS received,
			IFNULL(B.placedQty, 0) AS allocated,
			IFNULL((A.Qty - IFNULL(B.placedQty, 0)), 0) AS unallocated,
			(A.Qty / A.qty_carton) AS receivedCarton,
			IFNULL(B.placedQty, 0) / A.qty_carton AS allocatedCarton,
			(A.Qty / A.qty_carton) - (IFNULL(B.placedQty, 0) / A.qty_carton) AS unallocatedCarton
		FROM
			(
				SELECT
					tbl_stock_master.PkStockID,
					tbl_stock_detail.PkDetailID,
					tbl_stock_detail.BatchID,
					tbl_stock_master.TranDate,
					itminfo_tab.itm_name,
					itminfo_tab.qty_carton,
					itminfo_tab.itm_id,
					itminfo_tab.itm_type,
					tbl_stock_master.TranNo,
					stock_batch.batch_no,
					stock_batch.batch_expiry,
					SUM(tbl_stock_detail.Qty) AS Qty
				FROM
					tbl_stock_master
				INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
				INNER JOIN stock_batch ON tbl_stock_detail.BatchID = stock_batch.batch_id
				INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
				INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
				WHERE
					tbl_trans_type.trans_nature = '+'
				AND tbl_stock_master.WHIDTo = $wh_id
				GROUP BY
					tbl_stock_detail.BatchID
			) A
		LEFT JOIN (
			SELECT
				tbl_stock_master.PkStockID,
				tbl_stock_master.TranNo,
				SUM(placements.quantity) AS placedQty,
				tbl_stock_detail.BatchID
			FROM
				tbl_stock_master
			INNER JOIN tbl_stock_detail ON tbl_stock_master.PkStockID = tbl_stock_detail.fkStockID
			INNER JOIN placements ON tbl_stock_detail.PkDetailID = placements.stock_detail_id
			INNER JOIN tbl_trans_type ON tbl_stock_master.TranTypeID = tbl_trans_type.trans_id
			WHERE
				tbl_trans_type.trans_nature = '+'
			AND tbl_stock_master.WHIDTo = $wh_id
			AND placements.placement_transaction_type_id IN (89, 90)
			GROUP BY
				tbl_stock_detail.BatchID
		) B ON A.BatchID = B.BatchID
		WHERE
			(A.Qty - IFNULL(B.placedQty, 0)) > 0";
//query result
$result = mysql_query($qry) or die(mysql_error());
//get location name
$getLocationName = mysql_query("select location_name from placement_config where pk_id=" . $loc_id) or die(mysql_error());
//row location
$rowLocation = mysql_fetch_assoc($getLocationName);
?>
<style>
    .btn-link {
        color: #fff !important;
        text-shadow: none;
    }
</style>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
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

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="col-md-12"> 
                        <!-- Widget -->
                        <div class="widget"> 

                            <!-- Widget heading -->
                            <div class="widget-head">
                                <h3 class="heading"> Place stock from received list to Location: <?php echo $rowLocation['location_name'] ?> </h3>
                            </div>
                            <!-- // Widget heading END -->

                            <div class="widget-body"> 

                                <!-- Table --> 
                                <!-- Table -->

                                <form name="add_stock_frm" id="add_stock_frm" action="add_stock_action.php" method="post" onSubmit="return formValidation()">
                                    <table class="table table-striped table-bordered table-condensed">

                                        <!-- Table heading -->
                                        <thead>
                                            <tr>
                                                <th width="5%">S.No.</th>
                                                <th width="10%">Receive No.</th>
                                                <th width="12%">Product</th>
                                                <th width="8%">Batch</th>
                                                <th width="7%">Expiry</th>
                                                <th>Received Quantity</th>
                                                <th>Allocated Quantity</th>
                                                <th>Unallocated Quantity</th>
                                                <th>Allocate Quantity</th>
                                            </tr>
                                        </thead>
                                        <!-- // Table heading END -->
                                        <tbody>
                                            <!-- Table body --> 

                                            <!-- Table row -->
                                            <?php
                                            $counter = 1;
                                            //check if result exists
                                            if (mysql_num_rows($result) > 0) {
                                                //fetch result
                                                while ($rowStock = mysql_fetch_assoc($result)) {
                                                    //check received
                                                    if ($rowStock['received'] > 0) {
                                                        ?>
                                                        <tr class="gradeX">
                                                            <td class="center"><?php echo $counter; ?>
                                                                <input type="hidden" id="loc_id" name="loc_id" value="<?php echo $loc_id ?>"/></td>
                                                            <td><?php echo $rowStock['TranNo'] ?>
                                                                <input type="hidden" id="stock_detail_id" name="stock_detail_id[<?php echo $counter ?>]" value="<?php echo $rowStock['PkDetailID'] ?>"/></td>
                                                            <td><?php echo $rowStock['itm_name'] ?>
                                                                <input type="hidden" id="item_id" name="item_id[<?php echo $counter ?>]" value="<?php echo $rowStock['itm_id'] ?>"/></td>
                                                            <td><?php echo $rowStock['batch_no']; ?>
                                                                <input type="hidden" id="batch_id" name="batch_id[<?php echo $counter ?>]" value="<?php echo $rowStock['BatchID'] ?>"/>
                                                                <input type="hidden" id="qty_carton" name="qty_carton[<?php echo $counter ?>]" value="<?php echo $rowStock['qty_carton'] ?>"/></td>
                                                            <td><?php echo date('m/Y', (strtotime($rowStock['batch_expiry']))); ?></td>
                                                            <td><?php
                                                                //check received
                                                                if ($rowStock['received']) {
                                                                    //received
                                                                    echo number_format($rowStock['received']) . ' ' . $rowStock['itm_type'] . ' / ';
                                                                    //receivedCarton
                                                                    echo ((floor($rowStock['receivedCarton']) != $rowStock['receivedCarton']) ? number_format($rowStock['receivedCarton'], 2) : number_format($rowStock['receivedCarton'])) . ' Cartons';
                                                                } else {
                                                                    echo "0";
                                                                }
                                                                ?>
                                                                <input type="hidden" id="available_<?php echo $counter ?>" name="available_<?php echo $counter ?>" value="<?php echo $rowStock['received'] ?>"/>
                                                                <input type="hidden" id="batch_id" name="batch_id[<?php echo $counter ?>]" value="<?php echo $rowStock['BatchID'] ?>"/></td>
                                                            <td><?php
                                                                //check allocated
                                                                if ($rowStock['allocated'] > 0) {
                                                                    echo number_format($rowStock['allocated']) . ' ' . $rowStock['itm_type'] . ' / ';
                                                                    echo ((floor($rowStock['allocatedCarton']) != $rowStock['allocatedCarton']) ? number_format($rowStock['allocatedCarton'], 2) : number_format($rowStock['allocatedCarton'])) . ' Cartons';
                                                                } else {
                                                                    echo '0';
                                                                }
                                                                ?></td>
                                                            <td><?php
                                                                //check unallocated
                                                                if ($rowStock['unallocated'] > 0) {
                                                                    echo number_format($rowStock['unallocated']) . ' ' . $rowStock['itm_type'] . ' / ';
                                                                    echo ((floor($rowStock['unallocatedCarton']) != $rowStock['unallocatedCarton']) ? number_format($rowStock['unallocatedCarton'], 2) : number_format($rowStock['unallocatedCarton'])) . ' Cartons';
                                                                } else {
                                                                    echo '0';
                                                                }
                                                                ?></td>
                                                            <td><input autocomplete="off" class="qty form-control input-small input-sm" max="<?php echo round(($rowStock['unallocated']), 0) ?>" style="text-align:right;" <?php
                                                                       if ($rowStock['received'] == $rowStock['allocated']) {
                                                                           echo "readonly='readonly' ";
                                                                       }
                                                                       ?> type="text" name="allocate_qty[<?php echo $counter ?>]" id="allocate_qty_<?php echo $counter ?>" onKeyUp="showCartons(this.value, '<?php echo $rowStock['qty_carton']; ?>', '<?php echo $counter; ?>')" />
                                                                <span id="allocatedCarton<?php echo $counter; ?>"></span></td>
                                                        </tr>
                                                        <?php
                                                        $counter++;
                                                    }
                                                }
                                                ?>
                                                <tr>
                                                    <td width="10%" colspan="9" style="text-align:right;"><input type="hidden" id="hiddFld" name="hiddFld" value="<?php echo 'area=' . $_REQUEST['area'] . '&level=' . $_REQUEST['level']; ?>">
                                                        <button  type="submit" name="add_stock" id="add_stock" value="search" class="btn btn-primary">Save</button>
                                                        <button type="button" id="back_location_<?php echo $loc_id; ?>" class="btn btn-warning"> Back </button></td>
                                                </tr>
                                                <?php
                                            } else {
                                                echo '<tr><td colspan="9">No record found.</td></tr>';
                                                ?>
                                                <tr>
                                                    <td width="10%" colspan="9" style="text-align:right;"><input type="hidden" id="hiddFld" name="hiddFld" value="<?php echo 'area=' . $_REQUEST['area'] . '&level=' . $_REQUEST['level']; ?>">
                                                        <button type="button" id="back_location_<?php echo $loc_id; ?>" class="btn btn-warning"> Back </button></td>
                                                </tr>
    <?php
}
?>
                                            <!-- // Table row END -->
                                        </tbody>
                                    </table>
                                </form>
                                <!-- // Table END --> 
                            </div>
                        </div>
                        <!-- Widget --> 
                    </div>
                </div>
            </div>
        </div>
        <?php
//include footer
        include PUBLIC_PATH . "/html/footer.php";
        ?>
        <script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockplacement.js"></script>
<?php
//check success
if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
    ?>
            <script>
                                                            var self = $('[data-toggle="notyfy"]');
                                                            notyfy({
                                                                force: true,
                                                                text: 'Data has been saved successfully!',
                                                                type: 'success',
                                                                layout: self.data('layout')
                                                            });

            </script>
            <?php
            //unset session
            unset($_SESSION['success']);
        }
        ?>
        <script>
            function formValidation()
            {
                var q = 0;
                var inp = $('.qty');
                for (var i = 0; i < inp.length; i++) {
                    if (inp[i].value != '') {
                        q++;
                        if (parseFloat(inp[i].value) == 0)
                        {
                            alert('Quantity can not be 0');
                            inp[i].focus();
                            return false;
                        }
                        else if (parseFloat(inp[i].value) > parseFloat(inp[i].getAttribute('max'))) {
                            alert('Quantity can not be greater than ' + inp[i].getAttribute('max'));
                            inp[i].focus();
                            return false;
                        }
                    }
                }

                if (q == 0) {
                    alert('Please enter at least one quantity');
                    return false;
                }
                $('#add_stock').attr('disabled', true);
                $('#add_stock').html('Submitting...');
            }

            function showCartons(qty, cartonQty, cartonId)
            {
                $('#' + cartonId).html('').hide();
                if (qty != '' && parseInt(qty) > 0)
                {
                    var cartons = (parseFloat(qty) / parseFloat(cartonQty));
                    cartons = eval(cartons.toFixed(2) + 0) + ' Carton(s)';
                    $('#allocatedCarton' + cartonId).html(cartons).show();
                }
            }

            $(function() {
                $('.qty').keydown(function(e) {
                    if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
                        e.preventDefault();         // Prevent character input
                    } else {
                        var n = e.keyCode;
                        if (!(
                                (n == 8)              // backspace
                                || (n == 9)                // Tab
                                || (n == 46)                // delete
                                || (n >= 35 && n <= 40)     // arrow keys/home/end
                                || (n >= 48 && n <= 57)     // numbers on keyboard
                                || (n >= 96 && n <= 105)   // number on keypad
                                ))
                        {
                            e.preventDefault();     // Prevent character input
                        }
                    }
                });
            })
        </script>
</body>
<!-- END BODY -->
</html>