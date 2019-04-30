<?php
include("../includes/classes/AllClasses.php");
if (isset($_REQUEST['requisition_id'])) {

    $id = mysql_real_escape_string($_REQUEST['requisition_id']);

    $qry = "SELECT
				clr_master.requisition_num,
				clr_master.date_from,
				clr_master.date_to,
				clr_master.wh_id,
				clr_master.approval_status as master_approval_status,
				clr_details.approval_status as detail_approval_status,
				clr_details.pk_id,
				clr_details.pk_master_id,
				clr_details.avg_consumption,
				clr_details.soh_dist,
				clr_details.soh_field,
				clr_details.total_stock,
				clr_details.desired_stock,
				clr_details.replenishment,
				clr_details.qty_req_dist_lvl1,
                                clr_details.qty_req_dist_lvl2,
                                clr_details.qty_req_prov,
                                clr_details.qty_req_central,
                                clr_details.remarks_dist_lvl1,
                                clr_details.remarks_dist_lvl2,
                                clr_details.remarks_prov,
                                clr_details.remarks_central,
                                clr_details.sale_of_last_month,
                                clr_details.sale_of_last_3_months,
				DATE_FORMAT(clr_master.requested_on, '%d/%m/%Y') AS requested_on,
				itminfo_tab.itm_name,
				itminfo_tab.itm_id,
				itminfo_tab.itmrec_id,
				itminfo_tab.itm_type,
				itminfo_tab.generic_name,
				itminfo_tab.method_type
			FROM
				clr_master
				INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
				INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
			WHERE
				clr_master.pk_id = " . $id;
    //query result
    $qryRes = mysql_query($qry);
//    print_r($qry);
    //fetch result
    $remarks_prov = $items_arr = array();
    $show_prov_remarks = $show_dist_remarks = false;
    while ($row = mysql_fetch_assoc($qryRes)) {
        $clr_of_wh = $row['wh_id'];
        $master_approval_status = $row['master_approval_status'];
        //requisition Num 
        $requisitionNum = $row['requisition_num'];
        //date from
        $dateFrom = date('M-Y', strtotime($row['date_from']));
        //date to
        $dateTo = date('M-Y', strtotime($row['date_to']));
        //requested on 
        $requestedOn = $row['requested_on'];
        //item ids
        $itemIds[] = $row['itm_id'];
        //product
        $product[$row['method_type']][] = $row['itm_name'];
        $items_arr[$row['itm_id']] = $row['itm_name'];

        // implanon is now opened .
        //if ($row['itm_id'] == 8) 
        //set avg Consumption
        $avgConsumption[$row['itm_id']] = number_format($row['avg_consumption']);
        $sale_of_last_month[$row['itm_id']] = number_format($row['sale_of_last_month']);
        ;
        $sale_of_last_3_months[$row['itm_id']] = number_format($row['sale_of_last_3_months']);
        ;
        //set SOH Dist
        $SOHDist[$row['itm_id']] = number_format($row['soh_dist']);
        //set SOH Field
        $SOHField[$row['itm_id']] = number_format($row['soh_field']);
        //set total Stock
        $totalStock[$row['itm_id']] = number_format($row['total_stock']);
        //set desired Stock
        $desiredStock[$row['itm_id']] = number_format($row['desired_stock']);
        //set replenishment
        $replenishment[$row['itm_id']] = number_format($row['replenishment']);

        //set qty requested and remarks
        $qty_req_dist_lvl1[$row['itm_id']] = number_format($row['qty_req_dist_lvl1']);
        ;
        $qty_req_dist_lvl2[$row['itm_id']] = number_format($row['qty_req_dist_lvl2']);
        ;
        $qty_req_prov[$row['itm_id']] = number_format($row['qty_req_prov']);
        ;
        $qty_req_central[$row['itm_id']] = number_format($row['qty_req_central']);
        ;

        $remarks_dist_lvl1[$row['itm_id']] = $row['remarks_dist_lvl1'];
        $remarks_dist_lvl2[$row['itm_id']] = $row['remarks_dist_lvl2'];
        $remarks_prov[$row['itm_id']] = $row['remarks_prov'];
        $remarks_central[$row['itm_id']] = $row['remarks_central'];

        if (!empty($row['remarks_prov']))
            $show_prov_remarks = true;
        if (!empty($row['remarks_dist_lvl1']))
            $show_dist_remarks = true;

        if (strtoupper($row['method_type']) == strtoupper($row['generic_name'])) {
            $methodType[$row['method_type']]['rowspan'] = 2;
        } else {
            $genericName[$row['generic_name']][] = $row['itm_name'];
        }
    }
//    echo '<pre>';
//    print_r($remarks_prov); 
    $whTo = mysql_real_escape_string($clr_of_wh);
    $qry = "SELECT
				tbl_warehouse.dist_id,
				tbl_warehouse.prov_id,
				tbl_warehouse.stkid,
				tbl_locations.LocName,
				MainStk.stkname AS MainStk
			FROM
			tbl_warehouse
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			INNER JOIN stakeholder AS MainStk ON stakeholder.MainStakeholder = MainStk.stkid
			WHERE
			tbl_warehouse.wh_id = " . $whTo;
    //query result
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //distrct id
    $distId = $qryRes['dist_id'];
    //province id
    $provId = $qryRes['prov_id'];
    //stakeholder id
    $stkid = $qryRes['stkid'];
    //location name
    $distName = $qryRes['LocName'];
    //main stakeholder
    $mainStk = $qryRes['MainStk'];
    $duration = $dateFrom . ' to ' . $dateTo;
}
//echo '<pre>';print_r($remarks_dist_lvl1);print_r($items_arr);exit;
?>

<!-- BEGIN PAGE HEADER-->
<div class="row">
    <div class="col-md-12">
        <div class="widget">
            <div class="widget-head">
                <h3 class="heading">Remarks for Requisition </h3>
            </div>
            <div class="widget-body">
                <div id="printing" style="clear:both;margin-top:20px;">
                    <div style="margin-left:0px !important; width:100% !important;">
                        <style>
                            body {
                                margin: 0px !important;
                                font-family: Arial, Helvetica, sans-serif;
                            }

                            table#myTable {
                                margin-top: 20px;
                                border-collapse: collapse;
                                border-spacing: 0;
                            }

                            table#myTable tr td, table#myTable tr th {
                                font-size: 11px;
                                padding-left: 5px;
                                text-align: left;
                                border: 1px solid #999;
                            }

                            table#myTable tr td.TAR {
                                text-align: right;
                                padding: 5px;
                                width: 50px !important;
                            }

                            .sb1NormalFont {
                                color: #444444;
                                font-family: Verdana, Arial, Helvetica, sans-serif;
                                font-size: 11px;
                                font-weight: bold;
                                text-decoration: none;
                            }

                            p {
                                margin-bottom: 5px;
                                font-size: 11px !important;
                                line-height: 1 !important;
                                padding: 0 !important;
                            }

                            table#headerTable tr td {
                                font-size: 11px;
                            }

                            /* Print styles */
                            @media only print {
                                table#myTable tr td, table#myTable tr th {
                                    font-size: 8px;
                                    padding-left: 2 !important;
                                    text-align: left;
                                    border: 1px solid #999;
                                }

                                #doNotPrint {
                                    display: none !important;
                                }
                            }
                        </style>

                        <div style="clear:both;"></div>
                        <table width="100%" id="myTable" cellspacing="0" align="center">

                            <tbody>


<?php
if (true) {
    ?>
                                    <tr  >
                                        <td>#</td>
                                        <td>Product</td>
                                        <td>Requested by district</td>
                                        <td>Approved by province</td>
                                        <td>Remarks By District</td>
                                        <td>Your Remarks</td>
                                    </tr>
                                    <tr  >
                                        <?php
                                        foreach ($remarks_dist_lvl1 as $key => $val) {
                                            echo '<tr height="30">
                                                                    <td > </td>
                                                                    <td >' . $items_arr[$key] . '</td>
                                                                    <td >' . ($qty_req_dist_lvl1[$key]) . '</td>
                                                                    <td >' . ($qty_req_prov[$key]) . '</td>
                                                                    <td  colspan="">' . wordwrap($val, 50, "<br>\n", true) . '</td>
                                                                    <td  colspan="">
                                                                    <input class="remarks_input" type="text" size="60" data-id="' . $id . '" data-prodid="' . $key . '" value="' . $remarks_prov[$key] . '" >
                                                                    </td>
                                                                </tr>';
                                        }
                                        ?>
                                    </tr>

    <?php
}
?>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
 