$(function() {


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
                        var sh_id = self.attr("shipment_id");
                        $notyfy.close();
                        window.location.href = 'delete_shipment_receive.php?id=' + id+'&shipment_id='+sh_id;
                    }
                },
                {
                    addClass: 'btn btn-danger btn-medium btn-icon glyphicons remove_2',
                    text: '<i></i> Cancel',
                    onClick: function($notyfy) {
                        $notyfy.close();
                        
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
        //window.location.href = appPath + 'im/new_receive.php';
        history.go(-1);
    });
});


$('#print_vaccine_placement').click(function() {
    var ref_no, rec_no, rec_date, unit_pric, rec_from, stock_id;
    ref_no = $('#receive_ref').val();
    rec_no = $('#receive_no').val();
    rec_date = $('#receive_date').val();
    rec_from = $('#source_name').val();
    stock_id = $('#stock_id').val();
    window.open('shipment_receive_print.php?id='+stock_id+'&rec_no=' + rec_no + '&ref_no=' + ref_no + '&rec_date=' + rec_date + '&rec_from=' + rec_from, '_blank', 'scrollbars=1,width=842,height=595');
});

var notification = [];
notification['confirm'] = 'Do you want to continue?';

$('#qty').priceFormat({
    prefix: '',
    thousandsSeparator: '',
    suffix: '',
    centsLimit: 0,
    limit: 10
});

