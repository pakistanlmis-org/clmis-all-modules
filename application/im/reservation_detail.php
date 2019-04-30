<?php

include("../includes/classes/AllClasses.php");

include(PUBLIC_PATH . "html/header.php");
$id = $_REQUEST['id'];
?>

<div class="page-content" style="">
<div class="container" >
<div class="widget widget-tabs" style="">    
    <div class="widget-body" >
        
        <div class="text-center">Details of reserved quantity</div>
        <div  style="">
            <table class="table table-striped table-hover table-condensed">
                    <thead style="font-size: 10px">
                        <tr>
                            <th>#</th>
                            <th>District</th>
                            <th>Requisition From</th>
                            <th width="20%">Product</th>
                            <th>Reserved Quantity</th>
                            <th width="">Requisition Number</th>
                            <th>Requisition Status</th>
                        </tr>
                </thead>
                <tbody  style="font-size: 10px">
                    <?php
                $qry = "SELECT
                                (clr_details.qty_req_prov) AS reserved,
                                clr_details.itm_id,
                                clr_master.pk_id,
                                clr_master.requisition_num,
                                clr_master.requisition_to,
                                clr_master.wh_id,
                                clr_master.stk_id,
                                clr_master.date_from,
                                clr_master.date_to,
                                clr_master.approval_status,
                                tbl_warehouse.wh_name,
                                tbl_warehouse.dist_id,
                                tbl_warehouse.prov_id,
                                tbl_locations.LocName AS dist_name,
                                itminfo_tab.itm_name
                            FROM
                                clr_master
                            INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                            INNER JOIN tbl_warehouse ON clr_master.wh_id = tbl_warehouse.wh_id
                            INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
                            INNER JOIN itminfo_tab ON clr_details.itm_id = itminfo_tab.itm_id
                            WHERE
                                clr_master.approval_status in ('Prov_Approved','Approved') AND
                                tbl_warehouse.prov_id = ".$_SESSION['user_province1']." AND
                                tbl_warehouse.stkid = ".$_SESSION['user_stakeholder1']."
                                AND clr_details.itm_id = $id  ";
                $qryRes = mysql_query($qry);    
    
                    $c=1;
                    $total=0;
                    while($row = mysql_fetch_assoc($qryRes))
                    {  
                        
                        if($row['approval_status'] == 'Prov_Approved')
                         $st='Approved by '.(isset($_SESSION['user_stakeholder_name'])?$_SESSION['user_stakeholder_name']:'Procurement Stakeholder');    
                        
                        if($row['approval_status'] == 'Approved')
                         $st='Distribution Plan Submitted';  
                         
                         
                         echo '<tr>
                                <td>'.$c++.'</td>
                                <td>'.$row['dist_name'].'</td>
                                <td>'.$row['wh_name'].'</td>
                                <td>'.$row['itm_name'].'</td>
                                <td style="text-align:right">'.number_format($row['reserved']).'</td>
                                <td><a href="clr_view.php?id='.$row['pk_id'].'&wh_id='.$row['wh_id'].'">'.$row['requisition_num'].'</a></td>
                                <td>'.$st.'</td>
                                
                            </tr>';
                        $total+=$row['reserved'];

                    }
                    echo '<tr>
                                <td> <b>Total</b></td>
                                <td style="text-align:right" colspan="4"><b>'.number_format($total).'</b></td>
                                <td colspan="2"> </td>
                                
                            </tr>';
                    ?>   
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
</div>