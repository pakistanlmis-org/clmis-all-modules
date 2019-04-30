$(function(){
	$("#save_make").click(function(){
		var make;
		make=$('#new_make').val();
		if(make==''){
			alert('Please enter brand name.');
			return true;
		}
		$.ajax({
			type: "POST",
			url: "add_action.php",
			data: {
				add_action: 1,
				new_make: make
					},
			dataType: 'html',
			success: function(data){
				$('#ccMake').html(data);
                $('#ccMake2').html(data);
				$('#new_make').val('');
			}
			});
		});
		$("#save_model").click(function(){
			var make,model;
			make=$('#ccMake2').val();
			model=$('#new_model').val();
			if(make=='' || model==''){
				alert('Please enter model.');
				return false;
			}
			$.ajax({
				type: "POST",
				url: "add_action.php",
				data: {
					add_action: 1,
					model_make: make,
					new_model: model
						},
				dataType: 'html',
				success: function(data){
					$('#ccModel').html(data);
                    $('#ccMake').val(make);
					$('#ccMake2').val('');
				}
				});
		});
		$('#save_asset').click(function(){
			var asset;
			asset=$('#new_asset').val();
			if(asset==''){
				alert('Please enter asset name.');
				return false;
			}
			$.ajax({
				type: "POST",
				url: "add_action.php",
				data: {
					add_action: 1,
					add_asset: asset
						},
				dataType: 'html',
				success: function(data){
					$('#asset_type').html(data);
					$('#new_asset').val('');
				}
				});
		});
	
	});


