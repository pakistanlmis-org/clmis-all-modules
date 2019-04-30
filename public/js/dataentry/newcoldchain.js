$(function () {
    $(document).ready(function () {
        /*$.ajax({
            type: "POST",
            url: "ajaxuccenters.php",
            data: {uc: $('#uc').val()},
            dataType: 'html',
            success: function (data) {
                $('#uc_center_div').show();
                $('#uc_center').html(data);
            }
        });*/

        $('#estimated_life').priceFormat({
            prefix: '',
            thousandsSeparator: '',
            suffix: '',
            centsLimit: 0,
            limit: 2
        });
        $('#cc_capacity').priceFormat({
            prefix: '',
            thousandsSeparator: ',',
            suffix: '',
            centsLimit: 0,
            limit: 6
        });

        $('#loader').show();
        /*$.ajax({
            type: "POST",
            url: "ccGetModelsfromMakes_action.php",
            data: {makeid: $('#ccMake').val(), cc_model: $('#model_prev').val()},
            dataType: 'html',
            success: function (data) {
                $('#loader').hide();
                $('#ccModel').html(data);
            }
        });*/
        var svalue = $('input[name=placed_at]:radio:checked').val();
        //var svalue = $("#ass_asset input[type='radio']:checked");
        //var svalue = $("#ass_asset input[type='radio']:checked").val();
        if (svalue == '0') {
            $('#levelcombos').hide();
        }
        else {
            $('#levelcombos').show();
        }

        $("#ass_asset").validate({
            rules: {
                asset_id: {
                    required: true,
                    maxlength:20
                },
                working_since: {
                    number: true,
                    range: [1960, 2100]
                }
            }
        });
    });

    $('#ccMake').change(function () {
        $('#loader').show();
        $.ajax({
            type: "POST",
            url: "ccGetModelsfromMakes_action.php",
            data: {makeid: $(this).val()},
            dataType: 'html',
            success: function (data) {
                $('#loader').hide();
                $('#ccModel').html(data);
            }
        });
    });

    $("#save_record").click(function(e){
		if($("#pk_asset_id").val()!='')
			{
			exit();
			}
	
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "ajax_check_asset.php",
            data: {assetid: $("#asset_id").val()},
            dataType: 'html',
            success: function (data) {
                if(data == 'true'){
                    var validator = $("#ass_asset").validate();
                    validator.showErrors({
                        "asset_id": "Asset ID already exists..."
                    });
                } else {
                    $("#ass_asset").submit();
                }
            }
        });
    });


    $('#placed_at0').click(
        function () {
            $('#levelcombos').hide();
        }
    );
    $('#placed_at1').click(
        function () {
            $('#levelcombos').show();
        }
    );
    $("#monthly_status").change(function () {
        var action = $(this).val();
        var url = 'coldchain_statusUpdate.php?chksts=MQ==&Do=' + action;
        window.location.href = url;
    });
    $('#uc').change(function () {
	$('#StatusTable').html('');
	$('#showMonths').html('');
	
        $.ajax({
            type: "POST",
            url: "ajaxuccenters.php",
            data: {uc: $('#uc').val()},
            dataType: 'html',
            success: function (data) {
                $('#uc_center_div').show();
                $('#uc_center').html(data);
            }
        });
    });
    $('#uc_center').change(function () {
        $('#last-report').html('');
        show3Months();
        showCombos();
    });

//    $('#working_since').datepicker({
//        minDate: "-10Y",
//        maxDate: 0,
//        dateFormat: 'dd/mm/yy'
//    });

    $('#reset').click(function(){
        window.location.href = basePath + 'plmis_admin/coldchain_statusUpdate.php';
    });
});
$('#printit').click(function () {

    window.print();
});
$('#print_coldchain').click(
    function () {
        window.open('coldchain_listPrint.php', '_blank', 'scrollbars=1,width=842,height=595');
    }
);
function show3Months() {
    //$('#showMonths').html('');
	//$('#StatusTable').html('');
    var wh_id = $('#uc_center').val();
    var loc_id = $('#uc').val();
    if (wh_id != '') {
        $.ajax({
            type: "POST",
            url: "loadLast3MonthsStatus.php",
            data: {wharehouse_id: wh_id, location_id: loc_id},
            success: function (data) {
                $('#showMonths').html(data);
            }
        });
    } else {
        $('#showMonths').html('');
    }
}
function showCombos() {
    var wh_id = $('#uc_center').val();
    if (wh_id != '') {
        $.ajax({
            type: "POST",
            url: "ajaxStatusCombo.php",
            data: {wharehouse_id: wh_id},
            success: function (data) {
                $('#monthly_status').html(data);
            }
        });
    } else {
        $('#monthly_status').html('<option value="">Month - Year</option>');
    }
}
