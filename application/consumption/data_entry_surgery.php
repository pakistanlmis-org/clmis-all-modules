<?php
/**
 * data_entry_admin
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including files
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
//Checking user_id
if (isset($_SESSION['user_id'])) {
    //Getting user_id
    $userid = $_SESSION['user_id'];
    $objwharehouse_user->m_npkId = $userid;
    //Get wh user By Idc
    $result = $objwharehouse_user->GetwhuserByIdc();
} else {
    //Display error message
    echo "user not login or timeout";
}
//Setting limit
$_SESSION['LIMIT'] = (date('Y') - 2010) * 12 + date('m') - 1;
//Setting entry enable
$_SESSION['enable_entry'] = 1;
//Initializing variables
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
?>
<style>
    .btn-sm,
    .btn-xs {
        margin-bottom:4px;
    }
</style>
<script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script>
		$(function() {
		<?php
		//Checking warehouse
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
			//$('#warehouse').html('<option value="">Select</option>');
                        $('#warehouse').val('');
			showDistricts($('#province').val(), $('#stk_sel').val());
		});
		$('#stk_sel').change(function(e) {
			//$('#warehouse').html('<option value="">Select</option>');
                        $('#warehouse').val('');
		});
		$(document).on('change', '#province, #stk_sel, #district, #warehouse', function() {
			$('#data_months').html('');
		});
		/*$(document).on('change', '#warehouse', function(){
		 var wharehouse_id = $(this).val();
		 $.ajax({
		 url: 'sub_admin_ajax.php',
		 data: {wharehouse_id: wharehouse_id},
		 type: 'post',
		 dataType: 'JSON',
		 success: function(data){
		 var dataEntryURL;
		 var load3Months;
		 if(data.TotalHF > 0 && data.StoreLevel == 7)
		 {
		 if ( data.Stakeholder == 1 ){
		 dataEntryURL = 'data_entry_hf_pwd.php';
		 }else{
		 dataEntryURL = 'data_entry_hf.php';
		 }
		 load3Months = 'loadLast3MonthsHF.php';
		 }
		 else
		 {
		 dataEntryURL = 'data_entry.php';
		 load3Months = 'loadLast3Months.php';
		 }
		 showMonths(wharehouse_id, load3Months, dataEntryURL);
		 }
		 })
		 });*/
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
			alert(' warehouse is empty');
			return false;
		}
	}

	function showMonths(wharehouse_id)
	{
	
		$.ajax({
			url: 'sub_admin_ajax.php',
			data: {wharehouse_id: wharehouse_id},
			type: 'post',
			dataType: 'JSON',
			success: function(data) {
				var dataEntryURL;
				var load3Months;
				
					dataEntryURL = 'data_entry_surgery_form.php';
					load3Months = 'loadSurgeryMonths2.php';
				
				$.ajax({
					url: load3Months,
					data: {wharehouse_id: wharehouse_id, dataEntryURL: dataEntryURL},
					type: 'post',
					success: function(data) {
						$('#data_months').html(data);
					}
				})
			}
		});
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
					showStores($('#district').val());
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
				url: 'sub_surgery_ajax.php',
				data: {distId: dist, stkId: stk, whId: '<?php echo $sel_wh; ?>' , hf_type_id:'4'},
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
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //Including files
        include $_SESSION['menu'];
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
                                                        $qry = "SELECT
																user_stk.stk_id,
																user_prov.prov_id
															FROM
																user_stk
															JOIN user_prov ON user_stk.user_id = user_prov.user_id
															WHERE
																user_stk.user_id = " . $_SESSION['user_id'];
                                                        $qryRes = mysql_query($qry);
                                                        $arr['stk'] = array();
                                                        $arr['prov'] = array();
                                                        while ($row = mysql_fetch_array($qryRes)) {
                                                            if (!in_array($row['stk_id'], $arr['stk'])) {
                                                                $arr['stk'][] = $row['stk_id'];
                                                            }
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
                                                                //Populate stk_sel combo
                                                                $querystk = "SELECT stkid, stkname FROM stakeholder Where ParentID IS NULL AND stk_type_id IN (0, 1) AND stkid IN (" . implode(',', $arr['stk']) . ") ORDER BY stkorder";
                                                                $rsstk = mysql_query($querystk) or die();
                                                                while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                    ?>
                                                                    <option value="<?php echo $rowstk['stkid']; ?>" <?php echo ($sel_stk == $rowstk['stkid']) ? 'selected=selected' : '' ?>><?php echo $rowstk['stkname']; ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td class="col-md-2">
                                                            <?php
                                                            $qry = "SELECT
                                                                        tbl_locations.PkLocID,
                                                                        tbl_locations.LocName
                                                                    FROM
                                                                        tbl_locations
                                                                    WHERE
                                                                        tbl_locations.LocLvl = 2
                                                                    AND tbl_locations.ParentID IS NOT NULL
																	 AND PkLocID IN (" . implode(',', $arr['prov']) . ")";
                                                            $qryRes = mysql_query($qry);
                                                            ?>
                                                            <label class="control-label">Province/Region</label>
                                                            <select name="province" id="province" class="form-control input-sm" required="required">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //Populate province combo 
                                                                while ($row = mysql_fetch_array($qryRes)) {
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
                                                            <label id="warehouse_lbl" class="control-label" /></label>
                                                            <input hidden name="warehouse" id="warehouse" class="form-control input-sm" />
                                                               
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
    //Including files
    include PUBLIC_PATH . "html/footer.php"; ?>
    
</body>
</html>