$(function() {
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

        $("input[id$='-missing']").each(function() {
            var value = $(this).attr("id");
            var id = value.replace("-missing", "");
            $('#' + id + '-types').attr("required", true);
            var qty = $(this).val();
            var avaqty = $('#' + id + '-qty').val();

            if (parseInt(qty) > parseInt(avaqty)) {
                alert("Adjustment quantity should not greater then available quantity");
                $(this).focus();
                flag = 'false';
            }
            if (qty != '' && !$.isNumeric(qty)) {
                alert("Adjustment quantity should be an integer value");
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
        }
		$('#save').attr('disabled', true);
		$('#save').html('Submitting...');
    });

    $("input[id$='-missing']").change(function(e) {
        var value = $(this).attr("id");
        var id = value.replace("-missing", "");
        $('#' + id + '-types').attr("required", true);
        var qty = $(this).val();
        var avaqty = $('#' + id + '-qty').val();
        if (parseInt(qty) > parseInt(avaqty)) {
            alert("Adjustment quantity should not greater than available quantity");
            $(this).focus();
        }
        if (qty != '' && !$.isNumeric(qty)) {
            alert("Adjustment quantity should be an integer value");
           
            $(this).focus();
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