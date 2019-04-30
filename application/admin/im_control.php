<?php
/**
 * 
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");

include(PUBLIC_PATH . "html/header.php");

?>
<style>
    #go:hover{
        background-color: blue; 
        opacity: 0.5; 
    }
    #go:hover:after{
         content:'ABC';
    }
</style>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <div class="page-container">
        <?php
//include top_im 
        include $_SESSION['menu'];
        include PUBLIC_PATH . "html/top_im.php";
        
        
        
        if(true)
        {
        
        $selPro = '';
        $selStk = '';


        $qry="SELECT
stakeholder.stkid,
stakeholder.stkname
FROM
stakeholder
WHERE
stakeholder.lvl = 1 AND
stakeholder.is_reporting = 1 AND
stakeholder.stk_type_id = 0 AND
stakeholder.stkid <= 73
";
        $qryRes = mysql_query($qry);
        $num = mysql_num_rows($qryRes);
        $stk_arr =  array();
        $total_stk = 0;
        while ($row = mysql_fetch_assoc($qryRes)) {
            $stk_arr[$row['stkid']] = $row['stkname'];
            $total_stk++;
        }
       

        $qry = " SELECT
prov.LocName as prov_name,
prov.PkLocID as prov_id,
dist.PkLocID as dist_id,
dist.LocName as dist_name,
im_control.im_enabled,
im_control.last_updated,
stakeholder.stkname,
stakeholder.stkid
FROM
tbl_locations AS prov
INNER JOIN tbl_locations AS dist ON dist.ParentID = prov.PkLocID
LEFT JOIN im_control ON dist.PkLocID = im_control.dist_id
LEFT JOIN stakeholder ON im_control.stk_id = stakeholder.stkid
WHERE
dist.LocLvl = 3
ORDER BY
prov.PkLocID ASC,
dist.LocName ASC,
stakeholder.stkname ASC

                ";
        $qryRes = mysql_query($qry);
        $num = mysql_num_rows($qryRes);
        $disp_arr  = $prov_arr = $dist_arr =  array();
        while ($row = mysql_fetch_assoc($qryRes)) {
            $disp_arr[$row['prov_id']][$row['dist_id']][$row['stkid']] = $row['im_enabled'];
            $prov_arr[$row['prov_id']] = $row['prov_name'];
            $dist_arr[$row['dist_id']] = $row['dist_name'];
        }
        //echo '<pre>';print_r($disp_arr);print_r($actions_arr);exit;
        ?>
        <div class="page-content-wrapper">
            <div class="page-content">

                <?php
                if(!empty($disp_arr))
                {
                ?>
                <form id="frm1" name="frm1" action="im_control_action.php" method="post">
                <div class="row">
                    <div class="col-md-12">
                      
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Control IM Module - Enable/ Disable</h3>
                            </div>
                            
                            <div class="widget-body">
                                <table id="email_control_table" class="table table-condensed table-hover" style="table-layout:fixed">
                                    <tr>
                                        <th width="10%">Province</th>
                                        <th width="10%">District</th>
                                        <th width="10%">Stakeholder</th>
                                        <th width="10%">Enable / Disable</th>
                                    </tr>
                                <?php
                                $old_prov=$old_stk = '';
                                $used_dist = array();
                                foreach($disp_arr as $prov_id => $prov_data)
                                {
                                    foreach($prov_data as $dist_id => $dist_data)
                                    {
                                        foreach($stk_arr as $stk_id => $stk_name)
                                        {
                                            $prov_name = $prov_arr[$prov_id];
                                            if( $prov_name != $old_prov   )
                                            {
                                                echo '<tr>';
                                                echo '<th colspan="4" class="success">'.$prov_name.'</th>';
                                                echo '</tr>';
                                            
                                                $old_prov = $prov_name;
                                            }
                                            
                                            echo '<tr >';
                                            echo '<td>'.(isset($prov_name)?$prov_name:'').'</td>';
                                            if(!in_array($dist_id,$used_dist))
                                            {
                                                echo '<td rowspan="'.$total_stk.'">'.($dist_arr[$dist_id]).'</td>';
                                                $used_dist[$dist_id]=$dist_id;
                                            }
                                            echo '<td>'.($stk_arr[$stk_id]).'</td>';
                                            echo '<td><input class="checkboxes " name="enable['.$dist_id.'_'.$stk_id.']" type="checkbox" '.(( isset($dist_data[$stk_id]) && $dist_data[$stk_id] == '1')?' checked':'').'></td>';
                                            echo '</tr>';
                                            
                                        }
                                        
                                    
                                    
                                    }
                                    
                                }

                                ?>
                                </table>
                                <div class="control-group">
                                    <div class="">
                                        <div class="controls right">
                                            <input type="submit" name="submit" id="go" value="Save" class="btn btn-primary " style=" position: fixed;top: 190px;right: 20px; "/>
                                            <a id="add_new_btn" href="email_control.php?do=Add" class="hide btn btn-circle btn-lg green" style=" position: fixed;top: 190px;right: 20px; "><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                    
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                           
                        </div>
                    </div>
                </div>
                    <input type="hidden" name="stk_id"      value="<?=$selStk?>">
                    <input type="hidden" name="prov_id"     value="<?=$selPro?>">
                </form>
                    <?php
                    }
                    ?>
            </div>
        </div>
    </div>

    <?php 
        }
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <?php include PUBLIC_PATH . "/html/reports_includes.php"; ?>

    <script>
    $('.checkboxes').click(function(e) {
        $(this).attr("style","outline:2px solid #4FB366 !important;");
        $(this).parent().css('background-color', '#DFEBE2');
    })
    
    function hide_prov()
    {
        var a = $('#stk_sel').val();
        //alert(a);
        if(a=='all')
        {
            $('#prov_sel option').hide();
            $('#prov_sel option[value="10"]').show();
            $('#prov_sel').val('10');
        }
        else
        {
            $('#prov_sel option').show();
            $('#prov_sel option[value="10"]').hide();
            $('#prov_sel').val('1');
        }
    }
    hide_prov();
    </script>
 <?php
    if (isset($_SESSION['err'])) {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: '<?php echo $_SESSION['err']['text']; ?>',
                type: '<?php echo $_SESSION['err']['type']; ?>',
                layout: self.data('layout')
            });
        </script>
        <?php
        unset($_SESSION['err']);
    }
    ?>
    
</body>
<!-- END BODY -->
</html>