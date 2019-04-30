<?php
/**
 * Inventory Turn Over
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
//include header
include(PUBLIC_PATH . "html/header.php");
?>
<style>
    .modal {
        display:    none;
        position:   fixed;
        z-index:    10000;
        top:       	0;
        left:       0;
        height:     100%;
        width:      100%;
        background: rgba( 255, 255, 255, 0.3 )
            url('../../public/images/loader.gif')
            50% 50%
            no-repeat;
    }
    /* When the body has the loading class, we turn
       the scrollbar off with overflow:hidden */
    body.loading {
        overflow: auto;
    }
    /* Anytime the body has the loading class, our
       modal element will be visible */
    body.loading .modal {
        display: block;
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
                        <h3 class="page-title row-br-b-wp">Inventory Turnover</h3>
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Filter by</h3>
                            </div>
                            <div class="widget-body">
                                <form name="frm" id="frm" action="" method="post" role="form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Ending Quarter</label>
                                                    <div class="controls">
                                                        <select name="quarter" id="quarter" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <option value="1">First Quarter</option>
                                                            <option value="2">Second Quarter</option>
                                                            <option value="3">Third Quarter</option>
                                                            <option value="4">Fourth Quarter</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Year</label>
                                                    <div class="controls">
                                                        <select name="year" id="year" class="form-control input-sm">
                                                            <?php
                                                            for ($j = date('Y'); $j >= 2010; $j--) {
                                                                if ($selYear == $j) {
                                                                    $sel = "selected='selected'";
                                                                } else {
                                                                    $sel = "";
                                                                }
                                                                ?>
                                                                <option value="<?php echo $j; ?>" <?php echo $sel; ?> ><?php echo $j; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Sector</label>
                                                    <div class="controls">
                                                        <select class="form-control input-sm" id="sector" name="sector">
                                                            <option value="all">All</option>
                                                            <option value="0">Public</option>
                                                            <option value="1">Private</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Stakeholder</label>
                                                    <div class="controls">
                                                        <select name="stk_sel" id="stk_sel" class="form-control input-sm">
                                                            <option value="all" <?php echo ($selStk == 'all') ? "selected='selected'" : ""; ?>>All</option>
                                                            <?php
                                                            //select query
                                                            //gets
                                                            //stakeholder id
                                                            //stakeholder name
                                                            $querystk = "SELECT DISTINCT
																			stakeholder.stkid,
																			stakeholder.stkname
																		FROM
																			tbl_warehouse
																		INNER JOIN stakeholder ON tbl_warehouse.stkid = stakeholder.stkid
																		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
																		WHERE
																			tbl_warehouse.is_active = 1
																		ORDER BY
																			stakeholder.stk_type_id ASC,
																			stakeholder.stkorder ASC";
                                                            //query result
                                                            $rsstk = mysql_query($querystk) or die();
                                                            //fetch results
                                                            while ($rowstk = mysql_fetch_array($rsstk)) {
                                                                //populate stk_sel combo
                                                                ?>
                                                                <option value="<?php echo $rowstk['stkid']; ?>"><?php echo $rowstk['stkname']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Product</label>
                                                    <div class="controls">
                                                        <select name="item_id" id="item_id" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                            <?php
                                                            //select query
                                                            //gets
                                                            //item rec id
                                                            //item id
                                                            //item name
                                                            $querypro = "SELECT DISTINCT
																			itminfo_tab.itmrec_id,
																			itminfo_tab.itm_id,
																			itminfo_tab.itm_name
																		FROM
																			itminfo_tab
																		INNER JOIN stakeholder_item ON itminfo_tab.itm_id = stakeholder_item.stk_item
																		WHERE
																			itminfo_tab.itm_status = 1
																		AND itminfo_tab.itm_category = 1
																		ORDER BY
																			itminfo_tab.frmindex ASC";
                                                            $rspro = mysql_query($querypro);
                                                            while ($rowpro = mysql_fetch_array($rspro)) {
                                                                //populate item_id combo
                                                                ?>
                                                                <option value="<?php echo $rowpro['itmrec_id']; ?>" <?php echo $sel; ?>><?php echo $rowpro['itm_name']; ?></option>
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
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>Office Level</label>
                                                    <div class="controls">
                                                        <select name="office_level" id="office_level" class="form-control input-sm">
                                                            <option value="1">National</option>
                                                            <option value="2">Provincial</option>
                                                            <option value="3">District</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="province_div" style="display:none;">
                                                <div class="control-group">
                                                    <label>Province/Region</label>
                                                    <div class="controls">
                                                        <select name="province" id="province" class="form-control input-sm">
                                                            <?php
                                                            //select query
                                                            //gets
                                                            //province id
                                                            //province name
                                                            $qry = "SELECT DISTINCT
																			tbl_locations.PkLocID,
																			tbl_locations.LocName
																		FROM
																			tbl_locations
																		INNER JOIN tbl_warehouse ON tbl_locations.PkLocID = tbl_warehouse.prov_id
																		INNER JOIN wh_user ON tbl_warehouse.wh_id = wh_user.wh_id
																		WHERE
																			tbl_locations.ParentID IS NOT NULL
																		AND tbl_locations.LocLvl = 2
																		AND tbl_warehouse.is_active = 1
																		ORDER BY
																			tbl_locations.PkLocID";
                                                            //query result
                                                            $rsQry = mysql_query($qry) or die();
                                                            //fetch result
                                                            while ($row = mysql_fetch_array($rsQry)) {
                                                                //pipulate province combo
                                                                ?>
                                                                <option value="<?php echo $row['PkLocID']; ?>" <?php echo $sel; ?>><?php echo $row['LocName']; ?></option>
                                                                <?php
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2" id="district_div" style="display:none;">
                                                <div class="control-group" id="district_data">
                                                    <label>District</label>
                                                    <div class="controls">
                                                        <select name="district" id="district" class="form-control input-sm">
                                                            <option value="">Loading...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label>&nbsp;</label>
                                                    <div class="controls">
                                                        <input type="button" id="submit" value="GO" class="btn btn-primary input-sm">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" id="it-graph-row" style="display:none;">
                    <div class="col-md-12">
                        <div class="widget">
                            <div class="widget-body" id="it-graph"></div>
                        </div>
                    </div>
                </div>
                
                    <div class="row"  >
                        <div class="col-md-12">
                            <div class=" ">
                                <div class="note note-info h6"  ><em><?=$lastUpdateText?></em></div>
                            </div>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <?php 
    //include footer
    include PUBLIC_PATH . "/html/footer.php"; ?>
    <script>
        $(function() {
            $('#office_level').change(function() {
                officeType($(this).val());
            });
            $('#province').change(function() {
                var provId = $(this).val();
                showDistricts(provId);
            });
            $('#sector').change(function(e) {
                $('#item_id').html('<option>Select</option>');
                var val = $('#sector').val();
                getStakeholder(val, '');
            });
            $('#stk_sel').change(function(e) {
                showProducts('');
            });

            // Submit Form
            $('#submit').click(function(e) {
                if ($('#quarter').val() == '') {
                    alert('Select ending quarter');
                    $('#quarter').focus();
                    return false;
                }
                if (parseInt($('#office_level').val()) == 3 && $('#district').val() == '') {
                    alert('Select district');
                    $('#district').focus();
                    return false;
                }
                if ($('#item_id').val() == '') {
                    alert('Select product');
                    $('#item_id').focus();
                    return false;
                }
                $('body').addClass("loading");
                $('#it-graph-row').hide();
                $.ajax({
                    type: "POST",
                    url: "ajax-inventory-turn-over.php",
                    data: $('#frm').serialize(),
                    dataType: 'html',
                    success: function(data)
                    {
                        $('#it-graph-row').show();
                        $('#it-graph').html(data);
                        $('body').removeClass("loading");
                    }
                });
            });
        });
        function officeType(officeLevel)
        {
            if (parseInt(officeLevel) == 1)
            {
                $('#province_div').hide();
                $('#district_div').hide();
            }
            else if (parseInt(officeLevel) == 2)
            {
                $('#province_div').show();
                $('#district_div').hide();
            }
            else if (parseInt(officeLevel) == 3)
            {
                $('#province_div').show();
                showDistricts($('#province').val());
            }
        }
        function showDistricts(provId)
        {
            var officeLevel = $('#office_level').val();
            if (officeLevel == 3)
            {
                $('#district_div').show();
                $('#district').html('<option value="">Loading...</option>');
                $.ajax({
                    type: "POST",
                    url: "ajax_calls.php",
                    data: {provinceId: provId, validate: 'yes'},
                    dataType: 'html',
                    success: function(data)
                    {
                        $('#district_data').html(data);
                    }
                });
            }
        }
        function getStakeholder(val, stk)
        {
            $.ajax({
                url: 'ajax_stk.php',
                data: {type: val, stk: stk},
                type: 'POST',
                success: function(data) {
                    $('#stk_sel').html(data);
                    showProducts('');
                }
            })
        }
        function showProducts(pid) {
            var stk = $('#stk_sel').val();
            $.ajax({
                url: 'ajax_calls.php',
                type: 'POST',
                data: {stakeholder: stk, productId: pid},
                success: function(data) {
                    $('#item_id').html(data);
                }
            })
        }
		function showProvinces(pid) {
			var stk = $('#stk_sel').val();
			if (typeof stk !== 'undefined')
			{
				$.ajax({
					url: 'ajax_stk.php',
					type: 'POST',
					data: {stakeholder: stk, provinceId: pid, showProvinces: 1, showAllOpt: 0},
					success: function(data) {
						$('#province').html(data);
					}
				})
			}
		}
		$(function() {
			$('#stk_sel').change(function(e) {
				$('#province').html('<option value="">Select</option>');
				showProvinces('');
			});
		})
		<?php
		if (isset($selPro) && !empty($selPro)) {
			?>
				showProvinces('<?php echo $selPro; ?>');
			<?php
		}
		?>
    </script>
</body>
</html>