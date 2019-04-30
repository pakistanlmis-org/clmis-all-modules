<?php
/**
 * data_entry_hf_pwd
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses

include("../includes/classes/AllClasses.php");
//Including header
include(PUBLIC_PATH . "html/header.php");
//Including top_im
include PUBLIC_PATH . "html/top_im.php";

//Checking Do
if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
    //Getting do
    $temp = urldecode($_REQUEST['Do']);

	$tmpStr = substr($temp, 1, strlen($temp) - 1);
    $temp = explode("|", $tmpStr);
    // Warehouse ID
    $wh_id = $temp[0] - 77000;
    //Setting wh_id
    $objwharehouse_user->m_wh_id = $wh_id;
}

//Checking user id in session
if (isset($_SESSION['user_id'])) {
    //Getting user_id
    $userid = $_SESSION['user_id'];
    //Setting user id
    $objwharehouse_user->m_npkId = $userid;
    //Get Province Id By Idc
    $result_province = $objwharehouse_user->GetProvinceIdByIdc();
} else {
    //Display message
    echo "user not login or timeout";
}
$add_date='';
//Checking e
if (isset($_GET['e']) && $_GET['e'] == 'ok') {
    ?>
    <script type="text/javascript">
        function RefreshParent() {
            if (window.opener != null && !window.opener.closed) {
                window.opener.location.reload();
            }
        }
        window.close();
        RefreshParent();
        //window.onbeforeunload = RefreshParent;
    </script>
    <?php
    exit;
}
//Initializing variables
//isReadOnly
$isReadOnly = '';
//style
$style = '';
//Checking im_open
if ($_SESSION['is_allowed_im'] == 1) {
    $isReadOnly = 'readonly="readonly"';
    $style = 'style="background:#CCC"';
} else {
    $isReadOnly = '';
    $style = '';
}
?>
<link href="<?php echo PUBLIC_URL; ?>css/styles.css" rel="stylesheet" type="text/css"/>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left:0px !important">
                <?php
                //Initializing wh_id
                $wh_id = "";
                //Checking Do
                if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
                    //Getting Do
                    $temp = urldecode($_REQUEST['Do']);
                    $tmpStr = substr($temp, 1, strlen($temp) - 1);
                    //Explode 
                    $temp = explode("|", $tmpStr);

                    //****************************************************************************
                    // Warehouse ID
					
                    $wh_id = $temp[0] - 77000;
                    //Report Date
                    $RptDate = $temp[1];
                    //if value=1 then new report
                    $isNewRpt = $temp[2];
                    $tt = explode("-", $RptDate);
                    //Reprot year
                    $yy = $tt[0];
                    //report Month
                    $mm = $tt[1];
					
					// echo '<pre>';
					// print_r($temp);
					
                    // gets stakeholder level, hf type id, prov id of this warehouse
					
                    $qryLvl = mysql_fetch_array(mysql_query("SELECT
                                                            stakeholder.lvl,
                                                            tbl_warehouse.hf_type_id,
                                                            tbl_warehouse.prov_id
                                                        FROM
                                                            tbl_warehouse
                                                        INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                        WHERE
                                                            tbl_warehouse.wh_id = $wh_id"));
														
                    $hfTypeId = $qryLvl['hf_type_id'];
                    $whProvId = $qryLvl['prov_id'];

                    //month
                    $month = date('F', mktime(0, 0, 0, $mm, 1));

                    //****************************************************************************
                    //Setting wh_id
                    $objwarehouse->m_npkId = $wh_id;
                    //Get Stk ID By WH Id
                    $stkid = $objwarehouse->GetStkIDByWHId($wh_id);
                    //Get Warehouse Name By Id
                    $whName = $objwarehouse->GetWarehouseNameById($wh_id);
                    echo "<h3 class=\"page-title row-br-b-wp\">" . $whName . " <span class=\"green-clr-txt\">(" . $month . ' ' . $yy . ")</span> </h3>";
                    if ($isNewRpt == 1) {
                        //Get Previous Month Report Date
                        $PrevMonthDate = $objReports->GetPreviousMonthReportDate($RptDate);
                    } else {
                        $PrevMonthDate = $RptDate;
                    }
					
					// building query ......
					 
					$ref_arr  = array();
					$ref_arr['fwc_male']=0;
					$ref_arr['fwc_female']=0;
					$ref_arr['rhs_male']=0;
					$ref_arr['rhs_female']=0;
					$ref_arr['other_male']=0;
					$ref_arr['other_female']=0;
					
					$perf_arr = array();
					$perf_arr['camp_male']=0;
					$perf_arr['camp_female']=0;
					$perf_arr['static_male']=0;
					$perf_arr['static_female']=0;
					
					
					$s_qry_1="SELECT
									tbl_hf_data.pk_id,tbl_hf_data.warehouse_id,tbl_hf_data.item_id,tbl_hf_data.issue_balance,tbl_hf_data.reporting_date
								 
								FROM tbl_hf_data 
								where warehouse_id='".$wh_id."'
								AND item_id in (31,32)
								AND reporting_date ='".$RptDate."' ";
					$rs_s = mysql_query($s_qry_1);

					while($rsRow1 = mysql_fetch_array($rs_s))
					{
						//$s_qry_2="SELECT * from tbl_hf_data_reffered_by WHERE hf_data_id = '".$rsRow1['pk_id']."' AND hf_type_id='".$qryLvl['hf_type_id']."' ";
						 $s_qry_2="SELECT * from tbl_hf_data_reffered_by WHERE hf_data_id = '".$rsRow1['pk_id']."'  ";
						$rsTemp2 = mysql_query($s_qry_2);
						while($ref_data_set = mysql_fetch_array($rsTemp2))
						{
							//echo '<pre>';
							//print_r($qryLvl);
							//print_r($rsRow1);
							//echo $rsRow1['item_id'];
							//print_r($ref_data_set);
							
							if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='31') $perf_arr['camp_male']+=$ref_data_set['camp'];
							if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='32') $perf_arr['camp_female']+=$ref_data_set['camp'];
							
							if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='31') $perf_arr['static_male']+=$ref_data_set['static'];
							if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='32') $perf_arr['static_female']+=$ref_data_set['static'];
							
							
							if(!empty($ref_data_set['hf_type_id']) && $ref_data_set['hf_type_id']=='1')
							{
								if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='31')$ref_arr['fwc_male']+=$ref_data_set['ref_surgeries'];
								if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='32')$ref_arr['fwc_female']+=$ref_data_set['ref_surgeries'];
							}
							
							if(!empty($ref_data_set['hf_type_id']) && $ref_data_set['hf_type_id']=='4')
							{
								if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='31')$ref_arr['rhs_male']+=$ref_data_set['ref_surgeries'];
								if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='32')$ref_arr['rhs_female']+=$ref_data_set['ref_surgeries'];
							}
							
							if(!empty($ref_data_set['hf_type_id']) && $ref_data_set['hf_type_id']=='13')
							{
								if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='31')$ref_arr['other_male']+=$ref_data_set['ref_surgeries'];
								if(!empty($rsRow1['item_id']) && $rsRow1['item_id']=='32')$ref_arr['other_female']+=$ref_data_set['ref_surgeries'];
							}
						}
						
					}
					/// end building query .....
                    ?>

                    <form name="frmF7" id="frmF7" method="post">
                        <div id="errMsg"></div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
								
                                if ($hfTypeId == 4 ) {
								
                                    ?>
                                    <div class="col-md-4">
                                        <h4 style="margin-top:20px;"><span class="green-clr-txt">Surgery Cases(Reffered)</span></h4>
                                        <input type="hidden" name="hf_type_id" id="hf_type_id"  value="<?php echo $hfTypeId; ?>">
                                        <table class="table table-bordered">
                                            <tr>
                                                <th>Reffered By</th>
                                                <th>Male</th>
                                                <th>Female</th>
                                                
											 <tr>
                                                <td>RHS-A/FHC</td>
                                                <td><input class="form-control input-sm text-right reffered31" id="rhs_male" name="rhs_male" type="text"  value="<?=$ref_arr['rhs_male']?>" ></td>
                                                <td><input class="form-control input-sm text-right reffered32" id="rhs_female" name="rhs_female" type="text"  value="<?=$ref_arr['rhs_female']?>" ></td>
                                            </tr>
											 <tr>
                                                <td>FWC</td>
                                                <td><input class="form-control input-sm text-right reffered31" id="fwc_male" name="fwc_male"  type="text"  value="<?=$ref_arr['fwc_male']?>"  ></td>
                                                <td><input class="form-control input-sm text-right reffered32" id="fwc_female" name="fwc_female"  type="text"  value="<?=$ref_arr['fwc_female']?>"  ></td>
                                            </tr>
											 <tr>
                                                <td>Others</td>
                                                <td><input class="form-control input-sm text-right reffered31" id="other_male" name="other_male"  type="text"  value="<?=$ref_arr['other_male']?>"  ></td>
                                                <td><input class="form-control input-sm text-right reffered32" id="other_female" name="other_female"  type="text" value="<?=$ref_arr['other_female']?>"  ></td>
                                            </tr>
											 <tr>
                                                <td>Gross Total /Net Total</td>
                                                <td><input class="form-control input-sm text-right" id="FLDIsuueUP31" readonly value="<?=($ref_arr['fwc_male']+$ref_arr['rhs_male']+$ref_arr['other_male'])?>"  ></td>
                                                <td><input class="form-control input-sm text-right" id="FLDIsuueUP32" readonly value="<?=($ref_arr['fwc_female']+$ref_arr['rhs_female']+$ref_arr['other_female'])?>"  ></td>
                                            </tr>
                                          
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <h4 style="margin-top:20px;"><span class="green-clr-txt">Surgery Cases(Performed)</span></h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <td>&nbsp;</td>
                                                <td>Male</td>
                                                <td>Female</td>
                                            </tr>
                                            <tr>
                                                <td>Static Center</td>
                                                <td><input class="form-control input-sm text-right totalStaticCampMale31"  type="text" id="static_male" name="static_male" value="<?php echo $perf_arr['static_male']; ?>" /></td>
                                                <td><input class="form-control input-sm text-right totalStaticCampMale32"  type="text" id="static_female" name="static_female"  value="<?php echo $perf_arr['static_female']; ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Camp Cases</td>
                                                <td><input class="form-control input-sm text-right totalStaticCampMale31"  type="text"  id="camp_male" name="camp_male"  value="<?php echo $perf_arr['camp_male']; ?>" /></td>
                                                <td><input class="form-control input-sm text-right totalStaticCampMale32"  type="text"  id="camp_female" name="camp_female"  value="<?php echo $perf_arr['camp_female']; ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Gross Total /Net Total</td>
                                                <td><input class="form-control input-sm text-right" readonly type="text" id="totalStaticCampMale" value="<?php echo ((int)$perf_arr['static_male']+(int)$perf_arr['camp_male']); ?>" /></td>
                                                <td><input class="form-control input-sm text-right" readonly type="text" id="totalStaticCampFemale" value="<?php echo ((int)$perf_arr['static_female']+(int)$perf_arr['camp_female']); ?>" /></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                }

                              
                                ?>
                                
                                <?php
                                $hfPrograms = array(1, 2, 4, 11);
                                //check whProvId
                                ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-10 text-right" style="padding-top: 10px">
                                    <div id="eMsg" style="color:#060;"></div>
                                </div>
                                <div class="col-md-2 text-right">
                                    <button class="btn btn-primary" id="saveBtn" name="saveBtn" type="button" onClick="return formvalidate1()"> Save </button>
                                    <button class="btn btn-info" type="submit" onClick="document.frmF7.reset()"> Reset </button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="ActionType" value="Add">
                        <input type="hidden" name="RptDate" value="<?php echo $RptDate; ?>">
                        <input type="hidden" name="wh_id" value="<?php echo $wh_id; ?>">
                        <input type="hidden" name="yy" value="<?php echo $yy; ?>">
                        <input type="hidden" name="mm" value="<?php echo $mm; ?>">
                        <input type="hidden" name="isNewRpt" id="isNewRpt" value="<?php echo $isNewRpt; ?>" />
                        <input type="hidden" name="add_date" id="add_date" value="<?php echo $add_date; ?>" />
                    </form>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script> 
    <script language="javascript" type="text/javascript">
                                        var form_clean;
                                        $(document).ready(function() {

                                            form_clean = $("#frmF7").serialize();

                                            // Auto Save function call
                                            //setInterval('autoSave()', 20000);

                                            $('input[type="text"]').each(function() {
                                                if ($(this).val() == '')
                                                {
                                                    $(this).val(0);
                                                }
                                            });

                                            $('input[type="text"]').change(function(e) {
                                                if ($(this).val() == '')
                                                {
                                                    $(this).val('0');
                                                }
                                            });
                                            $('input[type="text"]').focus(function(e) {
                                                if ($(this).val() == '0')
                                                {
                                                    $(this).val('');
                                                }
                                            });
                                            $('input[type="text"]').focusout(function(e) {
                                                if ($(this).val() == '')
                                                {
                                                    $(this).val('0');
                                                }
                                            });
                                            $('input[type="text"]').keydown(function(e) {
                                                if (e.shiftKey || e.ctrlKey || e.altKey) { // if shift, ctrl or alt keys held down
                                                    e.preventDefault();         // Prevent character input
                                                } else {
                                                    var n = e.keyCode;
                                                    if (!((n == 8)              // backspace
                                                            || (n == 9)                // Tab
                                                            || (n == 46)                // delete
                                                            || (n >= 35 && n <= 40)     // arrow keys/home/end
                                                            || (n >= 48 && n <= 57)     // numbers on keyboard
                                                            || (n >= 96 && n <= 105))   // number on keypad
                                                            ) {
                                                        e.preventDefault();     // Prevent character input
                                                    }
                                                }
                                            });
                                        });

                                        function autoSave()
                                        {
                                        }

                                        //Total Calculation
                                        $(".reffered31").on("keyup keydown", function() {
                                            calculateSum('reffered31');
                                        });

                                        $(".reffered32").on("keyup keydown", function() {
                                            calculateSum('reffered32');
                                        });

                                        $(".totalStaticCampMale31").on("keyup keydown", function() {
                                            calculateSum('totalStaticCampMale31');
                                        });
                                        $(".totalStaticCampMale32").on("keyup keydown", function() {
                                            calculateSum('totalStaticCampMale32');
                                        });

                                        function calculateSum(field) {

                                            var sum = 0;
                                            if (field == 'reffered31') {
                                                var total = 'FLDIsuueUP31';
                                            }
                                            else if (field == 'reffered32') {
                                                var total = 'FLDIsuueUP32';
                                            }
                                            else if (field == 'totalStaticCampMale31') {
                                                var total = 'totalStaticCampMale';
                                            }
                                            else if (field == 'totalStaticCampMale32') {
                                                var total = 'totalStaticCampFemale';
                                            }

                                            //iterate through each textboxes and add the values
                                            $("." + field).each(function() {
                                                var reffered_male = $(this).val();

                                                if (!isNaN(reffered_male) && reffered_male.length != 0) {
                                                    sum += parseFloat(reffered_male);
                                                }
                                                else if (reffered_male.length != 0) {
                                                }
                                            });
                                            $("input#" + total).val(sum);
                                        }


                                        function formvalidate1()
                                        {
                                            $('#saveBtn').attr('disabled', false);
                                            $('#errMsg').hide();
                                            var itmLength = $("input[name^='flitmrec_id']").length;
                                            var itmArr = $("input[name^='flitmrec_id']");
                                            var itmCategory = $("input[name^='flitm_category']");
                                            var FLDOBLAArr = $("input[name^='FLDOBLA']");
                                            var FLDRecvArr = $("input[name^='FLDRecv']");
                                            var FLDIsuueUPArr = $("input[name^='FLDIsuueUP']");
                                            var FLDCBLAArr = $("input[name^='FLDCBLA']");
                                            var FLDReturnToArr = $("input[name^='FLDReturnTo']");
                                            var FLDUnusableArr = $("input[name^='FLDUnusable']");
                                            var refferedTotalMale = $("#FLDIsuueUP31").val();
                                            var refferedTotalFemale = $("#FLDIsuueUP32").val();
                                            var StaticCampTotalMale = $("#totalStaticCampMale").val();
                                            var StaticCampTotalFemale = $("#totalStaticCampFemale").val();
                                            /*
                                             var fieldval = document.frmaddF7.itmrec_id[i].value;
                                             fieldconcat = fieldval.split('-');
                                             var whobla = 'WHOBLA'+fieldconcat[1];
                                             var whrecv = 'WHRecv'+fieldconcat[1];
                                             var whissue = 'IsuueUP'+fieldconcat[1];
                                             var fldobla = 'FLDOBLA'+fieldconcat[1];
                                             var fldrecv = 'FLDRecv'+fieldconcat[1];
                                             var fldissue = 'FLDIsuueUP'+fieldconcat[1];
                                             */
                                            for (i = 0; i < itmLength; i++)
                                            {
                                                if (itmCategory.eq(i).val() == 1)
                                                {
                                                    itm = itmArr.eq(i).val();
                                                    //var itmInfo = itm.split('-');
                                                    //itmId = itmInfo[1];
                                                    var FLDOBLA = parseInt(FLDOBLAArr.eq(i).val());
                                                    var FLDRecv = parseInt(FLDRecvArr.eq(i).val());
                                                    var FLDIsuueUP = parseInt(FLDIsuueUPArr.eq(i).val());
                                                    var FLDCBLA = parseInt(FLDCBLAArr.eq(i).val());
                                                    var FLDReturnTo = parseInt(FLDReturnToArr.eq(i).val());
                                                    var FLDUnusable = parseInt(FLDUnusableArr.eq(i).val());


                                                    if ((FLDIsuueUP + FLDUnusable) > (FLDOBLA + FLDRecv + FLDReturnTo))
                                                    {
                                                        alert('Invalid Closing Balance.\nClosing Balance = Opening Balance + Received + Adjustment(+) - Issued -  Adjustment(-)');
                                                        FLDOBLAArr.eq(i).css('background', '#F45B5C');
                                                        FLDRecvArr.eq(i).css('background', '#F45B5C');
                                                        FLDIsuueUPArr.eq(i).css('background', '#F45B5C');
                                                        FLDCBLAArr.eq(i).css('background', '#F45B5C');
                                                        FLDReturnToArr.eq(i).css('background', '#F45B5C');
                                                        FLDUnusableArr.eq(i).css('background', '#F45B5C');
                                                        return false;
                                                    }
                                                }

                                            }
                                            var hf_type_id = $("#hf_type_id").val();
                                            if (hf_type_id == 4)
                                            {
                                                //console.log('refferedTotalMale:'+refferedTotalMale+',StaticCampTotalMale'+StaticCampTotalMale+',refferedTotalFemale:'+refferedTotalFemale+', StaticCampTotalFemale:'+StaticCampTotalFemale);
                                               // if (parseInt(refferedTotalMale) < parseInt(StaticCampTotalMale) ) console.log('male issue');
                                               // if (parseInt(refferedTotalFemale) < parseInt(StaticCampTotalFemale) ) console.log('feemale issue');   
            
                                                if (parseInt(refferedTotalMale) < parseInt(StaticCampTotalMale) || parseInt(refferedTotalFemale) < parseInt(StaticCampTotalFemale) )
                                                {
                                                    alert("Performed Surgery Cases Gross Totals can not be greater than Reffered Surgery Cases Gross Totals");
                                                    $('#totalStaticCampMale').css('background', '#F45B5C');
                                                    $('#totalStaticCampFemale').css('background', '#F45B5C');
                                                    $('#FLDIsuueUP031').css('background', '#F45B5C');
                                                    $('#FLDIsuueUP032').css('background', '#F45B5C');
                                                    return false;
                                                }
                                                else
                                                {
                                                    $('#totalStaticCampMale').css('background', 'none');
                                                    $('#totalStaticCampFemale').css('background', 'none');
                                                    $('#FLDIsuueUP031').css('background', 'none');
                                                    $('#FLDIsuueUP032').css('background', 'none');
                                                }
                                            }

                                            $('#saveBtn').attr('disabled', true);
                                            $("#eMsg").html('Saving...');
                                            $('body').addClass("loading");
                                            $.ajax({
                                                url: 'data_entry_surgery_action.php',
                                                data: $('#frmF7').serialize(),
                                                type: 'POST',
                                                dataType: 'json',
                                                success: function(data) {
                                                    $('body').removeClass("loading");
                                                    if (data.resp == 'err')
                                                    {
                                                        $('#errMsg').html(data.msg).show();
                                                    }
                                                    else if (data.resp == 'ok')
                                                    {
                                                        function RefreshParent() {
                                                            if (window.opener != null && !window.opener.closed) {
                                                                window.opener.location.reload();
                                                            }
                                                        }
														//$('#saveBtn').attr('disabled', false);
														//$("#eMsg").html('');
														//$('body').removeClass("loading");
                                                        window.close();
                                                        RefreshParent();
                                                    }
                                                }
                                            })
                                        }
                                        function roundNumber(num, dec)
                                        {
                                            var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
                                            return result;
                                        }
                                        function cal_balance(itemId)
                                        {
                                            if (document.getElementById('WHOBLA' + itemId))
                                                var wholba = (document.getElementById('WHOBLA' + itemId).value == "") ? 0 : parseInt(document.getElementById('WHOBLA' + itemId).value);
                                            else
                                                var wholba = 0;
                                            if (document.getElementById('WHRecv' + itemId))
                                                var WHRecv = (document.getElementById('WHRecv' + itemId).value == "") ? 0 : parseInt(document.getElementById('WHRecv' + itemId).value);
                                            else
                                                var WHRecv = 0;
                                            if (document.getElementById('IsuueUP' + itemId))
                                                var IsuueUP = (document.getElementById('IsuueUP' + itemId).value == "") ? 0 : parseInt(document.getElementById('IsuueUP' + itemId).value);
                                            else
                                                var IsuueUP = 0;
                                            //WH adj+
                                            if (document.getElementById('ReturnTo' + itemId))
                                                var ReturnTo = (document.getElementById('ReturnTo' + itemId).value == "") ? 0 : parseInt(document.getElementById('ReturnTo' + itemId).value);
                                            else
                                                var ReturnTo = 0;
                                            //WH adj-
                                            if (document.getElementById('Unusable' + itemId))
                                                var Unusable = (document.getElementById('Unusable' + itemId).value == "") ? 0 : parseInt(document.getElementById('Unusable' + itemId).value);
                                            else
                                                var Unusable = 0;
                                            if (document.getElementById('FLDOBLA' + itemId))
                                                var fldolba = (document.getElementById('FLDOBLA' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDOBLA' + itemId).value);
                                            else
                                                var fldolba = 0;
                                            if (document.getElementById('FLDRecv' + itemId))
                                                var FLDRecv = (document.getElementById('FLDRecv' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDRecv' + itemId).value);
                                            else
                                                var FLDRecv = 0;
                                            if (document.getElementById('FLDIsuueUP' + itemId))
                                                var FLDIsuueUP = (document.getElementById('FLDIsuueUP' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDIsuueUP' + itemId).value);
                                            else
                                                var FLDIsuueUP = 0;
                                            /*if(document.getElementById('FLDmyavg'+itemId))	
                                             var FLDmyavg = (document.getElementById('FLDmyavg'+itemId).value=="")? 0 : parseInt(document.getElementById('FLDmyavg'+itemId).value);
                                             else
                                             var FLDmyavg = 0;*/
                                            //Fld adj+
                                            if (document.getElementById('FLDReturnTo' + itemId))
                                                var FLDReturnTo = (document.getElementById('FLDReturnTo' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDReturnTo' + itemId).value);
                                            else
                                                var FLDReturnTo = 0;
                                            //Fld adj-
                                            if (document.getElementById('FLDUnusable' + itemId))
                                                var FLDUnusable = (document.getElementById('FLDUnusable' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDUnusable' + itemId).value);
                                            else
                                                var FLDUnusable = 0;
                                            /*if(document.getElementById('FLDmyavg'+itemId))
                                             {
                                             var myavg = document.getElementById('FLDmyavg'+itemId).value;
                                             }
                                             else {
                                             var myavg = document.getElementById('myavg'+itemId).value;
                                             }
                                             var mycalavg = myavg.split('-');
                                             if(document.getElementById('FLDIsuueUP'+itemId))
                                             var divisible = parseInt(mycalavg[1]+FLDIsuueUP);
                                             else
                                             var divisible = parseInt(mycalavg[1]+IsuueUP);
                                             var divider = parseInt(mycalavg[0]+1);
                                             if(parseInt(divider)>0)
                                             {
                                             var myactualavg = parseInt(divisible)/parseInt(divider);
                                             }
                                             else {
                                             var myactualavg = parseInt(divisible)/1;
                                             }*/
                                            if (document.getElementById('WHCBLA' + itemId))
                                                document.getElementById('WHCBLA' + itemId).value = (wholba + WHRecv + ReturnTo) - (IsuueUP + Unusable);
                                            if (document.getElementById('MOS' + itemId) && document.getElementById('WHCBLA' + itemId))
                                            {
                                                if (parseInt(myactualavg) > 0)
                                                {
                                                    document.getElementById('MOS' + itemId).value = roundNumber(parseInt(document.getElementById('WHCBLA' + itemId).value) / parseInt(myactualavg), 1);
                                                }
                                                else {
                                                    document.getElementById('MOS' + itemId).value = roundNumber(parseInt(document.getElementById('WHCBLA' + itemId).value) / 1, 1);
                                                }
                                            }
                                            if (document.getElementById('FLDCBLA' + itemId))
                                                document.getElementById('FLDCBLA' + itemId).value = (fldolba + FLDRecv + FLDReturnTo) - (FLDIsuueUP + FLDUnusable);
                                            if (document.getElementById('FLDMOS' + itemId) && document.getElementById('FLDCBLA' + itemId))
                                            {
                                                if (parseInt(myactualavg) > 0)
                                                {
                                                    document.getElementById('FLDMOS' + itemId).value = roundNumber(parseInt(document.getElementById('FLDCBLA' + itemId).value) / parseInt(myactualavg), 1);
                                                }
                                                else {
                                                    document.getElementById('FLDMOS' + itemId).value = roundNumber(parseInt(document.getElementById('FLDCBLA' + itemId).value) / 1, 1);
                                                }
                                            }
                                        }
                                        function get_browser_info() {
                                            var ua = navigator.userAgent, tem, M = ua.match(/(opera|chrome|safari|firefox|msie|trident(?=\/))\/?\s*(\d+)/i) || [];
                                            if (/trident/i.test(M[1])) {
                                                tem = /\brv[ :]+(\d+)/g.exec(ua) || [];
                                                return {name: 'IE', version: (tem[1] || '')};
                                            }
                                            if (M[1] === 'Chrome') {
                                                tem = ua.match(/\bOPR\/(\d+)/)
                                                if (tem != null) {
                                                    return {name: 'Opera', version: tem[1]};
                                                }
                                            }
                                            M = M[2] ? [M[1], M[2]] : [navigator.appName, navigator.appVersion, '-?'];
                                            if ((tem = ua.match(/version\/(\d+)/i)) != null) {
                                                M.splice(1, 1, tem[1]);
                                            }
                                            return {
                                                name: M[0],
                                                version: M[1]
                                            };
                                        }
                                        var browser = get_browser_info();
                                        //alert(browser.name + ' - ' + browser.version);
                                        if (browser.name == 'Firefox' && browser.version < 30)
                                        {
                                            alert('You are using an outdated version of the Mozilla Firefox. Please update your browser for data entry.');
                                            window.close();
                                        }
                                        else if (browser.name == 'Chrome' && browser.version < 35)
                                        {
                                            alert('You are using an outdated version of the Chrome. Please update your browser for data entry.');
                                            window.close();
                                        }
                                        else if (browser.name == 'Opera' && browser.version < 28)
                                        {
                                            alert('You are using an outdated version of the Opera. Please update your browser for data entry.');
                                            window.close();
                                        }
                                        else if (browser.name == 'MSIE')
                                        {
                                            alert('Please use Mozilla Firefox, Chrome or Opera for data entry.');
                                            window.close();
                                        }
    </script>
</body>
</html>