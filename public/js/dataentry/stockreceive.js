$(function () {
    /*$("#date_from,#date_to").datepicker({
     dateFormat: 'dd/mm/yy',
     changeMonth: true,
     changeYear: true
     });*/

    var startDateTextBox = $('#date_from');
    var endDateTextBox = $('#date_to');

    startDateTextBox.datepicker({
        minDate: "-10Y",
        maxDate: 0,
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        onClose: function (dateText, inst) {
            if (endDateTextBox.val() != '') {
                var testStartDate = startDateTextBox.datepicker('getDate');
                var testEndDate = endDateTextBox.datepicker('getDate');
                if (testStartDate > testEndDate)
                    endDateTextBox.datepicker('setDate', testStartDate);
            } else {
                endDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime) {
            endDateTextBox.datepicker('option', 'minDate', startDateTextBox.datepicker('getDate'));
        }
    });
    endDateTextBox.datepicker({
        maxDate: 0,
        dateFormat: 'dd/mm/yy',
        changeMonth: true,
        changeYear: true,
        onClose: function (dateText, inst) {
            if (startDateTextBox.val() != '') {
                var testStartDate = startDateTextBox.datepicker('getDate');
                var testEndDate = endDateTextBox.datepicker('getDate');
                if (testStartDate > testEndDate)
                    startDateTextBox.datepicker('setDate', testEndDate);
            } else {
                startDateTextBox.val(dateText);
            }
        },
        onSelect: function (selectedDateTime) {
            startDateTextBox.datepicker('option', 'maxDate', endDateTextBox.datepicker('getDate'));
        }
    });



    $('#product').change(function () {

        $.ajax({
            type: "POST",
            url: "add_action_manufacturer.php",
            data: {
                show: 1,
                product: $(this).val()
            },
            dataType: 'html',
            success: function (data) {
                $('#manufacturer').html(data);
            }
        });

    });









});

$('[data-toggle="notyfy"]').click(function () {
    var self = $(this);

    notyfy({
        text: notification[self.data('type')],
        type: self.data('type'),
        dismissQueue: true,
        layout: self.data('layout'),
        buttons: (self.data('type') != 'confirm') ? false : [
            {
                addClass: 'btn btn-success btn-small btn-icon glyphicons ok_2',
                text: '<i></i> Ok',
                onClick: function ($notyfy) {
                    var id = self.attr("id");
                    $notyfy.close();
                    window.location.href = 'delete_receive.php?p=stock&id=' + id;
                }
            },
            {
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
            }
        ]
    });
    return false;
});

var notification = [];
notification['confirm'] = 'Do you want to continue?';

//$('#print_stock').click(
//        function () {
//            window.open('stock_received_list.php', '_blank', 'scrollbars=1,width=842,height=595');
//        }
//);
//$('#print_vaccine_issue').click(
//        function () {
//            window.open('vaccine_placement_issue.php', '_blank', 'scrollbars=1,width=842,height=595');
//        }
//);


$('#print_vaccine_issue').click(
        function () {
            var qryString = 'grpBy=' + $('input[name="groupBy"]:checked').val();
            if ($('#searchby').val() != '') {
                qryString += '&searchby=' + $('#searchby').val();
            }
            if ($('#number').val() != '') {
                qryString += '&number=' + $('#number').val();
            }
            if ($('#funding_source').val() != '') {
                qryString += '&funding_source=' + $('#funding_source').val();
            }
            if ($('#product').val() != '') {
                qryString += '&product=' + $('#product').val();
            }
            if ($('#stakeholder').val() != '') {
                qryString += '&stakeholder=' + $('#stakeholder').val();
            }
            if ($('#province').val() != '') {
                qryString += '&province=' + $('#province').val();
            }
            if ($('#warehouse').val() != '') {
                qryString += '&warehouse=' + $('#warehouse').val();
            }
            if ($('#date_from').val() != '') {
                qryString += '&date_from=' + $('#date_from').val();
            }
            if ($('#date_to').val() != '') {
                qryString += '&date_to=' + $('#date_to').val();
            }

            window.open('receive_detail_print.php?' + qryString, '_blank', 'scrollbars=1,width=842,height=595');
        }
);
$('#print_vaccine_summary').click(
        function () {
            var qryString = 'type=' + $('input[name="summary"]:checked').val();
            if ($('#searchby').val() != '') {
                qryString += '&searchby=' + $('#searchby').val();
            }
            if ($('#number').val() != '') {
                qryString += '&number=' + $('#number').val();
            }
            if ($('#funding_source').val() != '') {
                qryString += '&funding_source=' + $('#funding_source').val();
            }
            if ($('#product').val() != '') {
                qryString += '&product=' + $('#product').val();
            }
            if ($('#stakeholder').val() != '') {
                qryString += '&stakeholder=' + $('#stakeholder').val();
            }
            if ($('#province').val() != '') {
                qryString += '&province=' + $('#province').val();
            }
            if ($('#warehouse').val() != '') {
                qryString += '&warehouse=' + $('#warehouse').val();
            }
            if ($('#date_from').val() != '') {
                qryString += '&date_from=' + $('#date_from').val();
            }
            if ($('#date_to').val() != '') {
                qryString += '&date_to=' + $('#date_to').val();
            }
            window.open('receive_summary_print.php?' + qryString, '_blank', 'scrollbars=1,width=842,height=595');
        }
);

$('#quantity').priceFormat({
    prefix: '',
    thousandsSeparator: ',',
    suffix: '',
    centsLimit: 0,
    limit: 6
});
$('#back').click(function () {
    parent.history.back();
    return false;
});

$('#reset').click(function () {
    window.location.href = appPath + 'im/stock_receive.php';
});

$('#print_coldchain').click(function () {
    var stockDetailId;
    stockDetailId = $('#stockDetailId').val();
    window.open('coldChainPlacementPrint.php?stockDetailId=' + stockDetailId, '_self', 'scrollbars=1,width=842,height=595');

});
function printit() {
    window.print();
}


function deleteRecord(detailId, batchId)
{
    if (confirm('Are you sure you want to delete this record? Action can not be undone. Continue?'))
    {
        $.ajax({
            data: {detailId: detailId, batchId: batchId},
            type: 'POST',
            url: 'delete_receive.php',
            success: function () {
                $('#' + detailId).remove();
            }
        })
    }
}