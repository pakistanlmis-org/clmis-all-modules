$(function() {
    $('#uc').change(function() {
        $.ajax({
            type: "POST",
            url: "ajaxuccenters.php",
            data: {uc: $('#uc').val()},
            dataType: 'html',
            success: function(data) {
                $('#uc_center_div').show();
                $('#uc_center').html(data);
            }
        });
    });
    
    // load month - year reports
    $('#warehouse').change(function() {
		showCombos();
    });
		
	$("#monthly_report").change(function(e){
	e.preventDefault();
		var action = $(this).val();
		$.ajax({
            type: "POST",
            url: "view_admin_whreport_action.php",
            data: {Do: action},
            success: function(data) {
                $('#showReport').html(data);
            }
        });
	});
});

function showCombos() {
    var wh_id = $('#warehouse').val();
    if (wh_id != '') {
        $.ajax({
            type: "POST",
            url: basePath+"plmis_admin/ajaxreportcombo.php",
            data: {wharehouse_id: wh_id, type: 'explorer'},
            success: function(data) {
                $('#monthly_report').html(data);
				$('#monthly_report').css('backgroundColor', 'Green');
				setTimeout(changeColor, 1000);				
            }
        });
    } else {
        $('#monthly_report').html('<option value="">Month - Year</option>');
    }
}

function changeColor(){
	$('#monthly_report').css('backgroundColor', 'White');
}