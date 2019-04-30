<?php
/**
 * placement_locations
 * @package im
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
//include AllClasses
include("../includes/classes/AllClasses.php");
//include header
include(PUBLIC_PATH . "html/header.php");
//get warehouse id
$wh_id = $_SESSION['user_warehouse'];
//select query
//gets
//pk id
//location name
//warehouse id
//status
//quantity
$qry = "SELECT DISTINCT
			placement_config.pk_id,
			placement_config.location_name,
			placement_config.warehouse_id,
			placement_config.status,
			SUM(placements.quantity) AS qty
		FROM
			placement_config
		LEFT JOIN placements ON placement_config.pk_id = placements.placement_location_id
		WHERE
			placement_config.warehouse_id = $wh_id
		GROUP BY
			placement_config.pk_id
		ORDER BY
			placement_config.location_name";
//query result
$getPlacementLocation = mysql_query($qry) or die("Err Get Placement Location");
?>
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-quick-sidebar-over-content" >
    <!-- BEGIN HEADER -->
    <div class="page-container">
        <?php
//include top
        include PUBLIC_PATH . "html/top.php";
        ?>
        <?php
//include top_im
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Manage Locations (Bins)</h3>
                            </div>
                            <div class="widget-body">
                                <?php
                                //check id			
                                if (isset($_GET['id'])) {
                                    //select query
                                    //get Placement Congfig
                                    $getPlacementCongfig = mysql_query("select * from placement_config where pk_id=" . $_GET['id']) || (die(mysql_error()));
                                    //query result
                                    $resPlacement = mysql_fetch_assoc($getPlacementCongfig);
                                    ?>
                                    <form method="POST" name="new_receive" id="new_receive" action="placement_locations_action.php" >
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3"> 
                                                    <!-- Group Receive No-->
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Area <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="area" id="area" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets area
                                                                //pk id 
                                                                //list value
                                                                $getArea = mysql_query("SELECT
																			list_detail.pk_id,
																			list_detail.list_value
																			FROM
																			list_master
																			INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																			WHERE
																			list_master.pk_id = 14") or die("ERR Get Area");
                                                                //fetch result							
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    // populate area combo
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>" <?php if ($resPlacement['area'] == $rowArea['pk_id']) {
                                                                echo "selected=selected";
                                                            } ?>><?php echo $rowArea['list_value']; ?></option>
    <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- // Group END Receive No-->
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Row <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="row" id="row" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets area
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																					list_master.pk_id = 15") or die("ERR Get Area");
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    //populate row combo
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>" <?php if ($resPlacement['row'] == $rowArea['pk_id']) {
                                                                echo "selected=selected";
                                                            } ?>><?php echo $rowArea['list_value']; ?></option>
    <?php } ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Rack <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="rack" id="rack" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets area
                                                                //pk id
                                                                //list value
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																					list_master.pk_id = 16") or die("ERR Get Area");
                                                                //fetch results							
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    //populate rack combo
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>" <?php if ($resPlacement['rack'] == $rowArea['pk_id']) {
                                                                echo "selected=selected";
                                                            } ?>><?php echo $rowArea['list_value']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Rack Type <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="rack_type" id="rack_type" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets area
                                                                //rack type
                                                                //pk id
                                                                $getArea = mysql_query("SELECT
																					rack_information.rack_type,
																					rack_information.pk_id
																				FROM
																					rack_information") or die("ERR Get Rack Type");
                                                                //fetch result							
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    //populate rack_type combo
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>" <?php if ($resPlacement['rack_information_id'] == $rowArea['pk_id']) {
                                                                echo "selected=selected";
                                                            } ?>><?php echo $rowArea['rack_type']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Pallet <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="pallet" id="pallet" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets area pallet
                                                                //pk id
                                                                //list id
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																					list_master.pk_id = 18") or die("ERR Get Area");
                                                                //fetch results							
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    //populate pallet combo
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>" <?php if ($resPlacement['pallet'] == $rowArea['pk_id']) {
                                                                echo "selected=selected";
                                                            } ?>><?php echo $rowArea['list_value']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Level <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="level" id="level" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets area level
                                                                //pk id
                                                                //list id
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																					list_master.pk_id = 19") or die("ERR Get Area");
                                                                //fetch result							
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    //populate level combo
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>" <?php if ($resPlacement['level'] == $rowArea['pk_id']) {
                                                                        echo "selected=selected";
                                                                    } ?>><?php echo $rowArea['list_value']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                    <label class="control-label" for="firstname"> &nbsp; </label>
                                                    <div class="controls" >
                                                        <button type="submit" class="btn btn-primary" id="add_receive"> Save Entry </button>
                                                        <button type="reset" class="btn btn-info" id="reset"> Reset </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="id" value="<?php echo $_GET['id'] ?>"/>
                                    </form>
    <?php
} else {
    ?>
                                    <form method="POST" name="new_receive" id="new_receive" action="placement_locations_action.php" >
                                        <!-- Row -->
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3"> 
                                                    <!-- Group Receive No-->
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Area <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="area" id="area" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets area 
                                                                //pk id
                                                                //list id
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																					list_master.pk_id = 14") or die("ERR Get Area");
                                                                //fetch result							
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    //populate area combo
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>"><?php echo $rowArea['list_value']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- // Group END Receive No-->
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Row <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="row" id="row" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                //select query
                                                                //gets area row
                                                                //pk id
                                                                //list id
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																					list_master.pk_id = 15") or die("ERR Get Area");
                                                                //fetch result							
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    //populate row combo 
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>"><?php echo $rowArea['list_value']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Rack <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="rack" id="rack" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																					list_master.pk_id = 16") or die("ERR Get Area");
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>"><?php echo $rowArea['list_value']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Rack Type <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="rack_type" id="rack_type" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                $getArea = mysql_query("SELECT
																					rack_information.rack_type,
																					rack_information.pk_id
																				FROM
																					rack_information") or die("ERR Get Rack Type");
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>"><?php echo $rowArea['rack_type']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Pallet <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="pallet" id="pallet" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																					list_master.pk_id = 18") or die("ERR Get Area");
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>"><?php echo $rowArea['list_value']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="control-group">
                                                        <label class="control-label" for="receive_no"> Level <span class="red">*</span></label>
                                                        <div class="controls">
                                                            <select name="level" id="level" required class="form-control input-medium">
                                                                <option value="">Select</option>
                                                                <?php
                                                                $getArea = mysql_query("SELECT
																					list_detail.pk_id,
																					list_detail.list_value
																				FROM
																					list_master
																				INNER JOIN list_detail ON list_master.pk_id = list_detail.list_master_id
																				WHERE
																				list_master.pk_id = 19") or die("ERR Get Area");
                                                                while ($rowArea = mysql_fetch_assoc($getArea)) {
                                                                    ?>
                                                                    <option value="<?php echo $rowArea['pk_id']; ?>"><?php echo $rowArea['list_value']; ?></option>
        <?php }
    ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="col-md-8"></div>
                                                <div class="col-md-4">
                                                    <label class="control-label" for="firstname"> &nbsp; </label>
                                                    <div class="controls" style="float:right;">
                                                        <button type="submit" class="btn btn-primary" id="add_receive"> Save Entry </button>
                                                        <button type="reset" class="btn btn-info" id="reset"> Reset </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
<?php } ?>
                            </div>
                        </div>

                        <!-- // Row END --> 
                        <!-- Widget -->

                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Location List</h3>
                            </div>
                            <!-- // Widget heading END -->

                            <div class="widget-body"> 

                                <!-- Table --> 
                                <!-- Table -->
                                <table class="dynamicTable2 table table-striped table-bordered table-condensed">

                                    <!-- Table heading -->
                                    <thead>
                                        <tr>
                                            <th> Location </th>
<?php if ($_SESSION['user_role'] != 5) { ?>
                                                <th width="50"> Action </th>
<?php } ?>
                                        </tr>
                                    </thead>
                                    <!-- // Table heading END --> 

                                    <!-- Table body -->
                                    <tbody>
                                        <!-- Table row -->
                                                <?php while ($row = mysql_fetch_assoc($getPlacementLocation)) { ?>
                                            <tr class="gradeX">
                                                <td nowrap><?php echo $row['location_name'] ?></td>
                                                    <?php if ($_SESSION['user_role'] != 5) { ?>
                                                    <td class="center">
                                                        <?php
                                                        if (is_null($row['qty'])) {
                                                            ?>
                                                            <span data-toggle="notyfy" class="del_action" id="<?php echo $row['pk_id']; ?>" data-type="confirm" data-layout="top"><img class="cursor" src="<?php echo PUBLIC_URL; ?>images/cross.gif" /></span>
                                                            <?php
                                                        } else if ($row['qty'] == 0) {
                                                            ?>
                                                            <input type="hidden" name="location_id" id="loc_<?php echo $row['pk_id']; ?>_id" value="<?php echo $row['pk_id']; ?>" />
                                                            <input type="hidden" name="status" id="loc_<?php echo $row['pk_id']; ?>_status" value="<?php echo $row['status']; ?>" />
                                                            <button class="btn input-sm input-small <?php echo ($row['status'] == 1) ? "btn-success" : "btn-danger"; ?> btn-mini" onClick="changeStatus(this.id)" id="loc_<?php echo $row['pk_id']; ?>-makeit"> Make it <span id="loc_<?php echo $row['pk_id']; ?>-button"> <?php echo ($row['status'] == 1) ? "Inactive" : "Active"; ?></span> </span> </button>
                                                        <?php
                                                    } else if ($row['qty'] > 0) {
                                                        
                                                    }
                                                    ?>
                                                    </td>
        <?php }
    ?>
                                            </tr>
    <?php }
?>
                                        <!-- // Table row END -->
                                    </tbody>
                                </table>
                                <div>
                                    Note:-
                                    <p>Location can be deleted only if no stock is placed on it<br>
                                        If stock is placed on location, it can not be deleted<br>
                                        If stock is placed on location, its status can be changed only after moving the stock to another location
                                    </p>
                                </div>
                                <!-- // Table END --> 
                            </div>
                        </div>
                        <!-- Widget --> 
                    </div>
                </div>
            </div>
        </div>
        <?php include PUBLIC_PATH . "/html/footer.php"; ?>
        <script src="<?php echo PUBLIC_URL; ?>js/dataentry/stockplacement.js"></script>
        <?php
        if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
            if ($_SESSION['success'] == 1) {
                $msg = '<strong>Location</strong> with the same name already exists';
                $type = 'error';
            } else if ($_SESSION['success'] == 2) {
                $msg = '<strong>Data</strong> has been saved successfully';
                $type = 'success';
            } else if ($_SESSION['success'] == 3) {
                $msg = '<strong>Data</strong> has been deleted successfully';
                $type = 'success';
            }
            ?>
            <script>
                                                        var self = $('[data-toggle="notyfy"]');
                                                        notyfy({
                                                            force: true,
                                                            text: '<?php echo $msg; ?>',
                                                            type: '<?php echo $type; ?>',
                                                            layout: self.data('layout')
                                                        });
            </script>
            <?php
            unset($_SESSION['success']);
        }
        ?>
</body>
<!-- END BODY -->
</html>