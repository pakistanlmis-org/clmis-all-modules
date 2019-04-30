$(function(){
    
    $('#product').change(function(){
            $.ajax({
                    type: "POST",
                    url: "ajaxrunningbatches.php",
                    data: {product: $('#product').val()},
                    dataType: 'html',
                    success: function(data){
                                    $('#batch').html(data);
                            }		
            });
    });
                        
    var startDateTextBox = $('#date_from');
	var endDateTextBox = $('#date_to');
	
	startDateTextBox.datepicker({
		minDate: "-10Y",
        maxDate: 0,
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
		onClose: function(dateText, inst) {
			if (endDateTextBox.val() != '') {
				var testStartDate = startDateTextBox.datepicker('getDate');
				var testEndDate = endDateTextBox.datepicker('getDate');
				if (testStartDate > testEndDate)
					endDateTextBox.datepicker('setDate', testStartDate);
			}
			else {
				endDateTextBox.val(dateText);
			}
		},
		onSelect: function (selectedDateTime){
			endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate') );
		}
	});
	endDateTextBox.datepicker({
        maxDate: 0,
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
		onClose: function(dateText, inst) {
			if (startDateTextBox.val() != '') {
				var testStartDate = startDateTextBox.datepicker('getDate');
				var testEndDate = endDateTextBox.datepicker('getDate');
				if (testStartDate > testEndDate)
					startDateTextBox.datepicker('setDate', testEndDate);
			}
			else {
				startDateTextBox.val(dateText);
			}
		},
		onSelect: function (selectedDateTime){
			startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate') );
		}
	});
});
$('#print_stock').click(function(){
	var qryString = '';
	if($('#adjustment_no').val() != ''){
		qryString += '&adjustment_no=' + $('#adjustment_no').val();
	}if($('#type').val() != ''){
		qryString += '&type=' + $('#type').val();
	}if($('#product').val() != ''){
		qryString += '&product=' + $('#product').val();
	}if($('#date_from').val() != ''){
		qryString += '&date_from=' + $('#date_from').val();
	}if($('#date_to').val() != ''){
		qryString += '&date_to=' + $('#date_to').val();
	}
	window.open('stock_adjustmentPrint.php?' + qryString, '_blank','scrollbars=1,width=842,height=595');
});

function PrintIt(){
	window.print();
}