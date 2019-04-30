<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");
$requisitionNum = 'TEMP';
$disabled = (isset($_GET['view']) && $_GET['view'] == 1) ? 'disabled="disabled"' : '';
$year = isset($_REQUEST['year']) ? mysql_real_escape_string($_REQUEST['year']) : '';
$month = isset($_REQUEST['month']) ? mysql_real_escape_string($_REQUEST['month']) : '';
$requisitionTo = isset($_REQUEST['wh_to']) ? mysql_real_escape_string($_REQUEST['wh_to']) : '';
$requisitionFrom = isset($_REQUEST['wh_id']) ? mysql_real_escape_string($_REQUEST['wh_id']) : '';
$consumptionArr = array();
if (isset($_POST['submit'])) {
   //echo '<pre>';print_r($_REQUEST);print_r($_FILES);exit;
   
   
   if(empty($_POST['chk'])){
        $url = 'new_clr_open.php?month='.$_REQUEST['month'].'&year='.$_REQUEST['year'].'&wh_to='.$_REQUEST['wh_id'];
        echo "<script>window.location='$url&err=2'</script>";
   }
    //select query
    //gets
    // Requisition Number
    $qry = mysql_fetch_array(mysql_query("SELECT
                                                MAX(clr_master.requisition_num) AS requisition_num
                                        FROM
                                                clr_master"));
    if (empty($qry['requisition_num'])) {
        $requisitionNum = 'RQ' . date('ym') . str_pad(1, 4, 0, STR_PAD_LEFT);
    } else {
        $requisitionNum = 'RQ' . date('ym') . str_pad((substr($qry['requisition_num'], 6) + 1), 4, 0, STR_PAD_LEFT);
    }
    
    $checked_items = implode(',',$_POST['chk']);
    //select query
    //Check if CLR-6 is already saved
    $check_q= "SELECT
                        COUNT(clr_master.requisition_num) AS Num
                FROM
                        clr_master
                INNER JOIN clr_details ON clr_details.pk_master_id = clr_master.pk_id
                WHERE
                        clr_master.wh_id = " . $requisitionFrom . "
                AND clr_master.date_to = '" . $_POST['date_to'] . "' 
                AND clr_master.approval_status='Pending'  
                AND clr_details.itm_id IN ($checked_items) ";
    //echo $check_q;exit;
    $qry = mysql_fetch_array(mysql_query($check_q));
    
    if ($qry['Num'] == 0 || true) {
        $qry = "INSERT INTO clr_master
                SET
                        requisition_num = '" . $requisitionNum . "',
                        requisition_to = '" . $_POST['requisition_to'] . "',
                        wh_id = '" . $_POST['wh_id'] . "',
                        stk_id = '" . $_POST['stkId'] . "',
                        date_from = '" . $_POST['date_from'] . "',
                        date_to = '" . $_POST['date_to'] . "',
                        requested_by = '" . $_POST['requested_by'] . "',
                        receiving_date = '" . $_POST['receiving_date'] . "',
                        approval_status = 'Hard_Copy',
                        requested_on = NOW()";
        mysql_query($qry);
        $lastInsId = mysql_insert_id();
        
        //inserting in clr_master_log
        $qry2 = "INSERT INTO clr_master_log
                SET
                        master_id = '" . $lastInsId . "',
                        requisition_to = '" . $_POST['requisition_to'] . "',
                        wh_id = '" . $_POST['wh_id'] . "',
                        requested_by = '" . $_POST['requested_by'] . "',
                        log_timestamp = NOW(),
                        approval_status = 'Hard_Copy',
                        user_id = '" . $_SESSION['user_id'] . "',
                        approval_level = 'dist_lvl1' ";
        mysql_query($qry2);
       
        //echo '<pre>';print_r($_REQUEST);
        for ($i = 0; $i < count($_POST['itm_id']); $i++) {
            if(in_array($_POST['itm_id'][$i], $_POST['chk']))
            {
                 $qry = "INSERT INTO clr_details
                    SET
                            pk_master_id = '" . $lastInsId . "',
                            itm_id = '" . $_POST['itm_id'][$i] . "',
                            avg_consumption = '" . ((!empty($_POST['avg_consumption'][$i]))?$_POST['avg_consumption'][$i]:'0') . "',
                            soh_dist = '" . ((!empty($_POST['soh_dist'][$i])?$_POST['soh_dist'][$i]:'0')) . "',
                            soh_field = '" . ((!empty($_POST['soh_field'][$i])?$_POST['soh_field'][$i]:'0')) . "',
                            total_stock = '" . ((!empty($_POST['total_stock'][$i])?$_POST['total_stock'][$i]:'0')) . "',
                            desired_stock = '" . ((!empty($_POST['desired_stock'][$i])?$_POST['desired_stock'][$i]:'0')) . "',
                            replenishment = '" . ((!empty($_POST['replenishment'][$i])?$_POST['replenishment'][$i]:'0')) . "',
                            qty_req_dist_lvl1 = '" . str_replace(',','',$_POST['quantity_requested'][$i]) . "',
                            sale_of_last_3_months = '" . str_replace(',','',((!empty($_POST['sale_of_last_3_months'][$i])?$_POST['sale_of_last_3_months'][$i]:'0'))) . "',
                            sale_of_last_month = '" . str_replace(',','',((!empty($_POST['sale_of_last_month'][$i]))?$_POST['sale_of_last_month'][$i]:'0')) . "',
                            remarks_dist_lvl1 = '" . $_POST['remarks'][$i] . "' ";
                mysql_query($qry);
                //echo $qry;
            }
        }
        //exit;
        
        //upload files
        $file_ext=strtolower(end(explode('.',$_FILES['fileToUpload']['name'])));
        //echo $file_ext;
        $expensions= array("jpeg","jpg","png","xls","xlsx","doc","docx","pdf");
        $errors=array();
         if(in_array($file_ext,$expensions)=== false){
            $errors[]="extension not allowed, please choose a scanned image or pdf file.";
         }

         if($_FILES['fileToUpload']['size'] > 2097152) {
            $errors[]='File size must be less than 2 MB';
         }

         if(empty($errors)==true) {
            $this_file_name = "Req_".$lastInsId.".".$file_ext;
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'],"../../user_uploads/requisitions_attachments/".$this_file_name);
            $qry2 = "UPDATE clr_master
                    SET attachment_name = '".$this_file_name."' WHERE 
                        pk_id = '" . $lastInsId . "'
                        ";
            //echo $qry2;exit;
            mysql_query($qry2);
         }else{
             echo 'Error while uploading file';
            print_r($errors);
            exit;
         }
        //exit;
        
        $temp_a = explode('-',$_POST['date_to']);
        $temp_y = $temp_a[0];
        $temp_m = $temp_a[1];
        if(!empty($_REQUEST['redirect_to'])) $page = $_REQUEST['redirect_to'];
        else $page = 'list_manual_requisitions';
        
        $url = $page.'.php';
        echo "<script>window.location='$url?e=1'</script>";
    } else {
        
        $url = 'new_clr_open.php?' . $_SERVER['QUERY_STRING'];
        echo "<script>window.location='$url&err=0'</script>";
    }
}

if (isset($_REQUEST['district_store'])) {
    $wh_id = $_REQUEST['district_store'];
    
    $qry = "SELECT
				tbl_warehouse.dist_id,
				tbl_warehouse.prov_id,
				tbl_warehouse.stkid,
				tbl_locations.LocName,
				stakeholder.stkname AS MainStk
			FROM
				tbl_warehouse 
			INNER JOIN tbl_locations ON tbl_warehouse.dist_id = tbl_locations.PkLocID
			INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
			WHERE
				tbl_warehouse.wh_id = " .  $wh_id. "
			LIMIT 1 ";
    //query result
    //echo $qry;exit;
    $qryRes = mysql_fetch_array(mysql_query($qry));
    //district id
    $distId = $qryRes['dist_id'];
    //province id
    $provId = $qryRes['prov_id'];
    //stakeholder id
    $stkid = $qryRes['stkid'];
    //district name
    $distName = $qryRes['LocName'];
    //main stakeholder
    $mainStk = $qryRes['MainStk'];
}
?>
<script>
    function printContents() {
        var w = 900;
        var h = screen.height;
        var left = Number((screen.width / 2) - (w / 2));
        var top = Number((screen.height / 2) - (h / 2));
        var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes,left=" + left + ",top=" + top + ",width=" + w + ",height=" + h;
        var printingContents = document.getElementById("printing").innerHTML;
        var docprint = window.open("", "", dispSetting);
        docprint.document.open();
        docprint.document.write('<html><head><title>CLR6</title>');
        docprint.document.write('</head><body onLoad="self.print(); "><center>');
        docprint.document.write(printingContents);
        docprint.document.write('</center></body></html>');
        docprint.document.close();
        docprint.focus();
    }
</script>

    <link rel="stylesheet" type="text/css" href="../../public/assets/global/plugins/select2/select2.css"/>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="init()">
    <div id="loading" style="position:absolute; width:100%; text-align:center; top:300px;"> <img src="../../plmis_img/ajax-loader1.gif" border=3></div>
    <script>
        var ld = (document.all);
        var ns4 = document.layers;
        var ns6 = document.getElementById && !document.all;
        var ie4 = document.all;
        if (ns4)
            ld = document.loading;
        else if (ns6)
            ld = document.getElementById("loading").style;
        else if (ie4)
            ld = document.all.loading.style;

        function init() {
            if (ns4)
            {
                ld.visibility = "hidden";
            } else if (ns6 || ie4)
                ld.display = "none";
        }
    </script> 
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
//include tio_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 

                <!-- BEGIN PAGE HEADER-->
                <div class="row">
                    <div class="row">
                        <div class="col-md-12">
                            <?php
                                if (!isset($_REQUEST['district_store'])) {
                                    ?>
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Enter Manually Received Requisition</h3>
                                </div>
                                <div class="widget-body">
                                    <form name="frm" id="frm" action="" method="get"  >
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?php if (true) { ?>
                                                    <div class="col-md-3">
                                                        <div class="control-group">
                                                            <label>District Store</label>
                                                            <div class="controls1">
                                                                
                                                                <select  name="district_store" id="district_store" style="width:300px" class="form-control1  input-large select2me" data-placeholder="Select...">
                                                                        <option value="">Select</option>
                                                                    <?php
                                                                    $qry = "SELECT 
                                                                                DISTINCT tbl_warehouse.wh_id,
                                                                                tbl_warehouse.wh_name,
                                                                                st.stkname
                                                                            FROM
                                                                                tbl_warehouse
                                                                            INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
                                                                            INNER JOIN stakeholder st ON tbl_warehouse.stkid = st.stkid
                                                                            /*INNER JOIN tbl_wh_data ON tbl_warehouse.wh_id = tbl_wh_data.wh_id*/
                                                                            WHERE
                                                                                stakeholder.lvl = 3 AND
                                                                                 
                                                                                st.is_reporting = 1 AND
                                                                                st.lvl = 1
                                                                            ORDER BY
                                                                                tbl_warehouse.wh_name ASC,
                                                                                tbl_warehouse.stkid ASC
                                                                    ";
                                                                    //echo $qry;exit;
                                                                    $qryRes = mysql_query($qry);
                                                                    while ($row = mysql_fetch_array($qryRes)) {
                                                                        if ($wh_id == $row['wh_id']) {
                                                                            $sel = "selected='selected'";
                                                                        } else {
                                                                            $sel = "";
                                                                        }
                                                                        //populate month combo
                                                                        ?>
                                                                        <option value="<?php echo $row['wh_id']; ?>"<?php echo $sel; ?> ><?php echo $row['wh_name'].' - '.$row['stkname']; ?></option>
                                                                        <?php
                                                                    }
                                                                    ?>
                                                                </select>
                                                                
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                
                                                <div class="col-md-2">
                                                    <div class="control-group">
                                                        <label>Requisition From (Year)</label>
                                                        <div class="controls">
                                                            <select name="year" id="year" required="required" onchange="calc_to_month()" class="form-control input-small">
                                                                <option value="">Select</option>
                                                                <?php
                                                                if(empty($year)) $year = date('Y');
                                                                for ($i = date('Y'); $i >= 2016; $i--) {
                                                                    $sel = ($year == $i) ? 'selected="selected"' : '';
                                                                    //populate year year
                                                                    echo "<option value=\"$i\" $sel>$i</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2">
                                                    <div class="control-group">
                                                        <label>( Month )</label>
                                                        <div class="controls">
                                                            <select name="month" id="month" required="required" onchange="calc_to_month()" class="form-control input-small">
                                                                <option value="">Select</option>
                                                                <?php
                                                                if(empty($month)) $month =date('m');
                                                                for ($i = 1; $i <= 12; $i++) {
                                                                    if ($month == $i) {
                                                                        $sel = "selected='selected'";
                                                                    } else {
                                                                        $sel = "";
                                                                    }
                                                                    //populate month combo
                                                                    ?>
                                                                    <option value="<?php echo $i; ?>"<?php echo $sel; ?> ><?php echo date('F', mktime(0, 0, 0, $i, 1)); ?></option>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-2 hide">
                                                    <div class="control-group">
                                                        <label>To</label>
                                                        <div id="to_month_div">
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                
                                                <div class="col-md-3 hide">
                                                    <div class="control-group">
                                                        <label>Requisitioned To Warehouse</label>
                                                        <div class="controls">
                                                            <select name="wh_to" id="wh_to" required="required" class="form-control input-medium">
                                                                <?php
//select query
//gets
//warehouse id 
//warehouse name
                                                                $qry = "SELECT
                                                                            tbl_warehouse.wh_id,
                                                                            tbl_warehouse.wh_name
                                                                        FROM
                                                                            stakeholder
                                                                        INNER JOIN tbl_warehouse ON stakeholder.stkid = tbl_warehouse.stkofficeid
                                                                        WHERE
                                                                            stakeholder.ParentID IS NULL
                                                                        AND stakeholder.stk_type_id = 0
                                                                        AND stakeholder.lvl = 1
                                                                        AND tbl_warehouse.prov_id = 10
                                                                        AND tbl_warehouse.stkid = 1
                                                                        ORDER BY
                                                                            tbl_warehouse.wh_name ASC";
//query result
                                                                $qryRes = mysql_query($qry);
//fetch result
                                                                while ($row = mysql_fetch_array($qryRes)) {
                                                                    $sel = ($requisitionTo == $row['wh_id']) ? 'selected="selected"' : '';
                                                                    echo "<option value=\"$row[wh_id]\" $sel>$row[wh_name]</option>";
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                                if (!isset($_REQUEST['district_store'])) {
                                                    ?>
                                                    <div class="col-md-2">
                                                        <div class="control-group">
                                                            <label>&nbsp;</label>
                                                            <div class="controls">
                                                                <input type="submit" id="submit" value="Create" class="btn btn-primary" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <input type="hidden" name="redirect_to" value="<?=(isset($_REQUEST['redirect_to'])?$_REQUEST['redirect_to']:'')?>">
                                    </form>
                                </div>
                            </div>
                            <?php
                                }
                            if (isset($_REQUEST['district_store']) ) {
                                //year
                                $year = mysql_real_escape_string($_REQUEST['year']);
                                //month
                                $month = mysql_real_escape_string($_REQUEST['month']);
                                //requisition To 
                                $requisitionTo = mysql_real_escape_string($_REQUEST['wh_to']);
                                //duration From 
                                $durationFrom = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
                                //duration to
                                $durationTo = date('Y-m-d', strtotime("-1 day", strtotime("+3 month", strtotime($durationFrom))));
                                //duration
                                $duration = date('M-Y', strtotime($durationFrom)) . ' to ' . date('M-Y', strtotime($durationTo));
                                //reporting Date 
                                $reportingDate = $year . '-' . str_pad($month, 2, 0, STR_PAD_LEFT) . '-01';
                                //select query
                                
                                //chech if record exists
                                if ($num > 0 || true) {
                                    
                                    $qry_itm = "SELECT
                                                itminfo_tab.itm_name,
                                                stakeholder_item.stkid,
                                                itminfo_tab.method_type,
                                                itminfo_tab.itm_id,
                                                itminfo_tab.itmrec_id
                                                FROM
                                                        stakeholder_item
                                                INNER JOIN itminfo_tab ON stakeholder_item.stk_item = itminfo_tab.itm_id
                                                WHERE
                                                        stakeholder_item.stkid = ".$_SESSION['user_stakeholder1']."
                                                            AND (itm_category = 1  OR itm_id = 30)
                                                ORDER BY
                                                        itminfo_tab.method_rank  ASC,
                                                        itminfo_tab.itm_id ASC
";
                                    $res= mysql_query($qry_itm);
                                    $itm_name_id=$product=$itemIds =$item_id_name=array();
                                    //print_r($_SESSION);
                                    while($row= mysql_fetch_assoc($res))
                                    {
                                        $itm_name_id[$row['itm_name']] = $row['itm_id'];
                                        $product[$row['method_type']][] = $row['itm_name'];
                                        $itemIds[] = $row['itm_id'];
                                        $item_id_name[$row['itm_id']] = $row['itm_name'];
                                    }

                                    if(!empty($SOHFieldArr))
                                    ksort($SOHFieldArr);
                                    //echo '<pre>';print_r($item_id_name);exit;
                                    ?>
                                    <br />
                                    <div id="printing" style="clear:both;margin-top:20px;">
                                        <div style="margin-left:0px !important; width:100% !important;">
                                            <style>
                                                table#myTable{margin-top:20px;border-collapse: collapse;border-spacing: 0; border:1px solid #999;}
                                                table#myTable tr td{font-size:11px;padding:3px; text-align:left; border:1px solid #999;}
                                                table#myTable tr th{font-size:11px;padding:3px; text-align:center; border:1px solid #999;}
                                                table#myTable tr td.TAR{text-align:right; padding:5px;width:50px !important;}
                                                .sb1NormalFont {
                                                    color: #444444;
                                                    font-size: 11px;
                                                    font-weight: bold;
                                                    text-decoration: none;
                                                }
                                               
                                                p{margin-bottom:5px; font-size:11px !important; line-height:1 !important; padding:0 !important;}
                                                table#headerTable tr td{ font-size:11px;}
                                                
                                                
                                                /* Print styles */
                                                @media only print
                                                {
                                                    table#myTable tr th{font-size:8px;padding:3px !important; text-align:center; border:1px solid #999;}
                                                    table#myTable tr td{font-size:8px;padding:3px !important; text-align:left; border:1px solid #999;}
                                                    .cls_print_input{width:inherit}
                                                    .remarks_box{width:inherit}
                                                    #desc{width:500px !important;}
                                                    #doNotPrint{display: none !important;}
                                                    
                                                }
                                            </style>
                                            <div class="well well-dark center">
                                                <b><u>Contraceptive Requisition Form ( Hard Copy Received)</u></b>
                                                <br/>
                                                <b><u><?php echo "For $mainStk District $distName"; ?></u></b>
                                            </div>
                                            <form name="frm" id="frm" method="post" action="" enctype="multipart/form-data">
                                            <div class="well well-dark center">
                                                <table width="400" id="headerTable" align="center">
                                                <tr>
                                                    <td align="left">Requisition Period:</td>
                                                    <td align="left"><?php echo $duration; ?></td>
                                                </tr>
                                                <tr>
                                                    <td align="left">Requisition No: </td>
                                                    <td align="left"><?php echo $requisitionNum; ?></td>
                                                </tr>
                                                <tr>
                                                    <td align="left">Recieving Date:</td>
                                                    <td align="left"><input name="receiving_date" value="<?=date('Y-m-d');?>" class="form-control" type="date" /></td>
                                                </tr>
                                            </table>
                                            </div>
                                            
                                            <div style="clear:both;"></div>
                                            
                                                <table width="40%" id="myTable" cellspacing="0" align="center">
                                                    <tr>
                                                        <td>Product</td>
                                                        <td>Requested Qty</td>
                                                        <td>Remarks (Max : 500 Chars)</td>
                                                    </tr>  
                                                        <tr>
                                                            <?php
                                                            //echo '<pre>';print_r($itemIds);
                                                            //print_r($product);
                                                            $col = '';
                                                            $itm2 =array();
                                                            foreach ($itemIds as $itemId) {
                                                                echo '<tr>';
                                                                    //echo "<td width=\"6%\" style=\"text-align:center !important;\" colspan=" . sizeof($proNames) . ">$proType</td>";
                                                                echo '<td width="40%" class=" td_chk" data-itm-id="'.$itemId.'"  style="text-align:left"> <input type="checkbox" checked="checked" class="prod_chk" id="chk_'.$itemId.'" name="chk['.$itemId.']" value="'.$itemId.'"> <span class="">'.$item_id_name[$itemId].'</span> </td>';
                                                                echo '<td width="20%" class="TAR td_chk\" data-itm-id=\"$itm\"><input type="" style="font-size: 11px;padding:1px 1px !important;text-align:right;" class="form-control input-sm qty cls_print_input" data-id="'.$itemId.'" step="1" min="0" name="quantity_requested[]" value="0" data-orig-val="0" /></td>';    
                                                                echo '<td width="20%" class="TAR td_chk cls_print_input\" data-itm-id=\"$itm\"><textarea rows="1" cols="10"   maxlength="500"class="form-control  input-sm remarks_box" data-id="'.$itemId.'" name="remarks[]"></textarea><span id="msg_'.$itemId.'" class="red"></span></td>';    
                                                        
                                                                echo '</tr>';
                                                                
                                                            }
                                                            //echo '<pre>';print_r($names);exit;
                                                            ?>
                                                        </tr> 
                                                    
                                                    <tr>
                                                        <td>Upload Scanned Requisition Letter (Mandatory*)</td>
                                                        
                                                        <td colspan="2"><input class="btn green" type="file" required name="fileToUpload" id="fileToUpload" accept=".xls,.xlsx,.jpeg, .jpg,.png,.pdf,.doc,.docx, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/msword, application/pdf, image/png,image/jpeg,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" ></td>
                                                    </tr>
                                                     
                                                     
                                                    <?php
                                                    foreach ($itemIds as $itemId) {
                                                        ?>
                                                        <input type="hidden" name="itm_id[]" value="<?php echo $itemId; ?>" />
                                                        <?php
                                                    }
                                                    ?>
                                                    <tr id="doNotPrint">
                                                        <td colspan="<?php echo count($itemIds) + 3; ?>" style="text-align:right; border:none; padding-top:15px;"><input type="hidden"  name="date_from" value="<?php echo $durationFrom; ?>" />
                                                            <input type="hidden"  name="date_to" value="<?php echo $durationTo; ?>" />
                                                            <input type="hidden"  name="requisition_to" value="<?php echo $requisitionTo; ?>" />
                                                            <input type="hidden"  name="wh_id" value="<?php echo $wh_id; ?>" />
                                                            <input type="hidden"  name="requested_by" value="<?php echo $_SESSION['user_id']; ?>" />
                                                            <input type="hidden"  name="stkId" value="<?php echo $stkid; ?>" />
                                                            <input id="submit_btn" type="submit" name="submit" value="Save" class="btn btn-primary" style="display:none;"/>
                                                            </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </form>
                                            
                                        </div>
                                    </div>
                                    <?php
                                } else {
                                    echo "No record found.";
                                }
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- END FOOTER -->
    <?php
//include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
    
    <script type="text/javascript" src="../../public/assets/global/plugins/select2/select2.min.js"></script>

    <?php
    if (isset($_REQUEST['err']) && $_REQUEST['err'] == '0') {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'CLR-6 of the items you selected already exists. Either edit OR delete the existing CLR',
                type: 'error',
                layout: self.data('layout')
            });
            
        </script>
    <?php }
    if (isset($_REQUEST['err']) && $_REQUEST['err'] == '2') {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: 'Please select atleast one product to create CLR-6.',
                type: 'error',
                layout: self.data('layout')
            });
            
        </script>
    <?php }
    ?>
 
        <script>
            
            $(function() {
                
                //$('#aaa').select2();
                $('.qty').priceFormat({
                    prefix: '',
                    thousandsSeparator: ',',
                    suffix: '',
                    centsLimit: 0,
                    limit: 10,
                    clearOnEmpty: false
                });
            })
            
            $('.qty').change(function(){
                //$.this = $(this);
                calc_total_req_val();
            });
          
          function calc_total_req_val(){
                var sum = 0;
                $('.qty').each(function() {
                    var a = $(this).val();
                    a = a.replace(/,/g, '');
                    sum += Number(a);
                });
                
                if(sum > 0 )
                    $('#submit_btn').show();
                else
                    $('#submit_btn').hide();
            }
            
            $('.prod_chk').click(function(){
                var v = $(this).val();
                var ch = $(this).attr('checked');
                if(ch == 'checked'){
                    $('.td_chk[data-itm-id='+v+']').attr('bgcolor','');
                    
                    $('.qty[data-id='+v+']').attr('readonly',false);
                    $('.remarks_box[data-id='+v+']').attr('readonly',false);
                }
                else
                {
                    $('.td_chk[data-itm-id='+v+']').attr('bgcolor','#eeeeee');
                    
                    $('.qty[data-id='+v+']').attr('readonly',true);
                    $('.qty[data-id='+v+']').val('0');
                    $('.remarks_box[data-id='+v+']').val('');
                    $('.remarks_box[data-id='+v+']').attr('readonly',true);
                    $('.remarks_box[data-id='+v+']').attr('required',false);
                }
                
                calc_total_req_val();
                //alert('value:'+v+',cehck:'+ch);
            });
            
            function calc_to_month(){
                
                var a = $('#year').val() + '-' +$('#month').val() +'-01';
                var d = new Date( a );
                d.setMonth( d.getMonth( ) + 2 );
                var m = d.getMonth( ) ;
                var months = [ "January", "February", "March", "April", "May", "June", 
                               "July", "August", "September", "October", "November", "December" ];

                var selectedMonthName = months[m];

                var z = selectedMonthName + ' - ' + d.getFullYear( );
                console.log(z);
                $('#to_month_div').html(z);
            }
            
        </script>
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>