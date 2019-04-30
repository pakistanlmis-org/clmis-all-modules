$(function() {

    var product = $('#product').val();

    if (product != '') {
        $("#add_m_p").show();
        
         $.ajax({
            type: "POST",
            url: "ajaxproductcat.php",
            data: {
                product: product
            },
            dataType: 'html',
            success: function(data) {
                if (data == '2') {
                    $("#expiry_date").rules("remove", "required");
                    //$( "#expiry_div" ).hide();
                      $("#vvmtype").val("");
                       $("#vvmstage").val("");
                    $("#vvmtype").attr("disabled", "disabled");
                    $("#vvmstage").attr("disabled", "disabled");
                    
                  //  $("#vvmtype_div").hide();
                    $("#vvmstage_div").hide();
                } else {
                    $("#expiry_date").rules("add", "required");
                    //$( "#expiry_div" ).show();
                 
                     $("#vvmtype").removeAttr("disabled");
                      $("#vvmstage").removeAttr("disabled");
                   // $("#vvmtype_div").show();
                  //  $("#vvmstage_div").show();
                }
            }
        });
        

    }
    else {
        $("#add_m_p").hide();
    }


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


    /*$("#receive_date").datepicker({
        dateFormat: 'dd/mm/yy',
        constrainInput: false,
        changeMonth: true,
        changeYear: true
    });*/
	
	$('#receive_date').datetimepicker({
		format: "dd/mm/yyyy HH:ii P",
	    showMeridian: true,
	    autoclose: true,
	    startDate: "dd/mm/yyyy 10:00",
	    todayBtn: true,
        changeMonth: true,
        changeYear: true
	});

    $("#expiry_date").datepicker({
        minDate: 0,
        maxDate: "+10Y",
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true
    });

    $("#prod_date").datepicker({
        minDate: "-10Y",
        maxDate: 0,
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        constrainInput: false
    });

    //$('#receive_date, #expiry_date, #prod_date').mask('00/00/0000');

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
                    //$( "#expiry_div" ).hide();
                      $("#vvmtype").val("");
                       $("#vvmstage").val("");
                    $("#vvmtype").attr("disabled", "disabled");
                    $("#vvmstage").attr("disabled", "disabled");
                    
                  //  $("#vvmtype_div").hide();
                    $("#vvmstage_div").hide();
                } else {
                    $("#expiry_date").rules("add", "required");
                    //$( "#expiry_div" ).show();
                 
                     $("#vvmtype").removeAttr("disabled");
                      $("#vvmstage").removeAttr("disabled");
                   // $("#vvmtype_div").show();
                  //  $("#vvmstage_div").show();
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

    $.inlineEdit({
        Qty: 'ajaxReceive.php?type=qty&Id=',
        Batch: 'ajaxReceive.php?type=batch&Id='

    }, {
        animate: false,
        filterElementValue: function($o) {
            return $o.html().trim();
        },
        afterSave: function() {
        }

    });

    $('#unit_price').priceFormat({
        prefix: '',
        thousandsSeparator: '',
        suffix: '',
        centsLimit: 2
    });

    $('[data-toggle="notyfy"]').click(function() {
        var self = $(this);
		$.notyfy.closeAll();
        notyfy({
            text: notification[self.data('type')],
            type: self.data('type'),
            dismissQueue: true,
            layout: self.data('layout'),
            buttons: (self.data('type') != 'confirm') ? false : [
                {
                    addClass: 'btn btn-success btn-medium btn-icon glyphicons ok_2',
                    text: '<i></i> Ok',
                    onClick: function($notyfy) {
                        var id = self.attr("id");
                        $notyfy.close();
                        window.location.href = 'delete_receive.php?id=' + id;
                    }
                },
                {
                    addClass: 'btn btn-danger btn-medium btn-icon glyphicons remove_2',
                    text: '<i></i> Cancel',
                    onClick: function($notyfy) {
                        $notyfy.close();
                        /*notyfy({
                            force: true,
                            text: '<strong>You clicked "Cancel" button<strong>',
                            type: 'error',
                            layout: self.data('layout')
                        });*/
                    }
                }
            ]
        });
        return false;
    });

    $.validator.setDefaults({
        ignore: ':hidden, [readonly=readonly]'
    });

    $('#reset').click(function() {
        window.location.href = appPath + 'im/new_receive.php';
    });
});

var notification = [];
notification['confirm'] = 'Do you want to continue?';

/*$('#vvmstage').priceFormat({
    prefix: '',
    thousandsSeparator: ',',
    suffix: '',
    centsLimit: 0,
    limit: 2
});*/
$('#qty').priceFormat({
    prefix: '',
    thousandsSeparator: ',',
    suffix: '',
    centsLimit: 0,
    limit: 10
});

$('#print_vaccine_placement').click(function() {
    var ref_no, rec_no, rec_date, unit_pric, rec_from, stock_id;
    ref_no = $('#receive_ref').val();
    rec_no = $('#receive_no').val();
    rec_date = $('#receive_date').val();
    rec_from = $('#source_name').val();
    stock_id = $('#stock_id').val();
    window.open('stockRecivePrint.php?id='+stock_id+'&rec_no=' + rec_no + '&ref_no=' + ref_no + '&rec_date=' + rec_date + '&rec_from=' + rec_from, '_blank', 'scrollbars=1,width=842,height=595');
});

$('#qty').focusout(function() {
    if ($(this).val() == 0)
    {
        $(this).val(1);
    }
    else
    {
        $(this).val($(this).val());
    }
})


$('.positive_number').focusout(function() {
	var this_v = parseFloat($(this).val());
	console.log(this_v);
	if ( this_v < 0 || isNaN(this_v))
    {
        $(this).val('0');
    }
   
})

$('#add_m_p').click(function() {
	$('#addnew')[0].reset();
	
})


$('.dimensions').focusout(function() {
	var pack_length = $('#pack_length').val();
	var pack_width 	= $('#pack_width').val();
	var pack_height = $('#pack_height').val();
	var gross = 0 ;
	
    if ( typeof pack_length== 'undefined') 	pack_length=0;
    if ( typeof pack_width== 'undefined') 	pack_width=0;
    if ( typeof pack_height== 'undefined') 	pack_height=0;
	
	gross = pack_length * pack_width * pack_height;
   
   $('#gross_capacity').val(gross);
   
})


$('#qty, #product').change(function(e) {

    var qty = $('#qty').val();
    var itemId = $('#product').val();

    $('#product-unit1').css('display', 'none');

    if (qty != 0 && qty != '' && itemId != '')
    {
        $.ajax({
            type: "POST",
            url: "ajaxproductcat.php",
            data: 'qty=' + qty + '&itemId=' + itemId,
            success: function(doses) {
                if (doses != '')
                {
                    $('#product-unit1').css('display', 'table-row');
                    $('#product-unit1').html(doses);
                }
            }
        });
    }
});

/*$("#add_receive").click(function (e) {
 e.preventDefault();
 var validator = $("#new_receive").validate();
 if ($("#qty").val() <= 0) {
 validator.showErrors({
 "qty": "Quantity should greater then 0"
 });
 } else {
 $("#new_receive").submit();
 }
 });*/

// validate signup form on keyup and submit
jQuery.validator.addMethod("mindate", function(value, element) {

    var x = new Date();
    var str = value;
    var day = str.substr(0, 2);
    var month = parseInt(str.substr(3, 2)) - 1;
    var year = str.substr(6);

    x.setFullYear(year, month, day);
    var today = new Date();

    return x > today;
}, ("Expiry date must be future date."));

jQuery.validator.addMethod("maxdate", function(value, element) {

    if (value != '')
    {
        var x = new Date();
        var str = value;
        var day = str.substr(0, 2);
        var month = parseInt(str.substr(3, 2)) - 1;
        var year = str.substr(6);

        x.setFullYear(year, month, day);
        var today = new Date();
        return x < today;
    }
    else
    {
        return true;
    }
}, ("Production date must be past date."));

$("#new_receive").validate({
    rules: {
        'product': {
            required: true
        },
        'batch': {
            required: true
        },
        'prod_date': {
            maxdate: true
        },
        'qty': {
            required: true
        },
        'expiry_date': {
            required: true,
            mindate: true
        },
        'manufacturer' : {
            required: true
        }
    },
    messages: {
        'receive_ref': {
            required: "Please enter refernce number"
        },
        'product': {
            required: "Please select product"
        },
        'batch': {
            required: "Please enter batch number"
        },
        'qty': {
            required: "Please enter quantity"
        },
        'expiry_date': {
            required: "Expiry date is required"
        },
        'manufacturer': {
            required: "Manufacturer is required"
        }
    },
	submitHandler: function(form) {
		$('#add_receive').attr('disabled', true);
		$('#add_receive').html('Submitting...');
		form.submit();
	}
});
