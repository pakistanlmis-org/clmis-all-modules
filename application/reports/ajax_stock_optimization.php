<?php
include_once("../includes/classes/AllClasses.php");
//include_once(PUBLIC_PATH . "html/header.php");
$date = $_REQUEST['month'];
$districtId = $_REQUEST['dist_id'];
$stakeholder = $_REQUEST['stk_id'];
$itm_id = $_REQUEST['prod_id'];
$selProv = $_REQUEST['prov_id'];
$to_array = $_REQUEST['to_array'];
$from_array = $_REQUEST['from_array'];
$trs_qty = $_REQUEST['trs_qty'];
$to_array_imp = implode(',', $to_array);
$from_array_imp = implode(',', $from_array);
$to_wh_name = $_REQUEST['whto'];
$from_wh_name = $_REQUEST['wh'];
$soh = $_REQUEST['soh'];
$mos = $_REQUEST['mos'];
$amc = $_REQUEST['amc'];
$os_qty = $_REQUEST['os_qty'];
$count=1;
//echo '<pre>';
//print_r(str_replace('_', ' ', $to_wh_name));
?>
<table class="table table-bordered table-hover table-condensed">
    <thead>
        <tr>
            <th>S No.</th>
            <th>Facility Name</th>


            <th>SOH</th>
            <th>AMC</th>
            <th>MOS</th>
            <th>Overstock Qty</th>
            
            <th> </th>
            <th>Transfer Qty</th>
            <th>Transfer To</th>
        </tr>
    </thead>
    <tbody>
        <?php for ($i = 0; $i < (count($from_wh_name)); $i++) { ?>
            <tr>
                <td><?php echo $count;?></td>
                <td width="35%" ><?php echo str_replace('_', ' ', $from_wh_name[$i]); ?></td>


                <td><?php echo $soh[$i]; ?></td>
                <td><?php echo $amc[$i]; ?></td>
                <td><?php echo $mos[$i]; ?></td>
                <td><?php echo $os_qty[$i]; ?></td>
                
                 <td width="35%" align="center"><i class="fa fa-arrow-right " style="color:black !important;"></i> </td>
<td><?php echo $trs_qty[$i]; ?></td>
                <td  width="35%" ><?php echo str_replace('_', ' ', $to_wh_name[$i]); ?></td>
            </tr>
        <?php $count++;
        
        } ?>
    </tbody>
</table>
