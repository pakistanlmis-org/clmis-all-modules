$(function(){
	$('#office').change(function(){
		if(parseInt($('#office').val()) > 4){
			$('#store-help-block').hide();
		}else{
			$('#store-help-block').show();
		}
		
		$('#div_combo1').hide();
		//$('#div_combo2').hide();
		$('#wh_combo').hide();
		$('#loader').show();

		$.ajax({
			type: "POST",
			url: "levelcombos_action.php",
			data: {office: $(this).val(),mainstkid :$('#mainstkid').val() },
			dataType: 'html',
			success: function(data){
				$('#loader').hide();
				var val1 = $('#office').val();
					switch(val1){
						case '1': 
							$('#wh_combo').show();
							$('#warehouse').html(data);
						break;
						case '2': 
							$('#lblcombo1').text('Province');
							$('#div_combo1').show();
							$('#combo1').html(data);
						break;
						case '3': 
							$('#lblcombo1').text('Division');
							$('#div_combo1').show();
							$('#combo1').html(data);
						break;
						case '4': 
							$('#lblcombo1').text('District');
							$('#div_combo1').show();
							$('#combo1').html(data);
						break;
						case '5': 
							$('#wh_combo').show();
							$('#warehouse').html(data);
						break;
						case '6': 
							$('#wh_combo').show();
							$('#warehouse').html(data);
						break;
						case '7': 
							$('#wh_combo').show();
							$('#warehouse').html(data);
						break;
						case '8':
							$('#wh_combo').show();
							$('#warehouse').html(data);
						break;
						case '9': 
							$('#wh_combo').show();
							$('#warehouse').html(data);
						break;
					}
			}
		});
	});
	
	$('#combo1').change(function(){
		$('#loader').show();
		//$('#div_combo2').hide();
		$('#wh_combo').hide();
		$.ajax({
			type: "POST",
			url: "levelcombos1_action.php",
			data: {combo1: $(this).val(), office: $('#office').val()},
			dataType: 'html',
			success: function(data){
				$('#loader').hide();
				
				var val = $('#office').val();
				switch(val)
				{
				case '1': 
					$('#wh_combo').show();
					$('#warehouse').html(data);
				break;
				case '2': 
					$('#wh_combo').show();
					$('#warehouse').html(data);
				break;
				case '3': 
					$('#wh_combo').show();
					$('#warehouse').html(data);
				break;
				case '7': 
					$('#wh_combo').show();
					$('#warehouse').html(data);
				break;

				case '3':
					$('#wh_combo').show();
					$('#warehouse').html(data);
				//$('#lblcombo2').text('Divisions');
				//	$('#div_combo2').show();
				//	$('#combo2').show();
//					$('#combo2').html(data);				
				break;
				case '4':
					$('#wh_combo').show();
					$('#warehouse').html(data);
/*					$('#lblcombo2').text('Districts');
					$('#div_combo2').show();
					$('#combo2').show();
					$('#combo2').html(data);	*/			
				break;
				case '8':
					$('#div_combo2').show();
					$('#combo2').show();
					$('#combo2').html(data);				
				break;
				case '9':
					$('#div_combo2').show();
					$('#combo2').show();
					$('#combo2').html(data);				
				break;

				}
			}		
		});
	});
	
	$('#combo2').change(function(){
		$('#loader').show();
		$.ajax({
			type: "POST",
			url: "levelcombos2_action.php",
			data: {division: $(this).val()},
			dataType: 'html',
			success: function(data){
				$('#loader').hide();
					$('#wh_combo').show();
					$('#warehouse').html(data);
			}		
		});
	});
});