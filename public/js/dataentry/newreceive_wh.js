$(function() {
    
        $('.types_select').attr("disabled", true);
        $('.types_select').hide();
        
	$('#checkAll').attr('checked', false);
	$('#checkAll').click(function(e) {
        if ($('#checkAll').is(':checked') )
		{
			$('input[type="checkbox"]').attr('checked', 'checked');
		}
		else
		{
			$('input[type="checkbox"]').attr('checked', false);
		}
    });
	
    $("#rec_date").datepicker({
        minDate: "-10Y",
        maxDate: 0,
        dateFormat: 'dd/mm/yy'
    });

    $("select[id$='-types']").attr("readonly", true);

    $('#estimated_life').priceFormat({
        prefix: '',
        thousandsSeparator: '',
        suffix: '',
        centsLimit: 0,
        limit: 2
    });

    $('#save').click(function(e) {
        e.preventDefault();
        var flag = 'true';

        if ($('#receive_stock').find('input[type=checkbox]:checked').length == 0) {
            alert('Please select atleast one checkbox');
            flag = 'false';
            return;
        }


        $('#save').attr('disabled', true);
        $('#save').html('Submitting...');
        
        $("input[id$='-missing']").each(function() {
            var value = $(this).attr("id");
            var id = value.replace("-missing", "");
            $('#' + id + '-types').attr("required", true);
            var qty = $(this).val();
            qty = Math.abs(qty);
            $(this).val(qty);
            var avaqty = $('#' + id + '-qty').val();

            console.log('Qty:'+qty+' , av:'+avaqty);
            if (parseInt(qty) > parseInt(avaqty)) {
                //alert("Adjustment quantity should not greater then available quantity");
                toastr.error("Adjustment quantity should not greater then available quantity");
                $(this).focus();
                flag = 'false';
            }
            if (qty != '' && (isNaN(qty) || qty < 0)) {
                //alert("Adjustment quantity should be an integer value.");
                toastr.error("Adjustment quantity should be an integer value.");
                $(this).focus();
                flag = 'false';
            }
            if(qty % 1 != 0)
            {
                //alert("Adjustment quantity can not be decimal value.");
                toastr.error("Adjustment quantity can not be decimal value.");
                $(this).focus();
                flag = 'false';
            }
        });

        if (flag == 'true') {
            if (confirm('Are you sure you received all of these items?')) {
                var checkedAtLeastOne = false;
                $('input[type="checkbox"]').each(function() {
                    if ($(this).is(":checked")) {
                        $('#receive_stock').submit();
                        return false;
                    }
                });
            }
            else{
                
		$('#save').attr('disabled', false);
		$('#save').html('Save');
            }
        }
        else{
            
		$('#save').attr('disabled', false);
		$('#save').html('Save');
        }
    });

    $("input[id$='-missing']").keyup(function(e) {
        var value = $(this).attr("id");
        var id = value.replace("-missing", "");
        $('#' + id + '-types').attr("required", true);
        var qty = $(this).val();
        var avaqty = $('#' + id + '-qty').val();
        console.log('Adj Qty changes : Qty:'+qty+' , av:'+avaqty);
        $(this).val(qty);
        if (parseInt(qty) > parseInt(avaqty)) {
            //alert("Adjustment quantity should not greater than available quantity");
            toastr.error("Adjustment quantity should not greater than available quantity");
            $(this).focus();
        }
        if (qty != '' && (isNaN(qty) || qty < 0)) {
            //alert("Adjustment quantity should be an integer value");
            toastr.error("Adjustment quantity should be an integer value");
            $(this).val('');
            //$('#' + id + '-types').attr("disabled", true);
            $(this).focus();
        }
        if(qty % 1 != 0)
            {
                //alert("Adjustment quantity can not be decimal value.");
                toastr.error("Adjustment quantity can Not be decimal value.");
                $(this).val('');
                $(this).focus();
                $('#' + id + '-types').attr("disabled", true);
                $('#' + id + '-types').hide();
            }
        if (qty <= 0 || qty == '' || qty % 1 != 0) {
            $('#' + id + '-types').attr("disabled", true);
            $('#' + id + '-types').hide();
        } else {
            $('#' + id + '-types').attr("disabled", false);
            $('#' + id + '-types').show();
        }
    });
    
    
    $("input[id$='-received']").keyup(function(e) {
        
        var value = $(this).attr("id");
        var id = value.replace("-received", "");
        console.log('received. id:'+id+',val:'+value);
        var rec_qty = $(this).val();
        
        if (rec_qty != '' && !$.isNumeric(rec_qty)) {
            alert("Received quantity should be a numeric value");
            $(this).focus();
        }
        var avaqty = $('#' + id + '-qty').val();
        if (parseInt(rec_qty) > parseInt(avaqty)) {
            alert("Received quantity should not greater than Issued quantity");
            $(this).focus();
        }
        var miss_val = parseInt(avaqty) - parseInt(rec_qty);
        $('#' + id + '-missing').val(miss_val);
    });
    
    $('input:checkbox').attr('checked',true);
});