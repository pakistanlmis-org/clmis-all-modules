<?php
/**
 * data_entry_admin1
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//including AllClasses
include("../includes/classes/AllClasses.php");
//including header
include(PUBLIC_PATH . "html/header.php");
//checking user_id
if (isset($_SESSION['user_id'])) {
    //getting user_id
    $userid = $_SESSION['user_id'];
    //setting user_id
    $objwharehouse_user->m_npkId = $userid;
    //Get wh user By Idc
    $result = $objwharehouse_user->GetwhuserByIdc();
} else {
    //display msg
    echo "user not login or timeout";
}

$_SESSION['LIMIT'] = (date('Y') - 2010) * 12 + date('m') - 1;

$sel_stk = '';
$sel_prov = '';
$sel_dist = '';
$sel_wh = '';
//Checking warehouse
if (isset($_GET['warehouse'])) {
    //Getting stk_sel
    $sel_stk = $_GET['stk_sel'];
    //Getting province
    $sel_prov = $_GET['province'];
    //Getting district
    $sel_dist = $_GET['district'];
    //Getting warehouse
    $sel_wh = $_GET['warehouse'];
}

//Checking wharehouse_id
if (isset($_GET['wharehouse_id'])) {
    //Getting wharehouse_id
    $wh_Id = $_GET['wharehouse_id'];
    //start date
    $startDate = date('2010-01-01');
    //end date
    $endDate = date('2015-02-01');
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    //date interval
    $i = DateInterval::createFromDateString('1 month');
    //Loop
    while ($end >= $start) {
        $selected = (!empty($rpt_date) && $end->format("Y-m") == $rpt_date) ? 'selected="selected"' : '';
        //encode url
        $do3Months = urlencode("Z" . ($wh_Id + 77000) . '|' . $end->format("Y-m-") . '01|0');
        $url = "data_entry1.php?Do=" . $do3Months;
        $allMonths[] = "<a href=\"javascript:void(0);\" onclick=\"openPopUp('$url')\" class=\"btn btn-xs red\">" . $end->format("M-Y") .(!empty($draft))." <i class=\"fa fa-edit\"></i></a>";
        $end = $end->sub($i);
    }
    //implode
    echo implode(' ', $allMonths);
    exit;
}
//Checking distId
if (isset($_REQUEST['distId']) && !empty($_REQUEST['distId']) && !isset($_REQUEST['provId'])) {
    //Getting whId
    $whId = (!empty($_REQUEST['whId'])) ? $_REQUEST['whId'] : '';
    //Getting stkId
    $stkId = $_REQUEST['stkId'];
    //Getting distId
    $distId = $_REQUEST['distId'];

    //query
    //gets
    //wh_id
    //wh_name
    $qry = "SELECT
				tbl_warehouse.wh_id,
				tbl_warehouse.wh_name
			FROM
				tbl_warehouse
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
				stakeholder.lvl = 4
			AND tbl_warehouse.dist_id = $distId
			AND tbl_warehouse.stkid = $stkId";
    //query result
    $qryRes = mysql_query($qry);
    $num = mysql_num_rows($qryRes);
    ?>
    <label class="control-label">Store/Facility</label>
    <select name="warehouse" id="warehouse" class="form-control input-sm" required="required">
        <option value="">Select</option>
        <?php
        //loop
        while ($row = mysql_fetch_array($qryRes)) {
            //polulate warehouse combo
            ?>
            <option value="<?php echo $row['wh_id']; ?>" <?php echo ($whId == $row['wh_id']) ? 'selected="selected"' : ''; ?>><?php echo $row['wh_name']; ?></option>
            <?php
        }
        ?>
    </select>
    <?php
    exit;
}
?>
<style>

    .btn-sm,
    .btn-xs {
        margin-bottom:5px !important;
    }
</style>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include menu
        include $_SESSION['menu'];
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-head">
                                <h3 class="heading">Select Store/Facility Name to Add/Edit data</h3>
                            </div>
                            <div class="widget-body">
                                <table width="99%">
                                    <tr>
                                        <td>
                                            <form action="" method="get" onSubmit="return form_validate()">
                                                <table width="100%">
                                                    <tr>
                                                        <?php
                                                        //query
                                                        //gets
                                                        //stk_id
                                                        //prov_id
                                                        $qry = "SELECT
																user_stk.stk_id,
																user_prov.prov_id
															FROM
																user_stk
															LEFT JOIN user_prov ON user_stk.user_id = user_prov.user_id
															WHERE
																user_stk.user_id = " . $_SESSION['user_id'];
                                                       //Query result
                                                        $qryRes = mysql_query($qry);
                                                        $arr['stk'] = array();
                                                        $arr['prov'] = array();
                                                        //Loop
                                                        while ($row = mysql_fetch_array($qryRes)) {
                                                            //check stk_id
                                                            if (!in_array($row['stk_id'], $arr['stk'])) {
                                                                $arr['stk'][] = $row['stk_id'];
                                                            }
                                                            //check prov_id
                                                            if (!in_array($row['prov_id'], $arr['prov'])) {
                                                                $arr['prov'][] = $row['prov_id'];
                                                            }
                                                        }
                                                        ?>
                                                        <td class="col-md-2">
                                                            <label class="control-label">Stakeholder</label>
                                                            <select name="stk_sel" id="stk_sel" class="form-control input-sm" required="required">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //query 
                                                                //gets
                                                                //stkid
                                                                //stkname
                                                                $querystk = "SELECT stkid, stkname FROM stakeholder Where ParentID IS NULL AND stk_type_id IN (0, 1) AND stkid IN (" . implode(',', $arr['stk']) . ") ORDER BY stkorder";
                                                                $rsstk = mysql_query($querystk) or die();
                                                                //loop
                                                                while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                    //polulate stk_sel combo
                                                                    ?>
                                                                    <option value="<?php echo $rowstk['stkid']; ?>" <?php echo ($sel_stk == $rowstk['stkid']) ? 'selected=selected' : '' ?>><?php echo $rowstk['stkname']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td class="col-md-2">
                                                            <?php
                                                            //query
                                                            //gets
                                                            //PkLocID
                                                            //LocName
                                                            $qry = "SELECT
                                                                        tbl_locations.PkLocID,
                                                                        tbl_locations.LocName
                                                                    FROM
                                                                        tbl_locations
                                                                    WHERE
                                                                        tbl_locations.LocLvl = 2
                                                                    AND tbl_locations.ParentID IS NOT NULL
																	 AND PkLocID IN (" . implode(',', $arr['prov']) . ")";
                                                            //result
                                                            $qryRes = mysql_query($qry);
                                                            ?>
                                                            <label class="control-label">Province/Region</label>
                                                            <select name="province" id="province" class="form-control input-sm" required="required">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //loop
                                                                while ($row = mysql_fetch_array($qryRes)) {
                                                                    //polulate province combo
                                                                    ?>
                                                                    <option value="<?php echo $row['PkLocID']; ?>" <?php echo ($sel_prov == $row['PkLocID']) ? 'selected=selected' : '' ?>><?php echo $row['LocName']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td id="districts" class="col-md-2">
                                                            <label class="control-label">District</label>
                                                            <select name="district" id="district" class="form-control input-sm">
                                                                <option value="">Select</option></select>
                                                        </td>
                                                        <td id="stores" class="col-md-2">
                                                            <label class="control-label">Store/Facility</label>
                                                            <select name="warehouse" id="warehouse" class="form-control input-sm">
                                                                <option value="">Select</option>
                                                            </select>
                                                        </td>
                                                        <td class="col-md-2">
                                                            <label class="control-label">&nbsp;</label>
                                                            <div class="controls">
                                                                <input type="submit" id="Submit" value="Go" class="btn green input-sm" />
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr><td colspan="5">&nbsp;</td></tr>
                                                    <tr>
                                                        <td colspan="5">
                                                            <div id="data_months"></div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </form>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!--<div class="row">
                                        <div class="col-md-12">
                        <iframe src="http://localhost/clmisr2/plmis_admin/data_entry.php?Do=Z77045|2015-06-01|0"></iframe>
                    </div>
                </div>-->
            </div>
        </div>
    </div>
<?php 
//include footer
include PUBLIC_PATH . "html/footer.php"; ?>
    <script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script>
	$(function() {
	<?php
	if (isset($_GET['warehouse'])) {
    ?>
		showDistricts('<?php echo $sel_prov; ?>', '<?php echo $sel_stk; ?>');
		showMonths('<?php echo $sel_wh; ?>');
		showStores('<?php echo $sel_dist; ?>');
    <?php
	}
	?>
		$('#district').change(function(e) {
			showStores($(this).val());
		});
		$('#province, #stk_sel').change(function(e) {
			$('#district').html('<option value="">Select</option>');
			$('#warehouse').html('<option value="">Select</option>');
			showDistricts($('#province').val(), $('#stk_sel').val());
		});
		$('#stk_sel').change(function(e) {
			$('#warehouse').html('<option value="">Select</option>');
		});
		$(document).on('change', '#province, #stk_sel, #district, #warehouse', function() {
			$('#data_months').html('');
		});
	})

	function form_validate()
	{
		if ($('#stk_sel').val() == '') {
			alert('Select stakeholder');
			return false;
		}
		if ($('#province').val() == '') {
			alert('Select province');
			return false;
		}
		if ($('#district').val() == '') {
			alert('Select district');
			return false;
		}
		if ($('#warehouse').val() == '') {
			alert('Select warehouse');
			return false;
		}
	}
	function showMonths(wharehouse_id)
	{
		$.ajax({
			url: 'data_entry_admin1.php',
			data: {wharehouse_id: wharehouse_id},
			type: 'GET',
			success: function(data) {
				$('#data_months').html(data);
			}
		})
	}

	function showDistricts(prov, stk) {
		if (stk != '' && prov != '')
		{
			$.ajax({
				type: 'POST',
				url: 'sub_admin_ajax.php',
				data: {provId: prov, stkId: stk, distId: '<?php echo $sel_dist; ?>'},
				success: function(data) {
					$("#districts").html(data);
				}
			});
		}
	}
	function showStores(dist) {
		var stk = $('#stk_sel').val();
		if (stk != '' && dist != '')
		{
			$.ajax({
				type: 'POST',
				url: 'data_entry_admin1.php',
				data: {distId: dist, stkId: stk, whId: '<?php echo $sel_wh; ?>'},
				success: function(data) {
					$("#stores").html(data);
				}
			});
		}
	}
	function openPopUp(pageURL)
	{
		var w = screen.width - 100;
		var h = screen.height - 100;
		var left = (screen.width/2)-(w/2);
		var top = 0;
		return window.open(pageURL, 'Data Entry', 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=yes, resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
	}
    </script>
</body>
</html>