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
    //print_r($result_province);
} else {
    //Display message
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

                    // Check warehouse level
                    //gets
                    // stakeholder.lvl,
                    //tbl_warehouse.hf_type_id,
                    //tbl_warehouse.prov_id

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

                    // Check if its 1st Month of Data Entry 
                    $checkData = "SELECT
                                tbl_hf_data.reporting_date
                            FROM
                                tbl_hf_data
                            WHERE
                                tbl_hf_data.warehouse_id = $wh_id
                            ORDER BY
                                tbl_hf_data.reporting_date ASC
                            LIMIT 1";
                    $checkDataRes = mysql_fetch_array(mysql_query($checkData));
                    //openOB
                    $openOB = ($checkDataRes['reporting_date'] == $RptDate) ? '' : $checkDataRes['reporting_date'];
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
                                        <th colspan="2" class="text-center">Cases/Clients</th>
                                    </tr>
                                    <tr>
                                        <th class="text-center">(+)</th>
                                        <th class="text-center">(-)</th>
                                        <th class="text-center">New</th>
                                        <th class="text-center">Old</th>
                                    </tr>
                                    <?php
                                    //query 
                                    //gets
                                    //all from itminfo_tab
                                    $q1 = "SELECT * FROM `itminfo_tab` WHERE `itm_status`=1 AND `itm_id` IN (SELECT `Stk_item` FROM `stakeholder_item` WHERE `stkid` =$stkid) ORDER BY `frmindex`";
                                    $rsTemp1 = mysql_query($q1);

                                    $SlNo = 1;
                                    $fldIndex = 0;
                                    //loop
                                    while ($rsRow1 = mysql_fetch_array($rsTemp1)) {
                                        if ((isset($hfTypeId) && $hfTypeId == 1) && (isset($result_province['prov_id']) && $result_province['prov_id'] == 1)) {
                                            if (in_array($rsRow1['itm_id'], array(8,13,30,34)))
                                                continue;
                                        }
                                        $item_char = $rsRow1['itm_id'];
                                        $qry_cf = "SELECT REPUpdateCarryForwardHF('$RptDate','$item_char', $wh_id) FROM DUAL";
                                        mysql_query($qry_cf);
                                        //$SlNo = ((strlen($SlNo) < 2) ? $SlNo : $SlNo);
                                        //query
                                        //gets
                                        //all from tbl_hf_data
                                        $qry = "SELECT * FROM tbl_hf_data WHERE `warehouse_id`='" . $wh_id . "' AND reporting_date='" . $PrevMonthDate . "' AND `item_id`='$rsRow1[itm_id]'";
                                        //result
                                        //echo $qry;exit;
                                        $rsTemp3 = mysql_query($qry);
                                        $rsRow2 = mysql_fetch_array($rsTemp3);
                                        //echo '<pre>';print_r($rsRow2);
                                        //add date
                                        $add_date = $rsRow2['created_date'];
                                        // if new report
                                        if ($isNewRpt == 1) {

                                            //check itm_category
                                            if ($rsRow1['itm_category'] == 1) {
                                                //wh_issue_up
                                                $wh_issue_up = 0;
                                                //wh_adja
                                                $wh_adja = 0;
                                                //wh_adjb
                                                $wh_adjb = 0;
                                                //wh_received
                                                $wh_received = 0;
                                                //ob_a
                                                $ob_a = $rsRow2['closing_balance'];
                                                //cb_a
                                                $cb_a = $rsRow2['closing_balance'];
                                                $new = 0;
                                                $old = 0;
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $SlNo; ?></td>
                                                    <td>
                                                        <?php echo $rsRow1['itm_name']; ?>
                                                        <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itm_id']; ?>">
                                                        <input type="hidden" name="flitm_category[]" value="<?php echo $rsRow1['itm_category']; ?>">
                                                        <input type="hidden" name="flitmname<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow1['itm_name']; ?>">
                                                    </td>
                                                    <td><input class="form-control input-sm text-right" <?php echo (!empty($openOB)) ? 'readonly="readonly"' : ''; ?> autocomplete="off"  type="text" name="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $ob_a; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" <?php echo $isReadOnly . $style; ?> autocomplete="off"  type="text" name="FLDRecv<?php echo $rsRow1['itm_id']; ?>" id="FLDRecv<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $wh_received; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" id="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $wh_issue_up; ?>" type="text"  size="8" maxlength="10" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" id="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $wh_adja; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" id="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $wh_adjb; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $cb_a; ?>" readonly></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDnew<?php echo $rsRow1['itm_id']; ?>" id="FLDnew<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $new; ?>"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDold<?php echo $rsRow1['itm_id']; ?>" id="FLDold<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $old; ?>"></td>
                                                </tr>
                                                <?php
                                            } else if ($rsRow1['itm_category'] == 2) {
                                                $surgeyArr[$rsRow1['itm_id']]['pk_id'] = $rsRow2['pk_id'];
                                                $surgeyArr[$rsRow1['itm_id']]['name'] = $rsRow1['itm_name'];
                                                $surgeyArr[$rsRow1['itm_id']]['category'] = $rsRow1['itm_category'];
                                                $surgeyArr[$rsRow1['itm_id']]['cases'] = $wh_issue_up;
                                            }
                                        }
                                        //Old report Edit Mode
                                        else {

                                            //check itm_category
                                            if ($rsRow1['itm_category'] == 1) {
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $SlNo; ?></td>
                                                    <td>
                                                        <?php echo $rsRow1['itm_name']; ?>
                                                        <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itm_id']; ?>">
                                                        <input type="hidden" name="flitm_category[]" value="<?php echo $rsRow1['itm_category']; ?>">
                                                        <input type="hidden" name="flitmname<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow1['itm_name']; ?>">
                                                    </td>
                                                    <td><input class="form-control input-sm text-right" <?php echo (!empty($openOB)) ? 'readonly="readonly"' : ''; ?> autocomplete="off"  type="text" name="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['opening_balance']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" <?php echo $isReadOnly . $style; ?> autocomplete="off"  type="text" name="FLDRecv<?php echo $rsRow1['itm_id']; ?>" id="FLDRecv<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['received_balance']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" id="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow2['issue_balance']; ?>" type="text" size="8" maxlength="10" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" id="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['adjustment_positive']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" id="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['adjustment_negative']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['closing_balance']; ?>" readonly></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDnew<?php echo $rsRow1['itm_id']; ?>" id="FLDnew<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['new']; ?>"></td>
                                                    <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDold<?php echo $rsRow1['itm_id']; ?>" id="FLDold<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['old']; ?>"></td>
                                                </tr>
                                                <?php
                                            } else if ($rsRow1['itm_category'] == 2) {
                                                //pk_id
                                                $surgeyArr[$rsRow1['itm_id']]['pk_id'] = $rsRow2['pk_id'];
                                                //name
                                                $surgeyArr[$rsRow1['itm_id']]['name'] = $rsRow1['itm_name'];
                                                //category
                                                $surgeyArr[$rsRow1['itm_id']]['category'] = $rsRow1['itm_category'];
                                                //cases
                                                $surgeyArr[$rsRow1['itm_id']]['cases'] = $rsRow2['issue_balance'];
                                            }
                                        }
                                        $SlNo++;
                                        $fldIndex = $fldIndex + 13;
                                    }
                                    //echo '<pre>';print_r($surgeyArr);exit;
                                    //free result
                                    mysql_free_result($rsTemp1);
                                    ?>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if ($hfTypeId == 4 || $hfTypeId == 5) {
                                    ?>
                                    <div class="col-md-4">
                                        <h4 style="margin-top:20px;"><span class="green-clr-txt">Surgery Cases(Reffered)</span></h4>
                                        <input type="hidden" name="hf_type_id" id="hf_type_id"  value="<?php echo $hfTypeId; ?>">
                                        <table class="table table-bordered">
                                            <tr>

                                                <th>Reffered By</th>
                                                <?php
                                                $counter = 0;
                                                //loop
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    $counter++;
                                                    ?>
                                                    <th><?php echo $data['name']; ?></th>
                                                    <?php
                                                }
                                                //check hfTypeId 
                                                if ($hfTypeId == 4) {
                                                    $and = " AND tbl_hf_type.pk_id != 5";
                                                } else if ($hfTypeId == 5) {
                                                    $and = " AND tbl_hf_type.pk_id != 4";
                                                }
                                                //query
                                                //gets
                                                //tbl_hf_type.pk_id,
                                                //tbl_hf_type.hf_type
                                                $qry = "SELECT
                                                                tbl_hf_type.pk_id,
                                                                tbl_hf_type.hf_type
                                                        FROM
                                                                tbl_hf_type
                                                        INNER JOIN tbl_hf_type_province ON tbl_hf_type.pk_id = tbl_hf_type_province.hf_type_id
                                                        WHERE
                                                                tbl_hf_type_province.province_id = " . $result_province['prov_id'] . "
                                                        AND tbl_hf_type_province.stakeholder_id = 1 $and";
                                                //result
                                                $rs_arr = mysql_query($qry);
                                                //loop
                                                while ($arr1 = mysql_fetch_array($rs_arr)) {
                                                    ?>
                                                </tr>
                                                <td><?php echo $arr1['hf_type']; ?></td>
                                                <input type="hidden" name="hf_type_id[]" value="<?php echo $arr1['pk_id']; ?>">
                                                <?php
                                                //loop
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    //check isNewRpt 
                                                    if ($isNewRpt == 1) {
                                                        $hf_pk_id = 0;
                                                        $hf_data_pk = 0;
                                                    } else {
                                                        $hf_pk_id = $arr1['pk_id'];
                                                        $hf_data_pk = $data['pk_id'];
                                                    }
                                                    //mcQry
                                                    //gets
                                                    //tbl_hf_data_reffered_by.pk_id,
                                                    //tbl_hf_data_reffered_by.hf_data_id,
                                                    //tbl_hf_data_reffered_by.hf_type_id,
                                                    //tbl_hf_data_reffered_by.ref_surgeries,
                                                    //tbl_hf_data_reffered_by.static,
                                                    //tbl_hf_data_reffered_by.camp
                                                    $mcQry = "SELECT
                                                                    tbl_hf_data_reffered_by.pk_id,
                                                                    tbl_hf_data_reffered_by.hf_data_id,
                                                                    tbl_hf_data_reffered_by.hf_type_id,
                                                                    tbl_hf_data_reffered_by.ref_surgeries,
                                                                    tbl_hf_data_reffered_by.static,
                                                                    tbl_hf_data_reffered_by.camp
                                                            FROM
                                                                    tbl_hf_data_reffered_by
                                                            WHERE
                                                                    tbl_hf_data_reffered_by.hf_type_id = $hf_pk_id
                                                            AND tbl_hf_data_reffered_by.hf_data_id = $hf_data_pk";
                                                    //result
                                                    $mcRow = @mysql_fetch_array(mysql_query($mcQry));
                                                    ?>
                                                    <td><input class="form-control input-sm text-right reffered<?php echo $itemid; ?>" autocomplete="off" type="text" name="reffered<?php echo $itemid; ?><?php echo $arr1['pk_id']; ?>" value="<?php echo $mcRow['ref_surgeries']; ?>"   size="8" maxlength="10" /></td>
                                                    <?php
                                                }
                                                ?>
                                                </tr>
                                                <?php
                                            }
                                            ?>
                                            <tr>
                                                <td>Gross Total /Net Total</td>
                                                <?php
                                                //loop
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    ?>
                                                <input type="hidden" name="flitmrec_id[]" value="<?php echo $itemid; ?>">
                                                <input type="hidden" name="flitm_category[]" value="<?php echo $data['category']; ?>">
                                                <td><input class="form-control input-sm text-right" readonly type="text" name="FLDIsuueUP<?php echo $itemid; ?>" id="FLDIsuueUP<?php echo $itemid; ?>" value="<?php echo $data['cases']; ?>" /></td>
                                                <?php
                                            }
                                            ?>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-4">
                                        <h4 style="margin-top:20px;"><span class="green-clr-txt">Surgery Cases(Performed)</span></h4>
                                        <table class="table table-bordered">
                                            <tr>
                                                <td>&nbsp;</td>
                                                <?php
                                                $counter = 0;
                                                //loop
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    $counter++;
                                                    ?>
                                                    <th><?php echo $data['name']; ?></th>
                                                    <?php
                                                }

                                                $arr = array('Static Center', 'Camp Cases');
                                                $counter = 1;
                                                //loop
                                                foreach ($arr as $val) {
                                                    ?>
                                                </tr>
                                                <td><?php echo $val; ?></td>
                                                <?php
                                                //loop
                                                //echo '<pre>';print_r($surgeyArr);exit;
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    if ($isNewRpt == 1) {
                                                        $hf_data_pk = 0;
                                                    } else {
                                                        $hf_data_pk = $data['pk_id'];
                                                    }
                                                    //mcQry1
                                                    //gets
                                                    // male_static,
                                                    // male_camp
                                                    $mcQry1 = "SELECT
                                                                        tbl_hf_data_reffered_by.static AS male_static,
                                                                        tbl_hf_data_reffered_by.camp AS male_camp
                                                                FROM
                                                                        tbl_hf_data_reffered_by
                                                                WHERE
                                                                        tbl_hf_data_reffered_by.hf_data_id = $hf_data_pk ";
                                                    //result
                                                    $mcRow1 = @mysql_fetch_array(mysql_query($mcQry1));
                                                    //mcQry2
                                                    //gets
                                                    //female_static,
                                                    //female_camp
                                                    $mcQry2 = "SELECT
                                                                        tbl_hf_data_reffered_by.static AS female_static,
                                                                        tbl_hf_data_reffered_by.camp AS female_camp
                                                                FROM
                                                                        tbl_hf_data_reffered_by
                                                                WHERE
                                                                        tbl_hf_data_reffered_by.hf_data_id = $hf_data_pk ";
                                                    //result
                                                    $mcRow2 = @mysql_fetch_array(mysql_query($mcQry2));

                                                    if ($counter == 1 && $itemid == '31') {
                                                        $static_camp = $mcRow1['male_static'];
                                                        $total_male = $mcRow1['male_static'];
                                                    } else if ($counter == 1 && $itemid = '32') {
                                                        $static_camp = $mcRow2['female_static'];
                                                        $total_female = $mcRow2['female_static'];
                                                    } else if ($counter == 2 && $itemid == '31') {
                                                        $static_camp = $mcRow1['male_camp'];
                                                        $total_male += $mcRow1['male_camp'];
                                                    } else if ($counter == 2 && $itemid = '32') {
                                                        $static_camp = $mcRow2['female_camp'];
                                                        $total_female += $mcRow2['female_camp'];
                                                    }
                                                    ?>
                                                    <td><input type="text" class="form-control input-sm text-right totalStaticCampMale<?php echo $itemid; ?>" autocomplete="off" name="staticCamp<?php echo $itemid; ?>[]" value="<?php echo $static_camp; ?>" size="8" maxlength="10" /></td>
                                                    <?php
                                                }
                                                ?>
                                                </tr>
                                                <?php
                                                $counter++;
                                            }
                                            ?>
                                            <tr>
                                                <td>Gross Total /Net Total</td>

                                                <td><input class="form-control input-sm text-right" readonly type="text" id="totalStaticCampMale" value="<?php echo $total_male; ?>" /></td>
                                                <td><input class="form-control input-sm text-right" readonly type="text" id="totalStaticCampFemale" value="<?php echo $total_female; ?>" /></td>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="col-md-4">
                                        <h4 style="margin-top:20px;"><span class="green-clr-txt">Surgery Cases(Reffered)</span></h4>
                                        <input type="hidden" name="hf_type_id" id="hf_type_id"  value="<?php echo $hfTypeId; ?>">
                                        <table class="table table-bordered">
                                            <tr>
                                                <?php
                                                $counter = 0;
                                                //loop
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    $counter++;
                                                    ?>
                                                    <th><?php echo $data['name']; ?></th>
                                                    <?php
                                                }
                                                ?>
                                            </tr>
                                            <tr>
                                                <?php
                                                $counter = 0;
                                                //loop
                                                foreach ($surgeyArr as $itemid => $data) {
                                                    $counter++;
                                                    ?>
                                                    <td>
                                                        <input type="hidden" name="flitmrec_id[]" value="<?php echo $itemid; ?>">
                                                        <input type="hidden" name="flitm_category[]" value="<?php echo $data['category']; ?>">
                                                        <input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $itemid; ?>" id="FLDIsuueUP<?php echo $itemid; ?>" value="<?php echo $data['cases']; ?>" type="text" size="8" maxlength="10" />
                                                    </td>
                                                    <?php
                                                }
                                                ?>
                                            </tr>
                                        </table>
                                    </div>
                                    <?php
                                }

                                if ($whProvId == 1 || $whProvId == 2) {
                                    $newText = 'New';
                                    $oldText = 'Old';
                                    $childText = 'Children';
                                } else {
                                    $newText = 'Children';
                                    $oldText = 'Adults';
                                    $childText = 'General Ailment';
                                }
                                //mcQry
                                //tbl_hf_mother_care.pre_natal_new,
                                //tbl_hf_mother_care.pre_natal_old,
                                //tbl_hf_mother_care.post_natal_new,
                                //tbl_hf_mother_care.post_natal_old,
                                //tbl_hf_mother_care.ailment_children,
                                //tbl_hf_mother_care.ailment_adults,
                                //tbl_hf_mother_care.general_ailment
                                $mcQry = "SELECT
                                    tbl_hf_mother_care.pre_natal_new,
                                    tbl_hf_mother_care.pre_natal_old,
                                    tbl_hf_mother_care.post_natal_new,
                                    tbl_hf_mother_care.post_natal_old,
                                    tbl_hf_mother_care.ailment_children,
                                    tbl_hf_mother_care.ailment_adults,
                                    tbl_hf_mother_care.general_ailment
                                FROM
                                    tbl_hf_mother_care
                                WHERE
                                    tbl_hf_mother_care.reporting_date = '$RptDate'
                                AND tbl_hf_mother_care.warehouse_id = $wh_id";
                                //result
                                $mcRow = mysql_fetch_array(mysql_query($mcQry));
                                ?>
                                <div class="col-md-4">
                                    <h4 style="margin-top:20px;"><span class="green-clr-txt">Mother & Child Care (No. of Cases)</span></h4>
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>&nbsp;</th>
                                            <th class="text-center">New</th>
                                            <th class="text-center">Old</th>
                                        </tr>
                                        <tr>
                                            <td>Ante-natal</td>
                                            <td><input class="form-control input-sm text-right" type="text" name="pre_natal_new" id="pre_natal_new" autocomplete="off" value="<?php echo $mcRow['pre_natal_new']; ?>" /></td>
                                            <td><input class="form-control input-sm text-right" type="text" name="pre_natal_old" id="pre_natal_old" autocomplete="off" value="<?php echo $mcRow['pre_natal_old']; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td>Post-natal</td>
                                            <td><input class="form-control input-sm text-right" type="text" name="post_natal_new" id="post_natal_new" autocomplete="off" value="<?php echo $mcRow['post_natal_new']; ?>" /></td>
                                            <td><input class="form-control input-sm text-right" type="text" name="post_natal_old" id="post_natal_old" autocomplete="off" value="<?php echo $mcRow['post_natal_old']; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo $childText; ?></td>
                                            <td><?php echo ($whProvId == 3) ? ($newText . '<br />') : ''; ?><input class="form-control input-sm text-right" type="text" name="ailment_child" id="ailment_child" autocomplete="off" value="<?php echo $mcRow['ailment_children']; ?>" /></td>
                                            <td><?php echo ($whProvId == 3) ? ($oldText . '<br />') : ''; ?><input class="form-control input-sm text-right" type="text" name="ailment_adult" id="ailment_adult" autocomplete="off" value="<?php echo $mcRow['ailment_adults']; ?>" /></td>
                                        </tr>
                                        <tr>
                                            <?php if ($whProvId == 1 || $whProvId == 2) { ?>
                                                <td>General Ailment</td>
                                            <?php } ?>
                                            <?php if ($whProvId == 1 || $whProvId == 2) { ?>
                                                <td colspan="2"><input class="form-control input-sm text-right" type="text" name="general_ailment" id="general_ailment" autocomplete="off" value="<?php echo $mcRow['general_ailment']; ?>" /></td>
                                            <?php } ?>
                                        </tr>
                                    </table>
                                </div>
                                <?php
                                $hfPrograms = array(1, 2, 4, 11);
                                //check whProvId
                                if ($whProvId == 3 && !in_array($hfTypeId, $hfPrograms)) {
                                    ?>
                                    <div class="col-md-4">
                                        <h4 style="margin-top:20px;"><span class="green-clr-txt">Total Number of Centers for <?php echo $whName; ?></span></h4>
                                        <table class="table table-bordered">
                                            <?php
                                            $qry = "SELECT
                                                tbl_hf_non_program_count.total_facilities
                                            FROM
                                                tbl_hf_non_program_count
                                            WHERE
                                                tbl_hf_non_program_count.warehouse_id = $wh_id
                                            AND tbl_hf_non_program_count.reporting_date = '$RptDate'";
                                            //result
                                            $totalHf = mysql_fetch_array(mysql_query($qry));
                                            $totalHf = $totalHf['total_facilities'];
                                            ?>
                                            <tr>
                                                <td><input style="width:150px" class="form-control input-sm text-right" type="text" name="total_hf" id="total_hf" value="<?php echo (!empty($totalHf)) ? $totalHf : 0; ?>" autocomplete="off" /></td>
                                            </tr>
                                        </table>
                                    </div>
                                <?php }
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
                                        $(document).ready(function () {

                                            form_clean = $("#frmF7").serialize();

                                            // Auto Save function call
                                            //setInterval('autoSave()', 20000);

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
                                                $.ajax({
                                                    type: "POST",
                                                    url: "data_entry_action_draft.php",
                                                    data: $('#frmF7').serialize(),
                                                    cache: false,
                                                    success: function () {
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

                                        //Total Calculation
                                        $(".reffered31").on("keyup keydown", function () {
                                            calculateSum('reffered31');
                                        });

                                        $(".reffered32").on("keyup keydown", function () {
                                            calculateSum('reffered32');
                                        });

                                        $(".totalStaticCampMale31").on("keyup keydown", function () {
                                            calculateSum('totalStaticCampMale31');
                                        });
                                        $(".totalStaticCampMale32").on("keyup keydown", function () {
                                            calculateSum('totalStaticCampMale32');
                                        });

                                        function calculateSum(field) {

                                            var sum = 0;
                                            if (field == 'reffered31') {
                                                var total = 'FLDIsuueUP31';
                                            } else if (field == 'reffered32') {
                                                var total = 'FLDIsuueUP32';
                                            } else if (field == 'totalStaticCampMale31') {
                                                var total = 'totalStaticCampMale';
                                            } else if (field == 'totalStaticCampMale32') {
                                                var total = 'totalStaticCampFemale';
                                            }

                                            //iterate through each textboxes and add the values
                                            $("." + field).each(function () {
                                                var reffered_male = $(this).val();

                                                if (!isNaN(reffered_male) && reffered_male.length != 0) {
                                                    sum += parseFloat(reffered_male);
                                                } else if (reffered_male.length != 0) {
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
                                                if (refferedTotalMale < StaticCampTotalMale || refferedTotalFemale < StaticCampTotalFemale)
                                                {
                                                    alert("Performed Surgery Cases Gross Totals can not be greater than Reffered Surgery Cases Gross Totals");
                                                    $('#totalStaticCampMale').css('background', '#F45B5C');
                                                    $('#totalStaticCampFemale').css('background', '#F45B5C');
                                                    $('#FLDIsuueUP031').css('background', '#F45B5C');
                                                    $('#FLDIsuueUP032').css('background', '#F45B5C');
                                                    return false;
                                                } else
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
                                                url: 'data_entry_hf_pwd_action.php',
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
                                                        $('#saveBtn').attr('disabled', false);
                                                        $('#errMsg').hide();
                                                        $('#eMsg').hide();
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
                                                } else {
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
                                                } else {
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