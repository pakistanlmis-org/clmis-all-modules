<?php 
/**
 * sub_dist_reports
 * @package reports
 * 
 * @author     Ajmal Hussain 
 * @email <ahussain@ghsc-psm.org>
 * 
 * @version    2.2
 * 
 */
?>


<div class="row">
    <div class="col-md-12 right">
        <img src="<?php echo PUBLIC_URL;?>images/print-16.png" onClick="printContents()" alt="Print" style="cursor:pointer;" />
        <img src="<?php echo PUBLIC_URL;?>images/excel-16.png" onClick="tableToExcel('export', 'sheet 1', '<?php echo (isset($fileName)?$fileName:'export'); ?>')" alt="Excel" style="cursor:pointer;" />
    </div>
</div>
<div class="row" id="export">
<style>
    table#myTable{margin-top:0px !important; color: #000;}
    table#myTable{margin-top:20px;border-collapse: collapse;border-spacing: 0; border:1px solid #999;}
    table#myTable tr td{font-size:11px;padding:3px; text-align:left; border:1px solid #999; color: #000;}
    table#myTable tr th{font-size:11px;padding:3px; text-align:center; border:1px solid #999; color: #000;}
    table#myTable tr td.TAR{text-align:right; padding:5px;width:50px !important;}
    .sb1NormalFont {color: #444444; font-size: 11px; font-weight: bold; text-decoration: none;}
    p{margin-bottom:5px; font-size:11px !important; line-height:1 !important; padding:0 !important; color: #000;}
    table#headerTable tr td{ font-size:11px; color: #000;}
    h4{margin:0; color: #000; font-size:14px;}
    h5{margin:15px 0 5px 0; color: #000;}
    h6{margin:0; color: #000; font-size:12px;}
    .right{text-align:right !important;}
    .center{text-align:center !important;}

    /* Print styles */
    @media only print
    {
        table#myTable{margin-top:0px !important;}
        table#myTable tr th{font-size:8px;padding:3px !important; text-align:center; border:1px solid #999; color: #000;}
        table#myTable tr td{font-size:8px;padding:3px !important; text-align:left; border:1px solid #999; color: #000;}
        #doNotPrint{display: none !important;}
        h4{margin:0; color: #000;}
        h5{margin:0; color: #000;}
		h6{margin:0; color: #000;}
        p{margin-bottom:5px; font-size:11px !important; line-height:1 !important; padding:0 !important; color: #000;}    
    }
</style>

<script src="<?php echo PUBLIC_URL;?>js/tableToExcel.js"></script>
<script>
	function printContents() {
		var w = 900;
		var h = screen.height;
		var left = Number((screen.width / 2) - (w / 2));
		var top = Number((screen.height / 2) - (h / 2));
		var dispSetting = "toolbar=yes,location=no,directories=yes,menubar=yes,scrollbars=yes,left=" + left + ",top=" + top + ",width=" + w + ",height=" + h;
		var printingContents = document.getElementById("export").innerHTML;
		var docprint = window.open("", "", dispSetting);
		docprint.document.open();
		docprint.document.write('<html><head>');
		docprint.document.write('</head><body onLoad="self.print();self.close();"><center>');
		docprint.document.write(printingContents);
		docprint.document.write('</center></body></html>');
		docprint.document.close();
		docprint.focus();
	}
	
</script>
