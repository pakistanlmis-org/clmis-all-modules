$(function(){
	$('#office').change(function(){
		if(parseInt($('#office').val()) == 1 || parseInt($('#office').val()) > 4){
			$('#store-help-block').hide();
		}else{
			$('#store-help-block').show();
		}
	})
	
	
	if ( $('#showSelection').val() > 0 )
	{
		showCombos();
	}
	
	$('#office').change(function(){
		showCombos();
	});
	
	$('#combo1').change(function(){
		showCombos2();
	});
	
	$('#combo2').change(function(){
		showCombos3();
	});
});
$('#mainstkid').change(function(){
	$('#office').val('');
	$('#office-span').show();
	$('#combo1').empty();
	$('#combo2').empty();
	$('#warehouse').empty();
	$('#div_combo1').hide();
	$('#div_combo2').hide();
	$('#wh_combo').hide();
});

function showCombos3()
{
	$('#loader').show();
	$.ajax({
		type: "POST",
		url: appPath+"im/levelcombos2_all_levels_action.php",
		data: {combo2: $('#combo2').val(), office: $('#office').val(),mainstkid:$('#mainstkid').val()},
		dataType: 'html',
		success: function(data){
				$('#loader').hide();
				$('#wh_combo').show();
				$('#wh_1').html('Store');
				$('#warehouse').html(data);
		}		
	});
}

function showCombos2()
{
	if ( $('#office').val() > 1 )
	{
		$('#loader').show();
		$('#combo2').empty();
		
		$('#warehouse').empty();
	
		$('#div_combo2').hide();
		$('#wh_combo').hide();
	
		$.ajax({
			type: "POST",
			url: appPath+"im/levelcombos1_all_levels_action.php",
			data: {combo1: $('#combo1').val(), office: $('#office').val(),mainstkid:$('#mainstkid').val()},
			dataType: 'html',
			success: function(data){
				$('#loader').hide();
				
				var val = $('#office').val();
				switch(val)
				{
					case '2': 
						$('#wh_combo').show();
						$('#warehouse').html(data);
					break;
					case '3':
						$('#wh_combo').show();
						$('#warehouse').html(data);
					break;
					case '4':
						$('#wh_combo').show();
						$('#warehouse').html(data);
					break;
					case '5':
					$('#lblcombo2').text('Districts');
						$('#div_combo2').show();
						$('#combo2').show();
						$('#combo2').html(data);				
					break;
					case '6':
					$('#lblcombo2').text('Districts');
						$('#div_combo2').show();
						$('#combo2').show();
						$('#combo2').html(data);				
					break;
					case '7':
					$('#lblcombo2').text('Districts');
						$('#div_combo2').show();
						$('#combo2').show();
						$('#combo2').html(data);				
					break;
					case '8':
					$('#lblcombo2').text('Districts');
						$('#div_combo2').show();
						$('#combo2').show();
						$('#combo2').html(data);				
					break;
				}
				if ( $('#showSelection').val() > 0 && $('#office').val() > 4 )
				{
					showCombos3();
				}
			}
		});
	}
}
function showCombos()
{
	$('#office-span').show();
	$('#loader').hide();
	$('#combo1').empty();
	$('#combo2').empty();
	$('#warehouse').empty();
	$('#div_combo1').hide();
	$('#div_combo2').hide();
	$('#wh_combo').hide();
        var show_national = $('#show_national').val();
	$.ajax({
		type: "POST",
		url: appPath+"im/levelcombos_all_levels_action.php",
		data: {office: $('#office').val(),mainstkid:$('#mainstkid').val(),show_national:show_national},
		dataType: 'html',
		success: function(data){
			$('#loader').hide();
			var val1 = $('#office').val();
			switch(val1){
				case '1':
					$('#wh_l').html('Store');
					$('#wh_combo').show();
					$('#warehouse').html(data);
				break;
				case '2': 
					$('#lblcombo1').text('Province');
					$('#div_combo1').show();
					$('#combo1').html(data);
				break;
				case '3': 
					$('#lblcombo1').text('Province');
					$('#div_combo1').show();
					$('#combo1').html(data);
				break;
				case '4': 
					$('#lblcombo1').text('Province');
					$('#div_combo1').show();
					$('#combo1').html(data);
				break;
				case '5': 
					$('#lblcombo1').text('Province');
					$('#div_combo1').show();
					$('#combo1').html(data);
				break;
				case '6': 
					$('#lblcombo1').text('Province');
					$('#div_combo1').show();
					$('#combo1').html(data);
				break;
				case '7': 
					$('#lblcombo1').text('Province');
					$('#div_combo1').show();
					$('#combo1').html(data);
				break;
				case '8': 
					$('#lblcombo1').text('Province');
					$('#div_combo1').show();
					$('#combo1').html(data);
				break;
			}
			if ( $('#showSelection').val() > 0 )
			{
				showCombos2();
			}
		}
		
	});
}