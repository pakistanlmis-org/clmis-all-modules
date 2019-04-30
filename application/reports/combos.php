<?php 

/**
 * combos
 * @package reports
 * 
 * @author     Ajmal Hussain
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
$showHFTypeArr = array('pwd3','sdp_p', 'spr2','pwd3y', 'spr2y', 'clr11y','outletp','spr10');
?>
<script>
	$(function() {
		<?php 
		if (in_array($rptId, $showHFTypeArr))
		{?>
			$('#prov_sel').change(function(e) {
				$('#hf_type_sel').html('<option value="">Select</option>');
				showHFType($(this).val(), '');
			});
		<?php 
		}
		else
		{
		?>
			showDistricts();
			$('#prov_sel').change(function(e) {
				$('#district').html('<option value="">Select</option>');
				showDistricts();
			});
		<?php
		}
                if($rptId == 'sdp_p' ){
                    ?>
                    showDistricts();
			$('#prov_sel').change(function(e) {
				$('#district').html('<option value="">Select</option>');
				showDistricts();
			});
                <?php
                }
		?>
	})
	function showDistricts()
	{
		var provinceId = $('#prov_sel').val();
		if (provinceId != '')
		{
			$.ajax({
				url: 'ajax_calls.php',
				data: {provinceId: provinceId, dId: '<?php echo $districtId; ?>', stkId: 1,rptId:'<?=(!empty($rptId)?$rptId:'')?>',<?=((!empty($rptId) && $rptId=='spr10')?'validate:\'no\'':'')?>},
				type: 'POST',
				success: function(data)
				{
					$('#districtDiv').html(data);
				}
			})
		}
	}
	
	function showHFType(provId, hfTypeId)
	{
		if ( provId != '' )
		{
			$.ajax({
				url: 'ajax_calls.php',
				type: 'post',
				data: {provId: provId, hfTypeId: hfTypeId},
				success: function(data){
					$('#hf_type_sel').html(data);
				}
			})
		}
	}
	
	
	$(function() {
		$('#from_date, #to_date').datepicker({
			dateFormat: "yy-mm",
			constrainInput: false,
			changeMonth: true,
			changeYear: true,
			maxDate: 0
		});
	})
</script>