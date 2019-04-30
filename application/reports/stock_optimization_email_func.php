<?php
ini_set('max_execution_time', 0);
include_once("../includes/classes/AllClasses.php");
//include_once(PUBLIC_PATH . "html/header.php");


      
      
      
      function single_product_output($date, $districtId, $stakeholder, $itm_id, $selProv) {
          
        $mos_required = 3;
        $html = '';
        $qry = "
            SELECT
            A.avg_consumption,
            A.closing_balance,
            A.mos,
            A.reporting_date,
            A.wh_id,
            A.wh_name,
            (
                    SELECT DISTINCT
                            sysuser_tab.usrlogin_id
                    FROM
                            wh_user
                    INNER JOIN sysuser_tab ON wh_user.sysusrrec_id = sysuser_tab.UserID
                    WHERE
                            sysuser_tab.sysusr_type <> 23
                    AND wh_user.wh_id = A.wh_id
            ) usrlogin_id,
            (
                    SELECT DISTINCT
                            itminfo_tab.itm_name
                    FROM
                            itminfo_tab
                    WHERE
                            itminfo_tab.itm_id = A.item_id
            ) product
    FROM
            (SELECT
                            tbl_hf_data.avg_consumption,
                            tbl_hf_data.closing_balance,
                            round(tbl_hf_data.closing_balance/tbl_hf_data.avg_consumption,2) AS mos,
                            tbl_warehouse.wh_name,
                            tbl_hf_data.reporting_date,
                            tbl_warehouse.wh_id,
                            tbl_hf_data.item_id
                        FROM
                            tbl_hf_data
                        INNER JOIN tbl_warehouse ON tbl_hf_data.warehouse_id = tbl_warehouse.wh_id
                        INNER JOIN wh_user ON wh_user.wh_id = tbl_warehouse.wh_id
    INNER JOIN sysuser_tab ON wh_user.sysusrrec_id = sysuser_tab.UserID
                        WHERE
                            tbl_warehouse.dist_id = $districtId AND
                            tbl_hf_data.reporting_date = '" . $date . "' AND
                            tbl_hf_data.item_id = $itm_id AND
                            tbl_warehouse.stkid = $stakeholder AND
                            tbl_warehouse.hf_type_id NOT IN ( 3, 9, 6, 7, 8, 12, 10, 11)
                        order by mos desc) A
                        ";

    //    print_r($qry);
    //    exit;
        $qryRes = mysql_query($qry);

        $data_arr = $reverse_arr = $req_arr_original = $tr_arr = array();
        while ($row = mysql_fetch_assoc($qryRes)) {

            $data_arr[$row['wh_id']] = $row;
            $transferrable = 0;
            if ($row['mos'] > $mos_required) {
                $per_month = $row['closing_balance'] / $row['mos'];
                $extra_months = $row['mos'] - $mos_required;
                $transferrable = $extra_months * $per_month;
            }
            $tr_arr[$row['wh_id']] = $transferrable;
            arsort($tr_arr);

            if (!empty($row['avg_consumption']) && $row['mos'] >= 0 && $row['mos'] <= 0.5) {
                $reverse_arr[$row['wh_id']] = $row;

                $req = ($mos_required * $row['avg_consumption']) - $row['closing_balance'];
                $req_arr_original[$row['wh_id']] = $req;
                asort($req_arr_original);
            }

            $username = $row['usrlogin_id'];
            $product = $row['product'];
        }

        $req_arr = $req_arr_original;
        $req_arr = array_map("round", $req_arr);
        $tr_arr = array_map("round", $tr_arr);

        $available_arr = $tr_arr;
        $from_to_arr = array();
        foreach ($req_arr as $wh_to => $required) {
            foreach ($available_arr as $wh_from => $available) {
                if ($required <= $available) {
                    $used = $required;
                } else {
                    $used = $available;
                }

                $used = abs($used);
                $remaining_req = $required - $used;
                $remaining_av = $available - $used;
                $from_to_arr[$wh_from][$wh_to] = $used;
    //                            echo '<br/>------From:'.$wh_from.' - To:'.$wh_to;
    //                            echo '<br/>Req:'.$required;
    //                            echo '<br/>Av:'.$available;
    //                            echo '<br/>used:'.$used;
    //                            echo '<br/>Remaining required:'.$remaining_req;
    //                            echo '<br/>remaining available:'.$remaining_av;
    //                            exit;
                $required = $remaining_req;
                $available_arr[$wh_from] = $remaining_av;
                $req_arr[$wh_to] = $remaining_req;
                if ($remaining_av <= 0) {
                    unset($available_arr[$wh_from]);
                }
                if ($remaining_req <= 0) {
                    unset($req_arr[$wh_to]);
                    break;
                }
            }
        }

    //                echo '<pre>';print_r($tr_arr);
    //                echo '<pre>';print_r($from_to_arr);
        //start of output html


       
        $html .= '<h4>'.$product.'</h4><table id="result_table" border="1" cellpadding=4 cellspacing=0 >
                                        <tr>
                                            <th></th>
                                            <th colspan="6" class="info" style="text-align:center">From</th>
                                            <th colspan="3"  class="success"  style="text-align:center">To</th>

                                        </tr>
                                        <tr>
                                            <th>#</th>
                                            <th>Transfer From</th>
                                            <th>SOH</th>
                                            <th>AMC</th>
                                            <th>MOS</th>
                                            <!--<th width="5%">MOS Required</th>-->
                                           <!--<th>Status</th>-->
                                            <th>Over Stock Qty</th>
                                            <th> </th>
                                            <th>Transfer Qty</th> 
                                            <th>Transfer To</th>
                                            <!--<th>Current SOH</th>-->
                                        </tr>';

        $c = 1;
        foreach ($from_to_arr as $wh_id => $v) {
            $row = $data_arr[$wh_id];
            $status = '-';
            $st = 0;

            if (!isset($row['avg_consumption']) || $row['avg_consumption'] <= 0) {
                $status = 'Unknown';
                $st = 1;
            } elseif ($row['mos'] <= 0.5) {
                $status = '<span style="color:red"><b>Stock Out</b></span>';
                $st = 2;
            } elseif ($row['mos'] <= 0.99) {
                $status = 'Under Stock';
                $st = 3;
            } elseif ($row['mos'] <= $mos_required) {
                $status = 'Satisfactory';
                $st = 4;
            } else {
                $status = '<span style="color:green"><b>Over Stock</b></span>';
                $st = 5;
            }

            $transferrable = 0;
            if ($row['mos'] > $mos_required) {
                $per_month = $row['closing_balance'] / $row['mos'];
                $extra_months = $row['mos'] - $mos_required;
                $transferrable = $extra_months * $per_month;
            }
            if ($st == 5) {
                $to_c = 1;
                if (!empty($from_to_arr[$wh_id])) {
                    foreach ($from_to_arr[$wh_id] as $to_id => $trans_qty) {

                        $qty_after = $reverse_arr[$to_id]['closing_balance'] + $trans_qty;
                        $mos_after = $qty_after / $reverse_arr[$to_id]['avg_consumption'];
                        $count_of_receivers = count($from_to_arr[$wh_id]);

                        if ($to_c == 1) {
                            $html .= '<tr>
                                                            <td rowspan="' . $count_of_receivers . '">' . $c++ . '</td>
                                                            <td rowspan="' . $count_of_receivers . '">' . $row['wh_name'] . '</td>
                                                            <td rowspan="' . $count_of_receivers . '" align="right">' . number_format($row['closing_balance']) . '</td>
                                                            <td rowspan="' . $count_of_receivers . '" align="right">' . number_format($row['avg_consumption']) . '</td>
                                                            <td rowspan="' . $count_of_receivers . '" align="right">' . number_format($row['mos'], 2) . '</td>
                                                            <!--<td rowspan="' . $count_of_receivers . '" align="right">' . $mos_required . '</td>-->
                                                            <!--<td rowspan="' . $count_of_receivers . '">' . $status . '</td>-->
                                                            <td rowspan="' . $count_of_receivers . '" align="right">' . number_format($transferrable) . '</td>
                                                            <td rowspan="' . $count_of_receivers . '" width="15%" align="center"> > </td>

                                                            <td class="success" align="right"><b>' . number_format($trans_qty) . '</b></td>
                                                            <td class="success">' . $reverse_arr[$to_id]['wh_name'] . '</td>
                                                            <!--<td class="success">' . $reverse_arr[$to_id]['closing_balance'] . '</td>-->
                                                        </tr>';
                        } else {
                            $html .= '<tr>
                                                                <td class="success" align="right"><b>' . number_format($trans_qty) . '</b></td> 
                                                                <td class="success">' . $reverse_arr[$to_id]['wh_name'] . '</td>
                                                                <!--<td class="success">' . $reverse_arr[$to_id]['closing_balance'] . '</td>-->
                                                            </tr>';
                        }
                        $to_c++;
                    }
                } else {
                    $html .= '<tr>
                                                    <td>' . $c++ . '</td>
                                                    <td>' . $row['wh_name'] . '</td>
                                                    <td align="right">' . number_format($row['closing_balance']) . '</td>
                                                    <td align="right">' . number_format($row['avg_consumption']) . '</td>
                                                    <td align="right">' . number_format($row['mos'], 2) . '</td>
                                                    <!--<td align="right">' . $mos_required . '</td>-->
                                                    <!--<td>' . $status . '</td>-->
                                                    <td align="right">' . number_format($transferrable) . '</td>
                                                </tr>';
                }
            }
        }
        $html .= '</table>';

       

        //End of output html

        if($c>1)
            return $html;
        else 
            return '';
    }
    
    
    function generate_stock_table_for_email($date, $districtId, $stakeholder, $itm_id, $selProv) {
          $ret_val =$html ='';
          $itm_arr = array();
          $itm_arr[1]=1;
          $itm_arr[5]=5;
          $itm_arr[7]=7;
          $itm_arr[9]=9;
          
          foreach($itm_arr as $k =>$item){
             $a = single_product_output($date, $districtId, $stakeholder, $item, $selProv);
             
             $ret_val .=$a; 
          }
           
          
            $html .= '<!--PRINT START-->';
            $html .= '        <p>Dear LMIS user,</p>';
            $html .= '        <p>LMIS has generated an optimal stock redistribution at SDP level for your district.</p>';
             $html .=$ret_val;
            $html .= '<p>Your username is: <b>'.$username.'</b> <br />For more detail, please login to your account and visit <a href=http://c.lmis.gov.pk/application/reports/stock_optimization_3.php?stakeholder=' . $stakeholder . '&prov_sel=' . $selProv . '&dist_id=' . $districtId . '&product=' . $itm_id . '&submit=Submit&date=' . $date . '>Stock analysis report</a><br />In case of any query please email us at support@lmis.gov.pk.<br /><br /><i style="color:#3a3838; font-size:12px">This message was sent by LMIS</i></p>';
            $html .= '<!--PRINT END-->';
          return $html;
      }