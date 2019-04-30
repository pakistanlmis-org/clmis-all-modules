<?php
include("../includes/classes/AllClasses.php");
?>
<html>
<body class="page-header-fixed page-quick-sidebar-over-content" >
    <div class="page-container">
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">


                        <div class="widget">
                            <div class="widget-body">
                                <?php include('sub_dist_reports.php'); ?>
                                <div class="row"><br></div>
                                <?php
                                $qry = "SELECT
                                                itminfo_tab.itm_name,
                                                stakeholder.stkname,
                                                stakeholder_item.brand_name,
                                                stakeholder_item.quantity_per_pack,
                                                stakeholder_item.carton_per_pallet,
                                                stakeholder_item.stk_id,
                                                stakeholder_item.pack_length,
                                                stakeholder_item.pack_width,
                                                stakeholder_item.pack_height,
                                                stakeholder_item.net_capacity,
                                                stakeholder_item.gross_capacity
                                            FROM
                                                itminfo_tab
                                            INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
                                            INNER JOIN stakeholder ON stakeholder_item.stkid = stakeholder.stkid
                                            WHERE
                                                stakeholder.stk_type_id = 3
                                            ORDER BY
                                                itminfo_tab.frmindex ASC,
                                                stkname,
                                                stakeholder_item.quantity_per_pack ASC
                                            ";
//                                    print_r($qry);
//                                    exit;
                                $res = mysql_query($qry);
                                $num = mysql_num_rows($res);
                                if ($num > 0) {
                                    ?>
                                    <table style="width:95%;margin-left: 2%;" align="center"   id="myTable" class="table table-striped table-bordered table-condensed">
                                        <thead style="background-color:lightgray">
                                            
                                            <tr>
                                                <th >#</th>
                                                <th >Product</th>
                                                <th >Manufacturer</th>
                                                <th >Brand</th>
                                                <th >Qty in One Carton</th>
                                                <th >Cartons in One Pallet</th>
                                                <th >Pack Length</th>
                                                <th >Pack Width</th>
                                                <th >Pack Height</th>
                                                <th >Net Capacity</th>
                                                <th >Gross Capacity</th>
                                            </tr>
                                        </thead>
                                        <?php
                                        $counter = 1;
                                        while ($row = mysql_fetch_assoc($res)) {
                                            ?>
                                            <tbody>

                                                <tr>
                                                    <td><?php echo $counter++; ?></td>
                                                    <td><?php echo $row['itm_name']; ?></td>
                                                    <td><?php echo $row['stkname']; ?></td>
                                                    <td><?php echo $row['brand_name']; ?></td>
                                                    <td><?php echo $row['quantity_per_pack']; ?></td>
                                                    <td><?php echo $row['carton_per_pallet']; ?></td>
                                                    <td><?php echo $row['pack_length']; ?></td>
                                                    <td><?php echo $row['pack_width']; ?></td>
                                                    <td><?php echo $row['pack_height']; ?></td>
                                                    <td><?php echo $row['net_capacity']; ?></td>
                                                    <td><?php echo $row['gross_capacity']; ?></td>
                                                </tr>
                                            </tbody>
                                        <?php 
                                        }
                                        ?>
                                    </table>
                                <?php } else {
                                    ?><div style="margin-left: 15px;"><label> <?php echo 'No record found'; ?>  </label> </div><?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- END FOOTER -->

</body>
<!-- END BODY -->
</html>