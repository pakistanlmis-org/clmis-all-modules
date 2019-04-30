$(function(){
	$("#date_from,#date_to").datepicker({
		dateFormat: 'dd/mm/yy'
	});
});

$('#print_stock').click(
			function()
			{
				window.open('stock_received_list.php','_blank','width=842,height=595');
			}
		);
