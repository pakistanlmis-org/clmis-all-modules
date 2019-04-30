<?php
//include Configuration
include("../includes/classes/Configuration.inc.php");
Login();
//include db
include(APP_PATH . "includes/classes/db.php");
//include functions
include APP_PATH . "includes/classes/functions.php";
include("mos_cwh.php");
//include FusionCharts
include(PUBLIC_PATH . "FusionCharts/Code/PHP/includes/FusionCharts.php");

//whId
$whId = $_SESSION['user_warehouse'];
$data_1 = '';
$province = (!empty($_POST['prov_sel']) ? $_POST['prov_sel'] : '');
$stk_arr = (!empty($_POST['stakeholder']) ? $_POST['stakeholder'] : '');
$itm_arr_request = (!empty($_POST['product']) ? $_POST['product'] : '');
$rep_date = $_POST['from_date'] . '-01';
$hf_arr = [2, 7, 73];
 $msd_cent = '';
if($province==1)
{
    if (count($stk_arr) >= 1) {
           if ($stk_arr == [1, 2, 7, 73] || $stk_arr == [1, 2] || $stk_arr == [1, 2, 7] || $stk_arr == [1, 7] || $stk_arr == [1, 73]) {

        $msd_cent = "MSD Lahore/Central Warehouse";
    } else {
        foreach ($stk_arr as $key => $value) {
            if ($value == 1) {
                $msd_cent = "Central Warehouse";
            }
            else if (in_array($value, $hf_arr)) {
                $msd_cent = "MSD Lahore";
            } 
            else if($value!=1&&$value!=2&&$value!=7&&$value!=73){
                $msd_cent = "Central Warehouse";
            }
        }
    }
}
}
else
{
    $msd_cent="Central Warehouse";
    
}
$itemId = 1;
?>


<div class="widget widget-tabs">
     <div class="widget-body" id="a4" style='background-color: white;'>
        <ul class="list-inline panel-actions" style='float: right;' >
                        <li><a   id="panel-fullscreen_a4" role="button" title="Toggle fullscreen"><i class="glyphicon glyphicon-resize-full"></i></a></li>
                    </ul>
        <?php
        //query
//        print_r($itm_name_id);
//        exit;
        foreach ($itm_name_id as $key_itm => $value_itm) {
//        print_r($mos_chart_array);exit;
            foreach ($mos_chart_array as $key => $value) {
                if($value<0)
                {
                    $value=0;
                }
                if ($value_itm == $key) {
                    $data_1 .= "<set color='#2ecc71' label='" . $key_itm . "' value=' " .  round($value,1) . " ' />";
                }
            }
        }
        $xmlstore = "<chart showLegend='1' theme='fint'   yaxismaxvalue='60'  exportEnabled='1' exportAction='Download' caption='$msd_cent Months of Stock' exportFileName='District Store Months of Stock" . date('Y-m-d H:i:s') . "'>";
        $xmlstore .= $data_1;
        $xmlstore .= "<trendlines>
        <line startvalue='6'  color='#FF0000' valueonright='1' displayvalue='Min:6' />
                <line startvalue='18'  color='#FF0000' valueonright='1' displayvalue='Max:18' />

    </trendlines>";
        $xmlstore .= "</chart>";
        FC_SetRenderer('javascript');
        echo renderChart(PUBLIC_URL . "FusionCharts/Charts/Column2D.swf", "", $xmlstore, 'central_warehouse_mos', '100%', 450, false, false);
        ?>
    </div>
</div>
<script>
$(document).ready(function () {
    //Toggle fullscreen
    $("#panel-fullscreen_a4").click(function (e) {
        e.preventDefault();
//        console.log('into js');
        var $this = $(this);
    
        if ($this.children('i').hasClass('glyphicon-resize-full'))
        {
            $this.children('i').removeClass('glyphicon-resize-full');
            $this.children('i').addClass('glyphicon-resize-small');
        }
        else if ($this.children('i').hasClass('glyphicon-resize-small'))
        {
            $this.children('i').removeClass('glyphicon-resize-small');
            $this.children('i').addClass('glyphicon-resize-full');
        }
        $(this).closest('div').toggleClass('panel-fullscreen');
    });
});


</script>