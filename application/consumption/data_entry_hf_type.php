<?php
/**
 * data_entry_hf_type
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("includes/AllClasses.php");
//include adminhtml
include("../html/adminhtml.inc.php");
//Check user_id
if (isset($_SESSION['user_id'])) {
    //get user_id
    $userid = $_SESSION['user_id'];
    //set user_id
    $objwharehouse_user->m_npkId = $userid;
    //Get wh user By Idc
    $result = $objwharehouse_user->GetwhuserByIdc();
} else {
    //error msg
    echo "user not login or timeout";
}
//get e
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
}
//Initialize variable

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <style>
            #hideit {
                display: none;
            }
            input[type="text"] {
                text-align: right;
            }
        </style>
        <title>Data Entry</title>
        <SCRIPT LANGUAGE="JAVASCRIPT" TYPE="TEXT/JAVASCRIPT">
            function roundNumber(num, dec)
            {
            var result = Math.round(num*Math.pow(10,dec))/Math.pow(10,dec);
            return result;
            }
            function cal_balance(itemId)
            {
            if(document.getElementById('WHOBLA'+itemId))	
            var wholba = (document.getElementById('WHOBLA'+itemId).value=="")? 0 : parseInt(document.getElementById('WHOBLA'+itemId).value);
            else
            var wholba = 0;
            if(document.getElementById('WHRecv'+itemId))	
            var WHRecv = (document.getElementById('WHRecv'+itemId).value=="")? 0 : parseInt(document.getElementById('WHRecv'+itemId).value);
            else
            var WHRecv = 0;
            if(document.getElementById('IsuueUP'+itemId))
            var IsuueUP = (document.getElementById('IsuueUP'+itemId).value=="")? 0 : parseInt(document.getElementById('IsuueUP'+itemId).value);
            else
            var IsuueUP = 0;
            //WH adj+
            if(document.getElementById('ReturnTo'+itemId))
            var ReturnTo = (document.getElementById('ReturnTo'+itemId).value=="")? 0 : parseInt(document.getElementById('ReturnTo'+itemId).value);
            else
            var ReturnTo = 0; 
            //WH adj-
            if(document.getElementById('Unusable'+itemId))
            var Unusable = (document.getElementById('Unusable'+itemId).value=="")? 0 : parseInt(document.getElementById('Unusable'+itemId).value);
            else
            var Unusable = 0;							
            if(document.getElementById('FLDOBLA'+itemId))	 
            var fldolba  = (document.getElementById('FLDOBLA'+itemId).value=="")? 0 : parseInt(document.getElementById('FLDOBLA'+itemId).value);
            else
            var fldolba  = 0;
            if(document.getElementById('FLDRecv'+itemId))	 	
            var FLDRecv  = (document.getElementById('FLDRecv'+itemId).value=="")? 0 : parseInt(document.getElementById('FLDRecv'+itemId).value);
            else
            var FLDRecv  = 0;
            if(document.getElementById('FLDIsuueUP'+itemId))	
            var FLDIsuueUP = (document.getElementById('FLDIsuueUP'+itemId).value=="")? 0 : parseInt(document.getElementById('FLDIsuueUP'+itemId).value);
            else
            var FLDIsuueUP = 0;							
            /*if(document.getElementById('FLDmyavg'+itemId))	
            var FLDmyavg = (document.getElementById('FLDmyavg'+itemId).value=="")? 0 : parseInt(document.getElementById('FLDmyavg'+itemId).value);
            else
            var FLDmyavg = 0;*/ 							
            //Fld adj+
            if(document.getElementById('FLDReturnTo'+itemId))
            var FLDReturnTo = (document.getElementById('FLDReturnTo'+itemId).value=="")? 0 : parseInt(document.getElementById('FLDReturnTo'+itemId).value);
            else
            var FLDReturnTo = 0;
            //Fld adj-
            if(document.getElementById('FLDUnusable'+itemId))
            var FLDUnusable = (document.getElementById('FLDUnusable'+itemId).value=="")? 0 : parseInt(document.getElementById('FLDUnusable'+itemId).value);
            else
            var FLDUnusable = 0; 
            if(document.getElementById('FLDmyavg'+itemId))
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
            }
            if(document.getElementById('WHCBLA'+itemId))	
            document.getElementById('WHCBLA'+itemId).value = (wholba+WHRecv+ReturnTo)-(IsuueUP+Unusable);
            if(document.getElementById('MOS'+itemId) && document.getElementById('WHCBLA'+itemId))
            {
            if(parseInt(myactualavg)>0)
            {
            document.getElementById('MOS'+itemId).value = roundNumber(parseInt(document.getElementById('WHCBLA'+itemId).value)/parseInt(myactualavg),1);
            }
            else {
            document.getElementById('MOS'+itemId).value = roundNumber(parseInt(document.getElementById('WHCBLA'+itemId).value)/1,1);
            }
            }
            if(document.getElementById('FLDCBLA'+itemId))	
            document.getElementById('FLDCBLA'+itemId).value = (fldolba+FLDRecv+FLDReturnTo)-(FLDIsuueUP+FLDUnusable);
            if(document.getElementById('FLDMOS'+itemId) && document.getElementById('FLDCBLA'+itemId))
            {
            if(parseInt(myactualavg)>0)
            {
            document.getElementById('FLDMOS'+itemId).value = roundNumber(parseInt(document.getElementById('FLDCBLA'+itemId).value)/parseInt(myactualavg),1);
            }
            else {
            document.getElementById('FLDMOS'+itemId).value = roundNumber(parseInt(document.getElementById('FLDCBLA'+itemId).value)/1,1);
            }
            }
            }
        </SCRIPT>
    </head>
    <body style="font-family:Verdana, Geneva, sans-serif; font-size: 0.8em;color:Black;">
        <table width="980" border="0" cellspacing="0" cellpadding="0" align="center">
            <tr id="hideit">
                <td><?php
//include top file
                    include(SITE_PATH . "plmis_inc/common/top.php");
                    ?></td>
            </tr>
            <tr>
                <td valign="top"><table width="100%" height="146" valign="top">
                        <tr>
                            <td width="80%" bgcolor="#E1E1E1" valign="top" id="showGrid"><?php 
                                $wh_id="";
                                //check Do
                                if(isset($_REQUEST['Do']) && !empty($_REQUEST['Do']))
                                {
                                //get Do	
                                $temp=urldecode($_REQUEST['Do']);
                                $tmpStr=substr($temp,1,strlen($temp)-1);
                                $temp=explode("|",$tmpStr);

                                // Warehouse ID
                                $wh_id=$temp[0]-77000; 
                                //Report Date
                                $RptDate=$temp[1]; 
                                //if value=1 then new report
                                $isNewRpt=$temp[2]; 
                                $tt=explode("-",$RptDate);
                                //Reprot year
                                $yy=$tt[0]; 
                                //report Month
                                $mm=$tt[1];		

                                
                                $readonly_for_im = false;
                                if ($_SESSION['is_allowed_im'] == 1  && $RptDate >= $_SESSION['im_start_month']) {
                                    $isReadOnly = 'readonly="readonly"';
                                    $style = 'style="background:#CCC"';
                                    $readonly_for_im = true;
                                } else {
                                    $isReadOnly = '';
                                    $style = '';
                                    $readonly_for_im = false;
                                }
                                
                                $month = date('M', mktime(0, 0, 0, $mm, 1));
                                //check data
                                $checkData = "SELECT DISTINCT
                                tbl_hf_type_data.reporting_date
                                FROM
                                tbl_hf_type_data
                                WHERE
                                tbl_hf_type_data.district_id = ".$_SESSION['dist_id']." AND tbl_hf_type_data.facility_type_id = $wh_id
                                ORDER BY
                                tbl_hf_type_data.reporting_date ASC
                                LIMIT 1";
                                $checkDataRes = mysql_fetch_array(mysql_query($checkData));
                                $openOB = ($checkDataRes['reporting_date'] == $RptDate) ? '' : $checkDataRes['reporting_date'];

                                //****************************************************************************
                                $objwarehouse->m_npkId=$wh_id;
                                print "<b>Health Facility Type:</b> ".$objwarehouse->GetWarehouseTypeNameById($wh_id);
                                //Get Stk ID By WH Type Id
                                $stkid=$objwarehouse->GetStkIDByWHTypeId($wh_id);
                                print "; ";
                                print "<b>Monthly Report:</b> ".$month.'-'.$yy;  
                                if ( $isNewRpt == 1 )
                                {
                                //Get Previous Month Report Date
                                $PrevMonthDate=$objReports->GetPreviousMonthReportDate($RptDate);
                                }
                                else 
                                {
                                $PrevMonthDate=$RptDate;
                                }
                                ?>
                                <FORM NAME="frmF7" id="frmF7" ACTION="data_entry_hf_type_action.php" METHOD="POST" ENCTYPE="MULTIPART/FORM-DATA" onSubmit="return formvalidate1();">
                                    <TABLE CELLPADDING="2" CELLSPACING="0" WIDTH="100%" BORDER="1" ALIGN="LEFT" CLASS="TableAreaSmall" BORDERCOLOR="#000000" STYLE="BORDER-COLLAPSE: COLLAPSE">
                                        <TR>
                                            <TD rowspan="2" width="19" CLASS="sb1GeenGradientBoxMiddle" TITLE="This Is Just A Simple Serial Number.">S.No.</TD>
                                            <TD rowspan="2" align="center" CLASS="sb1GeenGradientBoxMiddle" WIDTH="150" TITLE="This Is The Reported Article/Item Name.">Article</TD>
                                            <TD rowspan="2" align="center" CLASS="sb1GeenGradientBoxMiddle" TITLE="This Is The Opening Balance Of The Month,i.e. The Closing balance Of The Previous Month.">Opening balance</TD>
                                            <TD rowspan="2" align="center" width="62" CLASS="sb1GeenGradientBoxMiddle" TITLE="This Is The Quantity Of The Received Items From The CWH In This Month.">Received</TD>
                                            <TD rowspan="2" align="center" CLASS="sb1GeenGradientBoxMiddle" TITLE="This Is The Quantity Of The Issued Items In This Month.">Issued</TD>
                                            <TD colspan="2" align="center" CLASS="sb1GeenGradientBoxMiddle" TITLE="">Adjustments</TD>
                                            <TD rowspan="2" align="center" CLASS="sb1GeenGradientBoxMiddle" TITLE="This Is The Closing Balance Of The Stock For This Month.">Closing Balance</TD>
                                            <TD colspan="2" CLASS="sb1GeenGradientBoxMiddle" align="center">Cases/Clients</TD>
                                        </TR>
                                        <TR> 
                                            <TD width="53" align="center" CLASS="F7BCOLCAH" TITLE="This is Sum of Adjustments That Results  Increase In Stock."><b class="sb1FormLabel">(+)</b></TD>
                                            <TD width="53" align="center" CLASS="sb1GeenGradientBoxMiddle" TITLE="This is Sum of Adjustments That Results  Decrease In Stock."><b class="sb1FormLabel">(-)</b></TD>
                                            <TD width="53" align="center" CLASS="sb1GeenGradientBoxMiddle"><b class="sb1FormLabel">New</b></TD>
                                            <TD width="53" align="center" CLASS="sb1GeenGradientBoxMiddle"><b class="sb1FormLabel">Old</b></TD>
                                        </TR>
                                        <TR class="sb1GreenInfoBoxMiddleText">
                                            <TD CLASS="TDLCOLCABSMALL">1</TD>
                                            <TD CLASS="TDLCOLCABSMALL">2</TD>
                                            <TD CLASS="TDLCOLCABSMALL">3</TD>
                                            <TD CLASS="TDLCOLCABSMALL">4</TD>
                                            <TD CLASS="TDLCOLCABSMALL">5</TD>
                                            <TD CLASS="TDLCOLCABSMALL">6</TD>
                                            <TD CLASS="TDLCOLCABSMALL">7</TD>
                                            <TD CLASS="TDLCOLCABSMALL">8</TD>
                                            <TD CLASS="TDLCOLCABSMALL">9</TD>
                                            <TD CLASS="TDLCOLCABSMALL">10</TD>
                                        </TR>
                                        <?php
                                        //query
                                        //gets
                                        //all from itminfo_tab
                                        $rsTemp1 = mysql_query("SELECT * FROM `itminfo_tab` WHERE `itm_status`=1 AND `itm_id` IN (SELECT `Stk_item` FROM `stakeholder_item` WHERE `stkid` =$stkid) ORDER BY `frmindex`");
                                        $SlNo = 1;
                                        $fldIndex = 0;
                                        //get dist_id
                                        $districtId = $_SESSION['dist_id'];
                                        //ItemTableName 
                                        $ItemTableName = $TableName;
                                        //fetch data from rsTemp1
                                        while ($rsRow1 = mysql_fetch_array($rsTemp1)) {
                                            //$SlNo = ((strlen($SlNo) < 2) ? $SlNo : $SlNo);
                                            //query
                                            //gets
                                            //all from tbl_hf_type_data 
                                            $qry = "SELECT * FROM tbl_hf_type_data WHERE facility_type_id='" . $wh_id . "' AND district_id = " . $districtId . " AND reporting_date='" . $PrevMonthDate . "' AND `item_id`='$rsRow1[itm_id]'";
                                            //result
                                            $rsTemp3 = mysql_query($qry);
                                            $rsRow2 = mysql_fetch_array($rsTemp3);

                                            $add_date = $rsRow2['created_date'];
                                            // if new report
                                            if ($isNewRpt == 1) {
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
                                                    //new
                                                    $new = 0;
                                                    //old
                                                    $old = 0;
                                                    ?>
                                                    <TR>
                                                        <TD style="text-align:center !important;" CLASS="sb1NormalFontArial"><? echo $SlNo;?></TD>
                                                        <TD CLASS="TDLCOLLASMALL" NOWRAP><span class="sb1GeenGradientBoxMiddle"><?php echo $rsRow1['itm_name']; ?></span>
                                                            <INPUT TYPE="HIDDEN" NAME="flitmrec_id[]" VALUE="<?php echo $rsRow1['itm_id']; ?>">
                                                                <INPUT TYPE="HIDDEN" NAME="flitm_category[]" VALUE="<?php echo $rsRow1['itm_category']; ?>">
                                                                    </TD>
                                                                    <TD CLASS="TextBoxSmallRA"><INPUT <?php echo (!empty($openOB)) ? 'readonly="readonly" style="background-color:#CCC;"' : ''; ?> autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" ID="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10" VALUE="<?php echo $ob_a; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                    <TD class="sb1NormalFontArial"><INPUT <?php echo $isReadOnly . $style; ?> autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDRecv<?php echo $rsRow1['itm_id']; ?>" ID="FLDRecv<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10"  VALUE="<?php echo $wh_received; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                    <TD width="13" CLASS="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="hidden" NAME="FLDIsuueRWH<?php echo $rsRow1['itm_id']; ?>" ID="FLDIsuueRWH<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10"  VALUE="0" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');">
                                                                            <INPUT autocomplete="off" NAME="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" ID="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" VALUE="<?php echo $wh_issue_up; ?>" TYPE="TEXT" CLASS="TextBoxSmallRA" SIZE="8" MAXLENGTH="10" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                                <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="hidden" NAME="FLDmyavg<?php echo $rsRow1['itm_id']; ?>" ID="FLDmyavg<?php echo $rsRow1['itm_id']; ?>" SIZE="5" MAXLENGTH="10"  VALUE="<?php ?>">
                                                                                        <INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" ID="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10" VALUE="<?php echo $wh_adja; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                                            <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" ID="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10" VALUE="<?php echo $wh_adjb; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                                            <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" ID="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10" VALUE="<?php echo $cb_a; ?>"  style="background-color:#CCC;" readonly>
                                                                                                    <INPUT TYPE="HIDDEN" NAME="FLDrecord_id<?php echo $rsRow1['itm_id']; ?>" ID="FLDrecord_id<?php echo $rsRow1['itm_id']; ?>" VALUE="<?php echo $rsRow2['w_id']; ?>"></TD>
                                                                                                        <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDnew<?php echo $rsRow1['itm_id']; ?>" ID="FLDnew<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10"  VALUE="<?php echo $new; ?>"></TD>
                                                                                                        <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDold<?php echo $rsRow1['itm_id']; ?>" ID="FLDold<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10"  VALUE="<?php echo $old; ?>"></TD>
                                                                                                        </TR>
                                                                                                        <?php
                                                                                                    } else if ($rsRow1['itm_category'] == 2) {
                                                                                                        //pk_id
                                                                                                        $surgeyArr[$rsRow1['itm_id']]['pk_id'] = $rsRow2['pk_id'];
                                                                                                        //name
                                                                                                        $surgeyArr[$rsRow1['itm_id']]['name'] = $rsRow1['itm_name'];
                                                                                                        //category
                                                                                                        $surgeyArr[$rsRow1['itm_id']]['category'] = $rsRow1['itm_category'];
                                                                                                        //cases
                                                                                                        $surgeyArr[$rsRow1['itm_id']]['cases'] = $wh_issue_up;
                                                                                                    }
                                                                                                }
                                                                                                // If one of the 3 months 
                                                                                                else {
                                                                                                    if ($rsRow1['itm_category'] == 1) {
                                                                                                        ?>
                                                                                                        <TR>
                                                                                                            <TD style="text-align:center !important;" CLASS="sb1NormalFontArial"><? echo $SlNo;?></TD>
                                                                                                            <TD CLASS="TDLCOLLASMALL" NOWRAP><span class="sb1GeenGradientBoxMiddle"><?php echo $rsRow1['itm_name']; ?></span>
                                                                                                                <INPUT TYPE="HIDDEN" NAME="flitmrec_id[]" VALUE="<?php echo $rsRow1['itm_id']; ?>">
                                                                                                                    <INPUT TYPE="HIDDEN" NAME="flitm_category[]" VALUE="<?php echo $rsRow1['itm_category']; ?>">
                                                                                                                        <?php $itemid = explode('-', $rsRow1['itm_id']); ?></TD>
                                                                                                                        <TD CLASS="TextBoxSmallRA"><INPUT <?php echo (!empty($openOB)) ? 'readonly="readonly" style="background-color:#CCC;"' : ''; ?> autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" ID="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10" VALUE="<?php echo $rsRow2['opening_balance']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                                                                        <TD class="sb1NormalFontArial"><INPUT <?php echo $isReadOnly . $style; ?> autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDRecv<?php echo $rsRow1['itm_id']; ?>" ID="FLDRecv<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10"  VALUE="<?php echo $rsRow2['received_balance']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                                                                        <TD width="13" CLASS="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="hidden" NAME="FLDIsuueRWH<?php echo $rsRow1['itm_id']; ?>" ID="FLDIsuueRWH<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10"  VALUE="0" STYLE=" BACKGROUND-COLOR='<?php echo $rsRow1[field_color]; ?>'" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');">
                                                                                                                                <INPUT autocomplete="off" NAME="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" ID="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" VALUE="<?php echo $rsRow2['issue_balance']; ?>" TYPE="TEXT" CLASS="TextBoxSmallRA" STYLE=" BACKGROUND-COLOR='<?php echo $rsRow1[field_color]; ?>'"  SIZE="8" MAXLENGTH="10" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                                                                                    <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="hidden" NAME="FLDmyavg<?php echo $rsRow1['itm_id']; ?>" ID="FLDmyavg<?php echo $rsRow1['itm_id']; ?>" SIZE="5" MAXLENGTH="10"  VALUE="<?php ?>">
                                                                                                                                            <INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" ID="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10" VALUE="<?php echo $rsRow2['adjustment_positive']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                                                                                                <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" ID="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10" VALUE="<?php echo $rsRow2['adjustment_negative']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></TD>
                                                                                                                                                <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" ID="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10" VALUE="<?php echo $rsRow2['closing_balance']; ?>"  style="background-color:#CCC;" readonly>
                                                                                                                                                        <INPUT TYPE="HIDDEN" NAME="FLDrecord_id<?php echo $rsRow1['itm_id']; ?>" ID="FLDrecord_id<?php echo $rsRow1['itm_id']; ?>" VALUE="<?php echo $rsRow2['w_id']; ?>"></TD>
                                                                                                                                                            <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDnew<?php echo $rsRow1['itm_id']; ?>" ID="FLDnew<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10"  VALUE="<?php echo $rsRow2['new']; ?>"></TD>
                                                                                                                                                            <TD class="sb1NormalFontArial"><INPUT autocomplete="off" CLASS="TextBoxSmallRA" TYPE="TEXT" NAME="FLDold<?php echo $rsRow1['itm_id']; ?>" ID="FLDold<?php echo $rsRow1['itm_id']; ?>" SIZE="8" MAXLENGTH="10"  VALUE="<?php echo $rsRow2['old']; ?>"></TD>
                                                                                                                                                            </TR>
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
                                                                                                                                                //free result//free result
                                                                                                                                                mysql_free_result($rsTemp1);
                                                                                                                                                //get user_province
                                                                                                                                                $whProvId = $_SESSION['user_province'];
                                                                                                                                                if ($wh_id == 4 || $wh_id == 5) {
                                                                                                                                                    ?>
                                                                                                                                                    <input type="hidden" name="hf_type_id" id="hf_type_id"  value="<?php echo $wh_id; ?>">
                                                                                                                                                        <tr>
                                                                                                                                                            <td colspan="5">
                                                                                                                                                                <table id="myTable" width="100%">
                                                                                                                                                                    <TR>
                                                                                                                                                                        <TD colspan="10" CLASS="sb1GeenGradientBoxMiddle" style="text-align:left;">
                                                                                                                                                                            <h4 style="font-weight:bold; font-size:15px;">Surgery Cases(Reffered)</h4>
                                                                                                                                                                        </TD>
                                                                                                                                                                    </TR>
                                                                                                                                                                    <TR>
                                                                                                                                                                        <TD CLASS="sb1GeenGradientBoxMiddle"><b>Reffered By</b></TD>
                                                                                                                                                                        <?php
                                                                                                                                                                        $counter = 0;
                                                                                                                                                                        //get data from surgeyArr
                                                                                                                                                                        foreach ($surgeyArr as $itemid => $data) {
                                                                                                                                                                            $counter++;
                                                                                                                                                                            ?>
                                                                                                                                                                            <TD CLASS="sb1GeenGradientBoxMiddle" style="text-align:center !important;"><?php echo $data['name']; ?></TD>
                                                                                                                                                                            <?php
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
												tbl_hf_type_province.province_id = " . $whProvId . " AND
												tbl_hf_type_province.stakeholder_id = 1";
                                                                                                                                                                        //result
                                                                                                                                                                        $rs_arr = mysql_query($qry);
                                                                                                                                                                        //fetch data from rs_arr
                                                                                                                                                                        while ($arr1 = mysql_fetch_array($rs_arr)) {
                                                                                                                                                                            echo "</TR>";
                                                                                                                                                                            ?>
                                                                                                                                                                            <TD CLASS="sb1GeenGradientBoxMiddle"><?php echo $arr1['hf_type']; ?></TD>
                                                                                                                                                                            <INPUT TYPE="HIDDEN" NAME="hf_type_id[]" VALUE="<?php echo $arr1['pk_id']; ?>">
                                                                                                                                                                                <?php
                                                                                                                                                                                //fetch data from surgeyArr
                                                                                                                                                                                foreach ($surgeyArr as $itemid => $data) {
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
                                                                                                                                                                                    //hf_data_id,
                                                                                                                                                                                    //hf_type_id,
                                                                                                                                                                                    //ref_surgeries,
                                                                                                                                                                                    //static,
                                                                                                                                                                                    //camp
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
                                                                                                                                                                                    //query result
                                                                                                                                                                                    $mcRow = mysql_fetch_array(mysql_query($mcQry));
                                                                                                                                                                                    ?>

                                                                                                                                                                                    <TD CLASS="sb1NormalFontArial">

                                                                                                                                                                                        <INPUT TYPE="TEXT" CLASS="TextBoxSmallRA reffered<?php echo $itemid; ?>"   name="reffered<?php echo $itemid; ?><?php echo $arr1['pk_id']; ?>" value="<?php echo $mcRow['ref_surgeries']; ?>"   SIZE="8" MAXLENGTH="10" />
                                                                                                                                                                                    </TD>

                                                                                                                                                                                <?php } ?>
                                                                                                                                                                        </TR>

                                                                                                                                                                        <?php
                                                                                                                                                                    }
                                                                                                                                                                    ?>
                                                                                                                                                                    <TR>
                                                                                                                                                                        <TD CLASS="sb1GeenGradientBoxMiddle">Gross Total /Net Total</TD>
                                                                                                                                                                        <?php
                                                                                                                                                                        //fetch data from surgeyArr
                                                                                                                                                                        foreach ($surgeyArr as $itemid => $data) {
                                                                                                                                                                            ?>
                                                                                                                                                                            <INPUT TYPE="HIDDEN" NAME="flitmrec_id[]" VALUE="<?php echo $itemid; ?>">
                                                                                                                                                                                <INPUT TYPE="HIDDEN" NAME="flitm_category[]" VALUE="<?php echo $data['category']; ?>">
                                                                                                                                                                                    <TD CLASS="sb1NormalFontArial">
                                                                                                                                                                                        <INPUT TYPE="TEXT" CLASS="TextBoxSmallRA" style="background-color:#CCC;"   NAME="FLDIsuueUP<?php echo $itemid; ?>" ID="FLDIsuueUP<?php echo $itemid; ?>" VALUE="<?php echo $data['cases']; ?>" SIZE="8" MAXLENGTH="10" readonly />
                                                                                                                                                                                    </TD>
                                                                                                                                                                                <?php } ?>
                                                                                                                                                                                </TR>
                                                                                                                                                                                </table>
                                                                                                                                                                                </td>
                                                                                                                                                                                <td colspan="5" valign="top">
                                                                                                                                                                                    <table id="myTable" width="100%">
                                                                                                                                                                                        <TR>
                                                                                                                                                                                            <TD colspan="10" CLASS="sb1GeenGradientBoxMiddle" style="text-align:left;">
                                                                                                                                                                                                <h4 style="font-weight:bold; font-size:15px;">Surgery Cases(Performed)</h4>
                                                                                                                                                                                            </TD>
                                                                                                                                                                                        </TR>
                                                                                                                                                                                        <TR>
                                                                                                                                                                                            <TD CLASS="sb1GeenGradientBoxMiddle">&nbsp;</TD>
                                                                                                                                                                                            <?php
                                                                                                                                                                                            $counter = 0;
                                                                                                                                                                                            //fetch data from surgeyArr
                                                                                                                                                                                            foreach ($surgeyArr as $itemid => $data) {
                                                                                                                                                                                                $counter++;
                                                                                                                                                                                                ?>
                                                                                                                                                                                                <TD CLASS="sb1GeenGradientBoxMiddle" style="text-align:center !important;"><?php echo $data['name']; ?></TD>
                                                                                                                                                                                                <?php
                                                                                                                                                                                            }

                                                                                                                                                                                            $arr = array('Static Center', 'Camp Cases');
                                                                                                                                                                                            $counter = 1;
                                                                                                                                                                                            foreach ($arr as $val) {
                                                                                                                                                                                                echo "</TR>";
                                                                                                                                                                                                ?>
                                                                                                                                                                                                <TD CLASS="sb1GeenGradientBoxMiddle"><?php echo $val; ?></TD>

                                                                                                                                                                                                <?php
                                                                                                                                                                                                //getting data from surgeyArr
                                                                                                                                                                                                foreach ($surgeyArr as $itemid => $data) {
                                                                                                                                                                                                    if ($isNewRpt == 1) {
                                                                                                                                                                                                        $hf_data_pk = 0;
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        $hf_data_pk = $data['pk_id'];
                                                                                                                                                                                                    }
                                                                                                                                                                                                    //query
                                                                                                                                                                                                    //gets
                                                                                                                                                                                                    //male_static,
                                                                                                                                                                                                    //male_camp
                                                                                                                                                                                                    $mcQry1 = "SELECT
																tbl_hf_data_reffered_by.static as male_static,
																tbl_hf_data_reffered_by.camp as male_camp
															FROM
																tbl_hf_data_reffered_by
															WHERE
																tbl_hf_data_reffered_by.hf_data_id = $hf_data_pk ";
                                                                                                                                                                                                    //result
                                                                                                                                                                                                    $mcRow1 = mysql_fetch_array(mysql_query($mcQry1));
                                                                                                                                                                                                    //mcQry2
                                                                                                                                                                                                    //gets
                                                                                                                                                                                                    //female_static,
                                                                                                                                                                                                    //female_camp
                                                                                                                                                                                                    $mcQry2 = "SELECT
																tbl_hf_data_reffered_by.static as female_static,
																tbl_hf_data_reffered_by.camp as female_camp
															FROM
																tbl_hf_data_reffered_by
															WHERE
																tbl_hf_data_reffered_by.hf_data_id = $hf_data_pk ";
                                                                                                                                                                                                    //result
                                                                                                                                                                                                    $mcRow2 = mysql_fetch_array(mysql_query($mcQry2));

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
                                                                                                                                                                                                    <TD CLASS="sb1NormalFontArial">
                                                                                                                                                                                                        <INPUT TYPE="TEXT" CLASS="TextBoxSmallRA totalStaticCampMale<?php echo $itemid; ?>" name="staticCamp<?php echo $itemid; ?>[]" value="<?php echo $static_camp; ?>" SIZE="8" MAXLENGTH="10" />
                                                                                                                                                                                                    </TD>
                                                                                                                                                                                                    <?php
                                                                                                                                                                                                }
                                                                                                                                                                                                ?>
                                                                                                                                                                                            </TR>
                                                                                                                                                                                            <?php
                                                                                                                                                                                            $counter++;
                                                                                                                                                                                        }
                                                                                                                                                                                        ?>
                                                                                                                                                                                        <TR>
                                                                                                                                                                                            <TD CLASS="sb1GeenGradientBoxMiddle">Gross Total /Net Total</TD>
                                                                                                                                                                                            <TD CLASS="sb1NormalFontArial">
                                                                                                                                                                                                <INPUT TYPE="TEXT" style="background-color:#CCC;" CLASS="TextBoxSmallRA" id="totalStaticCampMale" value="<?php echo $total_male; ?>" SIZE="8" MAXLENGTH="10" readonly />
                                                                                                                                                                                            </TD>
                                                                                                                                                                                            <TD CLASS="sb1NormalFontArial">
                                                                                                                                                                                                <INPUT TYPE="TEXT" style="background-color:#CCC;" CLASS="TextBoxSmallRA" id="totalStaticCampFemale" value="<?php echo $total_female; ?>"  SIZE="8" MAXLENGTH="10" readonly />
                                                                                                                                                                                            </TD>
                                                                                                                                                                                        </TR>
                                                                                                                                                                                    </table>
                                                                                                                                                                                </td>
                                                                                                                                                                                </tr>
                                                                                                                                                                                <?php
                                                                                                                                                                            } else {
                                                                                                                                                                                ?>
                                                                                                                                                                                <TR>
                                                                                                                                                                                    <TD colspan="10" CLASS="sb1GeenGradientBoxMiddle" style="text-align:left;">
                                                                                                                                                                                        <h4 style="font-weight:bold; font-size:15px;">Surgery Cases (Referral)</h4>
                                                                                                                                                                                    </TD>
                                                                                                                                                                                </TR>
                                                                                                                                                                                <TR>
                                                                                                                                                                                    <?php
                                                                                                                                                                                    $counter = 0;
                                                                                                                                                                                    //getting data from surgeyArr
                                                                                                                                                                                    foreach ($surgeyArr as $itemid => $data) {
                                                                                                                                                                                        $counter++;
                                                                                                                                                                                        ?>
                                                                                                                                                                                        <TD CLASS="sb1GeenGradientBoxMiddle" align="center"><?php echo $data['name']; ?></TD>
                                                                                                                                                                                        <?php
                                                                                                                                                                                    }
                                                                                                                                                                                    echo "<TD colspan=" . (10 - $counter) . "></TD></TR><TR>";
                                                                                                                                                                                    $counter = 0;
                                                                                                                                                                                    //getting data from surgeyArr
                                                                                                                                                                                    foreach ($surgeyArr as $itemid => $data) {
                                                                                                                                                                                        $counter++;
                                                                                                                                                                                        ?>
                                                                                                                                                                                        <TD CLASS="sb1NormalFontArial">
                                                                                                                                                                                            <INPUT TYPE="HIDDEN" NAME="flitmrec_id[]" VALUE="<?php echo $itemid; ?>">
                                                                                                                                                                                                <INPUT TYPE="HIDDEN" NAME="flitm_category[]" VALUE="<?php echo $data['category']; ?>">
                                                                                                                                                                                                    <INPUT autocomplete="off" NAME="FLDIsuueUP<?php echo $itemid; ?>" ID="FLDIsuueUP<?php echo $itemid; ?>" VALUE="<?php echo $data['cases']; ?>" TYPE="TEXT" CLASS="TextBoxSmallRA" SIZE="8" MAXLENGTH="10" />
                                                                                                                                                                                                    </TD>
                                                                                                                                                                                                    <?php
                                                                                                                                                                                                }
                                                                                                                                                                                                echo "<TD colspan=" . (10 - $counter) . "></TD>";
                                                                                                                                                                                            }
                                                                                                                                                                                            //check whProvId
                                                                                                                                                                                            if ($whProvId == 1 || $whProvId == 2) {
                                                                                                                                                                                                $newText = 'New';
                                                                                                                                                                                                $oldText = 'Old';
                                                                                                                                                                                                $childText = 'Children';
                                                                                                                                                                                            } else {
                                                                                                                                                                                                $newText = 'Children';
                                                                                                                                                                                                $oldText = 'Adults';
                                                                                                                                                                                                $childText = 'General Ailment';
                                                                                                                                                                                            }
                                                                                                                                                                                            ?>
                                                                                                                                                                                            </TR>
                                                                                                                                                                                            <TR>
                                                                                                                                                                                                <TD colspan="10" CLASS="sb1GeenGradientBoxMiddle" style="text-align:left;">
                                                                                                                                                                                                    <h4 style="font-weight:bold; font-size:15px;">Mother & Child Care (No. of Cases)</h4>
                                                                                                                                                                                                </TD>
                                                                                                                                                                                            </TR>
                                                                                                                                                                                            <TR>
                                                                                                                                                                                                <TD CLASS="sb1GeenGradientBoxMiddle" align="center" colspan="2">Ante-natal</TD>
                                                                                                                                                                                                <TD CLASS="sb1GeenGradientBoxMiddle" align="center" colspan="2">Post-natal</TD>
                                                                                                                                                                                                <TD CLASS="sb1GeenGradientBoxMiddle" align="center" colspan="2"><?php echo $childText; ?></TD>
                                                                                                                                                                                                <?php 
                                                                                                                                                                                                //check whProvId
                                                                                                                                                                                                if ($whProvId == 1 || $whProvId == 2) { ?>
                                                                                                                                                                                                    <TD CLASS="sb1GeenGradientBoxMiddle" align="center" colspan="2" rowspan="2">General Ailment</TD>
                                                                                                                                                                                                <?php } ?>
                                                                                                                                                                                            </TR>
                                                                                                                                                                                            <TR>
                                                                                                                                                                                                <TD CLASS="sb1FormLabel" align="center">New</TD>
                                                                                                                                                                                                <TD CLASS="sb1FormLabel" align="center">Old</TD>
                                                                                                                                                                                                <TD CLASS="sb1FormLabel" align="center">New</TD>
                                                                                                                                                                                                <TD CLASS="sb1FormLabel" align="center">Old</TD>
                                                                                                                                                                                                <TD CLASS="sb1FormLabel" align="center"><?php echo $newText; ?></TD>
                                                                                                                                                                                                <TD CLASS="sb1FormLabel" align="center"><?php echo $oldText; ?></TD>
                                                                                                                                                                                            </TR>
                                                                                                                                                                                            <TR>
                                                                                                                                                                                                <?php
                                                                                                                                                                                                //mcQry
                                                                                                                                                                                                //gets
                                                                                                                                                                                                //facility_type_id,
                                                                                                                                                                                                //district_id,
                                                                                                                                                                                                //pre_natal_new,
                                                                                                                                                                                                //pre_natal_old,
                                                                                                                                                                                                //post_natal_new,
                                                                                                                                                                                                //post_natal_old,
                                                                                                                                                                                                //ailment_children,
                                                                                                                                                                                                //ailment_adults,
                                                                                                                                                                                                //general_ailment,
                                                                                                                                                                                                //reporting_date
                                                                                                                                                                                                $mcQry = "SELECT
											tbl_hf_type_mother_care.facility_type_id,
											tbl_hf_type_mother_care.district_id,
											tbl_hf_type_mother_care.pre_natal_new,
											tbl_hf_type_mother_care.pre_natal_old,
											tbl_hf_type_mother_care.post_natal_new,
											tbl_hf_type_mother_care.post_natal_old,
											tbl_hf_type_mother_care.ailment_children,
											tbl_hf_type_mother_care.ailment_adults,
											tbl_hf_type_mother_care.general_ailment,
											tbl_hf_type_mother_care.reporting_date
										FROM
											tbl_hf_type_mother_care
										WHERE
											tbl_hf_type_mother_care.facility_type_id = $wh_id
										AND tbl_hf_type_mother_care.district_id = $districtId
										AND tbl_hf_type_mother_care.reporting_date = '$RptDate'";
                                                                                                                                                                                                //query result
                                                                                                                                                                                                $mcRow = mysql_fetch_array(mysql_query($mcQry));
                                                                                                                                                                                                ?>
                                                                                                                                                                                                <TD CLASS="TDLCOLRASMALLW">
                                                                                                                                                                                                    <input type="text" class="TextBoxSmallRA" name="pre_natal_new" id="pre_natal_new" autocomplete="off" value="<?php echo $mcRow['pre_natal_new']; ?>" />
                                                                                                                                                                                                </TD>
                                                                                                                                                                                                <TD CLASS="TDLCOLRASMALLW">
                                                                                                                                                                                                    <input type="text" class="TextBoxSmallRA" name="pre_natal_old" id="pre_natal_old" autocomplete="off" value="<?php echo $mcRow['pre_natal_old']; ?>" />
                                                                                                                                                                                                </TD>
                                                                                                                                                                                                <TD CLASS="TDLCOLRASMALLW">
                                                                                                                                                                                                    <input type="text" class="TextBoxSmallRA" name="post_natal_new" id="post_natal_new" autocomplete="off" value="<?php echo $mcRow['post_natal_new']; ?>" />
                                                                                                                                                                                                </TD>
                                                                                                                                                                                                <TD CLASS="TDLCOLRASMALLW">
                                                                                                                                                                                                    <input type="text" class="TextBoxSmallRA" name="post_natal_old" id="post_natal_old" autocomplete="off" value="<?php echo $mcRow['post_natal_old']; ?>" />
                                                                                                                                                                                                </TD>
                                                                                                                                                                                                <TD CLASS="TDLCOLRASMALLW">
                                                                                                                                                                                                    <input type="text" class="TextBoxSmallRA" name="ailment_child" id="ailment_child" autocomplete="off" value="<?php echo $mcRow['ailment_children']; ?>" />
                                                                                                                                                                                                </TD>
                                                                                                                                                                                                <TD CLASS="TDLCOLRASMALLW">
                                                                                                                                                                                                    <input type="text" class="TextBoxSmallRA" name="ailment_adult" id="ailment_adult" autocomplete="off" value="<?php echo $mcRow['ailment_adults']; ?>" />
                                                                                                                                                                                                </TD>
                                                                                                                                                                                                <?php if ($whProvId == 1 || $whProvId == 2) { ?>
                                                                                                                                                                                                    <TD CLASS="TDLCOLRASMALLW" colspan="2">
                                                                                                                                                                                                        <input type="text" class="TextBoxSmallRA" name="general_ailment" id="general_ailment" autocomplete="off" value="<?php echo $mcRow['general_ailment']; ?>" />
                                                                                                                                                                                                    </TD>
                                                                                                                                                                                                <?php } ?>
                                                                                                                                                                                            </TR>
                                                                                                                                                                                            <TR>
                                                                                                                                                                                                <TD colspan="10" align="right" CLASS="TDLCOLRASMALLW">
                                                                                                                                                                                                    <IMG SRC="<?php echo PLMIS_IMG; ?>CmdReset.gif" WIDTH="83" HEIGHT="21" BORDER="0" ALT="Reset" ONCLICK="document.frmF7.reset()" CLASS="Himg" style=" cursor:pointer">
                                                                                                                                                                                                        <INPUT TYPE="image" name="saveBtn" id="saveBtn" SRC="<?php echo PLMIS_IMG; ?>CmdSave.gif">
                                                                                                                                                                                                        </TD>
                                                                                                                                                                                                        </TR>
                                                                                                                                                                                                        </TABLE>
                                                                                                                                                                                                        <INPUT TYPE="hidden" NAME="ActionType" VALUE="Add">
                                                                                                                                                                                                        <INPUT TYPE="HIDDEN" NAME="RptDate" VALUE="<?php echo $RptDate; ?>">
                                                                                                                                                                                                        <INPUT TYPE="HIDDEN" NAME="wh_id" VALUE="<?php echo $wh_id; ?>">
                                                                                                                                                                                                        <INPUT TYPE="HIDDEN" NAME="yy" VALUE="<?php echo $yy; ?>">
                                                                                                                                                                                                        <INPUT TYPE="HIDDEN" NAME="mm" VALUE="<?php echo $mm; ?>">
                                                                                                                                                                                                        <input type="hidden" name="mystake" id="mystake" />
                                                                                                                                                                                                        <input type="hidden" name="Stake" id="Stake" value="<?php
                                                                                                                                                                                                                                //check Stk
                                                                                                                                                                                                                                if ($Stk != '') {
                                                                                                                                                                                                                                    echo $Stk;
                                                                                                                                                                                                                                } else {
                                                                                                                                                                                                                                    //get stakeHolder
                                                                                                                                                                                                                                    echo $_GET['stakeHolder'];
                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                ?>">
                                                                                                                                                                                                        <input type="hidden" name="sysusr_type" id="sysusr_type" value="<?php echo $_GET['sysusr_type']; ?>">
                                                                                                                                                                                                        <input type="hidden" name="cws1" id="hiddenField2" value="<?php echo $_GET['cws1']; ?>">
                                                                                                                                                                                                        <input type="hidden" name="isNewRpt" id="isNewRpt" value="<?php echo $isNewRpt; ?>" />
                                                                                                                                                                                                        <input type="hidden" name="add_date" id="add_date" value="<?php echo $add_date; ?>" />
                                                                                                                                                                                                        </FORM>
                                                                                                                                                                                                        <? 
                                                                                                                                                                                                        }

                                                                                                                                                                                                        ?>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                        <td colspan="8"><div id="eMsg" style="color:#060;"></div></td>
                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                        </table>
                                                                                                                                                                                                        </td>
                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                        </table>
                                                                                                                                                                                                        <script>
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
                                                                                                                                                                                                        success: function() {
                                                                                                                                                                                                        $("#eMsg").fadeTo(500, 1, function() {
                                                                                                                                                                                                        $(this).show();
                                                                                                                                                                                                        $(this).html('Your data is saved is draft.').fadeTo(3000, 0, function() {
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
                                                                                                                                                                                                        var itmLength = $("input[name^='flitmrec_id']").length;
                                                                                                                                                                                                        var itmArr = $("input[name^='flitmrec_id']");
                                                                                                                                                                                                        var itmCategory = $("input[name^='flitm_category']");
                                                                                                                                                                                                        var FLDOBLAArr = $("input[name^='FLDOBLA']");
                                                                                                                                                                                                        var FLDRecvArr = $("input[name^='FLDRecv']");
                                                                                                                                                                                                        var FLDIsuueUPArr = $("input[name^='FLDIsuueUP']");
                                                                                                                                                                                                        var FLDCBLAArr = $("input[name^='FLDCBLA']");
                                                                                                                                                                                                        var FLDReturnToArr = $("input[name^='FLDReturnTo']");
                                                                                                                                                                                                        var FLDUnusableArr = $("input[name^='FLDUnusable']");
                                                                                                                                                                                                        /*
                                                                                                                                                                                                        var fieldval = document.frmaddF7.itm_id[i].value;
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
                                                                                                                                                                                                        var itmInfo = itm.split('-');
                                                                                                                                                                                                        itmId = itmInfo[1];
                                                                                                                                                                                                        var FLDOBLA = parseInt(FLDOBLAArr.eq(i).val());
                                                                                                                                                                                                        var FLDRecv = parseInt(FLDRecvArr.eq(i).val());
                                                                                                                                                                                                        var FLDIsuueUP = parseInt(FLDIsuueUPArr.eq(i).val());
                                                                                                                                                                                                        var FLDCBLA = parseInt(FLDCBLAArr.eq(i).val());
                                                                                                                                                                                                        var FLDReturnTo = parseInt(FLDReturnToArr.eq(i).val());
                                                                                                                                                                                                        var FLDUnusable = parseInt(FLDUnusableArr.eq(i).val());



                                                                                                                                                                                                        if (FLDIsuueUP > (FLDOBLA + FLDRecv + FLDReturnTo))
                                                                                                                                                                                                        {
                                                                                                                                                                                                        alert('Issue can not be greater than Opening Balance + Received + Adjustments(+)');
                                                                                                                                                                                                        FLDOBLAArr.eq(i).css('background', '#F45B5C');
                                                                                                                                                                                                        FLDRecvArr.eq(i).css('background', '#F45B5C');
                                                                                                                                                                                                        FLDIsuueUPArr.eq(i).css('background', '#F45B5C');
                                                                                                                                                                                                        FLDIsuueUPArr.eq(i).focus();
                                                                                                                                                                                                        return false;
                                                                                                                                                                                                        }
                                                                                                                                                                                                        var balance = (FLDOBLA + FLDRecv + FLDReturnTo) - (FLDIsuueUP + FLDUnusable);

                                                                                                                                                                                                        if (balance != FLDCBLA)
                                                                                                                                                                                                        {
                                                                                                                                                                                                        alert('There is an error in the data. Please correct the data.');
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
                                                                                                                                                                                                        $('#saveBtn').attr('disabled', true);
                                                                                                                                                                                                        }
                                                                                                                                                                                                        </script>
                                                                                                                                                                                                        <style>
                                                                                                                                                                                                        .sb1NormalFontArial{ text-align:right !important;}
                                                                                                                                                                                                        table#myTable tr td{padding:3px; text-align:left; border:1px solid #999;}
                                                                                                                                                                                                        </style>
                                                                                                                                                                                                        </body>
                                                                                                                                                                                                        </html>