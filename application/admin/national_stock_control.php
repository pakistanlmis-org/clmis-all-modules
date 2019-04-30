<?php

//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");

     $qry = " SELECT
                    stakeholder.stkid,
                    stakeholder.stkname
                FROM
                    stakeholder
                WHERE
                    stakeholder.stk_type_id  in (1,4) AND
                    stakeholder.lvl = 1
                ";
        $qryRes = mysql_query($qry);
        $stk_arr  =  array();
        while ($row = mysql_fetch_assoc($qryRes)) {
            $stk_arr[$row['stkid']] = $row['stkname'];
        }
        
        $qry = " SELECT
                tbl_locations.PkLocID,
                tbl_locations.LocName
                FROM
                tbl_locations
                WHERE
                tbl_locations.LocLvl = 2
                ";
        $qryRes = mysql_query($qry);
        $prov_arr  =  array();
        while ($row = mysql_fetch_assoc($qryRes)) {
            $prov_arr[$row['PkLocID']] = $row['LocName'];
        }
        //echo '<pre>';print_r($stk_arr);print_r($prov_arr);exit;
        $qry = " SELECT
                    national_stock_control.pk_id,
                    national_stock_control.stkid,
                    national_stock_control.provid,
                    national_stock_control.checked,
                    national_stock_control.date_from
                    FROM
                    national_stock_control
                    order by 
                    national_stock_control.date_from 
                ";
        $qryRes = mysql_query($qry);
        $disp_arr  =  array();
        while ($row = mysql_fetch_assoc($qryRes)) {
            $disp_arr[$row['date_from']][$row['stkid']][$row['provid']] = $row['checked'];
        }
        //echo '<pre>';print_r($disp_arr);exit;
?>
</head>

<body class="page-header-fixed page-quick-sidebar-over-content">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php include PUBLIC_PATH . "html/top.php"; ?>
        <?php include PUBLIC_PATH . "html/top_im.php"; ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 
                <form action="national_stock_control_action.php">
                <div class="row">
                    <div class="col-md-12">
                      
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Enter New Rule for USAID Supported Stock</h3>
                            </div>
                            
                            <div id="bdy" class="widget-body">
                                <table id="email_control_table" class="table table-condensed table-hover" style="table-layout:fixed">
                                    <tr>
                                        <td width="10%">NGOs/Province</td>
                                        <?php
                                        foreach($prov_arr as $prov_id => $prov_name)
                                        {
                                            echo '<td width="10%">'.$prov_name.'</td>';
                                        }
                                        ?>
                                    </tr>
                                <?php
                                $old_prov=$old_stk = '';

                                foreach($stk_arr as $stk_id =>$stk_name )
                                {
                                    echo '<tr>';
                                    echo '<td>'.$stk_name.'</td>';
                                    foreach($prov_arr as $prov_id => $prov_name)
                                        {
                                            echo '<td width="10%"><input '.(($prov_id=='10')?' checked ':'').' name="checkbox_'.$stk_id.'_'.$prov_id.'" type="checkbox"></td>';
                                        }
                                    echo '</tr>';
                                }

                                ?>
                                </table>
                                <div class="control-group">
                                    <div class="">
                                        <div class="controls right">
                                            <input type="date" name="date" id="date"  class="" value="<?=date('Y-m-d')?>" />
                                            <input type="submit" name="submit" id="go" value="Save" class="btn btn-primary " />
                                                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                           
                        </div>
                      
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Existing Rules of USAID Supported Stock</h3>
                            </div>
                            
                            <div class="widget-body">
                                
                                <?php
                                foreach($disp_arr as $date => $date_data)
                                {
                                    echo '<div class="row center"><h3>Rule of stock from :'.date('Y-M-d',strtotime($date)).'</h3></div>';
                                ?>
                                
                                <table id="email_control_table" class="table table-condensed table-hover table-bordered" style="table-layout:fixed">
                                   
                                <?php
                                

                                ?>
                                    <tr>
                                        <td width="10%">NGOs/Province</td>
                                        <?php
                                        foreach($prov_arr as $prov_id => $prov_name)
                                        {
                                            echo '<td width="10%">'.$prov_name.'</td>';
                                        }
                                        ?>
                                    </tr>
                                <?php
                                $old_prov=$old_stk = '';

                                foreach($stk_arr as $stk_id =>$stk_name )
                                {
                                    echo '<tr>';
                                    echo '<td>'.$stk_name.'</td>';
                                    foreach($prov_arr as $prov_id => $prov_name)
                                        {
                                            $a='';
                                            if(!empty($date_data[$stk_id][$prov_id]))
                                                $a = '<i class="fa fa-check" style="color:green !important;"></i>';
                                            echo '<td width="10%">'.$a.'</td>';
                                        }
                                    echo '</tr>';
                                }
                                ?>
                                </table>
                                <?php
                                }
                                ?>
                                
                                
                            </div>
                            
                           
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- END FOOTER -->
    <?php include PUBLIC_PATH . "/html/footer.php"; ?>

</body>
<!-- END BODY -->
</html>