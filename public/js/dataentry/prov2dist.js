$(function(){
	$('#wh_type').change(function(){
		//alert("test");
		if ($(this).val() == 4 || $(this).val() == 5)
		{
			if ($(this).val() == 5)
			{
				$('#wh_prov_sel').val(1);
			}
			$('#wh_prov_col').show();
			$.ajax({
				type: "POST",
				url: "provWH.php",
				data: {SkOfcLvl: $('#wh_type').val(),combo: '1'},
				dataType: 'html',
				success: function(data)
				{
					$('#warehouse_id').html(data);
				}
			});
		}
		else
		{
			$('#wh_prov_col').hide();
			$('#loader').show();
			$.ajax({
				type: "POST",
				url: "provWH.php",
				data: {SkOfcLvl: $(this).val(),combo:''},
				dataType: 'html',
				success: function(data)
				{
					$('#loader').hide();
					$('#warehouse_id').html(data);
				}
			});
		}
	});
	
	$('#wh_prov_sel').change(function(){
		$.ajax({
			type: "POST",
			url: "provWH.php",
			data: {SkOfcLvl: $('#wh_type').val(),combo: $(this).val()},
			dataType: 'html',
			success: function(data)
			{
				$('#warehouse_id').html(data);
			}
		});
	});

	$('#prov_sel').change(function(){
		$('#loader').show();
		$.ajax({
			type: "POST",
			url: "prov2dist.php",
			data: {prov_sel: $(this).val(), combo: '2'},
			dataType: 'html',
			success: function(data)
			{
				$('#loader').hide();
				$('#dist_id').html(data);
				$('#teh_id').empty();
				$('#uc_id').empty();
			}
		});
	});
	
	$('#dist_id').change(function(){
		var dist_id = $(this).val();
		$('#loader').show();
		$.ajax({
			type: "POST",
			url: "prov2dist.php",
			data: {dist_sel: dist_id, combo: '4'},
			dataType: 'html',
			success: function(data)
			{
				/*$('#hidden_dist').val(dist_id);
				$('#hidden_teh').val($('#teh_id').val());*/
				$('#loader').hide();
				$('#teh_id').html(data);
			}
		});
		$.ajax({
			type: "POST",
			url: "prov2dist.php",
			data: {dist_sel: dist_id, combo: '5'},
			dataType: 'html',
			success: function(data)
			{
				$('#loader').hide();
				$('#uc_id').html(data);
			}
		});
	});
	
	$('#teh_id').change(function(){
		var teh_id = $(this).val();
		$('#loader').show();
		$.ajax({
			type: "POST",
			url: "prov2dist.php",
			data: {teh_sel: teh_id, combo: '5'},
			dataType: 'html',
			success: function(data)
			{
				/*$('#hidden_dist').val(dist_id);
				$('#hidden_teh').val($('#teh_id').val());*/
				$('#loader').hide();
				$('#uc_id').html(data);
			}
		});
	});	
	
});