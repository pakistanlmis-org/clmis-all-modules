//$('#add-rows').hide();
var form_clean;

$(function () {

    form_clean = $("#future_arrival").serialize();

    // Auto Save function call
    //setInterval(autoSave, 20000);

    $("#expected_arrival_date").datepicker({
        minDate: "-5Y",
        maxDate: "+5Y",
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        defaultDate: new Date()
    });

    // validate signup form on keyup and submit
    $("#future_arrival").validate({
        rules: {
            'from_warehouse_id': {
                required: true
            },
            'expected_arrival_date': {
                required: true
            },
            'reference_number': {
                required: true
            }
        }
    });

    
	$("#add_more").click(function(){
		 $("#mytable").each(function () {
			 var tds = '<tr>';
			 jQuery.each($('tr:last td', this), function () {
				 tds += '<td>' + $(this).html() + '</td>';
			 });
			 tds += '</tr>';
			 if ($('tbody', this).length > 0) {
				 $('tbody', this).append(tds);
			 } else {
				 $(this).append(tds);
			 }
		 });
	});
	
	$("input[name='production_date[]'], input[name='expiry_date[]']").datepicker({
        minDate: "-10Y",
        maxDate: 0,
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        defaultDate: new Date()
    });

    $("input[name='quantity[]']").priceFormat({
        prefix: '',
        thousandsSeparator: ',',
        suffix: '',
        centsLimit: 0,
        limit: 10
    });

    $("input[name='unit_price[]']").priceFormat({
        prefix: '',
        thousandsSeparator: '',
        suffix: '',
        centsLimit: 2
    });
	
});

$('body').on('focus', ".hasDatepicker", function(){
    $(this).datepicker({
        minDate: "-10Y",
        maxDate: 0,
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        defaultDate: new Date()
    });
});

function autoSave() {
    var form_dirty = $("#future_arrival").serialize();
    if (form_clean != form_dirty)
    {
        $('#add_stock').attr('disabled', 'disabled');

        $.ajax({
            type: "POST",
            url: appName + "/stock/ajax-pipeline-consignments-draft",
            data: $('#future_arrival').serialize(),
            cache: false,
            success: function (data) {
                if (data == true) {
                    $('#notific8_show').trigger('click');
                }
                $('#add_stock').removeAttr('disabled');
            }
        });
        form_clean = form_dirty;
    }
}