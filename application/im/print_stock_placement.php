<style>
    .dashboard-stat .details .number{
        font-size: 9px !important;
    }
    td { vertical-align: top; 
         -webkit-print-color-adjust: exact !important;}



</style>

<?php
/**
 * stock_placement
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//get warehouse id
$wh_id = $_SESSION['user_warehouse'];
//get rack count
$getRackCount = 0;
//get row count
$getRowCount = 0;
//area
$area = $level = '';
//check area
if (isset($_GET) && !empty($_GET['area'])) {
    if (isset($_GET['area']) && !empty($_GET['area'])) {
        //get area
        $area = $_GET['area'];
    }
    //check level
    if (isset($_GET['level']) && !empty($_GET['level'])) {
        //get vevel	
        $level = $_GET['level'];
    }
    //get warehouse id
    $wh_id = $_SESSION['user_warehouse'];
    //select query
    //gets
    //pk id
    //location name
    //row
    //pallet
    //rack
    $mainSQL = "SELECT
				placement_config.pk_id,
				placement_config.location_name,
				rows.list_value AS myrow,
				Pallets.list_value AS mypallet,
				racks.list_value AS myrack
			FROM
				placement_config
			INNER JOIN list_detail AS rows ON placement_config.`row` = rows.pk_id
			INNER JOIN list_detail AS racks ON placement_config.rack = racks.pk_id
			INNER JOIN list_detail AS Pallets ON placement_config.pallet = Pallets.pk_id
			WHERE (area=" . $area . " AND level=" . $level . ")
				AND warehouse_id=" . $wh_id . "
				AND placement_config.`status` = 1
			ORDER BY
				myrow,
				myrack,
				mypallet";
    //query result
    $getLocationStatus = mysql_query($mainSQL) or die(mysql_error());
    //number of locations
    $NoofLocations = mysql_num_rows($getLocationStatus);

    //select query
    //gets
    //rows count
    $rowCountSQL = "SELECT
						ifnull(max(rows.list_value),0) AS rows
						FROM
						placement_config
						INNER JOIN list_detail AS rows ON placement_config.`row` = rows.pk_id
				WHERE
					area=$area AND level=$level AND warehouse_id =  $wh_id
				GROUP BY
					placement_config.warehouse_id";
    //query result
    $getRowCount = mysql_query($rowCountSQL) or die($rowCountSQL);
    //fetch result
    $getRowCount = mysql_fetch_row($getRowCount);
    //select query
    //gets
    //rack count
    $rackCountSQL = "SELECT ifnull(max(rack.list_value),0) AS racks 
				FROM placement_config INNER JOIN list_detail AS rack ON placement_config.`rack` = rack.pk_id
				WHERE
					area=$area AND level=$level AND warehouse_id =  $wh_id
				GROUP BY
					placement_config.warehouse_id";
    //query result
    $getRackCount = mysql_query($rackCountSQL) or die("Err Countracks");
    $getRackCount = mysql_fetch_row($getRackCount);
}
//row count
$Rowcounter = 0;
//rack count
$Rackcounter = 0;
?>
<style>
    .btn-link {
        color: #fff !important;
        text-shadow: none;
    }
</style>

<div id="content_print" class="page">
    <style type="text/css" media="print">
        .page
        {

            -webkit-print-color-adjust:exact ;

        }
        @media print {
            tr {

                -webkit-print-color-adjust: exact ; 
            }}

        @media print {
            td {
                color: white;
                -webkit-print-color-adjust: exact ; 
            }}
        </style>


        <?php
        if (isset($_GET) && !empty($_GET['area'])) {
            ?>

            <div class="widget-head">
            <h3 class="heading">Location Information</h3>
        </div>


        <?php
        //check if result exists
        if ($getRowCount[0] > 0) {
            ?>
            <table style="border: none; width: 100%;">
                <?php
                //location found flag
                $locationFound = 1;
                $hit = 0;

                for ($rr = 1; $rr <= $getRowCount[0]; $rr++) {
                    for ($cc = 1; $cc <= $getRackCount[0]; $cc++) {

                        for ($pp = 1; $pp < 5; $pp++) {
                            if ($hit == 0) {
                                $rowStatus = array();
                                //fetch result
                                $rowStatus[$Rowcounter] = mysql_fetch_assoc($getLocationStatus);
                                foreach ($rowStatus as $row):
                                    //location id
                                    $locid = $row['pk_id'];
                                    //placement location id    
                                    $plc_locid = $locid;
                                    //location name    
                                    $locname = $row['location_name'];
                                    //row
                                    $row1 = (int) $row['myrow'];
                                    //rack
                                    $rack = (int) $row['myrack'];
                                    //pallet
                                    $pallet = (int) $row['mypallet'];


                                endforeach;
                            }
                            if ($rr == $row1 && $cc == $rack && $pallet == $pp) {
                                $locArray[$rr][$cc][$pp] = $locname . "|" . $plc_locid;
                                $hit = 0;
                            } else {
                                $locArray[$rr][$cc][$pp] = "&nbsp;";
                                $hit = 5;
                            }
                        }

                        if ($hit == 0) {
                            $Rowcounter++;
                        }
                    }
                }

                for ($a = 1; $a <= $getRowCount[0]; $a++):
                    ?>
                    <tr style="border: 3px solid green;" >
                        <?php
                        for ($x = 1; $x <= $getRackCount[0]; $x++):
                            ?>
                            <td style="width:<?php print round((100 / $getRackCount[0]), 2) . '%'; ?>; height:86px;padding: 4px; border-right: 4px solid green; border-left: 4px solid green;"><?php
                                if ($locArray[$a][$x][1] != "&nbsp;" || $locArray[$a][$x][2] != "&nbsp;" ||
                                        $locArray[$a][$x][3] != "&nbsp;" || $locArray[$a][$x][4] != "&nbsp;") {
                                    ?>
                                    <table style="border: 2px solid green; width:100%;">
                                        <tr>
                                            <td style="width:10%;border: 2px solid white; ">

                                                <div class="capacity">
                                                    <table width="100%" height="40" >
                                                        <tbody>
                                                            <tr>
                                                                <?php
                                                                list($l1, $loc1) = explode('|', $locArray[$a][$x][1]);
                                                                if (!empty($loc1)) {
                                                                    //url
                                                                    $url = "stock_location.php?loc_id=$loc1&area=$area&level=$level";
                                                                    $qry1 = "SELECT
                                                                                stock_batch.batch_no,
                                                                                stock_batch.item_id,
                                                                                itminfo_tab.itm_name,
                                                                                itminfo_tab.itm_type,
                                                                                stock_batch.batch_expiry,
                                                                                placements.is_placed,
                                                                                Sum(placements.quantity) AS quantity,
                                                                                ROUND(
                                                                        (
                                                                                (
                                                                                        (
                                                                                                Sum(placements.quantity) / itminfo_tab.qty_carton
                                                                                        ) * stakeholder_item.carton_volume
                                                                                ) / (stakeholder_item.carton_volume * stakeholder_item.carton_per_pallet)
                                                                        ) * 100
                                                                        ) AS used_per,
                                                                                        stock_batch.batch_id,
                                                                                        placements.placement_location_id,
                                                                                        placements.stock_detail_id,
                                                                                        tbl_warehouse.wh_name,
                                                                                        stakeholder_item.carton_per_pallet,
                                                                                        stakeholder_item.carton_volume,
                                                                                        stakeholder.stkname,
                                                                                        stakeholder_item.stk_id
                                                                                FROM
                                                                                        stock_batch
                                                                                LEFT JOIN placements ON stock_batch.batch_id = placements.stock_batch_id
                                                                                INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                                                                                INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
                                                                                INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                                                                                LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                                                                                LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                                                                                WHERE

                                                                                                    placements.placement_location_id = $loc1
                                                                                            AND placement_config.warehouse_id = $wh_id
                                                                                            GROUP BY
                                                                                            placements.placement_location_id";
                                                                    $row1 = mysql_fetch_array(mysql_query($qry1));

                                                                    $used_per1 = $row1['used_per'];
                                                                    $total_per1 = 100 - $row1['used_per'];
                                                                    if ($used_per1 <= 100) {
                                                                        $color = "#DD8521";
                                                                        $color1 = "green";
                                                                    }
                                                                    if ($used_per1 > 100) {
                                                                        $total_per1 = 0;
                                                                        $color = "#E00000";
                                                                        $color1 = "green";
                                                                    }
                                                                } else {
                                                                    $used_per1 = 0;
                                                                    $total_per1 = 100;
                                                                    $color1 = "grey";
                                                                }
                                                                ?>

                                                                <td style="background-color: <?php echo $color1; ?>!important; color:white !important;" height="<?php echo $total_per1; ?>%"> 
                                                                    <?php if (!empty($loc1) && $total_per1 != 0) { ?>
                                                                        <a itemid="<?php echo $loc1; ?>" style="font-size: 7px; color:white !important;" > <?php echo $l1; ?></a>
                                                                        <?php
                                                                    }
                                                                    ?></td>
                                                            </tr>
                                                            <tr><td style="background-color: <?php echo $color; ?>!important; color:white !important;" height="<?php echo $used_per1; ?>%">
                                                                    <?php if (!empty($loc1) && $total_per1 == 0) { ?>
                                                                        <a itemid="<?php echo $loc1; ?>"  style="font-size: 7px;  color:white !important;" > <?php echo $l1; ?></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                </div>




                                            </td>
                                            <td style="width:10%;border: 2px solid white; background-color: green !important;">

                                                <div class="capacity"> 
                                                    <table width="100%" height="40" >
                                                        <tbody>
                                                            <tr>
                                                                <?php
                                                                list($l1, $loc1) = explode('|', $locArray[$a][$x][2]);
                                                                if (!empty($loc1)) {
                                                                    //url
                                                                    $url = "stock_location.php?loc_id=$loc1&area=$area&level=$level";
                                                                    $qry2 = "SELECT
                                                                                stock_batch.batch_no,
                                                                                stock_batch.item_id,
                                                                                itminfo_tab.itm_name,
                                                                                itminfo_tab.itm_type,
                                                                                stock_batch.batch_expiry,
                                                                                placements.is_placed,
                                                                                Sum(placements.quantity) AS quantity,
                                                                                ROUND(
                                                                        (
                                                                                (
                                                                                        (
                                                                                                Sum(placements.quantity) / itminfo_tab.qty_carton
                                                                                        ) * stakeholder_item.carton_volume
                                                                                ) / (stakeholder_item.carton_volume * stakeholder_item.carton_per_pallet)
                                                                        ) * 100
                                                                ) AS used_per,
                                                                                stock_batch.batch_id,
                                                                                placements.placement_location_id,
                                                                                placements.stock_detail_id,
                                                                                tbl_warehouse.wh_name,
                                                                                stakeholder_item.carton_per_pallet,
                                                                                stakeholder_item.carton_volume,
                                                                                stakeholder.stkname,
                                                                                stakeholder_item.stk_id
                                                                        FROM
                                                                                stock_batch
                                                                        LEFT JOIN placements ON stock_batch.batch_id = placements.stock_batch_id
                                                                        INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                                                                        INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
                                                                        INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                                                                        LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                                                                        LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                                                                        WHERE

                                                                                                    placements.placement_location_id = $loc1
                                                                                            AND placement_config.warehouse_id = $wh_id
                                                                                            GROUP BY
                                                                                            placements.placement_location_id";
                                                                    $row2 = mysql_fetch_array(mysql_query($qry2));

                                                                    $used_per2 = $row2['used_per'];
                                                                    $total_per2 = 100 - $row2['used_per'];
                                                                    if ($used_per2 < 100) {
                                                                        $color = "#DD8521";
                                                                        $color1 = "green";
                                                                    }
                                                                    if ($used_per2 > 100) {
                                                                        $total_per2 = 0;
                                                                        $color = "#E00000";
                                                                        $color1 = "green";
                                                                    }
                                                                } else {
                                                                    $used_per2 = 0;
                                                                    $total_per2 = 100;
                                                                    $color1 = "grey";
                                                                }
                                                                ?>

                                                                <td style="background-color: <?php echo $color1; ?> !important; color:white !important;" height="<?php echo $total_per2; ?>%"> 
                                                                    <?php if (!empty($loc1) && $total_per2 != 0) { ?>
                                                                        <a itemid="<?php echo $loc1; ?>"  style="font-size: 7px; color:white !important; padding: 1px 1px 1px 1px;"> <?php echo $l1; ?></a>
                                                                        <?php
                                                                    }
                                                                    ?></td>


                                                            </tr>
                                                            <tr><td style="background-color: <?php echo $color; ?> !important; color:white !important;" height="<?php echo $used_per2; ?>%">
                                                                    <?php if (!empty($loc1) && $total_per2 == 0) { ?>
                                                                        <a itemid="<?php echo $loc1; ?>" style="font-size: 7px; color:white !important; padding: 1px 1px 1px 1px; "> <?php echo $l1; ?></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                </div>


                                            </td>
                                        </tr>
                                        <tr >
                                            <td style="width:10%;border: 2px solid white; background-color: green !important;">


                                                <div class="capacity">
                                                    <table width="100%" height="40" >
                                                        <tbody>
                                                            <tr>
                                                                <?php
                                                                list($l1, $loc1) = explode('|', $locArray[$a][$x][3]);
                                                                if (!empty($loc1)) {
                                                                    //url
                                                                    $url = "stock_location.php?loc_id=$loc1&area=$area&level=$level";

                                                                    $qry3 = "SELECT
                                                                            stock_batch.batch_no,
                                                                            stock_batch.item_id,
                                                                            itminfo_tab.itm_name,
                                                                            itminfo_tab.itm_type,
                                                                            stock_batch.batch_expiry,
                                                                            placements.is_placed,
                                                                            Sum(placements.quantity) AS quantity,
                                                                            ROUND(
                                                                    (
                                                                            (
                                                                                    (
                                                                                            Sum(placements.quantity) / itminfo_tab.qty_carton
                                                                                    ) * stakeholder_item.carton_volume
                                                                            ) / (stakeholder_item.carton_volume * stakeholder_item.carton_per_pallet)
                                                                    ) * 100
                                                            ) AS used_per,
                                                                            stock_batch.batch_id,
                                                                            placements.placement_location_id,
                                                                            placements.stock_detail_id,
                                                                            tbl_warehouse.wh_name,
                                                                            stakeholder_item.carton_per_pallet,
                                                                            stakeholder_item.carton_volume,
                                                                            stakeholder.stkname,
                                                                            stakeholder_item.stk_id
                                                                    FROM
                                                                            stock_batch
                                                                    LEFT JOIN placements ON stock_batch.batch_id = placements.stock_batch_id
                                                                    INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                                                                    INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
                                                                    INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                                                                    LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                                                                    LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                                                                    WHERE

                                                                                                placements.placement_location_id = $loc1
                                                                                        AND placement_config.warehouse_id = $wh_id
                                                                                        GROUP BY
                                                                                        placements.placement_location_id";
                                                                    $row3 = mysql_fetch_array(mysql_query($qry3));

                                                                    $used_per3 = $row3['used_per'];
                                                                    $total_per3 = 100 - $row3['used_per'];

                                                                    if ($used_per3 < 100) {
                                                                        $color = "#DD8521";
                                                                        $color1 = "green";
                                                                    }
                                                                    if ($used_per3 > 100) {
                                                                        $total_per3 = 0;
                                                                        $color = "#E00000";
                                                                        $color1 = "green";
                                                                    }
                                                                } else {
                                                                    $used_per3 = 0;
                                                                    $total_per3 = 100;
                                                                    $color1 = "grey";
                                                                }
                                                                ?>

                                                                <td style="background-color: <?php echo $color1 ?> !important; color:white !important;" height="<?php echo $total_per3; ?>%"> 
                                                                    <?php if (!empty($loc1) && $total_per3 != 0) { ?>
                                                                        <a itemid="<?php echo $loc1; ?>" style="font-size: 7px; color:white !important; padding: 1px 1px 1px 1px; "> <?php echo $l1; ?></a>
                                                                        <?php
                                                                    }
                                                                    ?></td>
                                                            </tr>
                                                            <tr><td style="background-color: <?php echo $color; ?> !important; color:white !important;" height="<?php echo $used_per3; ?>%">
                                                                    <?php if (!empty($loc1) && $total_per3 == 0) { ?>
                                                                        <a itemid="<?php echo $loc1; ?>" style="font-size: 7px; color:white !important; padding: 1px 1px 1px 1px; " > <?php echo $l1; ?></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                </div>


                                            </td>
                                            <td style="width:10%;border: 2px solid white; background-color: green !important;">

                                                <div class="capacity">
                                                    <table width="100%" height="40" >
                                                        <tbody>
                                                            <tr>
                                                                <?php
                                                                list($l1, $loc1) = explode('|', $locArray[$a][$x][4]);
                                                                if (!empty($loc1)) {
                                                                    //url
                                                                    $url = "stock_location.php?loc_id=$loc1&area=$area&level=$level";


                                                                    $qry4 = "SELECT
                                                                            stock_batch.batch_no,
                                                                            stock_batch.item_id,
                                                                            itminfo_tab.itm_name,
                                                                            itminfo_tab.itm_type,
                                                                            stock_batch.batch_expiry,
                                                                            placements.is_placed,
                                                                            Sum(placements.quantity) AS quantity,
                                                                            ROUND(
                                                                    (
                                                                            (
                                                                                    (
                                                                                            Sum(placements.quantity) / itminfo_tab.qty_carton
                                                                                    ) * stakeholder_item.carton_volume
                                                                            ) / (stakeholder_item.carton_volume * stakeholder_item.carton_per_pallet)
                                                                    ) * 100
                                                            ) AS used_per,
                                                                            stock_batch.batch_id,
                                                                            placements.placement_location_id,
                                                                            placements.stock_detail_id,
                                                                            tbl_warehouse.wh_name,
                                                                            stakeholder_item.carton_per_pallet,
                                                                            stakeholder_item.carton_volume,
                                                                            stakeholder.stkname,
                                                                            stakeholder_item.stk_id
                                                                    FROM
                                                                            stock_batch
                                                                    LEFT JOIN placements ON stock_batch.batch_id = placements.stock_batch_id
                                                                    INNER JOIN itminfo_tab ON stock_batch.item_id = itminfo_tab.itm_id
                                                                    INNER JOIN placement_config ON placements.placement_location_id = placement_config.pk_id
                                                                    INNER JOIN tbl_warehouse ON stock_batch.funding_source = tbl_warehouse.wh_id
                                                                    LEFT JOIN stakeholder_item ON stock_batch.manufacturer = stakeholder_item.stk_id
                                                                    LEFT JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                                                                    WHERE

                                                                                                placements.placement_location_id = $loc1
                                                                                        AND placement_config.warehouse_id = $wh_id
                                                                                        GROUP BY
                                                                                        placements.placement_location_id";
                                                                    $row4 = mysql_fetch_array(mysql_query($qry4));

                                                                    $used_per4 = $row4['used_per'];
                                                                    $total_per4 = 100 - $row4['used_per'];
                                                                    if ($used_per4 < 100) {
                                                                        $color = "#DD8521";
                                                                        $color1 = "green";
                                                                    }
                                                                    if ($used_per4 > 100) {
                                                                        $total_per4 = 0;
                                                                        $color = "#E00000";
                                                                        $color1 = "green";
                                                                    }
                                                                } else {
                                                                    $used_per4 = 0;
                                                                    $total_per4 = 100;
                                                                    $color1 = "grey";
                                                                }
                                                                ?>

                                                                <td style="background-color: <?php echo $color1; ?> !important; color:white !important;" height="<?php echo $total_per4; ?>%">  
                                                                    <?php if (!empty($loc1) && $total_per4 != 0) { ?>
                                                                        <a itemid="<?php echo $loc1; ?>" style="font-size: 7px; color:white !important; padding: 1px 1px 1px 1px;"> <?php echo $l1; ?></a>
                                                                        <?php
                                                                    }
                                                                    ?></td>
                                                            </tr>
                                                            <tr><td style="background-color: <?php echo $color; ?> !important; color:white !important; " height="<?php echo $used_per4; ?>%">
                                                                    <?php if (!empty($loc1) && $total_per4 == 0) { ?>
                                                                        <a itemid="<?php echo $loc1; ?>" style="font-size: 7px; color:white !important; padding: 1px 1px 1px 1px;"> <?php echo $l1; ?></a>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </td>
                                                            </tr>
                                                        </tbody></table>
                                                </div>

                                            </td>
                                        </tr>
                                    </table>
                                <?php } ?></td>
                        <?php endfor; ?>
                    </tr>
                    <?php
                endfor;
                ?>
            </table>
            <?php
        } else {
            echo "No record found.";
        }
        ?>


        <?php
    }
    ?>
    </br>
    <table>
        <tr>
            <td>
                <div class="btn btn-sm" style="background-color: green !important;">&nbsp;&nbsp;</div> Unused Capacity&nbsp;&nbsp;
            </td>
            <td>

                <div class="btn btn-sm" style="background-color: #DD8521 !important;">&nbsp;&nbsp;</div> Used Capacity&nbsp;&nbsp;
            </td>
            <td>
                <div class="btn btn-sm" style="background-color: #E00000 !important;">&nbsp;&nbsp;</div> Overload&nbsp;&nbsp;
            </td>
            <td>
                <div class="btn btn-sm" style="background-color: grey !important;">&nbsp;&nbsp;</div> Non-functional&nbsp;&nbsp;
            </td>
            <td>

        </tr>
    </table>
</div>


<script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockplacement.js"></script>

