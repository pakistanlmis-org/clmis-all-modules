$(function(){

	 $.ajax({
			type: "POST",
			url: "../../plmis_admin/ajaxrunningbatches_all_items.php",
			data: {product: ''},
			dataType: 'html',
			success: function(data){
				$('#running_batches').html(data);
				}		
			});
	 $("#print_issue").click(function()
				{
					var req_no,recip;
					req_no=$("#issue_no").val();
					recip=$("#recipient").val();
					refrenceno=$("#issue_ref").val();
					window.open('stock_issue_voucher.php?req_no='+req_no+'&recip='+recip+'&refrenceno='+refrenceno,'_blank','width=842,height=595');
				}
				);
});