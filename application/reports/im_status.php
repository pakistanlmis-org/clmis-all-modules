<?php
ini_set('max_execution_time', 0);
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$rptId = 'clr15';
if (empty($stakeholder))
    $stakeholder = (!empty($_SESSION['user_stakeholder1']) ? $_SESSION['user_stakeholder1'] : '1');

?>
</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        //include top
        include PUBLIC_PATH . "html/top.php";
        //include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Inventory Management (IM) Enabled / Disabled </h3>


                    </div>
                </div>
                <?php
                $qryStk = "SELECT 
                    stakeholder.stkid,
                    stakeholder.stkname AS stakeholder
                    FROM
                    stakeholder
                    WHERE
                    stakeholder.lvl = 1 AND
                    stakeholder.is_reporting = 1 AND
                    stakeholder.stk_type_id = 0
                    AND stakeholder.stkid <74
                    ";
                $qry = "SELECT  
                        stakeholder.stkid,
                        stakeholder.stkname AS stakeholder,
                        tbl_locations.LocName AS districts,
                        tbl_warehouse.is_allowed_im,
                        tbl_warehouse.im_start_month,
                        tbl_warehouse.dist_id
                        FROM
                        stakeholder
                        INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkid
                        INNER JOIN tbl_locations ON tbl_locations.PkLocID = tbl_warehouse.dist_id
                        INNER JOIN stakeholder AS st ON tbl_warehouse.stkofficeid = st.stkid
                        WHERE
                        stakeholder.stk_type_id = 0 AND
                        stakeholder.is_reporting = 1 AND
                        st.lvl = 3 AND
                        tbl_warehouse.wh_type_id IS NOT NULL AND
                        tbl_warehouse.is_allowed_im IS NOT NULL  AND 
                         tbl_warehouse.wh_type_id NOT LIKE ''
                        ORDER BY
                        tbl_warehouse.prov_id,
                        tbl_locations.LocName ASC


                        ";

                $qryRes = mysql_query($qry);
                $data_arr = $date_of_im = array();
                while ($row = mysql_fetch_assoc($qryRes)) {
                    $data_arr[$row['dist_id']][$row['stkid']] = $row['is_allowed_im'];
                    $date_of_im[$row['dist_id']][$row['stkid']] = $row['im_start_month'];
                }



                $qryLoc = "SELECT 
                        tbl_locations.LocName AS districts,
                        tbl_locations.PkLocID
                        FROM
                        tbl_locations
                        WHERE
                        tbl_locations.ParentID NOT LIKE 10
                        ORDER BY
                        tbl_locations.ParentID,
                        tbl_locations.LocName ASC
                        ";
                $qryResStk = mysql_query($qryStk);
                $qryResLoc = mysql_query($qryLoc);
                $stk = array();
                $loc = array();
                //  $row = mysql_num_rows(mysql_query($qry));
                ?><table class="table table-bordered table-condensed" width="100%">

                    <th>
                        Districts ID
                    </th>
                    <th>
                        Districts
                    </th>
                    <?php
                    while ($rowst = mysql_fetch_array($qryResStk)) {
                        ?>

                        <th>
                            <?php echo $rowst['stakeholder'] ?> 
                        </th>

                        <?php
                        $stk[$rowst['stkid']] = $rowst['stkid'];
                    }
                    //  print_r($stk);exit;
                    while ($rowloc = mysql_fetch_array($qryResLoc)) {
                        $loc[$rowloc['PkLocID']] = $rowloc['districts'];
                        ?>
                        <tr>
                            <td>
                                <?php echo $rowloc['PkLocID'] ?>
                            </td>
                            <td align="left">
                                <?php echo $rowloc['districts'] ?>
                            </td>
                            <?php
                            foreach ($stk as $key => $value) {
                                ?>
                                <td align="center">
                                <?php
                                if (isset($data_arr[$rowloc['PkLocID']][$key]) && $data_arr[$rowloc['PkLocID']][$key] == 1) {
                                    echo '<span style="font-size:20px" class="glyphicon glyphicon-ok green"></span>';
                                    if(!empty($date_of_im[$rowloc['PkLocID']][$key]))
                                    echo '( '.date('M-Y',strtotime($date_of_im[$rowloc['PkLocID']][$key])).' )';
                                } else {
                                    echo '<span  class="glyphicon glyphicon-remove red"></span>';
                                }

                                echo '</td>';
                            }
                            ?>

                        </tr>
                                <?php
                            }
                            ?>                        

                </table>



<?php
//include footer
include PUBLIC_PATH . "/html/footer.php";
//include combos
include ('combos.php');
?>

                </body>
                </html>