<?php
ini_set('max_execution_time', 0);
include_once("../includes/classes/AllClasses.php");
//include_once(PUBLIC_PATH . "html/header.php");
 
        function generate_stock_table($date,$districtId,$stakeholder,$itm_id,$selProv)
        {
            
            $display="" ;
                    $mos_required = 3;
            $html = '';
            $qry = "SELECT
                        tbl_hf_data.avg_consumption,
                        tbl_hf_data.closing_balance,
                        round(tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption,2) AS mos,
                        tbl_warehouse.wh_name,
                        tbl_hf_data.reporting_date,
                        tbl_warehouse.wh_id
                    FROM
                        tbl_hf_data
                    INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                    WHERE
                        tbl_warehouse.dist_id = $districtId AND
                        tbl_hf_data.reporting_date = '".$date."' AND
                        tbl_hf_data.item_id = $itm_id AND
                        tbl_warehouse.stkid = $stakeholder
                       AND tbl_warehouse.hf_type_id NOT IN ( 3, 9, 6, 7, 8, 12, 10, 11)
                    order by mos desc
                    ";

//                    print_r($qry);exit;
                $qryRes = mysql_query($qry);

                $data_arr = $reverse_arr  = $req_arr_original = $tr_arr =  array();
                while ($row = mysql_fetch_assoc($qryRes)) {

                    $data_arr[$row['wh_id']]=$row;
                    $transferrable=0;
                    if($row['mos'] > $mos_required)
                    {
                        $per_month = $row['closing_balance'] / $row['mos'];
                        $extra_months = $row['mos'] - $mos_required;
                        $transferrable = $extra_months * $per_month;
                    }
                    $tr_arr[$row['wh_id']] = $transferrable;
                    arsort($tr_arr);

                    if(!empty($row['avg_consumption']) && $row['mos'] >= 0 && $row['mos'] <= 0.5 ){
                        $reverse_arr[$row['wh_id']] = $row ;

                        $req = ($mos_required * $row['avg_consumption'])-$row['closing_balance'];
                        $req_arr_original[$row['wh_id']] = $req;
                        asort($req_arr_original);
                    }
                }

                $req_arr = $req_arr_original;
                $req_arr = array_map("round", $req_arr);
                $tr_arr = array_map("round", $tr_arr);

                $available_arr = $tr_arr;
                $from_to_arr = array();
                foreach($req_arr as $wh_to=>$required)
                {
                    foreach($available_arr as $wh_from=>$available)
                    {
                        if($required <= $available)
                        {
                            $used = $required;
                        }
                        else{
                            $used = $available;
                        }

                        $used = abs($used);
                        $remaining_req = $required - $used;
                        $remaining_av  = $available - $used;
                        $from_to_arr[$wh_from][$wh_to] = $used;
//                            echo '<br/>------From:'.$wh_from.' - To:'.$wh_to;
//                            echo '<br/>Req:'.$required;
//                            echo '<br/>Av:'.$available;
//                            echo '<br/>used:'.$used;
//                            echo '<br/>Remaining required:'.$remaining_req;
//                            echo '<br/>remaining available:'.$remaining_av;
//                            exit;
                        $required=$remaining_req;
                        $available_arr[$wh_from] = $remaining_av;
                        $req_arr[$wh_to] = $remaining_req;
                        if($remaining_av <= 0)
                        {
                            unset($available_arr[$wh_from]);
                        }
                        if($remaining_req<=0)
                        {    
                            unset($req_arr[$wh_to]);
                            break;
                        }
                    }
                }
if(empty($reverse_arr))
{
    $display="display:none;";
}
                //start of output html

                        $html .= '<!--PRINT START-->';
                        $html .=  '<div class="row">';
                        $html .=  '    <div class="col-md-12">';
                        $html .=  '        <h3 class="page-title row-br-b-wp">Suggested Stock Movement Analysis For Stabilizing Stock at SDP Level</h3>';
                        $html .=  '    </div>';
                        $html .=  '</div>';

                        $html .=  '<div class="row">';
                        $html .=  '    <div class="col-md-12">';
                        $html .=  '        <div class="">';

                        $html .=  '<table id="result_table" class="table table-bordered table-hover table-condensed" style="width:100%;">
                                    <tr>
                                        <th></th>
                                        <th colspan="6" class="info" style="text-align:center">From</th>
                                        <th colspan="2"  class="success"  style="text-align:center">To</th>
<th  class="info"  style="text-align:left"><input type="checkbox" id="checkAll"></th>
                                    </tr>
                                    <tr>
                                        <th>#</th>
                                        <th>Facility Name</th>
                                        <th>SOH</th>
                                        <th>AMC</th>
                                        <th>MOS</th>
                                        <!--<th width="5%">MOS Required</th>-->
                                       <!--<th>Status</th>-->
                                        <th>Over Stock Qty</th>
                                        <th> </th>
                                        <th>Transfer Qty</th> 
                                        <th>Transfer To</th>
                                        <th><button id="modal_btn" class="btn btn-primary btn-sm" style="'.$display.'">Draft</button></th>
                                        <!--<th>Current SOH</th>-->
                                    </tr>';

                                 $c= 1;
                                foreach($tr_arr as $wh_id=>$v)
                                {
                                    $row = $data_arr[$wh_id];
                                    $status='-';
                                    $st = 0;

                                    if(!isset($row['avg_consumption']) || $row['avg_consumption'] <= 0 ){
                                        $status = 'Unknown';
                                        $st = 1;
                                    }
                                    elseif($row['mos'] <= 0.5){
                                        $status = '<span style="color:red"><b>Stock Out</b></span>';
                                        $st = 2;
                                    }elseif($row['mos'] <= 0.99){
                                        $status = 'Under Stock';
                                        $st = 3;
                                    }elseif($row['mos'] <= $mos_required){
                                        $status = 'Satisfactory';
                                        $st = 4;
                                    }else{
                                        $status = '<span style="color:green"><b>Over Stock</b></span>';
                                        $st = 5;
                                    }

                                    $transferrable=0;
                                    if($row['mos'] > $mos_required)
                                    {
                                        $per_month = $row['closing_balance'] / $row['mos'];
                                        $extra_months = $row['mos'] - $mos_required;
                                        $transferrable = $extra_months * $per_month;
                                    }
                                    if($st == 5)
                                    {
                                        $to_c = 1;
                                        if(!empty($from_to_arr[$wh_id]))
                                        {
                                            foreach($from_to_arr[$wh_id] as $to_id => $trans_qty)
                                            {

                                                $qty_after = $reverse_arr[$to_id]['closing_balance'] + $trans_qty;
                                                $mos_after = $qty_after /$reverse_arr[$to_id]['avg_consumption'];
                                                $count_of_receivers = count($from_to_arr[$wh_id]);

                                                if($to_c ==1 )
                                                {
                                                    $html .=  '<tr>
                                                        <td rowspan="'.$count_of_receivers.'">'.$c++.'</td>
                                                        <td rowspan="'.$count_of_receivers.'">'.$row['wh_name'].'</td>
                                                        <td rowspan="'.$count_of_receivers.'" align="right">'.number_format($row['closing_balance']).'</td>
                                                        <td rowspan="'.$count_of_receivers.'" align="right">'.number_format($row['avg_consumption']).'</td>
                                                        <td rowspan="'.$count_of_receivers.'" align="right">'.number_format($row['mos'],2).'</td>
                                                        <!--<td rowspan="'.$count_of_receivers.'" align="right">'.$mos_required.'</td>-->
                                                        <!--<td rowspan="'.$count_of_receivers.'">'.$status.'</td>-->
                                                        <td rowspan="'.$count_of_receivers.'" align="right">'.number_format($transferrable).'</td>
                                                        <td rowspan="'.$count_of_receivers.'" width="15%" align="center"><i class="fa fa-arrow-right " style="color:black !important;"></i> </td>

                                                        <td class="success" align="right"><b>'.number_format($trans_qty).'</b></td>
                                                        <td class="success">'.$reverse_arr[$to_id]['wh_name'].'</td>
                                                     <td><input type="checkbox" data-soh='.number_format($row['closing_balance']).' data-mos='.number_format($row['mos'],2).' data-amc='.number_format($row['avg_consumption']).' data-osqty='.number_format($transferrable).' data-wh='.str_replace(' ', '_', $row['wh_name']).' data-whto='.str_replace(' ', '_', $reverse_arr[$to_id]['wh_name']).' data-from='.$row['wh_id'].' data-to='.$reverse_arr[$to_id]['wh_id'].' data-trs='.number_format($trans_qty).' data-qty='.number_format($transferrable).' data-prod='.$itm_id.' data-month='.$date.'  data-dist='.$districtId.'  data-prov='.$selProv.'  data-stk='.$stakeholder.'></td>
                                                         <!--<td class="success">'.$reverse_arr[$to_id]['closing_balance'].'</td>-->
                                                    </tr>';
                                                }
                                                else
                                                {
                                                     $html .=  '<tr>
                                                            <td class="success" align="right"><b>'.number_format($trans_qty).'</b></td> 
                                                            <td class="success">'.$reverse_arr[$to_id]['wh_name'].'</td>
                                                              <td><input type="checkbox" data-soh='.number_format($row['closing_balance']).' data-mos='.number_format($row['mos'],2).' data-amc='.number_format($row['avg_consumption']).' data-osqty='.number_format($transferrable).' data-wh='.str_replace(' ', '_', $row['wh_name']).' data-whto='.str_replace(' ', '_', $reverse_arr[$to_id]['wh_name']).' data-from='.$row['wh_id'].' data-to='.$reverse_arr[$to_id]['wh_id'].' data-trs='.number_format($trans_qty).' data-qty='.number_format($transferrable).' data-prod='.$itm_id.' data-month='.$date.'  data-dist='.$districtId.'  data-prov='.$selProv.'  data-stk='.$stakeholder.'></td>
                                                       <!--<td class="success">'.$reverse_arr[$to_id]['closing_balance'].'</td>-->
                                                        </tr>';
                                                }
                                                $to_c++;
                                            }
                                        }
                                        else{
                                            $html .=  '<tr>
                                                <td>'.$c++.'</td>
                                                <td>'.$row['wh_name'].'</td>
                                                <td align="right">'.number_format($row['closing_balance']).'</td>
                                                <td align="right">'.number_format($row['avg_consumption']).'</td>
                                                <td align="right">'.number_format($row['mos'],2).'</td>
                                                <!--<td align="right">'.$mos_required.'</td>-->
                                                <!--<td>'.$status.'</td>-->
                                                <td align="right">'.number_format($transferrable).'</td>
                                            </tr>';
                                        }
                                    }
                                }
                                $html .=  '</table>';

                                $html .= '</div>';
                            $html .= '</div>';

                        $html .= '</div>';
                        $html .= '<!--PRINT END-->';

                //End of output html

            return $html;
        }//end of function
 ?>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title" id="myModalLabel">Press save to confirm draft</h4>

            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" id="save_btn" name="save_btn" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>
 <div class="modal fade" id="success" tabindex="-1" role="dialog" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                 <h4 class="modal-title" id="myModalLabel">Info</h4>

            </div>
            <div class="modal-body-2"><br><h3 style="align-content: center">Data has been saved</h3></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                
            </div>
        </div>
    </div>
</div>