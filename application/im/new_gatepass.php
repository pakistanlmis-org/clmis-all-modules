<?php
//echo '<pre>';print_r($_REQUEST);exit;
/**
 * new_gatepass
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
include(PUBLIC_PATH."html/header.php");

//get wh id
$whId = $_SESSION['user_warehouse'];
//date from
$date_from = date('01' . '/m/Y');
//date to 
$date_to = date('d/m/Y');
/**
 * getClosest
 * 
 * @param type $array
 * @param type $search
 * @return type
 */
function getClosest($array, $search)
{
    $closest = null;
    foreach ($array as $key => $val) {
        if ($closest == null || abs($search - $closest) > abs($val - $search)) {
            $closest = $val;
            $closestKey = $key;
            $arr = array($closestKey, $closest);
        }
    }
    return $arr;
}
//check date from
if (isset($_POST['date_from'])) {
    if (empty($_POST['vehicle_other'])) {
        //get vehicle
        $veicle = $_POST['vehicle'];
    } else {
        // check if already exists
        $vehicleQryRes = mysql_fetch_array(mysql_query("SELECT
                                                                gatepass_vehicles.pk_id
                                                        FROM
                                                                gatepass_vehicles
                                                        WHERE
                                                                gatepass_vehicles.number = '" . $_POST['vehicle_other'] . "' "));
        if (empty($vehicleQryRes['pk_id'])) {
            //insert query
            //inserts
            //number
            //vehicle type id
            $qry = "INSERT INTO gatepass_vehicles
				SET
					number = '" . $_POST['vehicle_other'] . "',
					vehicle_type_id = '" . $_POST['vehicle_type'] . "' ";
            //execute query
            mysql_query($qry);
            $veicle = mysql_insert_id();
        } else {
            $veicle = $vehicleQryRes['pk_id'];
        }
    }
    //transaction date
    $transaction_date = dateToDbFormat($_POST['trans_date']);
//insert query
    $insQry = "INSERT INTO gatepass_master (number, transaction_date, gatepass_vehicle_id, warehouse_id)
				SELECT IF(MAX(number) IS NULL, 'GP000001', CONCAT('GP', LPAD((SUBSTR(MAX(number), 3) + 1), 6, 0))), '" . $transaction_date . "', " . $veicle . ", " . $whId . " FROM gatepass_master";
    //execute query
    mysql_query($insQry);
    $masterPKId = mysql_insert_id();
	
    $detailId = array();
    foreach ($_POST['qty'] as $key => $val)
	{
	//master id	
        $masterId = implode(',', $_POST['issue_no']);
		if (!empty($val))
		{
			$batchId = $key;
                        //select query
                        //gets
                        //pk detail id
                        //qty
                        //master id
                        //batch id
			$qry = "SELECT
						tbl_stock_detail.PkDetailID,
						ABS(tbl_stock_detail.Qty) AS Qty,
						tbl_stock_detail.fkStockID AS masterId,
						tbl_stock_detail.BatchID
					FROM
						tbl_stock_detail
					WHERE
						tbl_stock_detail.fkStockID IN ($masterId)
						AND tbl_stock_detail.BatchID = $batchId
					ORDER BY
						tbl_stock_detail.Qty ASC";
                        //query result
			$qryRes = mysql_query($qry);
			$dataArr = array();
                        //fetch result
			while ($row = mysql_fetch_array($qryRes))
			{
				$dataArr[$row['PkDetailID']] = $row['Qty'];
			}
			while ($val > 0)
			{
				$arr = getClosest($dataArr, $val);
				$qty = $val;
				$val = $val - $arr[1];
				unset($dataArr[$arr[0]]);
				if ($val > 0)
				{
					$detailId[$arr[0]] = (int)$arr[1];
				}
				else
				{
					$detailId[$arr[0]] = (int)$qty;
				}
			}
		}
    }
    foreach ($detailId as $d_id => $quantity) {
        //insert query
        //set
        //quantity
        //stock detail id
        //gatepass master id
        $insQry = "INSERT INTO gatepass_detail
			SET
				quantity = '" . $quantity . "',
				stock_detail_id = '" . $d_id . "',
				gatepass_master_id = '" . $masterPKId . "' ";
        mysql_query($insQry);
    }
	$_SESSION['e'] = 1;
    echo "<script>window.location='view_gatepass.php'</script>";
	exit;
}
?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo PUBLIC_URL;?>css/jsDatePick_ltr.css"/>
</head><!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content">
<!-- BEGIN HEADER -->
<div class="page-container">
	<?php include PUBLIC_PATH."html/top.php";?>
	<?php include PUBLIC_PATH."html/top_im.php";?>
    <div class="page-content-wrapper">
        <div class="page-content"> 
            
            <!-- BEGIN PAGE HEADER-->
            
            <div class="row">
                <div class="col-md-12">
                    <div class="widget">
                        <div class="widget-head">
                            <h3 class="heading">New Gate Pass</h3>
                        </div>
                        <div class="widget-body">
                            <form name="frm" id="frm" action="" method="post" onSubmit="return formValidation()">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label class="control-label">Date From<span class="red">*</span></label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-small" name="date_from" id="date_from" readonly value="<?php echo $date_from;?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label class="control-label">Date To<span class="red">*</span></label>
                                                <div class="controls">
                                                    <input type="text" class="form-control input-small" name="date_to" id="date_to" readonly  value="<?php echo $date_to;?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label class="control-label">&nbsp;</label>
                                                <div class="controls">
                                                    <input type="button" id="searchBtn" value="Search" class="btn btn-primary"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="row1" style="display:none;">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label class="control-label">Vehicle Type<span class="red">*</span></label>
                                                <div class="controls">
                                                    <select name="vehicle_type" id="vehicle_type" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                        <?php
                                        $qry = mysql_query("SELECT
                                                        gatepass_vehicle_types.pk_id,
                                                        gatepass_vehicle_types.vehicle_type
                                                    FROM
                                                        gatepass_vehicle_types
                                                    ORDER BY
                                                    gatepass_vehicle_types.vehicle_type ASC");
                                        while ($row = mysql_fetch_array($qry)) {
                                            ?>
                                                        <option value="<?php echo $row['pk_id'];?>"><?php echo $row['vehicle_type'];?></option>
                                                        <?php
                                        }
                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label class="control-label">Vehicle<span class="red">*</span></label>
                                                <div class="controls">
                                                    <select name="vehicle" id="vehicle" style="width:130px;" class="form-control input-medium">
                                                        <option value="">Select</option>
                                                    </select>
                                                    <input type="text" name="vehicle_other" id="vehicle_other" style="display:none;" class="form-control input-medium" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label class="control-label">&nbsp;</label>
                                                <div class="controls">
                                                    <input type="checkbox" name="vehicle_other_option" id="vehicle_other_option" value="1"/>
                                                    <label class="sb1NormalFont">Other</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" id="row2" style="display:none;">
                                    <div class="col-md-12">
                                        <div class="col-md-3">
                                            <div class="control-group">
                                                <label class="control-label">Issue No.<span class="red">*</span></label>
                                                <div class="controls">
                                                    <select name="issue_no[]" id="issue_no" multiple="multiple" size="5" class="form-control input-medium">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="control-group">
                                                <label class="control-label">Date<span class="red">*</span></label>
                                                <div class="controls">
                                                    <input type="text" name="trans_date" id="trans_date" value="<?php echo date('d/m/Y');?>" readonly class="form-control input-small"/>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table cellpadding="7" style="width: 100%;">
                                    <tr>
                                        <td colspan="4" id="eMsg"></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td width="100%" id="gridData" colspan="4"></td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <tr id="row3" style="display:none; text-align:right;">
                                        <td colspan="4"><input type="submit" id="submit" name="submit" value="Save" class="btn btn-primary"/></td>
                                    </tr>
                                </table>
                            </form>
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
include PUBLIC_PATH."/html/footer.php";?>
<script type="text/javascript" src="<?php echo PUBLIC_URL?>js/jsDatePick.full.1.3.js"></script> 
<script type="text/javascript">
window.onload = function () {
	$('#date_from').datepicker({
		dateFormat: "dd/mm/yy",
		constrainInput: false,
		maxDate: 0,
		changeMonth: true,
		changeYear: true,
	});
	$('#date_to').datepicker({
		dateFormat: "dd/mm/yy",
		constrainInput: false,
		maxDate: 0,
		changeMonth: true,
		changeYear: true,
	});
	$('#trans_date').datepicker({
		dateFormat: "dd/mm/yy",
		constrainInput: false,
		maxDate: 0,
		changeMonth: true,
		changeYear: true,
	});
//        new JsDatePick({
//            useMode:2,
//            target:"date_from",
//            cellColorScheme:"beige",
//            dateFormat:"%d/%m/%Y"
//            /*selectedDate:{				This is an example of what the full configuration offers.
//                day:5,						For full documentation about these settings please see the full version of the code.
//                month:9,
//                year:2006
//            },
//            yearsRange:[1978,2020],
//            limitToToday:false,
//            cellColorScheme:"beige",
//            dateFormat:"%m-%d-%Y",
//            imgPath:"img/",
//            weekStartDay:1*/
//        });
};
</script> 
<script>
$(function () {
	showTransactions();
	$('#vehicle_type').change(function (e) {
		getVehicles();
	});
	$('#searchBtn').click(function (e) {
		$('#row3').hide();
		$('#gridData').html('');
		if ($('#date_from').val() == '') {
			alert('Please select date from');
			return false;
		}
		if ($('#date_to').val() == '') {
			alert('Please select date to');
			return false;
		}
		showTransactions();
	});
	$('#vehicle_other_option').click(function (e) {
		if ($('#vehicle_other_option').is(':checked')) {
			$('#vehicle_other').show();
			$('#vehicle').hide();
		}
		else {
			$('#vehicle_other').hide();
			$('#vehicle').show();
		}
	});
	$('#issue_no').change(function (e) {
		var issueNum = $('#issue_no').val();
		$.ajax({
			url:'gatepass_ajax.php',
			type:'POST',
			data:{issueNum:issueNum},
			success:function (data) {
				$('#gridData').html(data);
				$('#row3').show();
			}
		})
	});
})
function getVehicles() {
	var vehicleType = $('#vehicle_type').val();
	if (vehicleType != '') {
		$.ajax({
			url:'gatepass_ajax.php',
			type:'POST',
			data:{vehicleType:vehicleType},
			success:function (data) {
				$('#vehicle').html(data);
			}
		})
	}
	else {
		$('#vehicle').html('<option value=""></option>');
	}
}
function showTransactions() {
	var dateFrom = $('#date_from').val();
	var dateTo = $('#date_to').val();
	$.ajax({
		url:'gatepass_ajax.php',
		type:'POST',
		data:{dateFrom:dateFrom, dateTo:dateTo},
		success:function (data) {
			if (data != '') {
				$('#issue_no').html(data);
				$('#row1').show();
				$('#row2').show();
				$('#eMsg').html('');
			}
			else {
				$('#eMsg').html('No record found.');
				$('#row1').hide();
				$('#row2').hide();
			}
		}
	})
}
// validation
function formValidation() {
	if ($('#vehicle_type').val() == '') {
		alert('Please select vehicle type');
		$('#vehicle_type').focus();
		return false;
	}
	if ($('#vehicle_other_option').is(':checked')) 
	{
		if($('#vehicle_other').val() == '')
		{
			alert('Please enter vehicle number');
			$('#vehicle_other').focus();
			return false;
		}
	}
	else
	{
		if ($('#vehicle').val() == '') {
			alert('Please select vehicle');
			$('#vehicle').focus();
			return false;
		}
	}
	if ($('#trans_date').val() == '') {
		alert('Please enter transaction date');
		$('#trans_date').focus();
		return false;
	}
	var q = 0;
	var inp = $('.qty');
	for (var i = 0; i < inp.length; i++) {
		if (inp[i].value != '') {
			q++;
			if (parseInt(inp[i].value) == 0)
			{
				alert('Quantity can not be 0');
				inp[i].focus();
				return false;
			}
			if (parseInt(inp[i].value) > parseInt(inp[i].getAttribute('max'))) {
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
	
	$('#submit').attr('disabled', true);
	$('#submit').val('Submitting...');
}
</script>
<?php
if (isset($_SESSION['e'])) {
	?>
<script>
		var self = $('[data-toggle="notyfy"]');
		notyfy({
			force: true,
			text: 'Gatepass generated successfully!',
			type: 'success',
			layout: self.data('layout')
		});
	</script>
<?php 
//unset session
	unset($_SESSION['e']);
}
?>
</body>
<!-- END BODY -->
</html>