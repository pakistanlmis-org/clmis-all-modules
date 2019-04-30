<?php
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
?>

<style>
    .nav-pills > li > a:hover,.nav-pills > li.active > a{
        color: #FFFFFF !important;
        background-color: #009C00 !important;
    } 
    .nav-pills > li > a{
        color: #000 !important;
        background-color: #FFFFFF !important;
    }
    .grid_input_number{
        padding: 0px !important;
        font-size: 11px !important;
    }
</style>

</head>
<!-- END HEAD -->
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="modal"></div>
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
                        <h3 class="page-title row-br-b-wp">  
                            Forecasting List
                            <?php include("back_include.php"); ?>
                        </h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Forecasting List</h3>
                            </div>
                            <div class="widget-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <table class="table table-bordered table-condensed"f>
                                            <tr>
                                                <td>#</td>
                                                <td>Purpose</td>
<!--                                                <td>Category</td>-->
                                                <td>Stakeholders</td>
                                                <td>Location</td>
                                                <td>Products</td>
                                                <td>Forecasting Years</td>
                                                <td>Forecasting</td>
                                                <td>View</td> 
                                                <td>Quantification</td>
                                            </tr>
                                            <?php
                                            $qry = "SELECT
                                            fq_master_data.pk_id,
                                            fq_master_data.base_year,
                                            fq_master_data.start_year,
                                            fq_master_data.end_year,
                                            fq_master_data.purpose,
                                            fq_master_data.source,
                                            fq_master_data.stakeholders,
                                            (SELECT GROUP_CONCAT(stkname) from stakeholder where FIND_IN_SET(stkid,fq_master_data.stakeholders ) ) as stks,
                                            fq_master_data.province_id,
                                            fq_master_data.forecasting_methods,
                                            fq_master_data.item_group,
                                            tbl_locations.LocName, 
                                            (SELECT LocName from tbl_locations where PkLocID = fq_master_data.district_id ) as dist_name,
                                            fq_quantification_child.pk_id as quant_id,
                                            GROUP_CONCAT(distinct itminfo_tab.itm_name) as prods,
                                            fq_fp_products_forecasting.pk_id as fc_saved
                                        FROM
                                            fq_master_data
                                        INNER JOIN tbl_locations ON fq_master_data.province_id = tbl_locations.PkLocID 
                                        LEFT JOIN fq_quantification_child ON fq_master_data.pk_id = fq_quantification_child.quantification_master_id
                                        LEFT JOIN fq_fp_products_data ON fq_fp_products_data.master_id = fq_master_data.pk_id
                                        LEFT JOIN itminfo_tab ON fq_fp_products_data.prod_id = itminfo_tab.itm_id
                                        LEFT JOIN fq_fp_products_forecasting ON fq_fp_products_forecasting.fp_product_key = fq_fp_products_data.pk_id
                                        WHERE
                                            fq_master_data.created_by = '".$_SESSION['user_id']."'
                                        GROUP BY
                                            fq_master_data.pk_id
                                ";
                                            //query result
                                            //echo $qry;
                                            $rsQry = mysql_query($qry) or die();
                                            //fetch result
                                            $c = 1;
                                            while ($row = mysql_fetch_array($rsQry)) {
                                                echo '<tr>';
                                                echo '<td>' . $c++ . '</td>';
                                                echo '<td><a href="forecasting_master_demo.php?master_id=' . $row['pk_id'] . '">' . $row['purpose'] . '</a></td>';

//                                                if ($row['item_group'] == 'to3')
//                                                    echo '<td class="font-blue1">Family Planning</td>';
//                                                else
//                                                    echo '<td class="green">MNCH</td>';

                                                echo '<td>' . $row['stks'] . '</td>';
                                                echo '<td>' . $row['LocName'];
                                                if(!empty($row['dist_name']))
                                                echo ' - <b>'.$row['dist_name'].'</b>';
                                                echo '</td>';
                                                echo '<td class="'.(!empty($row['prods'])?'':' danger ').'">' . $row['prods'] . '</td>';
                                                echo '<td>' . $row['start_year'] . '-' . substr(($row['start_year'] + 1), 2) . ' <i class="fa fa-arrow-right" style="color:#000 !important;"></i>';
                                                echo ' ' . $row['end_year'] . '-' . substr(($row['end_year'] + 1), 2) . '</td>';


                                                echo '<td>';
                                                if(!empty($row['prods']))
                                                {
                                                    if ($row['item_group'] == 'to3') {
                                                        if (true) {
                                                            //echo ' <a class="btn btn-xs '.(isset($row['fc_saved'])?'green-haze':'yellow-gold').'" href="forecasting_adjustment.php?id='.$row['pk_id']. '"><i class="fa fa-table"></i>  Calculator</a>'.(isset($row['fc_saved'])?'<i class="fa fa-check font-green"></i>':'');
                                                            echo ' <a class="btn btn-xs ' . (isset($row['fc_saved']) ? 'green-haze' : 'yellow-gold') . '" href="forecasting_adjustment_demo.php?id=' . $row['pk_id'] . '"><i class="fa fa-table"></i>  Calculator</a>' . (isset($row['fc_saved']) ? '<i class="fa fa-check font-green"></i>' : '');
                                                        }
                                                    } else {
                                                        echo ' <a class="btn btn-xs blue-madison" href="mnch_forecasting_calculator.php?id=' . $row['pk_id'] . '"><i class="fa fa-table"></i>  Calculator</a>';
                                                    }
                                                }
                                                echo '</td>';
                                                echo '<td>';
                                                if ($row['item_group'] == 'to3' && isset($row['fc_saved']))
                                                    echo '<a href="forecasting_view.php?id=' . $row['pk_id'] . '"><i class="fa fa-folder-open font-blue"></i></a>';
                                                echo '</td>';

                                                echo '<td>';
                                                if ($row['item_group'] == 'to3' && isset($row['fc_saved']))
                                                    echo ' <a class="btn btn-xs ' . (isset($row['quant_id']) ? 'green-haze' : 'yellow-gold') . '" href="quantification.php?forecasted_id=' . $row['pk_id'] . '" ><i class="fa fa-table"></i> Quantification</a>' . (isset($row['quant_id']) ? '<i class="fa fa-check font-green"></i>' : '');
                                                echo '</td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </table>

                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <a class="btn btn-sm green-jungle" href="forecasting_master_demo.php?product_category=fp"><i class="fa fa-plus"></i> Add New Forecasting Master Data</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
</body>
</html>