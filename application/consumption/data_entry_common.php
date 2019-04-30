<?php
/**
 * data_entry_common
 * @package consumption
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
if ($src_count > 0) {
    $src_count += 1;
    $rowcolspan = "colspan=$src_count";
} else {
    $rowcolspan = "rowspan=2";
}

 if ($isNewRpt == 1) {
                        
    $qry4 = "SELECT
                    stock_batch.wh_id,
                    stock_batch.batch_id,
                    stock_batch.batch_no,
                    stock_batch.`status`,
                    Sum(tbl_stock_detail.Qty) as Qty,
                    tbl_stock_detail.IsReceived,
                    tbl_stock_master.TranDate,
                    stock_batch.item_id
                FROM
                stock_batch
                INNER JOIN tbl_stock_detail ON tbl_stock_detail.BatchID = stock_batch.batch_id
                INNER JOIN tbl_stock_master ON tbl_stock_detail.fkStockID = tbl_stock_master.PkStockID
                WHERE
                    stock_batch.wh_id = $wh_id AND
                    tbl_stock_detail.IsReceived = 1 AND
                      DATE_FORMAT(tbl_stock_master.TranDate,'%Y-%m') = '".(date('Y-m',strtotime($RptDate)))."'
                GROUP BY
                        stock_batch.item_id
                ";
        //result
        //echo $qry4;exit;
        $rsTemp4 = mysql_query($qry4);
        $rcvd_array = array();
        while ($rsRow4 = mysql_fetch_array($rsTemp4)) {
            $rcvd_array[$rsRow4['item_id']] = $rsRow4['Qty'];
        }
 }
// echo '<pre>';print_r($rcvd_array);exit;
?>
<form name="frmF7" id="frmF7" method="post">
    <div class="row">
        <div class="col-md-12">
            <div id="errMsg"></div>
            <table width="100%" align="center" class="table table-bordered">
                <tr>
                    <th rowspan="2" class="text-center">S.No.</th>
                    <th rowspan="2" class="text-center">Article</th>
                    <th rowspan="2" class="text-center">Opening balance</th>
                    <th <?php echo $rowcolspan; ?> class="text-center">Received</th>                    
                    <th rowspan="2" class="text-center">Issued</th>
                    <th colspan="2" class="text-center">Adjustments</th>
                    <th rowspan="2" class="text-center">Closing Balance</th>
                </tr>
                <tr>
                    <?php foreach ($sources as $rowsrc) { ?>
                        <th class="text-center"><?php echo $rowsrc; ?></th>
                    <?php } if ($src_count > 0) { ?>
                        <th class="text-center">Total</th>
                    <?php } ?>
                    <th class="text-center">(+)</th>
                    <th class="text-center">(-)</th>
                </tr>
                <?php
                $province_id_session = $_SESSION['user_province1'];
                if($province_id_session == 3){
                    $item_category_id = "1,5";
                } else {
                    $item_category_id = "1";
                }
                //query
                //gets
                //all from itminfo_tab
                $rsTemp1 = mysql_query("SELECT * FROM `itminfo_tab` WHERE `itm_status`=1 AND `itm_id` IN (SELECT `Stk_item` FROM `stakeholder_item` WHERE `stkid` =$stkid) AND itminfo_tab.itm_category IN ($item_category_id) ORDER BY itm_category,`frmindex`,itm_name ");
                $SlNo = 1;
                $fldIndex = 0;
                //loop
                while ($rsRow1 = mysql_fetch_array($rsTemp1)) {
//                    $item_char = $rsRow1['itm_id'];
//                    $qry_cf = "SELECT REPUpdateCarryForwardHF('$RptDate','$item_char', $wh_id) FROM DUAL";
//                    mysql_query($qry_cf);

                    //$SlNo = ((strlen($SlNo) < 2) ? $SlNo : $SlNo);
                    //query 
                    //gets
                    //all from tbl_hf_data
                    $qry = "SELECT * FROM tbl_hf_data WHERE `warehouse_id`='" . $wh_id . "' AND reporting_date='" . $PrevMonthDate . "' AND `item_id`='$rsRow1[itm_id]'";
                    //result
                    $rsTemp3 = mysql_query($qry);
                    $rsRow2 = mysql_fetch_array($rsTemp3);
                    
                    $add_date = $rsRow2['created_date'];
                    
                    ///// Code for Receive column bifurcation
                    $hf_data_id = $rsRow2['pk_id'];
                    $qryd = "SELECT
	stock_sources_data.stock_sources_id,
	stock_sources_data.received,
	tbl_hf_data.item_id
FROM
	stock_sources_data
INNER JOIN tbl_hf_data ON stock_sources_data.hf_data_id = tbl_hf_data.pk_id
WHERE
	stock_sources_data.hf_data_id = $hf_data_id";
                    //result
                    //echo $qryd;exit;
                    $rsTemp4 = mysql_query($qryd);
                    $sources_data = array();
                    if(!empty($hf_data_id))
                    while($rsRow4 = mysql_fetch_array($rsTemp4)){
                        $sources_data[$rsRow4['stock_sources_id']][$rsRow4['item_id']] = $rsRow4['received'];
                    }
                    
                    ///// End of Code for Receive column bifurcation

                    // if new report
                    if ($isNewRpt == 1) {
                        $wh_issue_up = 0;
                        $wh_adja = 0;
                        $wh_adjb = 0;
                        //$wh_received = 0;
                        $wh_received = ((!empty($rcvd_array[$rsRow1['itm_id']])) ? $rcvd_array[$rsRow1['itm_id']] : '0');
                        //ob_a
                        $ob_a = $rsRow2['closing_balance'];
                        //cb_a
                        $cb_a = $rsRow2['closing_balance'];
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $SlNo; ?></td>
                            <td><?php echo $rsRow1['itm_name']; ?>
                                <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itm_id']; ?>">
                                <input type="hidden" name="flitm_category[]" value="<?php echo $rsRow1['itm_category']; ?>">
                                <input type="hidden" name="flitmname<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow1['itm_name']; ?>"></td>
                            <td><input class="form-control input-sm text-right" <?php echo (!empty($openOB)) ? 'readonly="readonly"' : ''; ?> autocomplete="off"  type="text" name="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $ob_a; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <?php foreach ($sources as $key=>$rowsrc) { 
                                $sdf=0;
                                ?>
                                <td><input class="form-control input-sm text-right FLDSrcs<?php echo $rsRow1['itm_id']; ?>" 
                                            <?php 
                                                if($key==130 || $key==201) 
                                                { 
                                                    echo $isReadOnly . $style; 
                                                    $sdf=$wh_received;
                                                    
                                                    
                                                } 
                                            ?> 
                                           autocomplete="off"  type="text" name="FLDSrcs<?php echo $rsRow1['itm_id']; ?>[<?php echo $key; ?>]" id="<?php echo $rowsrc.$rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $sdf; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <?php } 
                                $cb_a+=$wh_received;
                            ?>
                                <td><input class="form-control input-sm text-right" <?=(($src_count>0 || $readonly_for_im)?'readonly':'')?> autocomplete="off"  type="text" name="FLDRecv<?php echo $rsRow1['itm_id']; ?>" id="FLDRecv<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $wh_received; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" id="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $wh_issue_up; ?>" type="text"  size="8" maxlength="10" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" id="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $wh_adja; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" id="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $wh_adjb; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" type="text" name="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $cb_a; ?>" readonly="readonly"></td>
                        </tr>
                        <?php
                        //isNewRpt == 0
                    } else if ($isNewRpt == 0) {
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $SlNo; ?></td>
                            <td><?php echo $rsRow1['itm_name']; ?>
                                <input type="hidden" name="flitmrec_id[]" value="<?php echo $rsRow1['itm_id']; ?>">
                                <input type="hidden" name="flitm_category[]" value="<?php echo $rsRow1['itm_category']; ?>">
                                <input type="hidden" name="flitmname<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow1['itm_name']; ?>"></td>
                            <td><input class="form-control input-sm text-right" <?php echo (!empty($openOB)) ? 'readonly="readonly"' : ''; ?> autocomplete="off"  type="text" name="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDOBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['opening_balance']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <?php foreach ($sources as $key=>$rowsrc) {
                                $this_value = (!empty($sources_data[$key][$rsRow1['itm_id']])?$sources_data[$key][$rsRow1['itm_id']]:'0');
                                ?>
                                <td><input class="form-control input-sm text-right FLDSrcs<?php echo $rsRow1['itm_id']; ?>" <?php if($key==130) { echo $isReadOnly . $style; } ?> autocomplete="off"  type="text" name="FLDSrcs<?php echo $rsRow1['itm_id']; ?>[<?php echo $key; ?>]" id="<?php echo $rowsrc.$rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $this_value; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <?php } ?>
                                <td><input class="form-control input-sm text-right"  <?=(($src_count>0 || $readonly_for_im)?'readonly':'')?>  autocomplete="off"  type="text" name="FLDRecv<?php echo $rsRow1['itm_id']; ?>" id="FLDRecv<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10"  value="<?php echo $rsRow2['received_balance']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off" name="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" id="FLDIsuueUP<?php echo $rsRow1['itm_id']; ?>" value="<?php echo $rsRow2['issue_balance']; ?>" type="text" size="8" maxlength="10" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" id="FLDReturnTo<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['adjustment_positive']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" id="FLDUnusable<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['adjustment_negative']; ?>" onKeyUp="cal_balance('<?php echo $rsRow1['itm_id']; ?>');"></td>
                            <td><input class="form-control input-sm text-right" autocomplete="off"  type="text" name="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" id="FLDCBLA<?php echo $rsRow1['itm_id']; ?>" size="8" maxlength="10" value="<?php echo $rsRow2['closing_balance']; ?>" readonly="readonly"></td>
                        </tr>
                        <?php
                        //isNewRpt == 2
                    } else if ($isNewRpt == 2) {
                        ?>
                        <tr>
                            <td class="text-center"><?php echo $SlNo; ?></td>
                            <td><?php echo $rsRow1['itm_name']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['opening_balance']; ?></td>
                            <?php foreach ($sources as $key=>$rowsrc) { ?>
                                <td class="text-right"><?php echo $sources_data[$key][$rsRow1['itm_id']]; ?></td>
                            <?php } ?>
                            <td class="text-right"><?php echo $rsRow2['received_balance']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['issue_balance']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['adjustment_positive']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['adjustment_negative']; ?></td>
                            <td class="text-right"><?php echo $rsRow2['closing_balance']; ?></td>
                        </tr>
                        <?php
                    }
                    $SlNo++;
                    $fldIndex = $fldIndex + 13;
                }
//free result
                mysql_free_result($rsTemp1);
                ?>

            </table>
            <br>
        </div>
    </div>
    <?php
    //isNewRpt != 2
    if ($isNewRpt != 2) {
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-10 text-right" style="padding-top: 10px">
                    <div id="eMsg" style="color:#060;"></div>
                </div>
                <div class="col-md-2 text-right">
                    <button class="btn btn-primary" id="saveBtn" name="saveBtn" type="button" onclick="return formvalidate1()"> Save </button>
                    <button class="btn btn-info" type="submit" onclick="document.frmF7.reset()"> Reset </button>
                </div>
            </div>
        </div>
        <?php
    }
//Hidden
    ?>

    <input type="hidden" name="ActionType" value="Add">
    <input type="hidden" name="RptDate" value="<?php echo $RptDate; ?>">
    <input type="hidden" name="wh_id" value="<?php echo $wh_id; ?>">
    <input type="hidden" name="yy" value="<?php echo $yy; ?>">
    <input type="hidden" name="mm" value="<?php echo $mm; ?>">
    <input type="hidden" name="isNewRpt" id="isNewRpt" value="<?php echo $isNewRpt; ?>" />
    <input type="hidden" name="add_date" id="add_date" value="<?php echo $add_date; ?>" />
    <input type="hidden" name="redir_url" id="redir_url" value="<?php echo (isset($redirectURL)) ? $redirectURL : ''; ?>" />
</form>
