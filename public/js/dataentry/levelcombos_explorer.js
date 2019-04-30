$(function(){
	
	$( document ).ready(function() {
		$('#div_combo1').hide();
		$('#div_combo2').hide();
		$('#div_combo3').hide();
		$('#div_combo4').hide();
		$('#div_combo5').hide();
		$('#wh_combo').hide();
	});
	
	$('#office').change(function(){
		$('#loader').show();
		$('#div_combo1').hide();
		$('#div_combo2').hide();
		$('#div_combo3').hide();
		$('#div_combo4').hide();
		$('#div_combo5').hide();
		$('#wh_combo').hide();
		$.ajax({
			type: "POST",
			url: basePath+"plmis_src/operations/levelcombos_explorer_action.php",
			data: {office: $(this).val()},
			dataType: 'html',
			success: function(data){
				$('#loader').hide();
				var val1 = $('#office').val();
				alert(val1);				
					switch(val1){
						case '1':
							$('#wh_l').html('Warehouse');
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
						break;					}
			}
		});
	});
	
	$('#combo1').change(function(){
		$('#loader').show();
		$('#div_combo2').hide();
		$('#div_combo3').hide();
		$('#div_combo4').hide();
		$('#div_combo5').hide();
		$('#wh_combo').hide();

		$.ajax({
			type: "POST",
			url: basePath+"plmis_src/operations/levelcombos_explorer_action2.php",
			data: {combo1: $(this).val(), office: $('#office').val()},
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
				

				}
			}		
		});
	});
	
	$('#combo2').change(function(){
		$('#loader').show();
		$.ajax({
			type: "POST",
			url: basePath+"plmis_src/operations/levelcombos_explorer_action.php",
			data: {combo2: $(this).val(), office: $('#office').val()},
			dataType: 'html',
			success: function(data){
				$('#loader').hide();
					$('#wh_combo').show();
					$('#warehouse').html(data);
			}		
		});
	});
});