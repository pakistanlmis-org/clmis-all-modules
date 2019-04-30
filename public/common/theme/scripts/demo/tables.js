/* ==========================================================
 * AdminKIT v1.5
 * tables.js
 * 
 * http://www.mosaicpro.biz
 * Copyright MosaicPro
 *
 * Built exclusively for sale @Envato Marketplaces
 * ========================================================== */


$(function ()
{
    /* DataTables */
    if ($('.dynamicTable').size() > 0)
    {
        $('.dynamicTable').dataTable({
            "sPaginationType": "bootstrap",
            "sDom": "<'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "aaSorting": []
        });
    }/* DataTables */

    if ($('.dynamicTable2').size() > 0)
    {
        var datatable = $('.dynamicTable2').dataTable({
            "aoColumnDefs": [
                {"bSortable": false, "aTargets": [-1]}
                /*{
                 "aTargets": [-1],
                 "bVisible": false
                 }*/
            ],
            "sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "sTitle": "Location List",
                        "mColumns": [0]
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": [0],
                        "sTitle": "Location List",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
    if ($('.receivesearch').size() > 0)
    {
        var datatable = $('.receivesearch').dataTable({
            "aoColumnDefs": [
                {"sType": 'date-uk', "aTargets": [4]}
                //{ "bSortable": false, "aTargets": [-1] }
                /*{
                 "aTargets": [-1],
                 "bVisible": false
                 }*/
            ],
            "aaSorting": [],
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            "sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "Stock Receive Search",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": "visible"
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": "visible",
                        "sTitle": "Stock Receive Search",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
    if ($('.shipmentsearch').size() > 0)
    {
        var datatable = $('.shipmentsearch').dataTable({
            "aoColumnDefs": [
                {"sType": 'date-uk', "aTargets": [4]}
                //{ "bSortable": false, "aTargets": [-1] }
                /*{
                 "aTargets": [-1],
                 "bVisible": false
                 }*/
            ],
            "aaSorting": [],
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            "sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "Shipment Search",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": "visible"
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": "visible",
                        "sTitle": "Shipment Search",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
    // Batch Management
    if ($('.batchmanagement').size() > 0)
    {
        var datatable = $('.batchmanagement').dataTable({
            "aoColumnDefs": [
                {"sType": 'date-uk', "aTargets": [4]},
                {"bSortable": false, "aTargets": [-1]}
                /*{
                 "aTargets": [-1],
                 "bVisible": false
                 }*/
            ],
            "aaSorting": [],
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            "sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "Batch Management",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": [0, 1, 2, 3, 4, 5, 6, 7, 8]
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": [0, 1, 2, 3, 4, 5, 6, 7, 8],
                        "sTitle": "Batch Management",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }

    if ($('.bincard').size() > 0)
    {
        var datatable = $('.bincard').dataTable({
            "aoColumnDefs": [
                {"sType": 'date-uk', "aTargets": [6]}
                //{ "bSortable": false, "aTargets": [-1] }
                /*{
                 "aTargets": [-1],
                 "bVisible": false
                 }*/
            ],
            "aaSorting": [],
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "Bin Card",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": "visible"
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": "visible",
                        "sTitle": "Bin Card",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }

    if ($('.adjustment').size() > 0)
    {
        var datatable = $('.adjustment').dataTable({
            "aoColumnDefs": [
                {"sType": 'date-uk', "aTargets": [0]}
                //{ "bSortable": false, "aTargets": [-1] }
                /*{
                 "aTargets": [-1],
                 "bVisible": false
                 }*/
            ],
            "aaSorting": [],
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            //"sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            //"aaSorting": [[1, 'desc'], [4, 'desc']],
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "Stock Adjustments",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": "visible"
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": "visible",
                        "sTitle": "Stock Adjustments",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }

    if ($('.requisitions').size() > 0)
    {
        var datatable = $('.requisitions').dataTable({
            "aaSorting": [],
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            //"sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            //"aaSorting": [[1, 'desc'], [4, 'desc']],
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "Requisitions",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": [0, 1, 2, 3, 4, 5, 6, 7],
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": [0, 1, 2, 3, 4, 5, 6, 7],
                        "sTitle": "Requisitions1",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
    
    if ($('.my_custom_datatable').size() > 0)
    {
           var datatable = $('.my_custom_datatable').dataTable({
            "aaSorting": [],
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            //"sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            //"aaSorting": [[1, 'desc'], [4, 'desc']],
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "Requisition",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": [0, 1, 2, 3, 4, 5, 6, 7],
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": [0, 1, 2, 3, 4, 5, 6, 7],
                        "sTitle": "Requisition",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    
    }
    if ($('.issuesearch').size() > 0)
    {
        var datatable = $('.issuesearch').dataTable({
            "aoColumnDefs": [
                {"sType": 'date-uk', "aTargets": [4, 10]}
                //{ "bSortable": false, "aTargets": [-1] }
                /*{
                 "aTargets": [-1],
                 "bVisible": false
                 }*/
            ],
            "aaSorting": [],
            "aLengthMenu": [
                [10, 15, 20, -1],
                [10, 15, 20, "All"] // change per page values here
            ],
            //"sPaginationType": "bootstrap",
            //"sDom": 'W<"clear">lfrtip',
            // "sDom": 'T<"clear">lfrtip',
            "sDom": "<'row'<'col-md-11'>T<'clear'>><'row'<'col-md-6'l><'col-md-6'f>r>t<'row'<'col-md-6'i><'col-md-6'p>>",
            // "sDom": '<"clear">lfrtipT',
            "oLanguage": {
                "sLengthMenu": "_MENU_ records per page"
            },
            //"aaSorting": [[1, 'desc'], [4, 'desc']],
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "Stock Issue Search",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": "visible"
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": "visible",
                        "sTitle": "Stock Issue Search",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
    if ($('.districtMOS').size() > 0)
    {
        var datatable = $('.districtMOS').dataTable({
            "sDom": '<"clear">T',
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            "oTableTools": {
                "aButtons": [
                    {
                        "sExtends": "xls",
                        "sTitle": "District MOS",
                        "sButtonText": "<img src=../../public/images/excel-16.png>",
                        "mColumns": "visible"
                    },
                    {
                        "sExtends": "pdf",
                        "sButtonText": "<img src=../../public/images/pdf-16.png>",
                        "mColumns": "visible",
                        "sTitle": "District MOS",
                        "sPdfOrientation": "landscape"
                    }

                ],

                "sSwfPath": basePath + "common/theme/scripts/plugins/tables/DataTables/extras/TableTools/media/swf/copy_csv_xls_pdf.swf"
            }

        });

    }
   
});