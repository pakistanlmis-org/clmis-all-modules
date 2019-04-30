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
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="doInitGrid()">
    <div class="page-container">
        <?php
//include top_im 
        include $_SESSION['menu'];
        include PUBLIC_PATH . "html/top_im.php";
        
        
        
        if(isset($_REQUEST['do']) && $_REQUEST['do']=='Add')
        {
         ?>
        <div class="page-content-wrapper">
            <div class="page-content">

            <form id="frm1" name="frm1" action="email_control_action.php" method="post">
                    <div class="row">
                        <div class="col-md-12">

                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Add new person against a stakeholder in email list</h3>
                                </div>
                                <div class="widget-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            
                                            
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stk_sel" id="stk_sel" required class="form-control input-sm" onchange="hide_prov()">
                                                            <option value="all">All</option>
                                                            <option value="1">PWD</option>
                                                            <option value="145">IRMNCH</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="control-group">
                                                    <label>Province</label>
                                                    <div class="controls">
                                                        <select name="prov_sel" id="prov_sel" required class="form-control input-sm" >
                                                           <?php
                                                            $queryprov = "SELECT
                                                                            tbl_locations.PkLocID AS prov_id,
                                                                            tbl_locations.LocName AS prov_title
                                                                        FROM
                                                                            tbl_locations
                                                                        WHERE
                                                                            LocLvl = 2 ";
                                                            $rsprov = mysql_query($queryprov) or die();
                                                            while ($rowprov = mysql_fetch_array($rsprov)) {
                                                               
                                                                //populate prov_sel
                                                                ?>
                                                                <option value="<?php echo $rowprov['prov_id']; ?>" ><?php echo $rowprov['prov_title']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Office</label>
                                                    <div class="controls">
                                                        <input required maxlength="150" name="office" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Name</label>
                                                    <div class="controls">
                                                        <input required maxlength="150" name="person_name" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Email Address</label>
                                                    <div class="controls">
                                                        <input required maxlength="150" type="email" name="email_address" placeholder="abc@gmail.com" class="form-control form-control-sm">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-1">
                                            <div class="control-group">
                                                <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="submit" name="submit" id="go" value="Add" class="btn btn-primary " />
                                                        
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>
                
            </div>
       </div>
                <?php
        }
        else
        {
        
        $selPro = '';
        $selStk = '';

        
        $qry = "SELECT
                    email_actions.pk_id,
                    email_actions.action_name,
                    email_actions.description
                FROM
                    email_actions
                ORDER BY
                    email_actions.pk_id

                ";
        //query result
        $qryRes = mysql_query($qry);
        $actions_arr =  array();
        while ($row = mysql_fetch_assoc($qryRes)) {
            $actions_arr[$row['pk_id']] = $row['action_name'];
        }

        $colspan = 5+(count($actions_arr));
        $qry = " SELECT
                    email_actions.action_name,
                    email_actions.pk_id,
                    email_persons_list.pk_id as person_pk_id,
                    email_persons_list.person_name,
                    email_persons_list.designation,
                    email_persons_list.office_name,
                    email_persons_list.email_address,
                    email_persons_list.stkid,
                    email_persons_list.prov_id,
                    tbl_locations.LocName,
                    (case when (email_persons_list.stkid = 'all') THEN 'ALL' ELSE stakeholder.stkname END) as stkname
                FROM
                    email_persons_list 
                    LEFT JOIN email_bridge ON email_persons_list.pk_id = email_bridge.person_id
                    LEFT JOIN email_actions ON email_actions.pk_id = email_bridge.action_id
                    LEFT JOIN tbl_locations ON email_persons_list.prov_id = tbl_locations.PkLocID
                    LEFT JOIN stakeholder ON email_persons_list.stkid = stakeholder.stkid
                ORDER BY
                    email_persons_list.pk_id

                ";
        //query result
        $qryRes = mysql_query($qry);
        //num of record
        $num = mysql_num_rows($qryRes);
        //fetch results
        $full_arr =$disp_arr  =  array();
        while ($row = mysql_fetch_assoc($qryRes)) {
            $full_arr[] = $row;
            //if(!empty($row['pk_id']))
            
            $disp_arr[$row['LocName']][$row['stkname']][$row['person_pk_id']]['person_name'] = $row['person_name'];
            $disp_arr[$row['LocName']][$row['stkname']][$row['person_pk_id']]['office_name'] = $row['office_name'];
            $disp_arr[$row['LocName']][$row['stkname']][$row['person_pk_id']]['email_address'] = $row['email_address'];
            $disp_arr[$row['LocName']][$row['stkname']][$row['person_pk_id']]['designation'] = $row['designation'];
            $disp_arr[$row['LocName']][$row['stkname']][$row['person_pk_id']]['actions'][$row['pk_id']] = 'enabled';
        }
        ksort($actions_arr);
        //echo '<pre>';print_r($actions_arr);exit;
        //echo '<pre>';print_r($disp_arr);print_r($actions_arr);exit;
        ?>
        
        
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">

                <?php
                if(!empty($disp_arr))
                {
                ?>
                <form id="frm1" name="frm1" action="email_control_action.php" method="post">
                <div class="row">
                    <div class="col-md-12">
                      
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Manage Emails - Enable/ Disable</h3>
                            </div>
                            
                            <div class="widget-body">
                                <table id="email_control_table" class="table table-condensed table-hover" style="table-layout:fixed">
                                    <tr>
                                        <th width="10%">Province</th>
                                        <th width="10%">Stakeholder</th>
                                        <th width="10%">Office</th>
                                        <th width="20%">Name</th>
                                        <th width="20%">Email Address</th>
                                        
                                        <?php
                                        foreach($actions_arr as $k=>$action_name)
                                        {
                                            echo '<th width="10%">'.$action_name.'</th>';
                                        }
                                        ?>
                                    </tr>
                                <?php
                                $old_prov=$old_stk = '';

                                foreach($disp_arr as $prov_name => $prov_data)
                                {
                                    foreach($prov_data as $stk_name => $stk_data)
                                    {
                                        foreach($stk_data as $person_id => $row)
                                        {
                                            if( $prov_name != $old_prov  ||  $stk_name !=$old_stk)
                                            {
                                                echo '<tr>';
                                                echo '<th colspan="'.$colspan.'" class="success">'.$prov_name.' - '.$stk_name.'</th>';
                                                echo '</tr>';
                                            
                                                $old_stk  = $stk_name;
                                                $old_prov = $prov_name;
                                            }
                                            
                                            echo '<tr data-person-id="'.$person_id.'">';
                                            echo '<td>'.(isset($prov_name)?$prov_name:'').'</td>';
                                            echo '<td>'.(isset($stk_name)?$stk_name:'').'</td>';
                                            echo '<td style="word-wrap:break-word">'.$row['office_name'].'</td>';
                                            echo '<td style="word-wrap:break-word">'.$row['person_name'].'</td>';
                                            echo '<td style="word-wrap:break-word">'.$row['email_address'].'</td>';
                                             foreach($actions_arr as $k=>$action_name)
                                                {
                                                    //echo '<th>'.$row['actions'][$k].'</th>';
                                                    echo '<td><input class="checkboxes " name="enable['.$person_id.'_'.$k.']" type="checkbox" '.(( isset($row['actions'][$k]) && $row['actions'][$k] == 'enabled')?' checked':'').'></td>';
                                                }
                                            echo '</tr>';
                                            
                                        }
                                        
                                    
                                    
                                    }
                                    
                                }

                                ?>
                                </table>
                                <div class="control-group">
                                    <div class="">
                                        <div class="controls right">
                                            <input type="submit" name="submit" id="go" value="Save" class="btn btn-primary " />
                                            <a id="add_new_btn" href="email_control.php?do=Add" class="btn btn-circle btn-lg green" style=" position: fixed;top: 190px;right: 20px; "><i class="fa fa-plus" aria-hidden="true"></i></a>
                                                    
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