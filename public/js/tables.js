/* ==========================================================
 * AdminKIT v1.5
 * tables.js
 * 
 * http://www.mosaicpro.biz
 * Copyright MosaicPro
 *
 * Built exclusively for sale @Envato Marketplaces
 * ========================================================== */ 


$(function()
{
	/* DataTables */
	if ($('.dynamicTable').size() > 0)
	{
		$('.dynamicTable').dataTable({
                        "sPaginationType": "bootstrap",
			"sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
			"oLanguage": {
				"sLengthMenu": "_MENU_ records per page"
			},
			"aaSorting": []
		});
	}/* DataTables */

	if ($('.dynamicTable2').size() > 0)
    {
        var datatable = $('.dynamicTable2').dataTable({
            "sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
           // "sDom": 'T<"clear">lfrtip',
          "sDom": "<'row-fluid'<'span11'>T<'clear'>><'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sButtonText": "<img src=../images/excel-16.png>",
                        "mColumns": "visible"
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../images/pdf-16.png>",
                        "mColumns": "visible",
                        "sTitle": "Training Database",
                        "sPdfOrientation": "landscape"
                    }
                    
                ],
                
                "sSwfPath": basePath  + "/common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
	if ($('.receivesearch').size() > 0)
    {
        var datatable = $('.receivesearch').dataTable({
            "sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
           // "sDom": 'T<"clear">lfrtip',
          "sDom": "<'row-fluid'<'span11'>T<'clear'>><'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sButtonText": "<img src=../images/excel-16.png>",
                        "mColumns": "visible"
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../images/pdf-16.png>",
                        "mColumns": "visible",
                        "sTitle": "Training Database",
                        "sPdfOrientation": "landscape"
                    }
                    
                ],
                
                "sSwfPath": basePath  + "/common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
});