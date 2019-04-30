<?php
//echo '<pre>';print_r($_REQUEST);exit;
include("../includes/classes/AllClasses.php");
include(PUBLIC_PATH . "html/header.php");

$userid = $_SESSION['user_id'];
$wh_id = $_SESSION['user_warehouse'];
$stk = $_SESSION['user_stakeholder1'];

@$open_form      = $_REQUEST['open_form'];
@$TranRef        = $_REQUEST['receive_ref'];
@$TranDate        = $_REQUEST['receive_date'];
@$productID      = $_REQUEST['product'];
@$manufacturer   = $_REQUEST['manufacturer'];
@$funding_source   = $_REQUEST['receive_from'];

if (!empty($open_form)) {
    //echo '<pre>';print_r($_REQUEST);exit;
}

$warehouses = $warehouses1 = $objwarehouse->GetUserWarehouses();
$items = $objManageItem->GetAllProduct_of_stk($stk);
$units = $objItemUnits->GetAllItemUnits();

if(!empty($manufacturer)){
    $q_m = "SELECT
            stakeholder.stkid,
            stakeholder_item.stk_id,
            CONCAT(
                stakeholder.stkname,
                ' | ',
                IFNULL(
                        stakeholder_item.brand_name,
                        ''
                )
            ) AS stkname
        FROM
            stakeholder
        INNER JOIN stakeholder_item ON stakeholder.stkid = stakeholder_item.stkid
        WHERE
            stakeholder.stk_type_id = 3
            AND stakeholder_item.stk_id = $manufacturer 
        ";
    //echo $q_m;exit;
    $res_m = mysql_query($q_m);
    $row = mysql_fetch_assoc($res_m);
    $manuf_name = $row['stkname'];
}

?>
</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
    <div class="page-container">
        <?php
        include PUBLIC_PATH . "html/top.php";
        include PUBLIC_PATH . "html/top_im.php";
        ?>
        <div class="page-content-wrapper">
            <div class="page-content"> 
                <div class="row">
                    <div class="col-md-12">
                        <div class="widget" data-toggle="collapse-widget">
                            <div class="widget-head">
                                <h3 class="heading">Add Opening Balance of Batches</h3>
                            </div>
                            <div class="widget-body">
                                <form method="POST" name="bulk_open_form" id="new_receive" action="" >
                                    <!-- Row -->
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="col-md-2">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_ref"> Ref. No. <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <input class="form-control input-sm" required id="receive_ref" name="receive_ref" type="text" value="<?php echo $TranRef; ?>" <?php if (!empty($TranRef)) { ?>disabled="" <?php } ?>/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2 hide">
                                                <div class="control-group">
                                                    <label class="control-label" for="receive_date"> Date </label>
                                                    <div class="controls">
                                                        <input class="form-control input-sm" readonly id="receive_date" tabindex="2" name="receive_date" type="text" value="<?php echo (!empty($TranDate)) ? date("d/m/y", strtotime($TranDate)) : date("d/m/Y"); ?>" required />
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="control-label">Funding Source<span class="red">*</span></label>
                                                <div class="controls">
                                                    <select class="form-control input-sm" id="receive_from" name="receive_from" required <?php if (!empty($open_form)){ echo 'readonly'; } ?>>
                                                        <option value="">Select</option>
                                                        <?php
                                                                    //Get User Warehouses
                                                                    $warehouses = $warehouses1 = $objwarehouse->GetUserWarehouses();
                                                                    if (mysql_num_rows($warehouses) > 0) {
                                                                        while ($row = mysql_fetch_object($warehouses)) {
                                                                             if($_SESSION['user_stakeholder']=='145'){
                                                                                    if($row->wh_id != '33677' && $row->wh_id != '33678' && $row->wh_id != '33680' && $row->wh_id != '20641'  && $row->wh_id != '9079') 
                                                                                        continue;
                                                                                }
                                                                            $sel = "";
                                                                            if(!empty($funding_source))$sel = " selected ";
                                                                            ?>
                                                        <?php //populate receive_from Combo?>
                                                        <option value="<?php echo $row->wh_id; ?>" <?=$sel?>> <?php echo $row->wh_name; ?> </option>
                                                        <?php
        }
    }
    ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="control-label" for="product"> Product <span class="red">*</span> </label>
                                                <div class="controls">
                                                    <select name="product" id="product" required="true" class="form-control input-sm"  <?php if (!empty($open_form)){ echo 'readonly'; } ?>>
                                                        <option value=""> Select </option>
                                                        <?php
                                                        //check if result exists
                                                        if (mysql_num_rows($items) > 0) {
                                                            //fetch results
                                                            while ($row = mysql_fetch_object($items)) {

                                                                $sel = '';
                                                                if ($productID == $row->itm_id) {
                                                                    $sel = ' selected ';
                                                                }
                                                                echo "<option value=" . $row->itm_id . " " . $sel . " >" . $row->itm_name . "</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php if (!isset($open_form)) { ?>
                                            <div class="col-md-3">
                                                <div class="col-md-6">
                                                    <label class="control-label" for="manufacturer"> Manufacturer <span class="red">*</span> </label>
                                                    <div class="controls">
                                                        <select name="manufacturer" id="manufacturer" class="form-control input-sm">
                                                            <option value="">Select</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-2" style="margin-top: 30px; "> <a class="btn btn-primary btn-sm alignvmiddle" style="display:none;" id="add_m_p"  onclick="javascript:void(0);" data-toggle="modal"  href="#modal-manufacturer">Add New</a> </div>
                                            </div>
                                            <?php 
                                            
                                            }
                                            else 
                                                { ?>
                                            <div class="col-md-3">
                                                <div class="col-md-6">
                                                    <label class="control-label" for="manufacturer"> Manufacturer</label>
                                                    <div class="controls">
                                                        <input class="form-input" value="<?=$manuf_name?>" disabled>
                                                        <input type="hidden" id="manufacturer_id" value="<?=$manufacturer?>" >
                                                    </div>
                                                </div>
                                            </div>
                                            <?php 
                                            
                                            }
                                            ?>
                                            <div class="col-md-1">
                                                <label class="control-label" for="firstname"> &nbsp; </label>
                                                <div class="controls right">
                                                    <?php if (!empty($open_form)) { ?>
                                                        <a href="" class="btn green btn-sm"  > Select Another Product </a>
                                                    <?php }else
                                                    {
                                                        ?>
                                                        <button type="submit" class="btn btn-primary btn-sm" id="open_form" name="open_form" value="open_form"> Start Entering Batches </button>
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
                        <div id="modal-manufacturer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content"> 
                                    <!-- Modal heading -->
                                    <div class="modal-header">
                                        <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                                        <div id="pro_loc"></div>
                                    </div>
                                    <!-- // Modal heading END --> 

                                    <!-- Modal body -->
                                    <div class="modal-body">
                                        <form name="addnew" id="addnew" action="add_action_manufacturer.php" method="POST">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-6">
                                                        <label class="control-label">Manufacturer<span class="red">*</span></label>
                                                        <div class="controls">
                                                            <input required class="form-control input-medium" type="text" id="new_manufacturer" name="new_manufacturer" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="control-label">Brand Name<span class="red">*</span></label>
                                                        <div class="controls">
                                                            <input required class="form-control input-medium" type="text" id="brand_name" name="brand_name" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <div class="controls">
                                                            <h4 style="padding-top:30px;">Carton Dimension</h4>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Length(cm)</label>
                                                        <div class="controls">
                                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_length" name="pack_length" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Width(cm)</label>
                                                        <div class="controls">
                                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_width" name="pack_width" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Height(cm)</label>
                                                        <div class="controls">
                                                            <input class="form-control input-sm dimensions positive_number" type="text" id="pack_height" name="pack_height" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <label class="control-label">Net Capacity</label>
                                                        <div class="controls">
                                                            <input required class="form-control input-sm positive_number" type="text"  id="net_capacity" name="net_capacity" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Cartons / Pallet<span class="red">*</span></label>
                                                        <div class="controls">
                                                            <input required class="form-control input-sm positive_number" type="text" id="carton_per_pallet" name="carton_per_pallet" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">Quantity/Pack<span class="red">*</span></label>
                                                        <div class="controls">
                                                            <input required class="form-control input-sm positive_number" type="text" id="quantity_per_pack" name="quantity_per_pack" value=""/>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="control-label">GTIN</label>
                                                        <div class="controls">
                                                            <input required class="form-control input-sm" type="text" id="gtin" name="gtin" value=""/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="col-md-3">
                                                        <label class="control-label">Gross :</label> 
                                                        <div class="controls"><input class="form-control input-sm " type="text" readonly id="gross_capacity" name="gross_capacity" ></div>

                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" id="add_manufacturer" name="add_manufacturer" value="1"/>
                                        </form>
                                    </div>
                                    <!-- // Modal body END --> 

                                    <!-- Modal footer -->
                                    <div class="modal-footer"> <a data-dismiss="modal" class="btn btn-default" href="#">Close</a> <a class="btn btn-primary" id="save_manufacturer" data-dismiss="modal" href="#">Save changes</a> </div>
                                    <!-- // Modal footer END --> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
<?php if (!empty($open_form)) { ?>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="widget" data-toggle="collapse-widget">
                                <div class="widget-head">
                                    <h3 class="heading">Enter Batches Information</h3>
                                </div>
                                <div class="widget-body" id="gridData">
                                    <table class="table table-bordered table-condensed" id="myTable">
                                        <!-- Table heading -->
                                        <thead>
                                            <tr bgcolor="#009C00" style="color:#FFF;">
                                                <th>#</th>
                                                <th class=""> Batch No.</th>
                                                <th class=""> Quantity </th>
                                                <th nowrap> Expiry Date </th>
                                                <th width=""> DTL 
                                                </th>
                                                <th nowrap> Action </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="sample_row" style="display:none;">
                                                <td>#</td>
                                                <td> <input class="form-control input-xs batch_input"> </td>
                                                <td> <input class="form-control input-xs qty_input" type="number" value="" min="0">  </td>
                                                <td > <input class="form-control input-xs expiry_input" type="date">  </td>
                                                <td >
                                                    <select name="dtl" id="dtl" class="form-control dtl_input" required >
                                                        <option value="2">NA</option>
                                                        <option value="0">Inprocess</option>
                                                        <option value="1">Completed</option>
                                                    </select>
                                                </td>
                                                <td > <a class="btn btn-xs green save_row_btn">Save</a>  </td>
                                            </tr>
                                            <tr is_new="1">
                                                    <td>#</td>
                                                    <td> <input class="form-control input-xs batch_input"> </td>
                                                    <td> <input class="form-control input-xs qty_input" type="number" value="" min="0">  </td>
                                                    <td > <input class="form-control input-xs expiry_input" type="date">  </td>
                                                    <td >
                                                        <select name="dtl" id="dtl" class="form-control dtl_input" required >
                                                            <option value="2">NA</option>
                                                            <option value="0">Inprocess</option>
                                                            <option value="1">Completed</option>
                                                        </select>
                                                    </td>
                                                    <td > <a class="btn btn-xs green save_row_btn">Save</a>  </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                <?php }
                ?>
            </div>
        </div>
    </div>
    <?php
    //include footer
    include PUBLIC_PATH . "/html/footer.php";
    ?>
<!--    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/newreceive.js"></script> -->
    <script src="<?php echo PUBLIC_URL; ?>js/dataentry/jquery.mask.min.js"></script> 
    <script src="<?php echo PUBLIC_URL; ?>js/jquery.inlineEdit.js"></script>
    <?php
    if (!empty($_SESSION['success'])) {
        if ($_SESSION['success'] == 1) {
            //display message
            $text = 'Data has been saved successfully';
        }
        if ($_SESSION['success'] == 2) {
            //display message
            $text = 'Data has been deleted successfully';
        }
        ?>
    <script >
            var self = $('[data-toggle="notyfy"]');
            notyfy({
                force: true,
                text: '<?php echo $text; ?>',
                type: 'success',
                layout: self.data('layout')
            });
            
          
        </script>
        <?php
        unset($_SESSION['success']);
    }
    ?>
        <script>
            $(function() {  
                $(document).on("click",".save_row_btn",function(){
                    
                    $.this = $(this);
                    var product = $('#product').val();
                    var reference = $('#receive_ref').val();
                    var qty  = $(this).parents('tr').find('.qty_input').val();
                    var batch  = $(this).parents('tr').find('.batch_input').val();
                    var expiry  = $(this).parents('tr').find('.expiry_input').val();
                    var dtl  = $(this).parents('tr').find('.dtl_input').val();
                    var new_row  = $(this).parents('tr').attr('is_new');
                    //console.log(expiry);
                    
                    if(batch && qty && expiry && qty > 0  && (qty+"").match(/^\d+$/) )
                    {
                        $.this.text('Saving...');
                        $.this.removeClass('save_row_btn').addClass('save_row_btn_disabled');
                        $.this.removeClass('green').addClass('default');
                        $.ajax({
                            type: "POST",
                            url: "bulk_open_batches_ajax.php",
                            data: 'new_row='+new_row+'&product='+product+'&quantity='+qty+'&batch='+batch+'&expiry='+expiry+'&types=16&ref_no='+reference+'&dtl='+dtl+'&manufacturer=<?=$manufacturer?>&receive_from=<?=$funding_source?>',
                            dataType: 'json',

                            success: function(data) {
                                //console.log('suces');
                                if(data.saved == 'ok'){

                                    if(new_row==1)
                                    {

                                        toastr.success('Batch Info Added.');
                                        $.this.removeClass('green').addClass('yellow');
                                        var html = $('#sample_row').html();
                                        $('#myTable tr:last').after('<tr is_new="1" class="info">'+html+'</tr>');

                                        $.this.text('Update');
                                        //hide the update btn
                                        $.this.hide();
                                        
                                        $.this.parents('tr').attr('is_new','0');
                                        $.this.parents('tr').removeClass('info').addClass('success');
                                    }
                                    else
                                    {
                                        toastr.success('Batch Information Updated.');
                                    }
                                }
                                else
                                {
                                    toastr.error(data.msg);
                                    $.this.text('Save');
                                    $.this.removeClass('save_row_btn_disabled').addClass('save_row_btn');
                                    $.this.removeClass('default').addClass('green');
                                }
                            }
                        });
                    }//end if
                    else{
                        toastr.warning('Please make sure Batch Number , Expiry and Quantity is not empty ,  and Quantity is a number greater than Zero.');
                    }
                    
                   
                        
                });
                
                
                
                 $("#save_manufacturer").click(function() {
                    var product = $('#product').val();
                    var manufacturer = $('#new_manufacturer').val();
                    if (manufacturer == '') {
                        alert('Enter Manufacturer.');
                                    $('#new_manufacturer').focus();
                        return false;
                    }
                    if ($('#brand_name').val() == '') {
                        alert('Enter Brand Name.');
                                    $('#brand_name').focus();
                        return false;
                    }
                    if ($('#quantity_per_pack').val() == '') {
                        alert('Enter Quantity per Pack.');
                                    $('#quantity_per_pack').focus();
                        return false;
                    }else if (isNaN($('#quantity_per_pack').val())){
                                    alert('Invalid data.');
                                    $('#quantity_per_pack').focus();
                        return false;
                            }
                    $.ajax({
                        type: "POST",
                        url: "add_action_manufacturer.php",
                        data: 'add_action=1&item_pack_size_id='+product+'&'+$("#addnew").serialize(),
                        dataType: 'html',
                        success: function(data) {
                            $('#manufacturer').html(data);
                                            // Clear the form
                                            $('#addnew input, #addnew select').val('');
                        }
                    });
                });

                
                
            $("#product").change(function() {
                var product = $('#product').val();
                if (product != '') {
                    $("#add_m_p").show();
                }
                else {
                    $("#add_m_p").hide();
                }
                $.ajax({
                    type: "POST",
                    url: "ajaxproductname.php",
                    data: {
                        product: $(this).val()
                    },
                    dataType: 'html',
                    success: function(data) {

                       $("#pro_loc").html('<h5>Add Manufacturer for '+data+'</h5>'); 
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "ajaxproductbatch.php",
                    data: {
                        product: $(this).val()
                    },
                    dataType: 'html',
                    success: function(data) {
                        $('#product-unit').html(data);
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "ajaxproductcat.php",
                    data: {
                        product: $(this).val()
                    },
                    dataType: 'html',
                    success: function(data) {
                        if (data == '2') {
                            $("#expiry_date").rules("remove", "required");
                              $("#vvmtype").val("");
                               $("#vvmstage").val("");
                            $("#vvmtype").attr("disabled", "disabled");
                            $("#vvmstage").attr("disabled", "disabled");
                            $("#vvmstage_div").hide();
                        } else {
                            $("#expiry_date").rules("add", "required");
                             $("#vvmtype").removeAttr("disabled");
                              $("#vvmstage").removeAttr("disabled");
                        }
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "add_action_manufacturer.php",
                    data: {
                        show: 1,
                        product: $(this).val()
                    },
                    dataType: 'html',
                    success: function(data) {
                        $('#manufacturer').html(data);
                    }
                });
            });
            });
        </script>
    <!-- END FOOTER --> 
    <!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>