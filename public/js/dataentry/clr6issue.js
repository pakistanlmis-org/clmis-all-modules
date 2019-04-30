$(function(){
	$('#product').change(function(){
        $("#available_qty").val('');
        $("#expiry_date").val('');
        $.ajax({
			type: "POST",
			url: "ajaxrunningbatches.php",
			data: {product: $('#product').val()},
			dataType: 'html',
			success: function(data){
				$('#batch').html(data);
				}		
			});
		$.ajax({
			type: "POST",
			url: "ajaxproductbatch.php",
			data: {product: $(this).val()},
			dataType: 'html',
			success: function(data){
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
            success: function (data) {
                if(data == '2'){
                    $( "#expiry_div" ).hide();
                } else {
                    $( "#expiry_div" ).show();
                }
            }
        });
		});
	
	$('#batch').change(function(){
		$.ajax({
			type: "POST",
			url: "ajaxrunningbatches.php",
			data: {batch: $('#batch').val()},
			dataType: 'html',
			success: function(data){
				$('#itembatches').html(data);
			}
			});
		});
	
/*	$('#issue_date').datepicker({
		dateFormat: "dd/mm/yy",
		constrainInput: false,
		maxDate: 0,
        changeMonth: true,
        changeYear: true,
	});
	$('#expiry_date').datepicker({
		dateFormat: "dd/mm/yy",
		constrainInput: false,
		minDate: 0,
        changeMonth: true,
        changeYear: true,
	});
	
	$('#issue_date, #expiry_date').mask('00/00/0000');
	*/
	$('#qty, #product').change(function(e) {
		
		var qty = $('#qty').val();
		var itemId = $('#product').val();
		
		$('#product-unit1').css('display', 'none');
		
		if (qty != 0 && qty != '' && itemId != '')
		{
			$.ajax({
				type: "POST",
				url: "ajaxproductcat.php",
				data: 'qty='+qty+'&itemId='+itemId,
				success: function(doses){
					if (doses != '')
					{
						$('#product-unit1').css('display', 'table-row');
						$('#product-unit1').html(doses);
					}
				}
			});
		}
	});
	
	/*$("#issue_clr6").click(function()
	{
		var id,wh_id,rq;
		id=$("#clr6_id").val();
		wh_id=$("#warehouse").val();
		rq=$("#rq_no").val();
		window.open('../../plmis_admin/print_clr6_issue.php?id='+id+'&wh_id='+wh_id+'&rq='+rq,'_blank','scrollbars=1,width=842,height=595');
	}
	);*/
	/*$("#approve_clr6").submit(function()
			{
		
				var id,warehouse;
				id=$("#clr6_id").val();
				warehouse=$("#warehouse").val();
				window.open('print_approve_clr6.php?id='+id+'&wh_id='+warehouse,'_blank', 'scrollbars=1,width=842,height=595');
			}
			);
	*/
	$("#print_issue2").click(function()
	{
		var req_no,recip;
		req_no=$("#issue_no").val();
		recip=$("#recipient").val();
		refrenceno=$("#issue_ref").val();
		window.open('stock_issue_voucher.php?req_no='+req_no+'&recip='+recip+'&refrenceno='+refrenceno,'_blank','width=842,height=595');
	}
	);
	$("#wh_link").click(function()
	{
		var wh_id;
		wh_id=$("#warehouse").val();
		window.open('coldchain_show_list.php?wh_id='+wh_id,'_blank','width=842,height=595');
	}
	);
	
	/*$.inlineEdit({
		
		Qty: 'ajaxIssue.php?type=qty&Id=',
		Batch: 'ajaxIssue.php?type=batch&Id='
		
		}, {
		
		animate: true,
		
		filterElementValue: function($o){
			return $o.html().trim();
			},
		
		afterSave: function(){
		}
		
		});
	
	$('#qty').priceFormat({
		prefix: '',
		thousandsSeparator: ',',
		suffix: '',
		centsLimit: 0,
		limit: 10
		});	
	*/
	$('#warehouse').change(function(){
		var warehouse = $(this).val();
		$('#wh_link').show();
		$('#wh_button').html('<a href="#?wh_id='+warehouse+'"><button type="button" class="btn btn-info" name="wh_link" id="wh_link">Cold chain</button></a>');
		});
	$('#batch_no').change(function(){
		//$objStockBatch->FindItemQtyByBatchId();
		});
	
	$('[data-toggle="notyfy"]').click(function () 
	{
		var self = $(this);
		
		notyfy({
			text: notification[self.data('type')],
			type: self.data('type'),
			dismissQueue: true,
			layout: self.data('layout'),
			buttons: (self.data('type') != 'confirm') ? false : [{
				addClass: 'btn btn-success btn-small btn-icon glyphicons ok_2',
				text: '<i></i> Ok',
				onClick: function ($notyfy) {
					var id = self.attr("id");
					var rq = $("#rq").val();
					$notyfy.close();					
					window.location.href = 'delete_clr6issue.php?id='+id+'&rq='+rq;
				}
				}, {
				addClass: 'btn btn-danger btn-small btn-icon glyphicons remove_2',
				text: '<i></i> Cancel',
				onClick: function ($notyfy) {
					$notyfy.close();
					notyfy({
						force: true,
						text: '<strong>You clicked "Cancel" button<strong>',
						type: 'error',
						layout: self.data('layout')
							});
				}
				}]
			});
		return false;
		});
	});

var notification = [];
notification['confirm'] = 'Do you want to continue?';

$("#add_issue").click(function(e){
    $(this).attr("disabled","");
    e.preventDefault();
    var validator = $( "#new_issue_form" ).validate();

    var aval_qty = $("#ava_qty").val();
    var qty = $("#qty").val();

    aval_qty = aval_qty.replace(",","");
    qty = qty.replace(",","");

    if(parseInt(qty) <= 0){
        validator.showErrors({
            "qty": "Quantity should greater then 0"
        });
    } else if( parseInt(aval_qty) < parseInt(qty) ){
        validator.showErrors({
            "qty": "Quantity not available"
        });
    } else {
        $("#new_issue_form").submit();        
    }
    $(this).removeAttr("disabled");
});

// validate signup form on keyup and submit
$("#new_issue_form").validate({
    rules: {
        'office': {
            required: true
        },
        'combo1': {
            required: true
        },
        'warehouse': {
            required: true
        },
        'product': {
            required: true
        },
        'batch': {
            required: true
        },
        'qty': {
            required: true
        }
    },
    messages: {
        'product': {
            required: "Please select product"
        },
        'batch': {
            required: "Please enter batch number"
        },
        'qty': {
            required: "Please enter quantity"
        }
    }
});