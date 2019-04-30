<?php
/**
 * data_entry
 * @package consumption
 * 
 * @author     Muhammad Waqas Azeem 
 * @email <waqas@deliver-pk.org>
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
//Checking user_id
if (isset($_SESSION['user_id'])) {
    //Getting user_id
    $userid = $_SESSION['user_id'];
    $objwharehouse_user->m_npkId = $userid;
    //Get wh user By Idc
    $result = $objwharehouse_user->GetwhuserByIdc();
    //Get Province Id By Idc
    $result_province = $objwharehouse_user->GetProvinceIdByIdc();
} else {
    //Display error
    echo "user not login or timeout";
}
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
</head><body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content" style="margin-left:0px !important">
                <?php
//warehouse id
                $wh_id = "";
//Checking Do
                if (isset($_REQUEST['Do']) && !empty($_REQUEST['Do'])) {
                    //Getting Do
                    $temp = urldecode($_REQUEST['Do']);
                    $tmpStr = substr($temp, 1, strlen($temp) - 1);
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

                    // Check level
                    $qryLvl = mysql_fetch_array(mysql_query("SELECT
                                                                    stakeholder.lvl
                                                            FROM
                                                                    tbl_warehouse
                                                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                            WHERE
                                            tbl_warehouse.wh_id = $wh_id"));
                    //Checking level	
                    if ($qryLvl['lvl'] == 4) {
                        $title = "Consumed";
                    } else {
                        $title = "Issued";
                    }
                    //opened for MSS 
                    if ($qryLvl['lvl'] == 4 || $_SESSION['user_stakeholder1'] == 6 || $yy <= 2017) {
                        $readonly = '';
                    } else {
                        $readonly = "readonly=readonly";
                    }
                    //echo $wh_id.','.$readonly.' Stk :'.$_SESSION['user_stakeholder1'];exit;
                    $month = date('M', mktime(0, 0, 0, $mm, 1));

                    // Check if its 1st Month of Data Entry 
                    $checkData = "SELECT
								tbl_wh_data.RptDate
							FROM
								tbl_wh_data
							WHERE
								tbl_wh_data.wh_id = '$wh_id'
							ORDER BY
								tbl_wh_data.RptDate ASC
							LIMIT 1";
                    //Query result
                    $checkDataRes = mysql_fetch_array(mysql_query($checkData));
                    $openOB = ($checkDataRes['RptDate'] == $RptDate) ? '' : $checkDataRes['RptDate'];

                    //****************************************************************************
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
                    ?>
                    <form name="frmF7" id="frmF7" method="post">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="errMsg"></div>
                                <table class="table table-bordered">
                                    <tr>
                                        <th rowspan="2" class="text-center">S.No.</th>
                                        <th rowspan="2" class="text-center">Article</th>
                                        <th rowspan="2" class="text-center">Opening balance</th>
                                        <th rowspan="2" class="text-center">Received</th>
                                        <th rowspan="2" class="text-center">Issued</th>
                                        <th colspan="2" class="text-center">Adjustments</th>
                                        <th rowspan="2" class="text-center">Closing Balance</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">(+)</th>
                                        <th class="text-center">(-)</th>
                                    </tr>
    <?php
    $isDraft = false;
    // See if this month data exists in drafts
    $qry = "SELECT
                                    COUNT(tbl_wh_data_draft.w_id) AS num
                                FROM
                                    tbl_wh_data_draft
                                WHERE
                                    tbl_wh_data_draft.report_month = $mm
                                AND tbl_wh_data_draft.report_year = $yy
                                AND tbl_wh_data_draft.wh_id = $wh_id";
    //Query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    if ($qryRes['num'] > 0) {
        //Setting isDraft
        $isDraft = true;
    }
    $rsTemp1 = mysql_query("SELECT * FROM `itminfo_tab` WHERE `itm_status`=1 AND `itm_id` IN (SELECT `Stk_item` FROM `stakeholder_item` WHERE `stkid` =$stkid) AND itminfo_tab.itm_category = 1 ORDER BY `frmindex`");
    $SlNo = 1;
    $fldIndex = 0;
    while ($rsRow1 = mysql_fetch_array($rsTemp1)) {
        $SlNo = ((strlen($SlNo) < 2) ? $SlNo : $SlNo);

        $item_char = $rsRow1['itmrec_id'];
        $qry_cf = "SELECT REPUpdateCarryForward('$RptDate','$item_char', $wh_id) FROM DUAL";
        mysql_query($qry_cf);

        // If draft then get from draft table
        if ($isDraft) {
            $qry = "SELECT * FROM tbl_wh_data_draft WHERE `wh_id`='" . $wh_id . "' AND tbl_wh_data_draft.report_month = $mm AND tbl_wh_data_draft.report_year = $yy AND `item_id`='$rsRow1[itmrec_id]'";
            $rsTemp3 = mysql_query($qry);
            $rsRow2 = mysql_fetch_array($rsTemp3);
        }
        // If Not drafted 
        else {
            $qry = "SELECT * FROM tbl_wh_data WHERE `wh_id`='" . $wh_id . "' AND RptDate='" . $PrevMonthDate . "' AND `item_id`='$rsRow1[itmrec_id]'";
            $rsTemp3 = mysql_query($qry);
            $rsRow2 = mysql_fetch_array($rsTemp3);
        }
        
        require 'get_field_store_receiving.php';
        
        $add_date = $rsRow2['add_date'];
        // if new report
        if ($isNewRpt == 1) {
            // If draft then get from draft table
            if ($isDraft) {
                //wh_issue_up
                $wh_issue_up = $rsRow2['wh_issue_up'];
                //wh_adja
                $wh_adja = $rsRow2['wh_adja'];
                //wh_adjb
                $wh_adjb = $rsRow2['wh_adjb'];
                //wh_received
                $wh_received = $rsRow2['wh_received'];
                //wh_obl_c
                $ob_c = $rsRow2['wh_obl_c'];
                //wh_obl_a
                $ob_a = $rsRow2['wh_obl_a'];
                //wh_cbl_c
                $cb_c = $rsRow2['wh_cbl_c'];
                //wh_cbl_a
                $cb_a = $rsRow2['wh_cbl_a'];
            } else {
                //wh_issue_up
                $wh_issue_up = 0;
                //wh_adja
                $wh_adja = 0;
                //wh_adjb
                $wh_adjb = 0;
                //wh_received
                $wh_received = 0;
                //wh_cbl_c
                $ob_c = $rsRow2['wh_cbl_c'];
                //wh_cbl_a
                $ob_a = $rsRow2['wh_cbl_a'];
                //wh_cbl_c
                $cb_c = $rsRow2['wh_cbl_c'];
                //wh_cbl_a
                $cb_a = $rsRow2['wh_cbl_a'];
            }
            
            if($issuance_flag == 'TRUE') {
                $wh_issue_up = $district_issuance;
            }
            
            $itemid = explode('-', $rsRow1['itmrec_id']);
            $item_array[] = $itemid[1];
            ?>
                                            <tr>
                                                <td class="text-center"><?php echo $SlNo; ?></td>
                                                <td>
            <?php echo $rsRow1['itm_name']; ?>
                                                    <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itmrec_id']; ?>">
                                                    <input type="hidden" name="flitmname<?php echo $itemid[1]; ?>" value="<?php echo $rsRow1['itm_name']; ?>">
                                                </td>
                                                <td><input class="form-control input-sm text-right" <?php echo (!empty($openOB)) ? 'readonly="readonly"' : ''; ?> autocomplete="off"  type="text" name="FLDOBLA<?php echo $itemid[1]; ?>" id="FLDOBLA<?php echo $itemid[1]; ?>" size="8" maxlength="10" value="<?php echo $ob_a; ?>" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');"></td>
                                                <td><input class="form-control input-sm text-right" <?php echo $isReadOnly . $style; ?> autocomplete="off"  type="text" name="FLDRecv<?php echo $itemid[1]; ?>" id="FLDRecv<?php echo $itemid[1]; ?>" size="8" maxlength="10"  value="<?php echo $wh_received; ?>" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');" <?php echo $readonly; ?>></td>
                                                <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $itemid[1]; ?>" id="FLDIsuueUP<?php echo $itemid[1]; ?>" value="<?php echo $wh_issue_up; ?>" type="text"  size="8" maxlength="10" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');" <?php echo $readonly; ?>></td>
                                                <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDReturnTo<?php echo $itemid[1]; ?>" id="FLDReturnTo<?php echo $itemid[1]; ?>" size="8" maxlength="10" value="<?php echo $wh_adja; ?>" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');"></td>
                                                <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDUnusable<?php echo $itemid[1]; ?>" id="FLDUnusable<?php echo $itemid[1]; ?>" size="8" maxlength="10" value="<?php echo $wh_adjb; ?>" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');"></td>
                                                <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDCBLA<?php echo $itemid[1]; ?>" id="FLDCBLA<?php echo $itemid[1]; ?>" size="8" maxlength="10" value="<?php echo $cb_a; ?>" readonly></td>
                                            </tr>
            <?php
        }
        // If oone of the 3 months
        else {
            $itemid = explode('-', $rsRow1['itmrec_id']);
            $item_array[] = $itemid[1];
            
            if($issuance_flag == 'TRUE') {
                $rsRow2['wh_issue_up'] = $district_issuance;
            }
            
            ?>
                <tr>
                    <td class="text-center"><?php echo $SlNo; ?></td>
                    <td>
                        <?php echo $rsRow1['itm_name']; ?>
                        <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itmrec_id']; ?>">
                        <input type="hidden" name="flitmname<?php echo $itemid[1]; ?>" value="<?php echo $rsRow1['itm_name']; ?>">
                    </td>
                    <td><input class="form-control input-sm text-right" <?php echo (!empty($openOB)) ? 'readonly="readonly"' : ''; ?> autocomplete="off"  type="text" name="FLDOBLA<?php echo $itemid[1]; ?>" id="FLDOBLA<?php echo $itemid[1]; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['wh_obl_a']; ?>" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');"></td>
                    <td><input class="form-control input-sm text-right" <?php echo $isReadOnly . $style; ?> autocomplete="off"  type="text" name="FLDRecv<?php echo $itemid[1]; ?>" id="FLDRecv<?php echo $itemid[1]; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['wh_received']; ?>" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');" <?php echo $readonly; ?>></td>
                    <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $itemid[1]; ?>" id="FLDIsuueUP<?php echo $itemid[1]; ?>" value="<?php echo $rsRow2['wh_issue_up']; ?>" type="text" size="8" maxlength="10" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');" <?php echo $readonly; ?>></td>
                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDReturnTo<?php echo $itemid[1]; ?>" id="FLDReturnTo<?php echo $itemid[1]; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['wh_adja']; ?>" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');"></td>
                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDUnusable<?php echo $itemid[1]; ?>" id="FLDUnusable<?php echo $itemid[1]; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['wh_adjb']; ?>" onKeyUp="cal_balance('<?php echo $itemid[1]; ?>');"></td>
                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDCBLA<?php echo $itemid[1]; ?>" id="FLDCBLA<?php echo $itemid[1]; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['wh_cbl_a']; ?>" readonly></td>
                </tr>
            <?php
        }
        ?>
                                        <?php
                                        $SlNo++;
                                        $fldIndex = $fldIndex + 13;
                                    }
                                    mysql_free_result($rsTemp1);
                                    ?>
                                </table>
                                <input type="hidden" name="ActionType" value="Add">
                                <input TYPE="hidden" name="RptDate" value="<?php echo $RptDate; ?>">
                                <input TYPE="hidden" name="wh_id" value="<?php echo $wh_id; ?>">
                                <input TYPE="hidden" name="yy" value="<?php echo $yy; ?>">
                                <input TYPE="hidden" name="mm" value="<?php echo $mm; ?>">
                                <input type="hidden" name="isNewRpt" id="isNewRpt" value="<?php echo $isNewRpt; ?>" />
                                <input type="hidden" name="add_date" id="add_date" value="<?php echo $add_date; ?>" />
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
                    </form>
    <?php
}
?>
            </div>
        </div>
    </div>

    <script src="<?php echo PUBLIC_URL; ?>assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script> 
    <script>
                                    var form_clean;

                                    $(document).ready(function () {

                                        form_clean = $("#frmF7").serialize();

                                        // Auto Save function call
                                        setInterval('autoSave()', 20000);

                                        $('input[type="text"]').each(function () {
                                            if ($(this).val() == '')
                                            {
                                                $(this).val(0);
                                            }
                                        });

                                        $('input[type="text"]').change(function (e) {
                                            if ($(this).val() == '')
                                            {
                                                $(this).val('0');
                                            }
                                        });
                                        $('input[type="text"]').focus(function (e) {
                                            if ($(this).val() == '0')
                                            {
                                                $(this).val('');
                                            }
                                        });
                                        $('input[type="text"]').focusout(function (e) {
                                            if ($(this).val() == '')
                                            {
                                                $(this).val('0');
                                            }
                                        });
                                        $('input[type="text"]').keydown(function (e) {
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
                                        var form_dirty = $("#frmF7").serialize();
                                        if (form_clean != form_dirty)
                                        {
                                            $('#saveBtn').attr('disabled', 'disabled');
                                            $("#eMsg").html('Saving...');
                                            $('body').addClass("loading");
                                            $.ajax({
                                                type: "POST",
                                                url: "data_entry_action_draft.php",
                                                data: $('#frmF7').serialize(),
                                                cache: false,
                                                success: function () {
                                                    $('body').removeClass("loading");
                                                    $("#eMsg").fadeTo(500, 1, function () {
                                                        $(this).show();
                                                        $(this).html('Your data is saved is draft.').fadeTo(3000, 0, function () {
                                                            $(this).hide();
                                                            $('#saveBtn').removeAttr('disabled');
                                                        });
                                                    });
                                                }
                                            });
                                            form_clean = form_dirty;
                                        }
                                    }
                                    function formvalidate1()
                                    {
                                        $('#saveBtn').attr('disabled', false);
                                        $('#errMsg').hide();
                                        var itmLength = $("input[name^='flitmrec_id']").length;
                                        var itmArr = $("input[name^='flitmrec_id']");
                                        var FLDOBLAArr = $("input[name^='FLDOBLA']");
                                        var FLDRecvArr = $("input[name^='FLDRecv']");
                                        var FLDIsuueUPArr = $("input[name^='FLDIsuueUP']");
                                        var FLDCBLAArr = $("input[name^='FLDCBLA']");
                                        var FLDReturnToArr = $("input[name^='FLDReturnTo']");
                                        var FLDUnusableArr = $("input[name^='FLDUnusable']");
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
                                            itm = itmArr.eq(i).val();
                                            var itmInfo = itm.split('-');
                                            itmId = itmInfo[1];
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
                                        $('#saveBtn').attr('disabled', true);
                                        $("#eMsg").html('Saving...');
                                        $('body').addClass("loading");
                                        $.ajax({
                                            url: 'data_entry_action.php',
                                            data: $('#frmF7').serialize(),
                                            type: 'POST',
                                            dataType: 'json',
                                            success: function (data) {
                                                $('body').removeClass("loading");
                                                if (data.resp == 'err')
                                                {
                                                    $('#errMsg').html(data.msg).show();
                                                } else if (data.resp == 'ok')
                                                {
                                                    function RefreshParent() {
                                                        if (window.opener != null && !window.opener.closed) {
                                                            window.opener.location.reload();
                                                        }
                                                    }
                                                    window.close();
                                                    RefreshParent();
                                                }
                                            }
                                        })
                                    }
    </script> 
    <script language="javascript" type="text/javascript">
        function roundNumber(num, dec)
        {
            var result = Math.round(num * Math.pow(10, dec)) / Math.pow(10, dec);
            return result;
        }
        function formReset()
        {
            document.getElementById("AddEditF7").reset();
        }

<?php foreach ($item_array as $item) { ?>
            cal_balance('<?php echo $item; ?>');
<?php } ?>

        function cal_balance(itemId)
        {
            if (document.getElementById('WHOBLC' + itemId))
                var wholbc = (document.getElementById('WHOBLC' + itemId).value == "") ? 0 : parseInt(document.getElementById('WHOBLC' + itemId).value);
            else
                var wholbc = 0;
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
            if (document.getElementById('FLDOBLC' + itemId))
                var fldolbc = (document.getElementById('FLDOBLC' + itemId).value == "") ? 0 : parseInt(document.getElementById('FLDOBLC' + itemId).value);
            else
                var fldolbc = 0;
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
            if (document.getElementById('WHCBLC' + itemId))
                document.getElementById('WHCBLC' + itemId).value = (wholbc + WHRecv + ReturnTo) - (IsuueUP + Unusable);
            if (document.getElementById('WHCBLA' + itemId))
                document.getElementById('WHCBLA' + itemId).value = (wholba + WHRecv + ReturnTo) - (IsuueUP + Unusable);
            if (document.getElementById('MOS' + itemId) && document.getElementById('WHCBLA' + itemId))
            {
                if (parseInt(myactualavg) > 0)
                {
                    document.getElementById('MOS' + itemId).value = roundNumber(parseInt(document.getElementById('WHCBLA' + itemId).value) / parseInt(myactualavg), 1);
                } else {
                    document.getElementById('MOS' + itemId).value = roundNumber(parseInt(document.getElementById('WHCBLA' + itemId).value) / 1, 1);
                }
            }
            if (document.getElementById('FLDCBLC' + itemId))
                document.getElementById('FLDCBLC' + itemId).value = (fldolbc + FLDRecv + FLDReturnTo) - (FLDIsuueUP + FLDUnusable);
            if (document.getElementById('FLDCBLA' + itemId))
                document.getElementById('FLDCBLA' + itemId).value = (fldolba + FLDRecv + FLDReturnTo) - (FLDIsuueUP + FLDUnusable);
            if (document.getElementById('FLDMOS' + itemId) && document.getElementById('FLDCBLA' + itemId))
            {
                if (parseInt(myactualavg) > 0)
                {
                    document.getElementById('FLDMOS' + itemId).value = roundNumber(parseInt(document.getElementById('FLDCBLA' + itemId).value) / parseInt(myactualavg), 1);
                } else {
                    document.getElementById('FLDMOS' + itemId).value = roundNumber(parseInt(document.getElementById('FLDCBLA' + itemId).value) / 1, 1);
                }
            }
        }

        function formvalidate()
        {
            var arrlength = document.frmaddF7.itmrec_id.length;
            var firstrec = document.frmaddF7.itmrec_id[0].value;
            var firstval = firstrec.split('-');
            var atLeastOneEntry = false;
            var fldAtLeastOneEntry = false;
            var fldExists = false;
            for (var i = 0; i < arrlength; i++)
            {
                var fieldval = document.frmaddF7.itmrec_id[i].value;
                fieldconcat = fieldval.split('-');
                var whobla = 'WHOBLA' + fieldconcat[1];
                var whrecv = 'WHRecv' + fieldconcat[1];
                var whissue = 'IsuueUP' + fieldconcat[1];
                var fldobla = 'FLDOBLA' + fieldconcat[1];
                var fldrecv = 'FLDRecv' + fieldconcat[1];
                var fldissue = 'FLDIsuueUP' + fieldconcat[1];
                if ((document.getElementById(whobla).value != 0 || document.getElementById(whobla).value != ''))
                {
                    if (document.getElementById(whrecv).value == 0 || document.getElementById(whrecv).value == '')
                    {
                        alert('Please Enter Recieved');
                        document.getElementById(whrecv).focus();
                        return false;
                    }
                    if (document.getElementById(whissue).value == 0 || document.getElementById(whissue).value == '')
                    {
                        alert('Please Enter Recieved');
                        document.getElementById(whissue).focus();
                        return false;
                    }
                }
                if ((document.getElementById(whobla).value != 0 || document.getElementById(whobla).value != ''))
                {
                    atLeastOneEntry = true;
                    break;
                }
            }
            if (atLeastOneEntry == false)
            {
                alert('Please Enter Atleast one Entry');
                document.getElementById('WHOBLA' + firstval[1]).focus();
                return false;
            }
        }
    </SCRIPT> 
    <script>
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
        } else if (browser.name == 'Chrome' && browser.version < 35)
        {
            alert('You are using an outdated version of the Chrome. Please update your browser for data entry.');
            window.close();
        } else if (browser.name == 'Opera' && browser.version < 28)
        {
            alert('You are using an outdated version of the Opera. Please update your browser for data entry.');
            window.close();
        } else if (browser.name == 'MSIE')
        {
            alert('Please use Mozilla Firefox, Chrome or Opera for data entry.');
            window.close();
        }
    </script>
</body>
</html>