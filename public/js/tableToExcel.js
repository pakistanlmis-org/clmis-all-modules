var tableToExcel = (function () {
	var uri = 'data:application/vnd.ms-excel;base64,',
	template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
	base64 = function (s) { return window.btoa(unescape(encodeURIComponent(s))) },
	format = function (s, c) { return s.replace(/{(\w+)}/g, function (m, p) { return c[p]; }) }
	return function (table, name, filename) {
		
		$('table#myTable').css('border', 'none');
		$('table#myTable tr th').css('border', 'none');
		$('table#myTable tr td').css('border', 'none');

		if (!table.nodeType) table = document.getElementById(table)
			var ctx = { worksheet: name || 'Worksheet', table: table.innerHTML }
			
			$('table#myTable').css('border', 'border:1px solid #999;');
			$('table#myTable tr th').css('border', 'border:1px solid #999;');
			$('table#myTable tr td').css('border', 'border:1px solid #999;');
			
			// Create Element
			var newA = document.createElement('a');
			newA.setAttribute('id',"dlink");
			document.body.appendChild(newA);
			
			document.getElementById("dlink").href = uri + base64(format(template, ctx));
			document.getElementById("dlink").download = filename+'.xls';
			document.getElementById("dlink").click();
			
			// Remove Element
			document.body.removeChild(newA);
		}
})()