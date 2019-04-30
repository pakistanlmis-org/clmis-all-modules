<?php
/**
 * Unlock Data Entry
 * @package Admin
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//Including AllClasses
include("../includes/classes/AllClasses.php");
//Including header
include(PUBLIC_PATH . "html/header.php");
 //echo '<pre>';print_r($_SESSION);exit;
//Un-lock data entry
//Checking wh_id
if (isset($_POST['wh_id']) && !empty($_POST['wh_id'])) {
    //$_SESSION['temp_e'] = $_REQUEST;
    //$_SESSION['temp_a'] = $_REQUEST;
    //echo '<pre>';print_r($_REQUEST);exit;
    //Getting wh_id
    $wh_id = mysql_escape_string($_POST['wh_id']);
    //Getting de_months
    $editable_data_entry_months = mysql_escape_string($_POST['de_months']);
    //Explode
    $all_ids = explode(',', $wh_id);
    foreach ($all_ids as $id) {
        //Checking id
        if (strpos($id, 'w_') !== false) {
            $wh_id_arr[] = substr($id, 2);
        }
    }

    $lock_de = "0";
    if(!empty($_REQUEST['mode']) && $_REQUEST['mode']=='lock')  $lock_de = "1";
    //Update query
    $qry = "UPDATE tbl_warehouse
			SET 
				is_lock_data_entry = $lock_de,
				editable_data_entry_months = $editable_data_entry_months
			WHERE
				tbl_warehouse.wh_id IN (" . implode(',', $wh_id_arr) . ")";
    //Query result
    mysql_query($qry);
    //Displat messahe
    $_SESSION['err']['text'] = 'Data has been successfully updated.';
    exit;
}
//Gets
//stk_id
//prov_id
$qry = "SELECT
			user_stk.stk_id,
			user_prov.prov_id
		FROM
			user_stk
		JOIN user_prov ON user_stk.user_id = user_prov.user_id
		WHERE
			user_stk.user_id = " . $_SESSION['user_id'];
//Query result
$qryRes = mysql_query($qry);
$arr['stk'] = array();
$arr['prov'] = array();
//Getting result
while ($row = mysql_fetch_array($qryRes)) {
    //Checki stk_id
    if (!in_array($row['stk_id'], $arr['stk'])) {
        $arr['stk'][] = $row['stk_id'];
    }
    //Checking prov_id
    if (!in_array($row['prov_id'], $arr['prov'])) {
        $arr['prov'][] = $row['prov_id'];
    }
}
$and = '';
//$and .= (!empty($arr['stk'])) ? " AND tbl_warehouse.stkid IN (" . implode(',', $arr['stk']) . ")" : '';
//$and .= (!empty($arr['prov'])) ? " AND province.PkLocID IN (" . implode(',', $arr['prov']) . ")" : '';
$and .= ($_SESSION['user_level']>1) ? " AND province.PkLocID = " . $_SESSION['user_province1'] ." " : '';
$and .= ($_SESSION['user_level']>1) ? " AND tbl_warehouse.stkid  = " . $_SESSION['user_stakeholder1'] ." " : '';

//Gets
//prov_id
//prov_name
//dist_id
//dist_name
//stkname
//wh_id
//wh_name
$qry = "SELECT
			province.PkLocID AS prov_id,
			province.LocName AS prov_name,
			district.PkLocID AS dist_id,
			district.LocName AS dist_name,
			stakeholder.stkname,
			tbl_warehouse.wh_id,
			CONCAT(tbl_warehouse.wh_name , ' (', stakeholder.stkname, ')') AS wh_name,
			stakeholder.lvl
		FROM
			tbl_warehouse
		INNER JOIN tbl_locations AS district ON tbl_warehouse.dist_id = district.PkLocID
		INNER JOIN tbl_locations AS province ON tbl_warehouse.prov_id = province.PkLocID
		INNER JOIN stakeholder ON tbl_warehouse.stkofficeid = stakeholder.stkid
		LEFT JOIN tbl_hf_type ON tbl_warehouse.hf_type_id = tbl_hf_type.pk_id
		LEFT JOIN tbl_hf_type_rank ON tbl_hf_type_rank.stakeholder_id = tbl_warehouse.stkid
		AND tbl_hf_type_rank.province_id = province.PkLocID
		AND tbl_hf_type_rank.hf_type_id = tbl_hf_type.pk_id
		WHERE
			tbl_warehouse.is_lock_data_entry = 1
			$and
		ORDER BY
			prov_id ASC,
			dist_name ASC,
			tbl_hf_type_rank.hf_type_rank ASC,
			tbl_warehouse.wh_name ASC";
//echo $qry;exit;
//Query result
$qryRes = mysql_query($qry);
$num = mysql_num_rows($qryRes);
$data_arr = array();
//Getting results
while ($row = mysql_fetch_array($qryRes)) {
    if (!in_array($row['prov_id'], $loc_name)) {
        $loc_name[$row['prov_id']] = $row['prov_name'];
    }
    if (!in_array($row['dist_id'], $loc_name)) {
        $loc_name[$row['dist_id']] = $row['dist_name'];
    }
    $data_arr[$row['prov_id']][$row['dist_id']][$row['wh_id']] = $row['wh_name'];
}
//xml

//end xml
$xmlfile_path = 'tree.xml';
?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content" onLoad="loadLocked()">
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
        //Including menu
        include $_SESSION['menu'];
        //Including top_im 
        include PUBLIC_PATH . "html/top_im.php";
        ?>

        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="page-title row-br-b-wp">Unlock Data Entry For Stores/Facilities</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Locked Store/Facility List</h3>
                            </div>
                            <div class="widget-body">
                                
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-6">
                                                <font color="#009C00">Check the checkboxes against Stores/Facilities to allow access to data entry</font>
                                                <div id="treeboxbox_tree" style="width:100%; height:350px;background-color:#f5f5f5;border :1px solid Silver; overflow:auto;"></div>
                                            </div>
                                            <div class="col-md-6" style="padding-top:20px;">
                                                <div class="row col-md-12">
                                                    <a href="javascript:void(0);" onClick="myTree.openAllItems(0);">Expand all</a><br>
                                                    <a href="javascript:void(0);" onClick="myTree.closeAllItems(0);">Collapse all</a><br>
                                                </div>
                                                <div class="row col-md-12">
                                                    <input name="show_fac" class="" type="radio" checked="checked" onclick="loadLocked_2()" value="locked"><label>Show locked facilities</label>
                                                </div>
                                                <div class="row col-md-12">
                                                    <input name="show_fac"  class="" type="radio" onclick="loadUnlocked_2()"  value="un_locked"><label>Show un-locked facilities</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div id="show_loader"><img src="<?php echo PUBLIC_URL; ?>assets/img/loading-spinner-blue.gif" alt="Loading"></div>
                                            </div>
                                            <div class="col-md-2" id="editable_mon">
                                                <div class="control-group">
                                                    <label>Editable Data Month</label>
                                                    <div class="controls">
                                                        <input type="text" maxlength="2" name="editable_data_entry_months" id="editable_data_entry_months" class="form-control input-small" value="1" />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 right">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <button id="submit" class="btn green">Unlock</button>
                                                        <input type="hidden" id="mode" name="mode" value="unlock">
                                                    </div>
                                                </div>

                                            </div>
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
    //Including footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>

    <link rel="stylesheet" type="text/css" href="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxTree/codebase/dhtmlxtree.css"/>
    <script src="<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxTree/codebase/dhtmlxtree.js"></script>
    <script>
                                                var myTree;
                                                function loadLocked() {
                                                    
                                                    myTree = new dhtmlXTreeObject("treeboxbox_tree", "100%", "100%", 0);
                                                    myTree.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxTree/codebase/imgs/dhxtree_skyblue/");
                                                    myTree.enableCheckBoxes(1);
                                                    myTree.enableTreeImages("false");
                                                    myTree.enableThreeStateCheckboxes(true);
                                                    myTree.setOnLoadingStart(func_a);
                                                    myTree.setOnLoadingEnd(func_b);
                                                    //myTree.loadXMLString('<?php //echo $xml_string; ?>');
                                                    //myTree.load("xml/tree.xml");
                                                    myTree.load("xml_locked_wh.php");
                                                    
                                                    $('#submit').html('Unlock');
                                                }
                                                function loadUnlocked() {
                                                    
                                                    myTree = new dhtmlXTreeObject("treeboxbox_tree", "100%", "100%", 0);
                                                    myTree.setImagePath("<?php echo PUBLIC_URL; ?>dhtmlxGrid/dhtmlxTree/codebase/imgs/dhxtree_skyblue/");
                                                    myTree.enableCheckBoxes(1);
                                                    myTree.enableTreeImages("false");
                                                    myTree.enableThreeStateCheckboxes(true);
                                                    myTree.setOnLoadingStart(func_a);
                                                    myTree.setOnLoadingEnd(func_b);
                                                    //myTree.loadXMLString('<?php //echo $xml_string; ?>');
                                                    myTree.load("xml_unlocked_wh.php");
                                                    
                                                    $('#submit').html('Lock');
                                                    
                                                    
                                                    
                                                }
                                                
                                                function loadLocked_2() {
                                                    $('#treeboxbox_tree').html('');
                                                    $('#mode').val('unlock');
                                                    $('#editable_mon').removeClass('hide');
                                                    loadLocked();
                                                    
                                                    
                                                }
                                                function loadUnlocked_2() {
                                                    $('#treeboxbox_tree').html('');
                                                    $('#mode').val('lock');
                                                    $('#editable_mon').addClass('hide');
                                                    loadUnlocked();
                                                }
                                                
                                                function func_a() {
                                                    $('#show_loader').show();
                                                }
                                                function func_b() {
                                                    $('#show_loader').hide();
                                                }
    </script>
    <?php
    if (isset($_SESSION['err'])) {
        ?>
        <script>
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: '<?php echo $_SESSION['err']['text']; ?>',
                type: 'success',
                layout: self.data('layout')
            });
        </script>
        <?php
        unset($_SESSION['err']);
    }
    ?>
    <script>
        $(function() {
            $('#submit').click(function(e) {
                var dataStr = myTree.getAllChecked();
                var de_months = parseInt($('#editable_data_entry_months').val());
                var mode_1 = $('#mode').val();
                if (dataStr.length > 0)
                {
                    if (de_months < 1 || de_months > 2)
                    {
                        alert('Please enter value between 1 or 2');
                        return false;
                    }

                    $('#submit').attr('disabled', 'disabled');
                    $.ajax({
                        url: 'unlock-data-entry.php',
                        data: {wh_id: dataStr, de_months: de_months , mode:mode_1},
                        type: 'POST',
                        success: function(response) {
                            $('#submit').removeAttr('disabled');
                            document.location.reload();
                        }
                    })
                }
                else
                {
                    alert('Please select at-least one Store/Facility');
                    $('#submit').removeAttr('disabled');
                    return false;
                }
            });
        })
    </script>
</body>
</html>