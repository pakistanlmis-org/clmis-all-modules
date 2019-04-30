$(function(){
    $('#available_div').hide();
	$('#types').change(function(e) {
		$('#add_m_p').hide();
        if(jQuery.inArray(parseInt($(this).val()), [8, 11, 13, 14, 16]) != -1){
			$('#add_m_p').show();
		}
    });
	$('#product').change(function(){
	 	$('#batch_no').select2('data', {id: null, text: 'Select'});
        $("#available").val('');
		$('#available_div').hide();
		$('#batch_no').html('<option>Select</option>');
		if($('#product').val() != '')
		{
			$('#batch_no, #available').show();
			$.ajax({
				type: "POST",
				url: "ajaxrunningbatches.php",
				data: {product: $('#product').val(), adjustment: 1},
				dataType: 'html',
				success: function(data){
					$('#batch_no').html(data);
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
				url: "add_action_manufacturer.php",
				data: {show: 1, product: $(this).val()},
				dataType: 'html',
				success: function(data) {
					$('#manufacturer').html(data);
				}
			});
		}
	});
	
	$("#save_batch").click(function() {
		var product = $('#product').val();
        if ($('#batch').val() == '') {
            alert('Enter batch number.');
			$('#batch').focus();
            return false;
        }
        if ($('#expiry_date').val() == '') {
            alert('Enter expiry date.');
			$('#expiry_date').focus();
            return false;
        }
        if ($('#receive_from').val() == '') {
            alert('Select funding source.');
			$('#receive_from').focus();
            return false;
        }
        if ($('#manufacturer').val() == '') {
            alert('Select manufacturer.');
			$('#manufacturer').focus();
            return false;
        }
        $.ajax({
            type: "POST",
            url: "add_action_batch.php",
            data: 'product='+product+'&'+$("#addnew").serialize(),
            dataType: 'html',
            success: function(batch_id) {
				// Clear the form
				$('#addnew input, #addnew select').val('');
				
				$.ajax({
					type: "POST",
					url: "ajaxrunningbatches.php",
					data: {product: $('#product').val(), adjustment: 1},
					dataType: 'html',
					success: function(data){
						$('#batch_no').html(data);
						//$('#batch_no').val(batch_id);
						$("#batch_no").select2().select2("val", batch_id); //set the value
					}		
				});
            }
        });
    });
	
	$('#quantity').priceFormat({
		prefix: '',
		thousandsSeparator: ',',
		suffix: '',
		centsLimit: 0,
		limit: 10
	});
	
	$('#quantity').keyup(function(e){
		
		var ava_qty = $("#available").val();
		ava_qty = parseInt(ava_qty.replace(/,/g,""),10);		
		var qty = $('#quantity').val();
		qty = parseInt(qty.replace(/,/g,""),10);
		var myarray = $('#negTransType').val().split(',');
		if($.inArray($('#types').val(), myarray) !== -1 && qty > ava_qty) {
			alert("Quantity should not greater then "+ava_qty);
			$(this).focus();
			return false;
		}
		else
		{
			return true;
		}
		/*else if($.inArray($('#types').val(), myarray) !== -1 && qty > ava_qty) {
			alert("Quantity should not greater then "+ava_qty);
			$(this).focus();
			return false;
		}*/
	});
	
	/*$('#batch_search').submit(function(e){		
		var ava_qty = $("#available").val();
		ava_qty = parseInt(ava_qty.replace(/,/g,""),10);
		
		var qty = $(this).val();
		qty = parseInt(qty.replace(/,/g,""),10);
		var myarray = new Array(5, 7, 9, 10, 12);
		if($.inArray($('#types').val(), myarray) === -1 && qty > ava_qty) {
			alert("Quantity should not greater then "+ava_qty);
			$(this).focus();
			return false;
		}
	});*/
	
	$('#batch_no').change(function(){
		if ($('#batch_no').val() != '')
		{
			$.ajax({
				type: "POST",
				url: "ajaxrunningbatches.php",
				data: {batch_no: $('#batch_no').val()},
				dataType: 'html',
				success: function(data){
					$('#available_div').fadeIn(1000);
					$('#itembatches').html(data);
				}
			});
		}
		else
		{
			$('#available_div').hide();
		}
	});

    $('#adjustment_date').datepicker({
        dateFormat: "dd/mm/yy",
		constrainInput: false,
		changeMonth: true,
        changeYear: true,
		maxDate: 0
    });

    $('#expiry_date').datepicker({
        dateFormat: "dd/mm/yy",
		constrainInput: false,
		changeMonth: true,
        changeYear: true,
		minDate: 0
    });
	$.fn.modal.Constructor.prototype.enforceFocus = function () {};
	
});

// validate signup form on keyup and submit
$("#batch_search").validate({
    rules: {
        'product': {
            required: true
        },
        'batch_no': {
            required: true
        },
        'quantity': {
            required: true
        },
        'types': {
            required: true
        }
    },
    messages: {
        'product': {
            required: "Please select product"
        },
        'batch_no': {
            required: "Please enter batch number"
        },
        'qty': {
            required: "Please enter quantity"
        },
        'types': {
            required: "Please enter adjustment type"
        }
    },
	submitHandler: function(form) {
		var ava_qty = $("#available").val();
		ava_qty = parseInt(ava_qty.replace(/,/g,""),10);		
		var qty = $('#quantity').val();
		qty = parseInt(qty.replace(/,/g,""),10);
		var myarray = $('#negTransType').val().split(',');
		if($.inArray($('#types').val(), myarray) !== -1 && qty > ava_qty) {
			alert("Quantity should not greater then "+ava_qty);
			$(this).focus();
			return false;
		}
		else
		{
			$('#add_adjustment').attr('disabled', true);
			$('#add_adjustment').html('Submitting...');
			form.submit();
		}
	}
});

/*$("#add_adjustment").click(function(e){
    e.preventDefault();
    var validator = $( "#batch_search" ).validate();
    if($("#quantity").val() <= 0){
        validator.showErrors({
            "quantity": "Quantity should greater then 0"
        });
    } else {
        $("#batch_search").submit();
    }
});*/

$("#reset").click(function(){
    $('#available_div').fadeOut(1000);
    $('#batch_no').empty();
});