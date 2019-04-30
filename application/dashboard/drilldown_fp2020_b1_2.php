<?php

ini_set('max_execution_time', 0);
//Including files
include("../includes/classes/Configuration.inc.php");
include(APP_PATH . "includes/classes/db.php");
include(PUBLIC_PATH . "/FusionCharts/Code/PHP/includes/FusionCharts.php");
$subCaption = '';
//echo '<pre>';print_r($_SESSION);exit;
//System settings 

$userSql = "SELECT
            system_settings.pk_id,
            system_settings.setting_name,
            system_settings.setting_value
            FROM
            system_settings
";
//echo $userSql;exit;
$userResult = mysql_query($userSql) or die("Error " . $userSql);
$system_settings = array();
while($row = mysql_fetch_assoc($userResult)){
    $system_settings[$row['setting_name']] = $row['setting_value'];
}
//echo '<pre>';print_r($system_settings);exit;
//End systems settings 


$prov_name = $_REQUEST['prov_name'];
$province = $_REQUEST['province'];
$from_date = date("Y-m-d", strtotime($_REQUEST['from_date']));
//$to_date = date("Y-m-d", strtotime($_REQUEST['to_date']));
$stakeholder = $_REQUEST['stakeholder'];
$stk_name = $_REQUEST['stk_name'];
$prod_id = $_REQUEST['prod_id'];
$prod_name = $_REQUEST['prod_name'];
?>
<div class="portlet portlet-light ">
    <?php
    $qry2 = "SELECT
                mosscale_tab.shortterm,
                mosscale_tab.longterm,
                mosscale_tab.sclstart,
                mosscale_tab.sclsend,
                mosscale_tab.colorcode,
                mosscale_tab.stkid,
                mosscale_tab.lvl_id,
                itminfo_tab.itm_id
            FROM
                mosscale_tab
            INNER JOIN itminfo_tab ON mosscale_tab.itmrec_id = itminfo_tab.itmrec_id
            WHERE
                mosscale_tab.lvl_id = 4 AND
                mosscale_tab.stkid = 1 AND
                itminfo_tab.itm_id = 1
            ";
    $qryRes2 = mysql_query($qry2);
    $legend = '<table class="table table-condensed table-bordered">';
    $legend .= '<tr>';
    $legend .=' <td id="show_all" style="display:none;" ><div class="btn btn-sm blue-hoki">Show All</div></td>';
    $legend .= '<td>';
    $legend .= '<span> MOS Legend :</span>';
    $legend .= '</td>';
    while ($row2 = mysql_fetch_assoc($qryRes2)) {
        $legend .= '<td><div class="legend_block" data-clr="' . $row2['colorcode'] . '" style="display:inline-block;width:15px; height:15px; vertical-align: bottom; background-color:' . $row2['colorcode'] . ';margin-left:20px;"></div>';
        $legend .= ' <div class="legend_block" data-clr="' . $row2['colorcode'] . '" style="display:inline-block;margin-left:5px;">' . $row2['longterm'] . '</div></td>';
    }
    $legend .= '</tr>';
    $legend .='</table>';

    $qry = "
            SELECT
              summary_district.district_id,
              tbl_locations.LocName,
              Province.LocName as province_name,
              stakeholder.stkname,
              itminfo_tab.itmrec_id,
	      summary_district.soh_district_store,
	      summary_district.soh_district_store / summary_district.avg_consumption as mos,
(
	SELECT
			GROUP_CONCAT(sysuser_tab.sysusr_email)
			FROM
			sysuser_tab
			INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
			INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
			tbl_warehouse.dist_id = summary_district.district_id AND
			stakeholder.lvl = 3 AND
			tbl_warehouse.stkid = summary_district.stakeholder_id
			AND sysuser_tab.user_level = 3
			AND ( sysusr_email <> '' )
			AND sysuser_tab.sysusr_type <> 23
            ) as email,
            (
                    SELECT
			GROUP_CONCAT(sysuser_tab.sysusr_ph)
			FROM
			sysuser_tab
			INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
			INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
			INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
			WHERE
			tbl_warehouse.dist_id = summary_district.district_id AND
			stakeholder.lvl = 3 AND
			tbl_warehouse.stkid = summary_district.stakeholder_id
			AND sysuser_tab.user_level = 3
			AND ( sysuser_tab.sysusr_ph > 0 OR sysuser_tab.sysusr_cell > 0 )
			AND sysuser_tab.sysusr_type <> 23
            ) as cell_no,
            (
                    SELECT
                            sysuser_tab.UserID
                    FROM
                            sysuser_tab
                    INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
                    INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                            tbl_warehouse.dist_id = summary_district.district_id
                    AND stakeholder.lvl = 3
                    AND tbl_warehouse.stkid = summary_district.stakeholder_id
                    AND sysuser_tab.user_level = 3
                    
                    AND sysuser_tab.sysusr_type <> 23
                    limit 1
            ) AS user_id,
            (
                    SELECT
                            tbl_warehouse.wh_id
                    FROM
                            sysuser_tab
                    INNER JOIN wh_user ON sysuser_tab.UserID = wh_user.sysusrrec_id
                    INNER JOIN tbl_warehouse ON wh_user.wh_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                            tbl_warehouse.dist_id = summary_district.district_id
                    AND stakeholder.lvl = 3
                    AND tbl_warehouse.stkid = summary_district.stakeholder_id
                    AND sysuser_tab.user_level = 3
                    AND sysuser_tab.sysusr_type <> 23
                    LIMIT 1
            ) AS wh_id,
            (SELECT
                        count(alerts_log.pk_id) as history_count
                    FROM
                        alerts_log
                    INNER JOIN tbl_warehouse ON alerts_log.warehouse_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                       alerts_log.interface = 'fp2020DashboardcLMIS' AND
                    tbl_warehouse.stkid = summary_district.stakeholder_id AND
                    tbl_warehouse.dist_id = summary_district.district_id
                    AND stakeholder.lvl = 3) as sent_count,
                    
            (
                  SELECT
                            count(moderation.pk_id) as history_count
                    FROM
                    moderation
                    INNER JOIN tbl_warehouse ON moderation.warehouse_id = tbl_warehouse.wh_id
                    INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                    WHERE
                       moderation.interface = 'fp2020DashboardcLMIS' AND
                       moderation.status <> 'sent' AND
                    tbl_warehouse.stkid = summary_district.stakeholder_id AND
                    tbl_warehouse.dist_id = summary_district.district_id
                    AND stakeholder.lvl = 3
                   
            ) AS pending_count
            FROM
                      summary_district
              INNER JOIN tbl_locations ON summary_district.district_id = tbl_locations.PkLocID
              INNER JOIN tbl_locations AS Province ON tbl_locations.ParentID = Province.PkLocID
              INNER JOIN stakeholder ON summary_district.stakeholder_id = stakeholder.stkid
              INNER JOIN itminfo_tab ON summary_district.item_id = itminfo_tab.itmrec_id
            WHERE
              summary_district.reporting_date = '" . $from_date . "' AND
              stakeholder.stkid = $stakeholder
              

              AND itminfo_tab.itm_category = 1
              AND summary_district.item_id='" . $prod_id . "'
           ";
    if($province!='all')
    $qry .= " AND Province.PkLocID = $province ";
    $qry .= "  ORDER BY Province.PkLocID,tbl_locations.LocName ";
    //echo $qry;exit;
    $qryRes = mysql_query($qry);
    $c = 1;
    $itm_arr = $so_arr = array();

    echo '<div class="row">';
    echo '<div class="well well-dark ">';
    echo '<div class=" ">';
    
    echo '<h3 align="center">MOS at districts of ';
    echo ' <b><span class="btn yellow-gold">' . $prov_name . '  - ' . $stk_name . '  - ' . $prod_name . '</span></b>';
    echo ' for ' . date('M-Y', strtotime($from_date)) . '</h3>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    echo ' <div class="row">';
    echo '     <div class="col-md-12 center">';
    echo $legend;
    echo '     </div>';
    echo ' </div>';

    echo '<div class="row">';
    echo '<div class=" "  style=" height: 430px; overflow-y: scroll;">';
    echo '<table width="70%"  class="table table-condensed table-striped custom_sortable ">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>Sr No  <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
    echo '<th>Province <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
    echo '<th>District <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
    echo '<th>Stakeholder <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
    echo '<th width="6%">SOH<i class="fa fa-sort" style="color:#000 !important;"></i></th>';
    echo '<th width="10%">Months of Stock (MOS) <i class="fa fa-sort" style="color:#000 !important;"></i></th>';
    echo '<th> </th>';
    echo '<th> </th>';
    echo '<th style="align:center;">Contact</th>';
    echo '</tr>';
     echo '</thead>';
     echo '<tbody style="overflow:scroll;" class="custom_sortable_body">';
    $c = 1;
    while ($row = mysql_fetch_assoc($qryRes)) {
        $mos_1 = ((isset($row['mos']) && $row['mos'] > 0) ? number_format($row['mos'], 2) : '0');

        $q_mos      = "SELECT getMosColor('" . $mos_1 . "', '" . $row['itmrec_id'] . "','" . $stakeholder . "',3)";
        $rs_mos     = mysql_query($q_mos);
        $bgcolor    = mysql_result($rs_mos, 0, 0);
        $cell       = $row['cell_no'];
        $email      = $row['email'];
        $user_id    = $row['user_id'];
        $wh_id      = $row['wh_id'];

        
        //$cell=$email='';

        echo '<tr class="t_rows" data-clr="'.$bgcolor.'">';
        echo '<td>' . $c++ . '</td>';
        echo '<td>' . $row['province_name'] . '</td>';
        echo '<td>' . $row['LocName'] . '</td>';
        echo '<td>' . $row['stkname'] . '</td>';
        echo '<td align="right">' . number_format($row['soh_district_store']) . '</td>';
        echo '<td align="right">' . $mos_1 . '</td>';
        echo '<td><div style="width:10px; height:12px; background-color:' . $bgcolor . ';" title=""></div></td>';
        echo '<td>';
        echo ' <a  class="pull-left " onclick="window.open(\'contact_history.php?wh_id='.$wh_id.'\', \'_blank\', \'scrollbars=1,width=800,height=500\');"><i class="fa fa-history" style="color:black !important;padding-top:5px;font-size:25px;"></i> </a>';
        
        $row['history_count'] =  $row['pending_count'] + $row['sent_count'];
        if(!empty($row['history_count']) && $row['history_count']>0)
        {
            echo ' <span class="badge badge-warning" style="position:relative;left: -5px;">'.$row['history_count'].'</span>';
        }
        echo '</td>';

        echo '<td>';
        echo ' <div class="btn-group btn-group-circle btn-group-sm btn-group-solid">';
        
        $deny_access =false;
        
//        $_SESSION['user_role'] == '16' 
//        || $_SESSION['user_role'] == '20' 
//        || $_SESSION['user_level'] == '' 
//        || $_SESSION['user_level'] == '0'  
//        || $_SESSION['user_level'] == NULL  
//        || $_SESSION['user_level'] > 2 
        
        
        if( $_SESSION['user_level'] > 2 )
        {
            $deny_access =true;
        }
        
        if(empty($email) || $system_settings['dashboard_email'] !='active' || $deny_access)
            echo ' <button type="button" class="btn  grey-gallery "   ><i class="fa fa-envelope"></i> Email</button>';
        else
            echo ' <button type="button" class="btn green-jungle "  onclick="window.open(\'send_email.php?user_id='.$user_id.'\', \'_blank\', \'scrollbars=1,width=600,height=600\');"><i class="fa fa-envelope"></i> Email</button>';
        
        if(empty($cell)|| $system_settings['dashboard_sms'] !='active' || $deny_access)
            echo ' <button type="button" class="btn grey-gallery"   ><i class="fa fa-comment"></i> Message</button>';
        else
            echo ' <button type="button" class="btn green"  onclick="window.open(\'send_sms.php?user_id='.$user_id.'\', \'_blank\', \'scrollbars=1,width=600,height=500\');"><i class="fa fa-comment"></i> Message</button>';
        
        if(empty($cell)|| $system_settings['dashboard_call'] !='active' || $deny_access){
            //echo ' <button type="button" class="btn grey-gallery" id="call_' . $cell . '" ><i class="fa fa-phone"></i> <span class="">Call</span></button>';
            $cell = '923331512197';
            $token = base64_encode($cell."|".$wh_id);
            ?>
<!--    <button type="button" class="btn grey-gallery" onclick="window.open('https://webcall.ntc.net.pk:8443?p=<?php echo $token; ?>', 
                         'LMIS Call', 
                         'width=600,height=400');"  ><i class="fa fa-phone"></i> <span class="">Call</span></button>-->
    <button type="button" class="btn grey-gallery" onclick="window.open('call-api.php?t=<?php echo $token; ?>', 
                         'LMIS Call', 
                         'width=600,height=400');"  ><i class="fa fa-phone"></i> <span class="">Call</span></button>
    <?php
        } else {
            $cell = '923331512197';
            $token = base64_encode($cell."|".$wh_id);
            ?>
    <button type="button" class="btn grey-gallery" onclick="window.open('call-api.php?t=<?php echo $token; ?>', 
                         'LMIS Call', 
                         'width=600,height=400');"><i class="fa fa-phone"></i> <span class="">Call</span></button>
    <?php
            //echo ' <button type="button" class="btn grey-gallery" id="call_' . $cell . '" ><i class="fa fa-phone"></i> <span class="">Call</span></button>';
        }
            
        echo ' </div>';
        echo '</td>';
        
        
        //echo '<td>'.$row['email'].'</td>';
        //echo '<td>'.$row['cell_no'].'</td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo ' </div>';
    echo ' </div>';
    ?>
</div>

<div id="stack1" class="modal fade" tabindex="-1" data-width="400" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
                <h4 class="modal-title">Stack One</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <h4>Some Input</h4>
                        <p><input type="text" class="col-md-12 form-control"></p>
                        <p><input type="text" class="col-md-12 form-control"></p>
                        <p><input type="text" class="col-md-12 form-control"></p>
                        <p><input type="text" class="col-md-12 form-control"></p>
                        <p><input type="text" class="col-md-12 form-control"></p>
                        <p><input type="text" class="col-md-12 form-control"></p>
                        <p><input type="text" class="col-md-12 form-control"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <input class="btn btn-primary" type="submit" value="Save">
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo PUBLIC_URL; ?>js/custom_table_sort_2.js" type="text/javascript"></script>
<script>
    $('button[id^="call_"]').click(function () {
        var id = $(this).attr('id');
        var arr = id.split('_');
        $('.' + arr[1]).html(arr[1]);
    });
</script>